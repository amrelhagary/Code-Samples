<?php

class LogHTTP extends Logger
{
    private static $reqTime;
    private static $resTime;

    public function __construct()
    {
        parent::__construct();

    }

    public static function logHTTP($data)
    {

        $sql = "insert into logs(url,code,type,start_time,end_time,condition,condition_result,http_status_code,curl_info) values ('"
            .$data['url']."','"
            .$data['code']."','"
            .$data['type']."','"
            .$data['start_time']."','"
            .$data['end_time']."','"
            .json_encode($data['condition'])."','"
            .$data['condition_result']."','"
            .$data['http_status_code']."','"
            .json_encode($data['curl_info'])
            ."')";
        try{
            DB::getInstance(dirname(__FILE__).'/../data/monitor.db')->query($sql);
        }catch (Exception $e){
            parent::getInstance()->log('Error: '. $e->getMessage());
        }
    }
}