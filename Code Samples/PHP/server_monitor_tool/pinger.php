<?php
/*if(php_sapi_name() != 'cli')
{
    echo "Can't open this file";
    exit;
}*/
require_once dirname(__FILE__).'/libs/Monitor.php';
require_once dirname(__FILE__).'/libs/AlertService.php';
require_once dirname(__FILE__).'/libs/Logger.php';
require_once dirname(__FILE__).'/libs/LogHTTP.php';
require_once dirname(__FILE__).'/libs/DB.php';

// load config array
$config = include 'config.php';


$monitor = new Monitor($config);


try{
    $outputs = $monitor->ping();
}catch (Exception $e){
    echo $e->getMessage();
    echo $e->getTraceAsString();
    Logger::getInstance()->log('Error: '. $e->getMessage(),$e->getTraceAsString());
}



// prepare for alerting
if(isset($outputs['failed']))
{

    AlertService::getInstance($config)->AlertByEmail($outputs['failed']);
    AlertService::getInstance($config)->AlertAllInOneSMS($outputs['failed']);
}

// output data
$monitor->outputReport($outputs);