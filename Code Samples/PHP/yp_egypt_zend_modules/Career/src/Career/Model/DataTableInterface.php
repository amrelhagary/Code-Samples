<?php
namespace Career\Model;

interface DataTableInterface
{
    public function dataOutput($columns,$data);

    public function limit($request);

    public function order($request,$columns);

    public function filter($request,$columns);

    public function query($request,$columns);

}

