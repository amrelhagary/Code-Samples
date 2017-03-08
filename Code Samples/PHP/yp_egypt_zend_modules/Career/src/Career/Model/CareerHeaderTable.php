<?php
namespace Career\Model;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;


class CareerHeaderTable
{
    public $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll(){
        $select = new Select($this->tableGateway->getTable());
        $select->columns(array(
            'id' => 'id',
            'headerEn' => 'header_en',
            'headerAr' => 'header_ar',
        ));
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function getCareerHeader($id = null){
        $id = (int) $id;
        $select = new Select($this->tableGateway->getTable());
        $select->columns(array(
            'id' => 'id',
            'headerEn' => 'header_en',
            'headerAr' => 'header_ar'
        ));

        if(!is_null($id) && $id != 0){
            $select->where(array('id' => $id));
        }

        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();

        if(!$row){
            return null;
        }
        return $row;
    }

    public function saveCareerHeader(CareerHeader $header){

        $data = array(
            'header_en' => $header->headerEn,
            'header_ar' => $header->headerAr
        );

        $id = (int) $header->id;

        if($id == 0){
            $this->tableGateway->insert($data);
        }else{
            if($this->getCareerHeader($id)){
                $this->tableGateway->update($data,array('id' => $id));
            }else{
                throw new \Exception("Form id not exist");
            }
        }
    }

    public function deleteCareerHeader($id){
        $this->tableGateway->delete(array('id' => $id));
    }


}