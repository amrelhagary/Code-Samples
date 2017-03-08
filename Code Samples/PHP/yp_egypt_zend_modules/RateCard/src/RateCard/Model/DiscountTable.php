<?php
namespace RateCard\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;

class DiscountTable extends TableGateway
{
    private $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getDiscountByPlatformId($platformId)
    {
        $resultSet = $this->tableGateway->select(array('platform_id' => $platformId));
        if(!$resultSet){
            return 0;
        }

        $data = array();
        foreach($resultSet as $result)
        {
            $data[$result->platformId][] = array('conditionalAmount' => $result->conditionalAmount,'discountPercentage' => $result->discountPercentage);
        }
        return $data;

    }

    public function getDiscount($platformId,$totalAmount)
    {
        $select = $this->tableGateway->getSql()->select();
        $where = new Where();
        $where->lessThan('conditional_amount',$totalAmount);
        $where->equalTo('platform_id',$platformId);
        $select->where($where);
        $select->order('conditional_amount DESC');
        $select->limit(1);

        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();
        if(!$row){
            return 0;
        }
        return (int) $row->discountPercentage;
    }
}