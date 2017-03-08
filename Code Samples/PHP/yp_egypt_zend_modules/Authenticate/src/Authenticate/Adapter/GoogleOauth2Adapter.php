<?php

namespace Authenticate\Adapter;
use Authenticate\Model\GoogleOauth2;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class GoogleOauth2Adapter implements AdapterInterface
{

    protected $client;
    protected $request;
    protected $response;
    protected $session;
    protected $config;

    public function __construct($session,$config)
    {
        $this->session = $session;
        $this->config = $config;
    }

    public function initClient()
    {
        $clientId = $this->config['ClientId'];
        $clientSecret = $this->config['Secret'];
        $redirectUrl = $this->config['RedirectURL'];

        $client = new \Google_Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($redirectUrl);
        $client->setScopes(array('https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'));

        $this->client = $client;
    }

    public function getClient()
    {
        if(!$this->client)
        {
            $this->initClient();
        }
        return $this->client;
    }
    /**
     * Performs an authentication attempt
     */
    public function authenticate()
    {
        if(!$this->client)
        {
            $this->initClient();
        }

        if($this->isAuthenticated())
        {
            return $this->getResult(Result::SUCCESS,'','SUCCESS');
        }else{
            return $this->getResult(Result::FAILURE,'','FAILURE');
        }
    }

    private  function isAuthenticated()
    {
        if($this->session->offsetExists('access_token'))
        {
            return true;
        }else{
            return false;
        }
    }

    public function verify($email)
    {
        if(preg_match('/\@(yellow.com.eg)/',$email))
        {
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $type
     * @param $identity
     * @param $message
     * @return \Zend\Authentication\Result
     */
    public function getResult($type, $identity, $message)
    {
        return new Result($type, $identity, array(
            $message
        ));
    }
} 