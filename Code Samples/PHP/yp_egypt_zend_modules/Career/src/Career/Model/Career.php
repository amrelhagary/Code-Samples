<?php
namespace Career\Model;


class Career
{
    public $id;
    public $path;
    public $full_name;
    public $email;
    public $mobile;
    public $date_added;
    public $dept_id;
    public $other_dept;


    public function exchangeArray($data){
        $this->id = (isset($data['id'])) ? $data['id']: null;
        $this->path = (isset($data['path'])) ? $data['path']:null;
        $this->full_name = (isset($data['full_name'])) ? $data['full_name'] : null ;
        $this->email = (isset($data['email'])) ? $data['email'] : null ;
        $this->mobile = (isset($data['mobile'])) ? $data['mobile'] : null ;
        $this->date_added = (isset($data['date_added'])) ? $data['date_added'] : null ;
        $this->dept_id = (isset($data['dept_id'])) ? $data['dept_id'] : null ;
        $this->other_dept = (isset($data['other_dept'])) ? $data['other_dept'] : null ;
    }

}