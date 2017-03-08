<?php

require_once 'Crawler.php';
require_once 'Logger.php';
require_once 'SnapShot.php';
require_once 'Callbacks.php';
require_once 'AlertService.php';
//require_once 'LogHTTP.php';
//require_once 'DB.php';

class Monitor
{
    protected $websiteUrl;
    protected $domElement;
    protected $config;
    protected $websites;
    protected $webservices;
    private $startTime;
    private $endTime;

    public function __construct($config)
    {
        $this->config = $config;
        $this->websites = $config['websites'];
        $this->webservices = $config['webservices'];

    }

    public function ping()
    {
        $this->startTime =  time();
        $websiteOutput = $this->pingWebsites();
        $webservicesOutout = $this->pingWebServices();
        $output = array_merge_recursive($websiteOutput,$webservicesOutout);
        $this->endTime = time();
        Logger::getInstance()->log($output);
        return $output;
    }

    private function pingWebsites()
    {
        $output = array();

        foreach ($this->websites as $websiteCode => $pages) {
            foreach ($pages as $page) {
                $status = false;
                $params = array();
                $params['referer'] = (!empty($page['page_referer'])) ? $page['page_referer'] : null;

                $response = Crawler::getInstance()->execute($page['page_url'], $params);
                $method = '';
                $status = false;
                if (!empty($response['response']) && $response['http_status_code'] == 200) {
                    if (isset($page['search'])) {
                        $method = $page['search'];
                        $status = $this->findElement($response['response'], $page['search']);
                    } elseif (isset($page['http_response_code'])) {
                        $method = $page['http_response_code'];
                        $status = $response['http_status_code'] == $page['http_response_code'] ? true : false;
                    } else {
                        $method = 'Error: Not Defined Method for search';
                    }
                }

                if ($status === true) {
                    $statusCondition = 'success';
                } else {
                    $statusCondition = 'failed';
                }

//                DB::getInstance('../data/monitor.db')->query("insert into logs(id,url,start_time,end_time,condition)values('1','test url','123456789','12345679',1)");

                LogHTTP::logHttp(array(
                    'url' => $page['page_url'],
                    'code' => $page['page_code'],
                    'type' => 'website',
                    'start_time' => $response['start_time'],
                    'end_time'   => $response['end_time'],
                    'condition' => $method,
                    'condition_result' => $status,
                    'http_status_code' =>  $response['http_status_code'],
                    'response' => $response['response'],
                    'curl_info' => $response['curl_info']
                ));

//                Logger::getInstance()->log($page['page_url'], $response['http_status_code'], $method, $statusCondition);
//                $this->takeSnapshot($response['response'], $websiteCode . '_' . $page['page_name'],$statusCondition);

                // execute callbacks
                if(isset($page['callback']) !== false){
                    $callback = new Callbacks();
                    if(isset($page['callback']['success']) !== false){
                        $callback->onSuccess($page['callback']['success']['actions']);
                    }elseif(isset($page['callback']['failed']) !== false) {
                        $callback->onFailed($page['callback']['failed']['actions']);
                    }else{
                        $callback->onDefault($page['callback']['default']['actions']);
                    }
                }

                $output[$statusCondition][] = array(
                    $websiteCode => array(
                        'url' => $page['page_url'],
                        'code' => $page['page_code'],
                        'http_status_code' => (!empty($response['http_status_code']) ? $response['http_status_code']: ''),
                        'condition_result' => $status,
                        'http_return_headers' => (!empty($response['http_return_headers']) ? $response['http_return_headers'] : ''),
                    ));
            }
        }
        return $output;
    }

    private function pingWebservices()
    {
        $output = array();

        foreach ($this->webservices as $serviceName => $service) {
            $status = false;
            $response = Crawler::getInstance()->execute($service['url']);

            if (!empty($response['response']) && $response['http_status_code'] == 200) {
                // check if response is json
                if(($res = json_decode($response['response'])) === NULL)
                {
                    // the response not json
                    $res = strip_tags($response['response']);
                }

                if ($res == $service['expected_response']) {
                    $status = true;
                }
            }

            if ($status == true) {
                $statusCondition = 'success';
            } else {
                $statusCondition = 'failed';
            }


//            Logger::getInstance()->log($service['url'], $response['http_status_code'], $statusCondition);
            LogHTTP::logHttp(array(
                'url' => $service['url'],
                'code' => $service['code'],
                'type' => 'webservice',
                'start_time' => $response['start_time'],
                'end_time'   => $response['end_time'],
                'condition' => $service['expected_response'],
                'condition_result' => $status,
                'http_status_code' =>  $response['http_status_code'],
                'response' => $response['response'],
                'curl_info' => $response['curl_info']
            ));

            $output[$statusCondition][] = array(
                $serviceName => array(
                    'url' => $service['url'],
                    'code' => $service['code'],
                    'http_status_code' => $response['http_status_code'],
                    'condition_result' => $status,
                    'http_return_headers' => $response['http_return_headers'],
                ));
        }

        return $output;
    }

    private function takeSnapshot($response,$pageName,$condition){
        try{
            SnapShot::getInstance()->take($response,$pageName,$condition);
        }catch (Exception $e){
            Logger::getInstance()->log($e->getMessage());
        }
    }

    private function findElement($response,array $search)
    {
        list($searchMethod, $searchParams) = each($search);

        if($searchMethod == 'xpath'){
            libxml_use_internal_errors(true);
            $doc = new DOMDocument();
            $doc->loadHTML($response);
            if(!$doc){
                Logger::getInstance()->log(libxml_get_errors());
                libxml_clear_errors();
                return false;
            }
            $xpath = new DOMXPath($doc);
            $query = $searchParams['dom_element_xpath'];
            $elements = $xpath->query($query);
            if($elements->length == $searchParams['counter'])
            {
                return true;
            }else{
                return false;
            }

        }elseif($searchMethod == 'preg_match'){
            if(!empty($searchParams['dom_element_value'])){
                $pattern = "/{$searchParams['dom_element_name']}=\"{$searchParams['dom_element_value']}\"/";
            }else{
                $pattern = "/({$searchParams['dom_element_name']})+/";
            }
            preg_match_all($pattern,$response,$matchArray);
            if(sizeof($matchArray[0]) > 0){
                return true;
            }else{
                return false;
            }
        }elseif($searchMethod == 'strpos'){
            if((strpos($response,$searchParams['dom_element_name'])) === false){
                return false;
            }else{
                return true;
            }
        }

    }

    public function outputReport($outputs)
    {
        
        $diff = abs($this->endTime - $this->startTime);
        $success = (!empty($outputs['success'])) ? count($outputs['success']): 0;
        $failed = (!empty($outputs['failed'])) ? count($outputs['failed']): 0;
        echo sprintf("time elapsed (H:M:S): %d:%d:%d , success req: %s, failed req: %s \n",($diff/60/60),($diff/60),$diff,$success,$failed);
    }

    public function sendOutputReport()
    {
        if(AlertService::getInstance($this->config)->isStopNotification()){
            return;
        }
        // compress the log file
        $fileName = 'log_'.date('Y_m_d');
        $logFile = dirname(__FILE__).'/../logs/'.$fileName;
        $zipFile = $logFile.'.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE)!==TRUE) {
            exit("cannot open <$zipFile>\n");
        }
        $zip->addFile($logFile,$fileName);
        $zip->close();

        return;
        // send email containing the log file
        try{
            AlertService::sendReportByEmail($this->config,'reporting service','Yellow Monitor Service Report',$zipFile);
        }catch (Exception $e){
            Logger::getInstance()->log($e->getMessage());
        }
    }


}