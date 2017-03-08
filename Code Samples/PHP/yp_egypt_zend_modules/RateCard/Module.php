<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Test for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace RateCard;

use RateCard\Exceptions\RateCardException;
use RateCard\Exceptions\UnauthorizedException;
use RateCard\Model\AuthService;
use RateCard\Model\Client;
use RateCard\Model\ClientTable;
use RateCard\Model\DealItem;
use RateCard\Model\DealItemTable;
use RateCard\Model\Discount;
use RateCard\Model\DiscountTable;
use RateCard\Model\Invoice;
use RateCard\Model\InvoiceTable;
use RateCard\Model\Platform;
use RateCard\Model\PlatformTable;
use RateCard\Model\SubPlatformTable;
use RateCard\Model\User;
use RateCard\Model\UserTable;
use RateCard\Model\Service;
use RateCard\Model\ServiceTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Http\Response;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap($e)
    {

        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);


        // authenticate rest api calls
        $eventManager->attach(MvcEvent::EVENT_ROUTE,function($e){

            $routeMatch = $e->getRouteMatch();
            $request = $e->getRequest();
            $routeName = $routeMatch->getMatchedRouteName();
            $moduleName = $routeMatch->getParam('module');
            $response = $e->getResponse();

            $restAPIRoute = array(
                'rate_card_platform_rest',
                'rate_card_subPlatform_rest',
                'rate_card_order_platform_rest',
                'rate_card_service_rest',
                'rate_card_user_rest',
                'rate_card_client_rest',
                'rate_card_deal_item_rest',
                'platform_service_rest',
                'invoice_rest',
                'final_invoice',
                'download_invoice'
            );



            if($moduleName == "RateCard" && in_array($routeName,$restAPIRoute))
            {
                $userEmail = "";
                $userToken = "";

                $headers = $e->getApplication()->getRequest()->getHeaders();
                if($headers->get('Authorization'))
                {
                    $authorization = $headers->get('Authorization')->getFieldValue();
                    $authorization = preg_replace('/Basic\s/','',$authorization);
                    $token = explode(':',base64_decode($authorization));
                    $userEmail = $token[0];
                    $userToken = $token[1];
                // assume username and usertoken in query params
                // this workaround for down pdf proposal
                }elseif(false !== ($request->getQuery('useremail',false) && $request->getQuery('usertoken',false)))
                {
                    $userEmail = $request->getQuery('useremail',false);
                    $userToken = $request->getQuery('usertoken',false);

                }else{
                    $response->setStatusCode(401);
                    $response->setContent('Authentication Required');
                    $response->sendHeaders();
                    return $response;
                }

                if(!empty($userEmail) && !empty($userToken))
                {
                    $authService = $e->getApplication()->getServiceManager()->get('CheckAuthService');
                    if(!$authService->checkAuth($userEmail,$userToken))
                    {
                        $response->setStatusCode(401);
                        $response->setContent('Invalid Authentication Token');
                        $response->sendHeaders();
                        return $response;
                    }
                }
            }
        },-10000);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR,function($e){
            $exception = $e->getResult()->exception;
            if($exception instanceof UnauthorizedException){
                $response = $e->getResponse();
                $response->setStatusCode(401);
                $response->sendHeaders();

                $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, function($e) use ($response){
                    $e->stopPropagation();
                    return $response;
                },-10000);
            }elseif($exception instanceof RateCardException){
                $response = $e->getResponse();
                $response->setStatusCode(404);
                $response->sendHeaders();

                $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, function($e) use ($response){
                    $e->stopPropagation();
                    return $response;
                },-10000);
            }
        });

    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
            'getAdapter'=>function ($sm){ /** @todo to refactor */
                    $config = $sm->get('Configuration');
                    $dbAdapter =new \Zend\Db\Adapter\Adapter(array(
                        'driver' => $config['db']['driver'],
                        'hostname' => $config['db']['server'],
                        'database' => $config['db']['databaseName'],
                        'username' => $config['db']['username'],
                        'password' => $config['db']['password']
                    ));
                    /**
                     * disable those line on production server
                     * because it's already sets , and produce bug in resetting
                     */
                    $dbAdapter->query("SET NAMES 'utf8'")->execute();
                    $dbAdapter->query("SET CHARACTER SET 'utf8'")->execute();
                    return $dbAdapter;
                },
                'PlatformTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('getAdapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Platform());
                        return new TableGateway('yp_ratecard_platform', $dbAdapter, null, $resultSetPrototype);
                    },
                'RateCard\Model\PlatformTable' => function ($sm) {
                        $tableGateway = $sm->get('PlatformTableGateway');
                        $dbAdapter = $sm->get('getAdapter');
                        $discountTable = $sm->get('RateCard\Model\DiscountTable');
                        $table = new PlatformTable($tableGateway,$dbAdapter,$discountTable);
                        return $table;
                    } ,
                'RateCard\Model\SubPlatformTable' => function ($sm) {
                        $tableGateway = $sm->get('PlatformTableGateway');
                        $dbAdapter = $sm->get('getAdapter');
                        $discountTable = $sm->get('RateCard\Model\DiscountTable');
                        $table = new SubPlatformTable($tableGateway,$dbAdapter,$discountTable);
                        return $table;
                    } ,
                'ServiceTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('getAdapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Service());
                        return new TableGateway('yp_ratecard_service', $dbAdapter, null, $resultSetPrototype);
                    },
                'RateCard\Model\ServiceTable' => function ($sm) {
                        $tableGateway = $sm->get('ServiceTableGateway');
                        $table = new ServiceTable($tableGateway);
                        return $table;
                    },
                'UserTableGateway' => function($em) {
                        $dbAdapter = $em->get('getAdapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->getArrayObjectPrototype(new User());
                        return new TableGateway('yp_ratecard_user', $dbAdapter, null, $resultSetPrototype);
                },
                'RateCard\Model\UserTable' => function($em) {
                        $tableGateway = $em->get('UserTableGateway');
                        return new UserTable($tableGateway);
                },
                'ClientTableGateway' => function($em) {
                        $dbAdapter = $em->get('getAdapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->getArrayObjectPrototype(new Client());
                        return new TableGateway('yp_ratecard_client', $dbAdapter, null, $resultSetPrototype);
                },
                'RateCard\Model\ClientTable' => function($em) {
                        $tableGateway = $em->get('ClientTableGateway');
                        return new ClientTable($tableGateway);
                },
                'DealItemTableGateway' => function($em) {
                        $dbAdapter = $em->get('getAdapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->getArrayObjectPrototype(new DealItem());
                        return new TableGateway('yp_ratecard_deal_item', $dbAdapter, null, $resultSetPrototype);
                },
                'RateCard\Model\DealItemTable' => function($em) {
                        $tableGateway = $em->get('DealItemTableGateway');
                        return new DealItemTable($tableGateway);
                },
                'InvoiceTableGateway' => function($em) {
                        $dbAdapter = $em->get('getAdapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->getArrayObjectPrototype(new Invoice());
                        return new TableGateway('yp_ratecard_invoice', $dbAdapter, null, $resultSetPrototype);
                },
                'RateCard\Model\InvoiceTable' => function($em) {
                        $dbAdapter = $em->get('getAdapter');
                        $tableGateway = $em->get('InvoiceTableGateway');
                        $discountTable = $em->get('RateCard\Model\DiscountTable');
                        return new InvoiceTable($tableGateway,$dbAdapter,$discountTable);
                },
                'DiscountTableGateway' => function($sm){
                        $dbAdapter = $sm->get('getAdapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Discount());
                        return new TableGateway('yp_ratecard_discount',$dbAdapter,null,$resultSetPrototype);
                },
                'RateCard\Model\DiscountTable' => function($sm) {
                        $tableGateway = $sm->get('DiscountTableGateway');
                        return new DiscountTable($tableGateway);
                },
                'CheckAuthService' => function($sm){
                        $userTable = $sm->get('RateCard\Model\UserTable');
                        return new AuthService($userTable);
                }
            )
        );
    }
}
