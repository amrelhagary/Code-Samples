<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Career for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Career\Controller;

use Career\Form\careerHeaderForm;
use Career\Model\Career;
use Career\Model\CareerHeader;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CareerController extends AbstractActionController
{

    protected $careerTable;

    // not used
    public function listWithPaginatorAction(){

        $paginator = $this->getCareerTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page',1));
        $paginator->setItemCountPerPage(10);

        $view = new ViewModel();
        $this->layout('admin/layout');
        $view->setVariable('paginator',$paginator);
        return $view->setTemplate('career/career/index.phtml');
    }

    public function indexAction(){

        $view = new ViewModel();
        $this->layout('admin/layout');
        return $view->setTemplate('career/career/datatable.phtml');
    }

    public function ajaxProcessingAction(){
        $request = $this->getRequest();

        $columns = $this->initDataTableCoulmns();

        if($request->isXmlHttpRequest()){
            $reqData = $this->params()->fromQuery();
            $dataTbaleService = $this->getServiceLocator()->get('DataTableService');
            $data = $dataTbaleService->query($reqData,$columns);
            return new JsonModel($data);
        }
    }
    private function getCareerTable(){
        if(!$this->careerTable){
            $sm = $this->getServiceLocator();
            $this->careerTable = $sm->get('Career\Model\CareerTable');
        }

        return $this->careerTable;
    }

    private function initDataTableCoulmns(){
        return array(
            array( 'db' => 'full_name','dt' => 0 ),
            array( 'db' => 'email', 'dt' => 1 ),
            array( 'db' => 'mobile', 'dt' => 2 ),
            array( 'db' => 'name_en','dt' => 3 ),
            array(
                    'db'        => 'date_added',
                    'dt'        => 4,
                    'formatter' => function( $d, $row ) {
                            return date( 'jS M y', strtotime($d));
                 }),
            array( 'db' => 'path','dt' => 5 ),
        );
    }

}
