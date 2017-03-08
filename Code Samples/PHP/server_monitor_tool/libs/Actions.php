<?php
require_once 'Crawler.php';
require_once 'Logger.php';
require_once 'AlertService.php';

class Actions
{
    public function restartSolr($params)
    {
        $username = !empty($params['username']) ? $params['username'] : null ;
        $password = !empty($params['password']) ? $params['password'] : null ;

        $response = Crawler::getInstance()->execute($params['url'],array(
            'username' => $username,
            'password' => $password
        ));

        if($response['httpStatusCode'] == 200 && ($response['response'] == $params['expected_response']))
        {
            $state = 'Success';
        }else{
            $state = 'Failed';
        }

        Logger::getInstance()->log('Execute Callback Ping: '.$params['url'] .' Exptected  Response: '. $params['expected_response'].' Status: '. $state);
    }
}