<?php
require_once dirname(__FILE__).'/libs/Monitor.php';
require_once dirname(__FILE__).'/libs/Logger.php';
require_once dirname(__FILE__).'/libs/AlertService.php';


// load config array
$config = include 'config.php';

$monitor = new Monitor($config);

$monitor->sendOutputReport();

Logger::getInstance()->cleanLog();

echo "Monitor Report Service send @ ". date('Y-m-d H:i:s');