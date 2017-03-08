<?php

class Logger
{
    private static $instance;
    private static $logFile;
    private static $logFileName;
    private static $logDir;


    public function __construct()
    {
        $this->init();
    }

    private function init()
    {            
        self::$logDir = dirname(__FILE__).'/../logs/';
        self::$logFileName = 'log_'.date('Y_m_d');
        self::$logFile = $this->createLogFile();
    }
    
    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function createLogFile()
    {

        try{
            $this->checkLogDir();
        }catch (Exception $e){
            echo $e->getMessage();
            exit;
        }

        $fileName = self::$logDir.self::$logFileName;

        if(file_exists($fileName))
        {
            $file = @fopen($fileName,'a+');
        }else{
            $file = @fopen($fileName,'w+');
        }
        
        if(!$file)
            throw new Exception('can not open log file');
        
        return $file;

    }

    public static function log()
    {

        $args = func_get_args();
        if(is_array($args)){
            $str = print_r($args,true);
        }
        $data = "\n====================".date('H:i:s')."=========================\n".$str."\n====================".date('H:i:s')."=========================\n";

        $status = fwrite(self::$logFile,$data);
        if($status == false)
        {
            throw new Exception("failed to write in log file");
        }
    }

    private function checkLogDir()
    {

        if(is_dir(self::$logDir) == false){
            mkdir(self::$logDir);

        }elseif(is_writable(self::$logDir) == false){
            if(chmod(self::$logDir,777) == false){
                throw new Exception(" log director in not writable");
            }
        }
    }



    public static function cleanLog()
    {
        $currentLogFile = self::$logFileName;
        $currentLogZip = $currentLogFile.'.zip';

        $logFiles = scandir(self::$logDir);
        foreach($logFiles as $file){
            if(($file != '.' && $file != '..') && ($file != $currentLogFile && $file != $currentLogZip))
            {
                unlink(self::$logDir.$file);
            }
        }
    }

    public function __destruct()
    {
        fclose(self::$logFile);
    }
}