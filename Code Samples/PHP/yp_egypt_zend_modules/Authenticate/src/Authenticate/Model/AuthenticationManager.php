<?php

namespace Authenticate\Model;

use yellow\DataObjects\Web\ConnectionInfo;
use yellow\Wrappers\Connection;
use Zend\Session\Container;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;
use Authenticate\Model\AuthStorage;
use Zend\Debug\Debug;

class AuthenticationManager {
	
	const USER_SESSION_NAME ='user';

	public static function checkAuthentication(MvcEvent $e){

		$userSession = new Container(AuthenticationManager::USER_SESSION_NAME);
		$username = $userSession->username;
        $routeMatch = $e->getRouteMatch();
        $routeName = $routeMatch->getMatchedRouteName();
        $moduleName = $routeMatch->getParam('module');


        if (self::isAllowed($moduleName)){ 
        	
			if($routeName!="Authenticate"&& $routeName!="login"){
	            $userSession->refereRouteName  =$routeName;
	        }


			if($username==null && $routeName!="login" && $routeName!="authenticate" ){
			
				$url = $e->getRouter()->assemble(array(), array('name' => 'login'));
	
				$response=$e->getResponse();
				$response->getHeaders()->addHeaderLine('Location', $url);
				$response->setStatusCode(302);
				$response->sendHeaders();
	
				// When an MvcEvent Listener returns a Response object,
				// It automatically short-circuit the Application running
				// -> true only for Route Event propagation see Zend\Mvc\Application::run
					
				// To avoid additional processing
				// we can attach a listener for Event Route with a high priority
				$stopCallBack = function($event) use ($response){
					$e->stopPropagation();
					return $response;
				} ;
	            $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, $stopCallBack,-10000);
				return $response;
					
			}
        }
	}


	public static function getAuthServiceConfig(){}
	

	/**
	 * 
	 * @return array of rules
	 */
	public static function getRules() {
		$rules = array();
		$userRules = new \yellow\Models\Web\UsersRules();

		$rulesObjects = $userRules->getUserRules();
		
		foreach ($rulesObjects as $rule){
			$rules[$rule->name] = $rule->routes;
		}
		
		return $rules;
	}
	
	public static function initAcl(MvcEvent $e) {

        $routeMatch = $e->getRouteMatch();
        $routeName = $routeMatch->getMatchedRouteName();
        $moduleName = $routeMatch->getParam('module');

        if (self::isAllowed($moduleName)){
            $acl = new \Zend\Permissions\Acl\Acl();
		
		$allResources = array();
		$rules = self::getRules();

		foreach ($rules as $role => $resources) {

			$role = new \Zend\Permissions\Acl\Role\GenericRole($role);
			$acl->addRole($role);
	
			$allResources = array_merge($resources, $allResources);

			//adding resources
			foreach ($resources as $resource) {

				if(!$acl ->hasResource($resource))
					$acl -> addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
			}

			//adding restrictions
			foreach ($allResources as $resource) {
				$acl->allow($role, $resource);
			}
		}

		//setting to view
		$e->getViewModel()->acl=$acl;
        }
	}
	
	public static function checkAcl(MvcEvent $e) {

		$routeMatch = $e->getRouteMatch();
        $module = $routeMatch->getParam( 'module' );
        $route = $routeMatch->getMatchedRouteName();
        //var_dump($route);die;
        // get user role from session
        $userSession = new Container(AuthenticationManager::USER_SESSION_NAME);
		$userRole = $userSession->role;

        if(self::isAllowed($module)){

            if (!$e -> getViewModel() -> acl ->hasResource($route) || !$e->getViewModel()->acl->isAllowed($userRole,$route)) {
                $response = $e->getResponse();
                $response->getHeaders()-> addHeaderLine('Location', $e ->getRequest()->getBaseUrl() . '/404');
                $response->setStatusCode(404);
                $response->sendHeaders();

            }
        }

	}

    private function isAllowed($moduleName){

        if(strtolower($moduleName) === 'career'){
            return true;
        }else{
            return false;
        }
    }
	
}

?>