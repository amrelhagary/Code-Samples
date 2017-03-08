<?php

namespace RateCard\Controller;

use RateCard\Form\ClientForm;
use RateCard\Model\Client;
use RateCard\Model\ClientTable;
use RateCard\Model\UserTable;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

/**
 * ClientRestController Restful Controller for Client
 * @package RateCard\Controller
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class ClientRestController extends  AbstractRestfulController {

    private $clientTable;
    private $userTable;

    /**
     * Get ClientTable Instance
     * @return ClientTable
     */
    private function getClientTable() {

        if (!$this->clientTable) {

            $sm = $this->getServiceLocator();
            $this->clientTable = $sm->get('RateCard\Model\ClientTable');
        }

        return $this->clientTable;
    }

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
     * Get All Client Objects into ClientTable
     * @return JsonModel
     */
    public function getList() {

        $clients = $this->getClientTable()->fetchAll();
        $clientsArray = array();

        foreach ($clients as $client) {

            $clientsArray[]  = $client;
        }

        return new JsonModel(
            $clientsArray
        );

    }

    /**
     * Get Client Object using his id into ClientTable
     * @param int $id
     * @return JsonModel
     */
    public function get($id) {

        try {

            $id = (int)$id;
            $client = $this->getClientTable()->getClient($id);
        } catch (\Exception $e) {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $e->getMessage()
                )
            );
        }

        return new JsonModel(
            $client
        );

    }

    /**
     * Create new Client
     * @param array $data
     * @return JsonModel
     */
    public function create($data) {

        $data['id'] = 0;

        // get user session
        $userSession     = new Container('user');
        $data['user_id'] = $userSession->userId;

        $client     = new Client();
        $clientForm = new ClientForm();
            $clientForm->setInputFilter($client->getInputFilter());
            $clientForm->setData($data);

        if ($clientForm->isValid()) {

            try {

                $client->exchangeArray($data);
                //check if user which create his client is exists in yp_RateCard_user Table or not
                $this->getUserTable()->getUser($client->user_id);
                $clientId = $this->getClientTable()->saveClient($client);

            } catch (\Exception $e) {

                $this->response->setStatusCode(400);
                return new JsonModel(array(
                        "data"  => array(),
                        "error" => $e->getMessage()
                    )
                );
            }
        } else {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $clientForm->getInputFilter()->getMessages()
                )
            );
        }

        return new JsonModel(
            $this->getClientTable()->getClient($clientId)
        );

    }

    /**
     * Update Exists Client
     * @param int $id
     * @param array $data
     * @return JsonModel
     */
    public function update($id, $data) {

        $data['id'] = (int)$id;

        try {

            $client = new Client();
                $client->exchangeArray($this->getClientTable()->getClient($id));

            $clientForm = new clientForm();
                $clientForm->bind($client);
                $clientForm->setInputFilter($client->getInputFilter());
                $clientForm->setData($data);

            if ($clientForm->isValid()) {

                $client->exchangeArray($data);
                //check if user which update his client is exists in yp_RateCard_user Table or not
                $this->getUserTable()->getUser($client->user_id);

                $clientId = $this->getClientTable()->saveClient($client);
            } else {

                $this->response->setStatusCode(400);
                return new JsonModel(array(
                        "data"  => array(),
                        "error" => $clientForm->getInputFilter()->getMessages()
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
            $this->getClientTable()->getClient($clientId)
        );

    }

    /**
     * Delete Exists Client
     * @param int $id
     * @return JsonModel
     */
    public function delete($id) {

        try {

            $this->getClientTable()->getClient($id);
            $this->getClientTable()->deleteClient($id);
        } catch (\Exception $e) {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $e->getMessage()
                )
            );
        }

        return new JsonModel(array(
                "data" => "Client had been deleted"
            )
        );

    }

} 