<?php

namespace RateCard\Model;

use Zend\Db\TableGateway\TableGateway;
use \Zend\Db\ResultSet\ResultSet;

/**
 * ClientTable class is Model/Client`s TableGetaway class
 * @package RateCard\Model
 * @author ahmed hamdy <a.abdelhak.yellow.com.eg>
 */
class ClientTable extends TableGateway {

    private $tableGateway;

    /**
     * ClientTable Constructor
     * @param TableGateway $tableGateway tableGateway object
     */
    public function __construct(TableGateway $tableGateway) {

        $this->tableGateway = $tableGateway;
    }

    /**
     * Get All client Objects in yp_RateCard_client Table
     * @return ResultSet array of Client`s Objects
     */
    public function fetchAll() {

        return $this->tableGateway->select();
    }

    /**
     * Get Client Object in yp_RateCard_client Table
     * @param int $id Client`s id
     * @return array | \ArrayObject Client Object
     * @throws \Exception if client`s id which used to get client object does not exist in yp_RateCard_client Table
     */
    public function getClient($id) {

        $id = (int) $id;

        $resultSet = $this->tableGateway->select( array('id' => $id) );
        // get array of Client Object
        $result = $resultSet->current();

        if (!$result) {

            throw new \Exception("Client ID Does not Exist");
        }

        return $result;
    }

    /**
     * Get all Client Objects Which Created by User`s id in yp_RateCard_client Table
     * @param int $userId User`s id who had created Clients
     * @return ResultSet array of Clients Objects
     * @throws \Exception if There isn`t Clients Created by User in yp_RateCard_client Table
     */
    public function getUserClients($userId) {

        $userId   = (int) $userId;

        $resultSet = $this->tableGateway->select( array('userId' => $userId) );

        if (!$resultSet) {

            throw new \Exception("There isn`t Clients Created by User");
        }

        return $resultSet;
    }

    /**
     * Save Client Object in yp_RateCard_client Table if is not exists
     * OR
     * Update Client Object with new values if is already exists in yp_RateCard_client Table
     * @param Client $client Client object
     * @return int saved/updated Client`s Id
     * @throws \Exception in case Update Client Object is not exists in yp_RateCard_client Table
     */
    public function saveClient(Client $client) {

        $data = array(
            'user_id'            => $client->user_id,
            'account_name'       => $client->account_name,
            'contact_name'       => $client->contact_name,
            'mobile'             => $client->mobile,
            'email'              => $client->email,
            'meeting_objectives' => $client->meeting_objectives
        );

        $clientId = (int) $client->id;

        if ($clientId == 0) {

            $data['created_at'] =  date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->tableGateway->insert($data);
            $clientId = $this->tableGateway->lastInsertValue;
        } else {

            if ($this->getClient($clientId)) {

                $data['updated_at'] = date('Y-m-d H:i:s');
                $this->tableGateway->update($data, array('id' => $clientId) );
            } else {

                throw new \Exception("Client ID Does not Exist");
            }
        }

        return $clientId;
    }

    /**
     * Delete Client Object from yp_RateCard_client Table
     * @param int $id Client`s id
     */
    public function deleteClient($id) {

        $id = (int) $id;
        $this->tableGateway->delete( array('id' => $id) );
    }

}