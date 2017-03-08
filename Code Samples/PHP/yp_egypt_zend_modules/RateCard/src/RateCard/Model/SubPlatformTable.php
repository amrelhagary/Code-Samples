<?php
namespace RateCard\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class SubPlatformTable extends PlatformTable
{
    public function fetchAllSubPlatform()
    {
        $resultSet = $this->tableGateway->select(function(Select $select){
            $select->where->notEqualTo('parent','0');
            $select->order('order ASC');
        });
        return $resultSet;
    }


    public function getSubPlatformByParentId($parentPlatformId, $pathsArray = null,$status = null)
    {

        $parentPlatformId = (int) $parentPlatformId;
        if($parentPlatformId == 0){
            $resultSet = $this->tableGateway->select(function(Select $select) use ($status){
                $select->where->notEqualTo('parent',0);

                if($status != null)
                    $select->where->equalTo('status',$status);

                $select->order('order ASC');
            });
        }else{
            $resultSet = $this->tableGateway->select(function(Select $select) use ($parentPlatformId){
                $select->where->equalTo('parent',$parentPlatformId);
                $select->order('order ASC');
            });
        }

        $platforms = array();
        if (!empty($resultSet)) {

            foreach($resultSet as $result){

                $platform = array();

                $platform['id']            = (int) $result->id;
                $platform['title']         = $result->title;
                $platform['icon']          = (isset($result->icon))? $this->fillPathForIconImage($result->icon, $pathsArray) : $result->icon;
                $platform['iconActive']    = (isset($result->iconActive))? $this->fillPathForIconActiveImage($result->iconActive, $pathsArray) : $result->iconActive;
                $platform['logo']          = (isset($result->logo))? $this->fillPathForLogoImage($result->logo, $pathsArray) : $result->logo;
                $platform['bgColor']       = $result->bgColor;
                $platform['bgColorActive'] = $result->bgColorActive;
                $platform['parent'] = (int) $result->parent;
                $platform['status'] = (int) $result->status;

                $platforms [] = $platform;
            }
        }

        return $platforms;
    }
}