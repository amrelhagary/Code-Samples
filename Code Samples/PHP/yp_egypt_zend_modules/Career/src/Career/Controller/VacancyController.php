<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Career for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Career\Controller;

use Career\Form\vacancyForm;
use Career\Model\Vacancy;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class VacancyController extends AbstractActionController
{

    protected $vacanciesTable;

    public function indexAction(){

        $vacancies = $this->getVacancyTable()->fetchAll();
        $view = new ViewModel();
        $this->layout('admin/layout');
        $view->setVariable('vacancies',$vacancies);
        return $view;
    }

    private function getVacancyTable(){
        if(!$this->vacanciesTable){
            $sm = $this->getServiceLocator();
            $this->vacanciesTable = $sm->get('Career\Model\VacancyTable');
        }

        return $this->vacanciesTable;
    }

    public function addAction(){

        $form = new vacancyForm();
        $form->get('submit')->setValue('add');
        $request = $this->getRequest();
        if($request->isPost()){

            $vacancy = new Vacancy();
            $form->setInputFilter($vacancy->getInputFilter());
            $form->setData($request->getPost());

            if($form->isValid()){
                $vacancy->exchangeArray($form->getData());
                $this->getVacancyTable()->saveVacancy($vacancy);

                return $this->redirect()->toRoute('list_vacancies');
            }
        }

        $this->layout('admin/layout');
        return array('form' => $form);
    }

    public function editAction(){
        $id = (int) $this->params()->fromRoute('id', 0);
//        if(!$id){
//            return $this->redirect()->toUrl('error/404');
//        }

        $vacancy = $this->getVacancyTable()->getVacancy($id);
        $form = new vacancyForm();
        $form->bind($vacancy);
        $form->get('submit')->setValue('Save');

        $request = $this->getRequest();

        if($request->isPost()){

            $form->setInputFilter($vacancy->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                $this->getVacancyTable()->saveVacancy($form->getData());

                return $this->redirect()->toRoute('list_vacancies');
            }
        }

        $this->layout('admin/layout');
        return array(
            'id' => $id,
            'form' => $form
        );
    }

    public function deleteAction(){
        $id = (int) $this->params()->fromRoute('id',0);
        if(!$id){
            return $this->redirect()->toRoute('list_vacancies');
        }
        $request = $this->getRequest();
        if($request->isPost()){
            $del = $request->getPost('del','No');
            if($del == 'Yes'){
                $id = (int) $request->getPost('id');
                $this->getVacancyTable()->deletevacancy($id);
            }

            return $this->redirect()->toRoute('list_vacancies');
        }

        $this->layout('admin/layout');
        return array(
            'id' => $id,
            'vacancy' => $this->getVacancyTable()->getVacancy($id)
        );
    }
}
