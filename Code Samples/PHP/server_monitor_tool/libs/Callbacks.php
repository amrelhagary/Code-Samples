<?php

require_once 'Actions.php';
require_once 'Logger.php';

class Callbacks
{

    public function onSuccess($actionParams)
    {
        list($method,$params) =  each($actionParams);
        call_user_func_array(array('Actions',$method),array($params));
    }

    public function onFailed($actionParams){
        list($method,$params) =  each($actionParams);
        call_user_func_array(array('Actions',$method),array($params));
    }

    public function onDefault($actionParams){
        list($method,$params) =  each($actionParams);
        call_user_func_array(array('Actions',$method),array($params));
    }

    public function __call($name , array $argument)
    {
        if(!method_exists($this,$name)){
            Logger::getInstance()->log('this callback is not available: '. $name);
        }else{
            call_user_func_array(array($this,$name),array($argument));
        }
    }
}