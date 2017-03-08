<?php
class SnapShot
{
    private static $path;
    private static $instance;

    public function __construct(){
        $this->init();
    }

    private function init(){
        $path = dirname(__FILE__).'/../snapshots/'.date('Y_m_d');
        if(!is_dir($path)){
            if(!mkdir($path,0777,true)){
                throw new Exception('Error create snapshot directory');
            }
        }else{
            if(chmod($path,0777) == false)
                throw new Exception('Error Change Mode');
        }
        self::$path = $path;
    }

    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function take($response,$pageName,$condition = null){
        $pageName = str_replace(' ','_',$pageName);
        $file = self::$path.'/'.$pageName.'_'.date('H_i_s').'_'.$condition.'.html';
        $status = file_put_contents($file,$response);
        if(!$status){
            throw new Exception('Error take snapshot');
        }
    }
}