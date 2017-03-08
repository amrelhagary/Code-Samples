<?php

namespace RateCard\Controller;

use RateCard\Exceptions\UnauthorizedException;
use RateCard\Form\DealForm;
use RateCard\Model\ClientTable;
use RateCard\Model\ProposalReport;
use RateCard\Model\InVoiceTable;
use RateCard\Model\UserTable;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Session\Container;

/**
 * InvoiceReportRestController Restful Controller for Creating Invoice with it`s DealItems Report
 * @package RateCard\Controller
 */
class InvoiceReportRestController extends  AbstractRestfulController {

    private $invoiceTable;
    private $clientTable;
    private $userTable;

    protected $allowedCollectionMethods = array(
        'GET',
        'POST',
    );

    protected $allowedResourceMethods = array(
        'GET'
    );

    public function SetEventManager(EventManagerInterface $event)
    {
        parent::setEventManager($event);
        $event->attach('dispatch',array($this,'checkOptions'),10);
    }

    public function checkOptions($e)
    {
        $matches = $e->getRouteMatch();
        $response = $e->getResponse();
        $request = $e->getRequest();
        $method = $request->getMethod();

        if($matches->getParam('id',false)){
            if(!in_array($method,$this->allowedResourceMethods)){
                $response->setStatusCode(405);
                return $response;
            }
            return;
        }

        if(!in_array($method,$this->allowedCollectionMethods)){
            $response->setStatusCode(405);
            return $response;
        }
    }

    /**
     * Get InvoiceTable Instance
     * @return InvoiceTable
     */
    private function getInvoiceTable() {

        if (!$this->invoiceTable) {

            $sm = $this->getServiceLocator();
            $this->invoiceTable = $sm->get('RateCard\Model\InvoiceTable');
        }

        return $this->invoiceTable;
    }

    /**
     * Get ClientTable Instance
     * @return ClientTable
     */
    private function getClientTable() {

        if (!$this->clientTable) {

            $sm = $this->getServiceLocator();
            $this->clientTable = $sm->get('RateCard\Model\ClientTable');
        }

        return $this->clientTable;
    }

    /**
     * Get UserTable Instance
     * @return UserTable
     */
    private function getUserTable() {

        if (!$this->userTable) {

            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('RateCard\Model\UserTable');
        }

        return $this->userTable;
    }

    public function getList() {}

    public function get($id) {
        // get invoice
        $invoice = $this->getInvoiceTable()->getInvoiceDealItem($id);
        if ($invoice) {

            $report= new ProposalReport();
            $report->exportPDF($invoice);
        } else {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => "Invoice not found"
                )
            );
        }

    }

    public function create($data) {

        try {

            $userId    = $this->getUserId();
            $clientId  = $this->getClientId();
            $invoiceId = (!empty($data['invoice']))? (int)$data['invoice'] : null;

            $user   = $this->getUserTable()->getUser($userId);
            $client = $this->getClientTable()->getClient($clientId);

            //create a new Zend\Mail\Message object
            $message = new Message;
            // create a MimeMessage object that will hold the mail body and any attachments
            $bodyPart = new MimeMessage();


            // create the attachment
            $invoice = $this->getInvoiceTable()->getInvoiceDealItem($invoiceId);
            if(!$invoice){
                throw new \Exception("invoice not found");
            }

            $report= new ProposalReport();
            $invoiceReportFile = $report->exportPDF($invoice,true);
            $attachment = new MimePart(file_get_contents($invoiceReportFile));

            // delete invoice file
            unlink($invoiceReportFile);

            // set attachment content type
            $attachment->type        = 'application/pdf';
            $attachment->filename    = 'InvoiceReport';
            $attachment->encoding    = \Zend\Mime\Mime::ENCODING_BASE64;
            $attachment->disposition = \Zend\Mime\Mime::DISPOSITION_ATTACHMENT;

            // get smtp config
            $config = $this->getServiceLocator()->get('Config');
            $smtp = $config['smtp'];

            // body Massage Content
            $bodyMessageContent = $user->name." create an Invoice with ".$client->account_name;
            $bodyMessage       = new MimePart($bodyMessageContent);
            $bodyMessage->type = 'text/html';
            // add the message body and attachment(s) to the MimeMessage
            $bodyPart->setParts(array(
                $bodyMessage,
                $attachment
            ));

            $message->setEncoding('utf-8')
                    ->setFrom($user->username)
                    ->setSubject("Invoice Report")
                    ->setBody($bodyPart)
                    ->setTo($client->email)
                    ->setCc($user->username);

            $mailOptions = new SmtpOptions(array(
                'name'              => 'yellow.com.eg',
                'host'              => $smtp['host'],
                'port'              => $smtp['port'],
                'connection_class'  => 'login',
                'connection_config' => array(
                    'username' => $smtp['username'],
                    'password' => $smtp['password'],
                    'ssl'      => $smtp['ssl']
                )
            ));

            $transport = new Smtp($mailOptions);
            $transport->send($message);


            return new JsonModel(array(
                    "data" => 'ok'
                )
            );

        } catch (\Exception $e) {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $e->getMessage()
                )
            );
        }
    }

    public function update($id, $data) {}

    public function delete($id) {}

    private function getClientId(){
        $session = new Container('user');
        $clientId = $session->clientId;

        return $clientId;
    }

    private function getUserId()
    {
        $session = new Container('user');
        if (!empty($session->userId)) {

            return $session->userId;
        } else {

            throw new UnauthorizedException("user not found");
        }
    }
} 