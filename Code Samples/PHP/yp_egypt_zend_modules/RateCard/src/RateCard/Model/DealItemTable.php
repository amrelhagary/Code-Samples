<?php

namespace RateCard\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * DealItemTable class is Model/DealItem`s TableGetaway class
 * @package RateCard\Model
 * @author ahmed hamdy <a.abdelhak.yellow.com.eg>
 */
class DealItemTable extends TableGateway {

    private $tableGateway;

    /**
     * DealItemTable Constructor
     * @param TableGateway $tableGateway tableGateway object
     */
    public function __construct(TableGateway $tableGateway) {

        $this->tableGateway = $tableGateway;
    }

    /**
     * Get All DealItem Objects in yp_ratecard_deal_item Table
     * @return ResultSet array of DealItem`s Objects
     */
    public function fetchAll() {

        return $this->tableGateway->select();
    }

    /**
     * Get DealItem Object in yp_ratecard_deal_item Table
     * @param int $id DealItem`s id
     * @return array | \ArrayObject DealItem Object
     * @throws \Exception if DealItem`s id which used to get DealItem object does not exist in yp_ratecard_deal_item Table
     */
    public function getDealItem($id) {

        $id = (int) $id;

        $resultSet = $this->tableGateway->select( array('id' => $id) );
        // get array of DealItem Object
        $result = $resultSet->current();

        if (!$result) {

            throw new \Exception("Deal Item ID Does not Exist");
        }

        return $result;
    }

    /**
     * Save DealItem Object in yp_ratecard_deal_item Table if is not exists
     * OR
     * Update DealItem Object with new values if is already exists in yp_ratecard_deal_item Table
     * @param DealItem $dealItem DealItem object
     * @return int created/updated DealItem`s id
     * @throws \Exception in case Update DealItem Object is not exists in yp_ratecard_deal_item Table
     */
    public function saveDealItem(DealItem $dealItem) {

        $data = array(
            'platform_id'  => $dealItem->platform_id,
            'service_id'   => $dealItem->service_id,
            'invoice_id'   => $dealItem->invoice_id,
            'amount'       => $dealItem->amount,
            'package_name' => $dealItem->package_name,
            'quantity'     => $dealItem->quantity,
            'comment'      => $dealItem->comment,
            'created_at'   => $dealItem->created_at,
            'updated_at'   => $dealItem->updated_at
        );

        $dealItemId = (int) $dealItem->id;

        if ($dealItemId == 0) {

            $this->tableGateway->insert($data);
            $dealItemId = $this->tableGateway->lastInsertValue;
        } else {

            if ($this->getDealItem($dealItemId)) {

                $this->tableGateway->update($data, array('id' => $dealItemId) );
            } else {

                throw new \Exception("Deal Item ID Does not Exist");
            }
        }

        return $dealItemId;
    }

    /**
     * Delete DealItem Object from yp_ratecard_deal_item Table
     * @param int $id DealItem`s id
     */
    public function deleteDealItem($id) {

        $id = (int) $id;
        $this->tableGateway->delete( array('id' => $id) );
    }

} 