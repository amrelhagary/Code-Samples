<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Career for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Career;

use Career\Model\Career;
use Career\Model\CareerTable;
use Career\Model\DataTableModel;
use Career\Model\Vacancy;
use Career\Model\VacancyTable;
use Career\Model\CareerHeader;
use Career\Model\CareerHeaderTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{

	
	private $serviceManager= null;
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

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->serviceManager= $e->getApplication()->getServiceManager();

    }

    public function getServiceConfig(){

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
                        $dbAdapter->query("SET NAMES 'utf8'")->execute();
                        $dbAdapter->query("SET CHARACTER SET 'utf8'")->execute();
                    return $dbAdapter;
            	},
                'Career\Model\VacancyTable' =>  function($sm){
                        $tableGateway = $sm->get('VacancyTableGateway');
                        $table = new VacancyTable($tableGateway);
                        return $table;
                    },
                'VacancyTableGateway' => function($sm){
                        $dbAdapter = $sm->get('getAdapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Vacancy());
                        return new TableGateway('career_departments',$dbAdapter,null,$resultSetPrototype);
                    },
                'Career\Model\CareerHeaderTable' =>  function($sm){
                        $tableGateway = $sm->get('CareerHeaderTableGateway');
                        $table = new CareerHeaderTable($tableGateway);
                        return $table;
                    },
                'CareerHeaderTableGateway' => function($sm){
                        $dbAdapter = $sm->get('getAdapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new CareerHeader());
                        return new TableGateway('career_header',$dbAdapter,null,$resultSetPrototype);
                    },
                'Career\Model\CareerTable' =>  function($sm){
                        $tableGateway = $sm->get('CareerTableGateway');
                        $table = new CareerTable($tableGateway);
                        return $table;
                    },
                'CareerTableGateway' => function($sm){
                        $dbAdapter = $sm->get('getAdapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Career());
                        return new TableGateway('career_cv',$dbAdapter,null,$resultSetPrototype);
                    },
                'DataTableService' => function($sm){
                        $dbAdapter = $sm->get('getAdapter');
                        return new DataTableModel($dbAdapter,'career_cv');
                    }
            )
        );
    }
}
