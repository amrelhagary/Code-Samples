<?php
require_once 'Logger.php';
require_once dirname(__FILE__).'/../vendor/sms.class.php';
require_once dirname(__FILE__).'/../vendor/swiftmailer/swiftmailer/lib/swift_required.php';

class AlertService
{
    private static $instance;
    private static $config;
    private static $contacts;
    private static $logger;

    public function __construct($config){
        $this->init($config);
    }

    private function init($config){
        self::$config = $config;
        self::$logger = Logger::getInstance();
    }

    public static function isStopNotification(){
        $now = strtotime(date('l H:i:s'));
        $stopNotificationFrom = strtotime(self::$config['notification']['stop']['from']);
        $stopNotificationTo = strtotime(self::$config['notification']['stop']['to']);

        if($now >= $stopNotificationFrom && $now <= $stopNotificationTo || ( defined('ENV') && ENV == 'dev' ))
        {
            return true;
        }else{
            return false;
        }
    }

    public static function getInstance($config){
        if(is_null(self::$instance)){
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    public static function AlertAllInOneSMS($failedRequests)
    {
        if(self::isStopNotification())
        {
            self::$logger->log('Alert SMS is Disabled');
            return;
        }

        $smsContent = self::prepareOutputForSMS($failedRequests);

        $mobiles = array();

        // send to interested contacts
        foreach($failedRequests as $req){
            $contacts = self::getContact($req);

            if(!$contacts)
                continue;

            foreach($contacts as $name => $contact){
                $mobiles[] = $contact['mobile'];
            }

            if(count($mobiles) == 0){
                contine;
            }

            self::sendSMS($smsContent,$mobiles);
        }

        $mobiles = array();
        // send to general contacts
        foreach(self::$config['contacts'] as $name => $contact)
        {
            if(isset($contact['mobile'])){
                $mobiles[] = $contact['mobile'];
            }
        }

        if(sizeof($mobiles) == 0){
            Logger::getInstance()->log("No SMS to Send");
            return false;
        }

        self::sendSMS($smsContent,$mobiles);
    }

    private function sendSMS($smsContent,$mobiles){
        if(is_array($smsContent))
        {
            // send multiple sms
            $status = array();
            foreach($smsContent as $message)
            {
                $status = self::send($message,$mobiles);
            }
        }else{

            $status = self::send($smsContent,$mobiles);
        }

        Logger::getInstance()->log("Send SMS To ",$mobiles,$status);
    }

    private static function send($message,$mobiles,$batch=false)
    {
        $config = self::$config;
        if(sizeof($mobiles) > 1)
        {
            $batch = true;
        }

        $mobileNos = implode(',',$mobiles);

        $SMSConfig = $config['sms'];
        $sms = new SMS();
        $sms->setUserName($SMSConfig['username']);
        $sms->setPassWord($SMSConfig['password']);
        $sms->set_api_id($SMSConfig['api_id']);
        $sms->setMsgBody($message);
        $sms->setNumberToSend($mobileNos);
        $sms->setbatch($batch);
        $sms->setFrom($SMSConfig['from']);
        $sms->authenticate();
        $sms->sendEnglishSMS();
        return $sms->getReturnResult();

    }


    private static function prepareOutputForSMS($failedRequests)
    {
        $maxMessageSize = 160;

        $txt = "";
        foreach($failedRequests as $req){
            foreach($req as $websiteCode => $info){
                $status = ($info['condition_result'] == true) ? 'OK' : 'Failed';
                $txt .= "{$websiteCode} \n Pg: {$info['code']} \t HTTP: {$info['http_status_code']} \t RST: {$status}\n";
            }
        }

        //divide message to multiple message
        if(strlen($txt) > $maxMessageSize)
        {
            $n = ceil(strlen($txt) / $maxMessageSize);
            $start = 0;
            $parts = array();
            for($i=0;$i<$n;$i++)
            {
                $parts[] = substr($txt,$start,$maxMessageSize);
                $start += $maxMessageSize;
            }
            $txt = $parts;
        }
        return $txt;
    }

    public static function AlertByEmail($failedRequests)
    {
        if(self::isStopNotification())
        {
            self::$logger->log('Alert Email is Disabled');
            return;
        }

        $message = self::prepareOutputForEmail($failedRequests);

        // send to interested contacts
        $emails = array();
        foreach($failedRequests as $req){
            $contacts = self::getContact($req);

            if(!$contacts)
                continue;

            foreach($contacts as $name => $contact){
                $emails[] = $contact['email'];
            }

            if(count($emails) == 0){
                contine;
            }

            self::sendEmail($message,$emails);
        }

         // send to general contacts
        $contacts = self::$config['contacts'];
        $emails = array();
        foreach($contacts as $name => $contact){
            $emails[] = $contact['email'];
        }

        self::sendEmail($message,$emails);
    }

    private function getContact($req){

        foreach($req as $codeName => $info)
        {
            foreach(self::$config['websites'] as $website)
            {
                foreach($website as $params){

                    if(!empty($params['page_url']) && $params['page_url'] == $info['url'])
                    {
                        if(!empty($params['contacts']))
                        {
                            return $params['contacts'];
                            break;
                        }
                    }
                }
            }
        }
        return false;
    }

    // get all contacts merged
    private static function getContacts($reqs)
    {
        $config = self::$config;
        if(is_null(self::$contacts))
        {
            $interestedContacts = array();
            foreach($reqs as $req)
            {
                foreach($req as $alias => $info)
                {
                    foreach($config['websites'] as $website)
                    {
                        foreach($website as $params){

                            if(!empty($params['page_url']) && $params['page_url'] == $info['url'])
                            {
                                if(!empty($params['contacts']))
                                {
                                    $interestedContacts = $params['contacts'];
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            self::$contacts = array_merge($interestedContacts,$config['contacts']);
        }

        return self::$contacts;
    }

    private static function sendEmail($message,$emails)
    {
        $numSent = self::sendEmailBySwiftMailer($message,$emails);
        Logger::getInstance()->log("No of email send: ".$numSent);
    }

    private static function initSwiftMailer()
    {
        $config = self::$config;
        if((
            empty($config['smtp']['host'])     ||
            empty($config['smtp']['port'])     ||
            empty($config['smtp']['username']) ||
            empty($config['smtp']['password'])
            ) &&
            $config['email']['method'] != 'smtp'
            ){
            throw new Exception('Error Missing SMTP configuration');
        }

        if($config['email']['method'] == 'smtp')
        {
            $transport = Swift_SmtpTransport::newInstance($config['smtp']['host'],$config['smtp']['port'],$config['smtp']['ssl'])
                        ->setUsername($config['smtp']['username'])
                        ->setPassword($config['smtp']['password']);
        }else{
            $transport = Swift_MailTransport::newInstance();
        }
      
        $mailer = Swift_Mailer::newInstance($transport);

        return $mailer;
    }

    private static function sendEmailBySwiftMailer($messageContent,$emails)
    {
        try{
            $mailer = self::initSwiftMailer();
            $message = Swift_Message::newInstance()
                ->setSubject('Yellow Monitor Service Down')
                ->setFrom(array('no-replay@yellow.com.eg' => 'Yellow Monitor'))
                ->setTo($emails)
                ->addPart($messageContent,'text/html');

            return $mailer->send($message);
        }catch (Exception $e){
            Logger::getInstance()->log($e->getMessage());
        }

    }

    private function prepareOutputForEmail($failedRequests)
    {
        // include email template
        ob_start();
        include 'templates/EmailAlertTemplate.phtml';
        return ob_get_clean();
    }

    public static function sendReportByEmail($config,$message,$subject,$attachment)
    {
        $emails = array();

        foreach($config['contacts'] as $name => $contact){
            $emails[] = $contact['email'];
        }

        if(count($emails) == 0)
        {
            throw new Exception('No Emails To Send');
        }

        $mailer = self::initSwiftMailer($config);
        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('no-replay@yellow.com.eg' => 'Yellow Monitor'))
            ->setTo($emails)
            ->addPart($message,'text/html')
            ->attach(Swift_Attachment::fromPath($attachment,'application/zip'));

        return $mailer->send($message);
    }

}