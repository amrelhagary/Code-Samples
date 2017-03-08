<?php

namespace RateCard\Model;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use \Zend\Db\ResultSet\ResultSet;

/**
 * UserTable class is Model/User`s TableGetaway class
 * @package RateCard\Model
 * @author ahmed hamdy <a.abdelhak.yellow.com.eg>
 */
class UserTable extends TableGateway {

    private $tableGateway;

    /**
     * UserTable Constructor
     * @param TableGateway $tableGateway tableGateway object
     */
    public function __construct(TableGateway $tableGateway) {

        $this->tableGateway = $tableGateway;
    }

    /**
     * Get All User Objects in yp_RateCard_user Table
     * @return ResultSet array of User`s Objects
     */
    public function fetchAll() {

        return $this->tableGateway->select();
    }

    /**
     * Get User Object in yp_RateCard_user Table
     * @param int $id User`s id
     * @return array | \ArrayObject User Object
     * @throws \Exception if user`s id which used to get user object does not exist in yp_RateCard_user Table
     */
    public function getUser($id) {

        $id = (int) $id;

        $resultSet = $this->tableGateway->select( array('id' => $id) );
        // get array of User Object
        $result = $resultSet->current();

        if (!$result) {

            throw new \Exception("User ID Does not Exist");
        }

        return $result;
    }

    /**
     * Save User Object in yp_RateCard_user Table if is not exists
     * OR
     * Update User Object with new values if is already exists in yp_RateCard_user Table
     * @param User $user User object
     * @return int saved/updated User`s Id
     * @throws \Exception in case Update User Object is not exists in yp_RateCard_user Table
     */
    public function saveUser(User $user) {

        $data = array(
            'username' => $user->username,
            'name'     => $user->name,
            'token'    => $user->token,
            'role'     => $user->role,
        );

        $userId = (int) $user->id;
        if ($userId == 0) {

            $this->tableGateway->insert($data);
            $userId = $this->tableGateway->lastInsertValue;

        } else {

            if ($this->getUser($userId)) {

                $this->tableGateway->update($data, array('id' => $userId) );
            } else {

                throw new \Exception("User ID Does not Exist");
            }
        }

        return $userId;
    }

    /**
     * Delete User Object from yp_RateCard_user Table
     * @param int $id User`s id
     */
    public function deleteUser($id) {

        $id = (int) $id;
        $this->tableGateway->delete( array('id' => $id) );
    }

    public function getUserId($useEmail)
    {
        $resultset = $this->tableGateway->select(array('username' => $useEmail));
        $row = $resultset->current();
        if($row)
        {
            return $row['id'];
        }else{
            return 0;
        }

    }

    public function updateToken($userId,$userToken)
    {
        return $this->tableGateway->update(array('token' => $userToken),array('id' => (int) $userId));
    }

    public function checkAuth($userEmail,$userToken)
    {
        $row = $this->tableGateway->select(array('username' => $userEmail));
        $result = $row->current();
        if($result)
        {
            if(($token = json_decode($result->token,true)) != false && $userToken == $token['access_token'])
            {
                return true;
            }
        }

        return false;

    }

} 