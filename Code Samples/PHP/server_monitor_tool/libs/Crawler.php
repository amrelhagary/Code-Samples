<?php
set_include_path(dirname(__FILE__).'/../vendor/library');
require_once dirname(__FILE__).'/../vendor/library/Zend/Http/Client.php';
class Crawler
{
    private static $instance;
    private static $logData;

    public function __construct()
    {
        $this->init();
    }

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function init()
    {
        if(!extension_loaded('curl')){
            throw new Exception('curl not loaded');
            exit;
        }
    }

    public static function execute($url,array $params = null,$method = 'socket')
    {
        if($method == 'socket')
        {
            return self::socket($url,$params);
        }elseif($method == 'curl'){
            return self::curl($url,$params);
        }
    }

    private static function curl($url,$params = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_TIMEOUT,30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIE, true);
        curl_setopt($ch, CURLOPT_USERAGENT, self::getUserAgent());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(!empty($params['referer'])){
            curl_setopt($ch, CURLOPT_REFERER, $params['referer']);
        }

        if(!empty($params['username']) && !empty($params['password']))
        {
            curl_setopt($ch,CURLOPT_USERPWD,$params['username'].':',$params['password']);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        }

        $startTime = microtime();

        if(($response = curl_exec($ch)) === false)
        {
            throw new Exception(curl_error($ch));
        }

        $endTime = microtime();

        $curlInfo = curl_getinfo($ch);
        $httpStatusCode = $curlInfo['http_code'];

        curl_close($ch);
        $return = array('http_status_code' => $httpStatusCode , 'response' => $response,'curl_info' => $curlInfo , 'start_time' => $startTime, 'end_time' => $endTime);
        return $return;
    }

    private function socket($url,$params = null){
        $config = array(
            'adapter' => 'Zend_Http_Client_Adapter_Socket',
            'timeout' => 30
        );

        $client = new Zend_Http_Client();
        $client->setUri($url);
        $client->setConfig($config);

        if(!empty($params['referer'])){
            $client->setHeaders('Referer',$params['referer']);
        }

        $startTime = microtime();

        $response = '';

        try{
            $response =  $client->request();
        }catch (Zend_Http_Client_Exception $e){
            Logger::getInstance()->log('Error '. $e->getMessage(),$e->getTraceAsString());
        }


        $endTime = microtime();
        if($response instanceof Zend_Http_Response)
        {
            $return = array(
                'http_status_code' => $response->getStatus(),
                'response' => $response->getBody(),
                'curl_info' => $response->getHeaders(),
                'start_time' => $startTime,
                'end_time' => $endTime,
	            'http_return_headers' => $response->getHeadersAsString()
            );
        }else{
            $return = array(
                'http_status_code' => '',
                'response' => '',
                'curl_info' => '',
                'start_time' => $startTime,
                'end_time' => $endTime
            );
        }
        return $return;
    }

    private static function getUserAgent()
    {
        //return 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0';
        if(!empty($_SERVER['HTTP_USER_AGENT']))
        {
            return $_SERVER['HTTP_USER_AGENT'];
        }else{
            return 'Yellow Monitor/Agent';
        }
    }
}