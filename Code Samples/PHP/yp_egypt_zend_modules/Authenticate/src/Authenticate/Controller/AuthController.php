<?php

//module/\Authenticate/src/Authenticate/Controller/AuthController.php
namespace Authenticate\Controller;
 
use Authenticate\Form\LoginForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Authenticate\Model\AuthenticationManager;
use Authenticate\Form\FormValidator;
 
class AuthController extends AbstractActionController
{
    protected $form;
    protected $storage;
    protected $authservice;

    public function getAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }
         
        return $this->authservice;
    }
     
    public function getSessionStorage()
    {
        if (! $this->storage) {
            $this->storage = $this->getServiceLocator()
                                  ->get('Authenticate\Model\AuthStorage');
        }
         
        return $this->storage;
    }

    public function loginAction()
    {

        if ($this->getAuthService()->hasIdentity()){
            return $this->redirect()->toRoute('list_vacancies');
        }

        $this->layout('admin/login/layout');

        $form = new LoginForm();
        return array(
            'form'      => $form,
            'messages'  => $this->flashmessenger()->getMessages()
        );
    }
     
    public function authenticateAction()
    {
        $form       = new LoginForm();
        $redirect = 'login';
        
        $request = $this->getRequest();
        if ($request->isPost()){
            $form->setData($request->getPost());
            $validator = new FormValidator();
            $form->setInputFilter($validator->getInputFilter());

            if ($form->isValid()){

                //check authentication...
                $this->getAuthService()->getAdapter()
                                       ->setIdentity($request->getPost('username'))
                                       ->setCredential($request->getPost('password'));



                $result = $this->getAuthService()->authenticate();

                foreach($result->getMessages() as $message)
                {
                    //save message temporary into flashmessenger
                    $this->flashmessenger()->addMessage($message);
                }
                 
                if ($result->isValid()) {

                    if ($request->getPost('rememberme') == 1 ) {
                        $this->getSessionStorage()
                             ->setRememberMe(1);
                        //set storage again 
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $this->getAuthService()->getStorage()->write($request->getPost('username'));
                    $userSession = new Container(AuthenticationManager::USER_SESSION_NAME);
                    $userSession->username = $request->getPost('username');

                    $result = $this->getAuthService()->getAdapter()->getResultRowObject();
                    $userSession->role = $result->role;

                    $redirect = $userSession->refereRouteName;

                    if(is_null($redirect)){
                        $redirect = 'list_vacancies';
                    }
                }
            }
        }
        $this->flashMessenger()->addMessage($form->getMessages());
        return $this->redirect()->toRoute($redirect);
    }
     
    public function logoutAction()
    {
        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();

        $userSession = new Container(AuthenticationManager::USER_SESSION_NAME);
        if($userSession->offsetExists('username')){
            $userSession->offsetUnset('username');
        }

        $this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toRoute('login',array('action' => 'login'));
    }
}
?>