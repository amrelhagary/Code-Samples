<?php

//module/\Authenticate/src/Authenticate/Controller/AuthController.php
namespace Authenticate\Controller;
 
use Authenticate\Adapter\GoogleOauth2Adapter;
use Authenticate\Model\GoogleOauth2;
use Zend\Authentication\AuthenticationService;
//use Zend\Authentication\Storage\Session;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\XmlRpc\Request\Http;


class GoogleController extends AbstractActionController
{
    public function signinAction()
    {
        $auth = new AuthenticationService();
        $authAdapter = new GoogleOauth2Adapter(new Container('user'));

        $auth->authenticate($authAdapter);

        $result = $authAdapter->authenticate();
        if(!$result->isValid())
        {
            $client = $authAdapter->getClient();
            $authUrl = $client->createAuthUrl();
            $view = new ViewModel();
            $view->setTerminal(true);
            $view->setVariable('authUrl',$authUrl);
            return $view;
        }

    }

    public function oauthCallbackAction()
    {
        $data= $this->params()->fromQuery();
        if(isset($data['code']) && $data['code'])
        {
            $auth = new GoogleOauth2Adapter(new Container('user'));
            $client = $auth->getClient();
            $client->authenticate($data['code']);
            $plus = new \Google_Service_Plus($client);
            $me = $plus->people->get('me');
            $user_object = $me->getEmails();
            $user_email = $user_object[0]['value'];

            if($auth->verify($user_email))
            {
                $session = new Container('user');
                $session->user_id = $me->getId();
                $session->user_email = $user_email;
                $session->access_token = $client->getAccessToken();
                $session->frinedly_name = $me->name->givenName;
                $image = $me->getImage();
                $session->avatar = $image['url'];

                $this->flashMessenger()->addMessage('you signin successfully');
                return $this->redirect()->toRoute('home');
            }
        }

        $this->flashMessenger()->addErrorMessage('you must login with authorized email');
        $this->redirect()->toRoute('googleSignin');
    }

    public function logoutAction()
    {
        $session = new Container('user');
        $authAdapter = new GoogleOauth2Adapter($session);
        $client = $authAdapter->getClient();
        $session->getManager()->getStorage()->clear('user');
        $client->revokeToken();
        $this->redirect()->toRoute('googleSignin');
    }
}
?>