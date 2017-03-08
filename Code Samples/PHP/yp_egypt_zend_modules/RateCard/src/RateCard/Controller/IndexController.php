<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Test for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace RateCard\Controller;

use RateCard\Model\User;
use Zend\Http\Header\SetCookie;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Authenticate\Adapter\GoogleOauth2Adapter;
use Zend\Session\Container;
use Zend\Http\Response;
use RateCard\Model\GenerateInvoicePdf;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $eventManager = $this->getEvent();
        $currentRoute =  $eventManager->getRouteMatch()->getMatchedRouteName();
        $params =  $eventManager->getRouteMatch()->getParams();

        $session = new Container('user');
        if(!$session->access_token && $currentRoute  != 'rate_card_login' && $currentRoute != 'rate_card_login' && $currentRoute != 'rate_card_google_oauth2_callback'){
            return $this->redirect()->toRoute('rate_card_login');
        }else{
            $view = new ViewModel();
            $this->layout('RateCard/layout');
            return $view->setTemplate('rate-card/index/index.phtml');
        }
    }

    public function loginAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $auth = new AuthenticationService();
        $authAdapter = new GoogleOauth2Adapter(new Container('user'),$config['GoogleOauth2']);

        $auth->authenticate($authAdapter);

        $result = $authAdapter->authenticate();
        if(!$result->isValid())
        {
            $client = $authAdapter->getClient();
            $authUrl = $client->createAuthUrl();
            $view = new ViewModel();
            $this->layout('RateCard/layout');
            $view->setVariable('authUrl',$authUrl);
            return $view;
        }else{
            return $this->redirect()->toRoute('rate_card_admin');
        }

    }

    public function oauthCallbackAction()
    {
        try{
            $code = $this->getRequest()->getQuery('code');
            if($code)
            {
                $config = $this->getServiceLocator()->get('Config');
                $session = new Container('user');
                $auth = new GoogleOauth2Adapter($session,$config['GoogleOauth2']);
                $client = $auth->getClient();
                $client->authenticate($code);
                $plus = new \Google_Service_Plus($client);
                $me = $plus->people->get('me');
                $userObject = $me->getEmails();
                $userEmail = $userObject[0]['value'];

                if($auth->verify($userEmail))
                {
                    $userToken = $client->getAccessToken();
                    $userName  = $me->name->givenName;
                    $user = $this->saveUser($userEmail, $userName, $userToken);
                    // set session time out to 1 day
                    $session->getDefaultManager()->rememberMe(60*60*24);
                    $session->userId = $user->id;
                    $session->google_user_id = $me->getId();
                    $session->user_email = $userEmail;
                    $token = json_decode($userToken,true);
                    $session->access_token = $token['access_token'];
                    $session->frinedly_name = $userName;
                    $session->user_role = $user->role;
                    $image = $me->getImage();
                    $session->avatar = $image['url'];

                    //set cookie
//                    $this->setCookies($user->id,$userEmail,$userToken);


                    $this->flashMessenger()->addMessage('you signin successfully');
                    //
                    $routeSeesion = new Container("route");
                    if($routeSeesion->routeName){
                        $redirectUrl = $this->redirect()->toRoute($routeSeesion->routeName, $routeSeesion->params);
                    }else{
                        $redirectUrl = $this->redirect()->toRoute('rate_card_admin');
                    }
                    return $redirectUrl;
                }
        }

        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }

        $this->flashMessenger()->addErrorMessage('you must login with authorized email');
        $this->redirect()->toRoute('rate_card_login');
    }

    /**
     * @todo disable set cookie and use session instead
     * @param $userId
     * @param $userEmail
     * @param $userToken
     */
    private function setCookies($userId,$userEmail,$userToken)
    {
        $data = array(
            'user_id' => $userId,
            'user_email' => $userEmail,
            'user_token' => $userToken
        );

        $cookie = new SetCookie('user',base64_encode(json_encode($data)),null,'/');
        $this->getServiceLocator()->get('Response')->getHeaders()->addHeader($cookie);
    }

    private function saveUser($userEmail, $userName, $userToken)
    {
        $userTable = $this->getServiceLocator()->get('RateCard\Model\UserTable');
        $userId = $userTable->getUserId($userEmail);
        $user = new User();
        if($userId == 0){
            $data = array(
                "username" => $userEmail,
                "name"     => $userName,
                "token"    => $userToken,
            );

            $user->exchangeArray($data);

            $userId = $userTable->saveUser($user);
            $user->id = $userId;
        }else{
            //update user token
            $userTable->updateToken($userId,$userToken);
            $user = $userTable->getUser($userId);
        }
        return $user;
    }

    public function logoutAction()
    {
        $session = new Container('user');
        $userId = $session->user_id;
        $config = $this->getServiceLocator()->get('Config');
        $authAdapter = new GoogleOauth2Adapter($session,$config['GoogleOauth2']);
        $client = $authAdapter->getClient();
        $session->getManager()->getStorage()->clear('user');
        $client->revokeToken();

        //remove user token
        $userTable = $this->getServiceLocator()->get('RateCard\Model\UserTable');
        $userTable->updateToken($userId,'');

        $this->redirect()->toRoute('rate_card_login');
    }

    public function demoAction()
    {
        $eventManager = $this->getEvent();
        $currentRoute =  $eventManager->getRouteMatch()->getMatchedRouteName();
        $params =  $eventManager->getRouteMatch()->getParams();

        $session = new Container('user');
        if(!$session->access_token && $currentRoute  != 'rate_card_login' && $currentRoute != 'rate_card_login' && $currentRoute != 'rate_card_google_oauth2_callback'){
            return $this->redirect()->toRoute('rate_card_login');
        } else {
            if ($this->request->isPost()) {

                // set client id in user session
                $session = new Container('user');
                $session->clientId = $this->getRequest()->getPost('clientId');
                $view = new ViewModel();
                $this->layout('RateCard/layout');
                return $view->setTemplate('rate-card/index/demo.phtml');
            } else {

                return $this->redirect()->toRoute('rate_card_admin');
            }
        }
    }
}
