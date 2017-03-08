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
use Career\Model\CareerHeader;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CareerHeaderController extends AbstractActionController
{

    protected $careerHeaderTable;


    public function indexAction(){

        $careerHeader = $this->getCareerHeaderTable()->fetchAll();
        $view = new ViewModel();
        $this->layout('admin/layout');
        $view->setVariable('careerHeader',$careerHeader);
        return $view->setTemplate('career/careerHeader/index.phtml');
    }

    private function getCareerHeaderTable(){
        if(!$this->careerHeaderTable){
            $sm = $this->getServiceLocator();
            $this->careerHeaderTable = $sm->get('Career\Model\CareerHeaderTable');
        }

        return $this->careerHeaderTable;
    }


    public function addAction(){

        $id = (int) $this->params()->fromRoute('id',0);

        $form = new careerHeaderForm();
        $header = $this->getCareerHeaderTable()->getCareerHeader($id);

        if($header instanceof CareerHeader){
            $id = (int) $header->id;
            $form->bind($header);
        }else{
            $header = new CareerHeader();
        }

        $form->get('submit')->setValue('Save');
        $request = $this->getRequest();

        if($request->isPost()){
            $form->setInputFilter($header->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                if($id == 0){
                    $header->exchangeArray($form->getData());
                    $this->getCareerHeaderTable()->saveCareerHeader($header);
                }else{
                    $this->getCareerHeaderTable()->saveCareerHeader($form->getData());
                }
                return $this->redirect()->toRoute('career_header');
            }
        }

        $this->layout('admin/layout');
        $view = new ViewModel();
        $view->setVariable('form',$form);
        $view->setVariable('id',$id);
        return $view->setTemplate('career/careerHeader/add.phtml');
    }

    public function deleteAction(){
        $id = (int) $this->params()->fromRoute('id',0);
        if(!$id){
            return $this->redirect()->toRoute('career_header',array('action' => 'index'));
        }
        $request = $this->getRequest();
        if($request->isPost()){
            $del = $request->getPost('del','No');
            if($del == 'Yes'){
                $id = (int) $request->getPost('id');
                $this->getCareerHeaderTable()->deleteCareerHeader($id);
            }

            return $this->redirect()->toRoute('career_header');
        }

        $this->layout('admin/layout');
        $view = new ViewModel();
        $view->setVariable('header',$this->getCareerHeaderTable()->getCareerHeader($id));
        $view->setVariable('id',$id);
        return $view->setTemplate('career/careerHeader/delete.phtml');
    }
}
