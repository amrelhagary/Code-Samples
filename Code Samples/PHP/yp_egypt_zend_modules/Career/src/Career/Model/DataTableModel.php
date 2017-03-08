<?php
namespace Career\Model;


use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;

class DataTableModel implements DataTableInterface
{

    protected $columns;
    protected $tableName;
    protected $id;
    protected $request;
    protected $DbAdapter;
    protected $select;
    private $error;

    public function __construct(Adapter $DbAdapter,$tableName){
        $this->DbAdapter = $DbAdapter;
        $this->tableName = $tableName;
        $this->select = new Select();
        $this->error = false;
    }

    /**
     * output datatable required format
     * @param $columns
     * @param $data
     * @return array
     */
    public function dataOutput($columns,$data){
        //var_dump($data);die;
        $out = array();

        for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
            $row = array();

            for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
                $column = $columns[$j];

                // Is there a formatter?
                if ( isset( $column['formatter'] ) ) {
                    $row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
                }
                else {
                    $row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
                }
            }

            $out[] = $row;
        }

        return $out;
    }

    /**
     * query the database
     * @param $request
     * @param $columns
     * @return array
     */
    public function query($request,$columns){
        $sql = new Sql($this->DbAdapter);
        $this->select->columns(array(
            'id' => 'id',
            'full_name' => 'full_name',
            'mobile'    => 'mobile',
            'email'    => 'email',
            'date_added'    => 'date_added',
            'path'    => 'path'
        ));
        $this->select->from(array('career' => $this->tableName));
        $this->select->join(array('depart' => 'career_departments'),'depart.id = career.dept_id',array('name_en'),Select::JOIN_LEFT);

        $this->limit($request);
        $this->order($request,$columns);
        $this->filter($request,$columns);

        $statement = $sql->prepareStatementForSqlObject($this->select);
        try{
            $result= $statement->execute();
        }catch (\Exception $e){
            $this->error = true;
            return array('error' => $e->getMessage(),'data' => '');
        }

        $data = array();
        if($result->count() == 0){
            $this->error = true;
            return array('error' => 'no data found','data' => '');
        }else{
            foreach($result as $row){
                $data[] = $row;
            }
        }


        $recordsTotal = $this->getTotal();
        $recordsFiltered = $result->getAffectedRows();
        return array(
            "draw"            => intval( $request['draw'] ),
            "recordsTotal"    => intval( $recordsTotal ),
            "recordsFiltered" => intval( $recordsFiltered ),
            "data"            => $this->dataOutput( $columns, $data )
        );
    }

    /**
     * add limit and offset for sql query
     * @param $request
     */
    public function limit($request){

        if(isset($request['start']) && $request['length'] != -1){
            $this->select->limit(intval($request['length'] ));
            $this->select->offset(intval($request['start']));
        }
    }

    /**
     * order by column
     * @param $request
     * @param $columns
     */
    public function order($request,$columns){

        if ( isset($request['order']) && count($request['order']) ) {
            $orderBy = array();
            $dtColumns = $this->pluck( $columns, 'dt' );

            for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                if ( $requestColumn['orderable'] == 'true' ) {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';

                    $orderBy[] = $column['db'].' '.$dir;
                }
            }
            $order = implode(', ', $orderBy);
            $this->select->order($order);
        }

    }

    /**
     * filter searchable data
     * @param $request
     * @param $columns
     */
    public function filter($request,$columns){
        $dtColumns = $this->pluck( $columns, 'dt' );
        $globalPredication = array();
        $columnPredication = array();

        if ( isset($request['search']) && $request['search']['value'] != '' ) {
            $str = $request['search']['value'];

            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                if ( $requestColumn['searchable'] == 'true' ) {
                    $globalPredication[] = new \Zend\Db\Sql\Predicate\Like($column['db'],$str.'%');
                }
            }
        }

        // Individual column filtering
        for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
            $requestColumn = $request['columns'][$i];
            $columnIdx = array_search( $requestColumn['data'], $dtColumns );
            $column = $columns[ $columnIdx ];

            $str = $requestColumn['search']['value'];

            if ( $requestColumn['searchable'] == 'true' &&
                $str != '' ) {
                $columnPredication[] = new \Zend\Db\Sql\Predicate\Like($column['db'],$str.'%');
            }
        }



        if ( count( $globalPredication ) > 0) {
            $this->select->where($globalPredication,PredicateSet::OP_OR);
        }

        if ( count( $columnPredication ) > 0) {
            if(count($globalPredication) > 0){
                $this->select->where($columnPredication,PredicateSet::OP_AND);
            }else{
                $this->select->where($columnPredication,PredicateSet::COMBINED_BY_AND);
            }
        }

    }

    /**
     * get total number of records
     * @return int
     */
    private function getTotal(){
        $sql = new Sql($this->DbAdapter);
        $select = $sql->select($this->tableName);
        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute()->count();
    }

    /**
     * get property from array
     * @param array $a
     * @param $prop
     * @return array
     */
    private function pluck (array $a, $prop ) {
        $out = array();
        for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
            $out[] = $a[$i][$prop];
        }
        return $out;
    }
}