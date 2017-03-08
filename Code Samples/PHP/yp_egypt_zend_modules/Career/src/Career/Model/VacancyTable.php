<?php
namespace Career\Model;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class VacancyTable
{
    public $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll(){
        $select = new Select($this->tableGateway->getTable());
        $select->columns(array(
            'id' => 'id',
            'nameEn' => 'name_en',
            'nameAr' => 'name_ar',
            'descriptionEn' => 'description_en',
            'descriptionAr' => 'description_ar',
            'availableVacancies' => 'available_vacancies'
        ));
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function getVacancy($id){
        $id = (int) $id;
        $select = new Select($this->tableGateway->getTable());
        $select->columns(array(
            'id' => 'id',
            'nameEn' => 'name_en',
            'nameAr' => 'name_ar',
            'descriptionEn' => 'description_en',
            'descriptionAr' => 'description_ar',
            'availableVacancies' => 'available_vacancies'
        ));
        $select->where(array('id' => $id));
        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();

        if(!$row){
            throw new \Exception("could find vacancy no ".$id);
        }
        return $row;
    }

    public function saveVacancy(Vacancy $vacancy){

        $data = array(
            'name_en' => $vacancy->nameEn,
            'name_ar' => $vacancy->nameAr,
            'description_en' => $vacancy->descriptionEn,
            'description_ar' => $vacancy->descriptionAr,
            'available_vacancies' => $vacancy->availableVacancies
        );

        $id = (int) $vacancy->id;
        if($id == 0){
            $this->tableGateway->insert($data);
        }else{
            if($this->getVacancy($id)){
                $this->tableGateway->update($data,array('id' => $id));
            }else{
                throw new \Exception("Form id not exist");
            }
        }
    }

    public function deleteVacancy($id){
        $this->tableGateway->delete(array('id' => $id));
    }


}