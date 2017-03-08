<?php

namespace RateCard\Controller;

use RateCard\Form\UserForm;
use RateCard\Model\User;
use RateCard\Model\UserTable;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * UserRestController Restful Controller for User
 * @package RateCard\Controller
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class UserRestController extends  AbstractRestfulController {

      private $userTable;

     /**
      * Get UserTable Instance
      * @return UserTable
      */
      private function getUserTable() {

          if (!$this->userTable) {

              $sm = $this->getServiceLocator();
              $this->userTable = $sm->get('RateCard\Model\UserTable');
          }

          return $this->userTable;
      }

     /**
      * Get All User Objects into UserTable
      * @return JsonModel
      */
      public function getList() {

          $users = $this->getUserTable()->fetchAll();
          $usersArray = array();

          foreach ($users as $user) {

              $usersArray[]  = $user;
          }

          return new JsonModel(
                $usersArray
          );

      }

    /**
     * Get User Object using his id into UserTable
     * @param int $id
     * @return JsonModel
     */
      public function get($id) {

          try {

              $id = (int)$id;
              $user = $this->getUserTable()->getUser($id);
          } catch (\Exception $e) {

              $this->response->setStatusCode(400);
              return new JsonModel(array(
                      "data"  => array(),
                      "error" => $e->getMessage()
                  )
              );
          }

          return new JsonModel(
              $user
          );

      }

     /**
      * Create new User
      * @param array $data
      * @return JsonModel
      */
      public function create($data) {

          $data['id'] = 0;

          $user     = new User();
          $userForm = new UserForm();
              $userForm->setInputFilter($user->getInputFilter());
              $userForm->setData($data);

          if ($userForm->isValid()) {

                $user->exchangeArray($data);
                $userId = $this->getUserTable()->saveUser($user);
          } else {

              $this->response->setStatusCode(400);
              return new JsonModel(array(
                      "data"  => array(),
                      "error" => $userForm->getInputFilter()->getMessages()
                  )
              );
          }

          return new JsonModel(
              $this->getUserTable()->getUser($userId)
          );

      }

     /**
      * Update Exists User
      * @param int $id
      * @param array $data
      * @return JsonModel
      */
      public function update($id, $data) {

          $data['id'] = (int)$id;

          try {

             $user = new User();
                $user->exchangeArray($this->getUserTable()->getUser($id));

             $userForm = new UserForm();
                $userForm->bind($user);
                $userForm->setInputFilter($user->getInputFilter());
                $userForm->setData($data);

             if ($userForm->isValid()) {

                $user->exchangeArray($data);
                $userId = $this->getUserTable()->saveUser($user);
             } else {

                $this->response->setStatusCode(400);
                return new JsonModel(array(
                        "data"  => array(),
                        "error" => $userForm->getInputFilter()->getMessages()
                    )
                );
             }
          } catch (\Exception $e) {

              $this->response->setStatusCode(400);
              return new JsonModel(array(
                      "data"  => array(),
                      "error" => $e->getMessage()
                  )
              );
          }

          return new JsonModel(
            $this->getUserTable()->getUser($userId)
          );

      }

     /**
      * Delete Exists User
      * @param int $id
      * @return JsonModel
      */
      public function delete($id) {

          try {

              $id = (int)$id;
              $this->getUserTable()->getUser($id);
              $this->getUserTable()->deleteUser($id);
          } catch (\Exception $e) {

              $this->response->setStatusCode(400);
              return new JsonModel(array(
                      "data"  => array(),
                      "error" => $e->getMessage()
                  )
              );
          }

          return new JsonModel(array(
                  "data" => "User had been deleted"
              )
          );

      }

} 