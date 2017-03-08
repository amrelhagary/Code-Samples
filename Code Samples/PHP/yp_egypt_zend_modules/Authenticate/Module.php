<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Test for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Authenticate;

use Authenticate\Model\User;
use Authenticate\Model\UserTable;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
//use Authenticate\Adapter\GoogleOauth2Adapter;
//use Authenticate\Model\GoogleOpenIdStorage;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Authenticate\Model\AuthenticationManager;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class Module implements AutoloaderProviderInterface {

	public function getAutoloaderConfig () {

		return array('Zend\Loader\ClassMapAutoloader' => array(__DIR__ . '/autoload_classmap.php',), 'Zend\Loader\StandardAutoloader' => array('namespaces' => array(// if we're in a namespace deeper than one level we need to fix the \ in the path
			__NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__),),),);
	}

	public function getConfig () {
		return include __DIR__ . '/config/module.config.php';
	}

	public function onBootstrap ($e) {
		// You may not need to do this if you're doing it elsewhere in your
		// application
		$eventManager = $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);

		$eventManager->attach( MvcEvent::EVENT_ROUTE, array(
		 		$this,
		 		'checkAuthentication'
		 ), - 3 );

		$eventManager->attach( MvcEvent::EVENT_ROUTE, array(
		 		$this,
		 		'checkAcl'
		 ), - 4 );
	}

	Public function checkAuthentication (MvcEvent $e) {

//		AuthenticationManager::initAcl($e);
//		AuthenticationManager::checkAuthentication($e);
	}
	

	public function getServiceConfig () {

        return array(
            'factories'=>array(
                'Authenticate\Model\AuthStorage' => function($sm){
                        return new \Authenticate\Model\AuthStorage('yellow');
                    },
                'AuthService' => function($sm) {
                        $dbAdapter           = $sm->get('getAdapter');
                        $dbTableAuthAdapter  = new Adapter\DbTable($dbAdapter,
                            'career_admin','username','password','MD5(?)');

                        $authService = new AuthenticationService();
                        $authService->setAdapter($dbTableAuthAdapter);
                        //$authService->setStorage($sm->get('Authenticate\Model\AuthStorage'));

                        return $authService;
                    }
            ),
        );
	}
	
	
	 public	function checkAcl(MvcEvent $e){
	 	//AuthenticationManager::checkAcl($e);
	}

	
}
