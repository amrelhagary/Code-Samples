<?php

namespace RateCard\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use RateCard\Model\DiscountTable;

/**
 * InvoiceTable class is Model/Invoice`s TableGetaway class
 * @package RateCard\Model
 * @author ahmed hamdy <a.abdelhak.yellow.com.eg>
 */
class InvoiceTable  extends TableGateway {

    private $tableGateway;
    private $dbAdapter;
    private $discountTable;

    /**
     * InVoiceTable Constructor
     * @param TableGateway $tableGateway tableGateway object
     */
    public function __construct(TableGateway $tableGateway,$dbAdapter,DiscountTable $discountTable) {


        $this->tableGateway = $tableGateway;
        $this->dbAdapter = $dbAdapter;
        $this->discountTable = $discountTable;
    }

    /**
     * Get All Invoice Objects in yp_RateCard_invoice Table
     * @return ResultSet array of InVoice`s Objects
     */
    public function fetchAll() {

        return $this->tableGateway->select();
    }


    /**
     * Get Invoice Object in in yp_RateCard_invoice Table
     * @param int $id Invoice`s id
     * @return array | \ArrayObject Invoice Object
     * @throws \Exception if Invoice`s id which used to get Invoice object does not exist in yp_ratecard_invoice Table
     */
    public function getInvoice($id) {

        $id = (int) $id;

        $resultSet = $this->tableGateway->select( array('id' => $id) );
        // get array of Invoice Object
        $result = $resultSet->current();

        if (!$result) {

            throw new \Exception("Invoice ID Does not Exist");
        }

        return $result;
    }

    /**
     * Save Invoice Object in yp_ratecard_invoice Table if is not exists
     * OR
     * Update Invoice Object with new values if is already exists in yp_ratecard_invoice Table
     * @param Invoice $invoice Invoice object
     * @return int created/updated Invoice`s id
     * @throws \Exception in case Update Invoice Object is not exists in yp_ratecard_invoice Table
     */
    public function saveInvoice(Invoice $invoice) {

        $data = array(
            'user_id'      => $invoice->user_id,
            'client_id'    => $invoice->client_id,
            'total_amount' => $invoice->total_amount,
            'comment'      => $invoice->comment,
            'created_at'   => $invoice->created_at,
            'updated_at'   => $invoice->updated_at
        );

        $invoiceId = (int) $invoice->id;

        if ($invoiceId == 0) {

            $this->tableGateway->insert($data);
            $invoiceId = $this->tableGateway->lastInsertValue;
        } else {

            if ($this->getInVoice($invoiceId)) {

                $this->tableGateway->update($data, array('id' => $invoiceId) );
            } else {

                throw new \Exception("Invoice ID Does not Exist");
            }
        }

        return $invoiceId;
    }

    /**
     * Delete Invoice Object from yp_ratecard_invoice Table
     * @param int $id Invoice`s id
     */
    public function deleteInvoice($id) {

        $id = (int) $id;
        $this->tableGateway->delete( array('id' => $id) );
    }

    public function getInvoiceDealItem($invoiceId)
    {
        $sql = new Sql($this->dbAdapter);
        $select = new Select();
        $select->from(array('items' => 'yp_ratecard_deal_item'));
        $select->columns(array('item_id' => 'id','platform_id','service_id','amount','package_name','quantity','comment'));
        $select->join(array('invoice' => 'yp_ratecard_invoice'),'invoice.id = items.invoice_id',array('invoice_id' => 'id','client_id','total_amount','created_at'),Select::JOIN_RIGHT);
        $select->join(array('service' => 'yp_ratecard_service'),'service.id = items.service_id',array('service_id' => 'id','service_title' => 'title'),Select::JOIN_LEFT);
        $select->join(array('platform' => 'yp_ratecard_platform'),'platform.id = items.platform_id',array('platform_title' => 'title'),Select::JOIN_LEFT);
        $select->join(array('client' => 'yp_ratecard_client'),'client.id = invoice.client_id',array('client_name' => 'account_name','client_mobile' => 'mobile','client_email' => 'email'),Select::JOIN_LEFT);


        $where = new Where();
        $where->equalTo('invoice.id',$invoiceId);
        $select->where($where);

        $statement = $sql->prepareStatementForSqlObject($select);
        $rowsets = $statement->execute();

        $data = array();
        foreach($rowsets as $row){

            $platformAmount = $this->getPlatformTotalAmount($row['platform_id'],$invoiceId);
            $data['invoice_id'] = $row['invoice_id'];
            $data['client_id'] = $row['client_id'];
            $data['client_name'] = $row['client_name'];
            $data['client_email'] = $row['client_email'];
            $data['client_mobile'] = $row['client_mobile'];
            $data['total_amount'] = $row['total_amount'];
            $data['created_at'] = $row['created_at'];
            $data['platform'][] = array(
                'id' => $row['platform_id'],
                'title' => $row['platform_title'],
                'platform_amount' => $platformAmount,
                'discount' => $this->discountTable->getDiscount($row['platform_id'],$platformAmount),
                'service' => array(
                    'id' => $row['service_id'],
                    'title' => $row['service_title'],
                    'items' => array(
                        $row['item_id'] => array(
                            'package_name' => $row['package_name'],
                            'quantity' => $row['quantity'],
                            'amount' => $row['amount'],
                            'comment' => $row['comment']
                        )
                    )
                )
            );
        }

        return $data;
    }

    public function getPlatformTotalAmount($platformId,$invoiceId)
    {
        $sql = new Sql($this->dbAdapter);
        $select = new Select();
        $select->columns(array(
            'total' => new Expression('SUM(amount*quantity)')
        ));
        $select->from('yp_ratecard_deal_item');
        $where = new Where();
        $where->equalTo('platform_id',$platformId);
        $where->equalTo('invoice_id',$invoiceId);
        $select->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $rowset = $statement->execute();
        $row = $rowset->current();
        return (int) $row['total'];

    }

    public function getInvoiceByUserId($userId)
    {
        $resultset = $this->tableGateway->select(array('user_id' => $userId));
        return $resultset->toArray();
    }

} 