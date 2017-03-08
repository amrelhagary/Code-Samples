<?php
namespace RateCard\Model;

use Application\Model\Cdn;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class PlatformTable extends TableGateway
{
    protected $tableGateway;
    protected $dbAdapter;
    protected $discountTable;

    public function __construct(TableGateway $tableGateway,$dbAdapter,DiscountTable $discountTable)
    {
        $this->tableGateway = $tableGateway;
        $this->dbAdapter = $dbAdapter;
        $this->discountTable = $discountTable;
    }


    /**
     * @todo change warpping key
     * @param $id
     * @param null $wrappingKey
     * @param array $pathsArray
     * @return array
     * @throws \Exception
     */
    public function getPlatform($id, $wrappingKey = null, $pathsArray = null, $status = null)
    {
        $this->discountTable->getDiscountByPlatformId($id);
        $id     = (int) $id;
        $rowSet = $this->tableGateway->select(function(Select $select)use($id,$status){
            $select->where->equalTo('id',$id);

            if($status != null)
                $select->where->equalTo('status',$status);

        });
        $row    = $rowSet->current();

        if (!$row) {
            throw new \Exception("Not Found platform : {$id} ");
        }

        $platform = array();

        $platform['id']               = $row->id;
        $platform['title']            = $row->title;
        $platform['icon']             = (isset($row->icon))? $this->fillPathForIconImage($row->icon, $pathsArray) : $row->icon;
        $platform['iconActive']       = (isset($row->iconActive))? $this->fillPathForIconActiveImage($row->iconActive, $pathsArray) : $row->iconActive;
        $platform['logo']             = (isset($row->logo))? $this->fillPathForLogoImage($row->logo, $pathsArray) : $row->logo;
        $platform['bgColor']          = $row->bgColor;
        $platform['bgColorActive']    = $row->bgColorActive;
        $platform['parent']           = (int) $row->parent;
        $platform['order']            = (int) $row->order;
        $platform['status']           = (int) $row->status;
        $platform['discount']         = $this->discountTable->getDiscountByPlatformId($row->id);

        if(isset($wrappingKey))
        {
            $platform = array(
                $wrappingKey => $platform
            );
        }

        return $platform;
    }

    public function getPlatformObj($id)
    {
        $id     = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row    = $rowSet->current();

        if (!$row) {
            return false;
        }

        return $row;
    }

    /**
     * get parent platform
     * @todo change wrappingkey
     * @param null $wrappingKey
     * @param array $pathsArray
     * @return array
     */
    public function getParentPlatform($wrappingKey = null, $pathsArray = null , $status = null)
    {
        $resultSet =  $this->tableGateway->select(function(Select $select)use($status){
            $select->where->equalTo('parent','0');

            if($status != null)
                $select->where->equalTo('status',$status);

            $select->order('order ASC');
        });

        $data = array();
        if(!empty($resultSet))
        {
            $row = array();
            foreach($resultSet as $result){
                $row['id'] =  (int) $result->id;
                $row['title'] = $result->title;
                $row['icon'] = (isset($result->icon))? $this->fillPathForIconImage($result->icon, $pathsArray) : $result->icon;
                $row['iconActive'] = (isset($result->iconActive))? $this->fillPathForIconActiveImage($result->iconActive, $pathsArray) : $result->iconActive;
                $row['logo'] = (isset($result->logo))? $this->fillPathForLogoImage($result->logo, $pathsArray) : $result->logo;
                $row['bgColor'] = $result->bgColor;
                $row['bgColorActive'] = $result->bgColorActive;
                $row['parent'] = (int) $result->parent;
                $row['order'] = (int) $result->order;
                $row['status'] = (int) $result->status;
                $row['discount'] = $this->discountTable->getDiscountByPlatformId($result->id);

                if(isset($wrappingKey))
                {
                    $data [] = array( $wrappingKey => $row);
                }else{
                    $data [] = $row;
                }
            }
        }

        return $data;
    }

    /**
     * save platform object
     * @param Platform $platform
     * @return int
     * @throws \Exception
     */
    public function savePlatform(Platform $platform,$oldOrder = null)
    {
        $oldOrder = (int) $oldOrder;
        $data = array(
            'title' => $platform->title,
            'icon' => $platform->icon,
            'iconActive' => $platform->iconActive,
            'logo' => $platform->logo,
            'bgColor' => $platform->bgColor,
            'bgColorActive' => $platform->bgColorActive,
            'parent' => $platform->parent,
            'order' => $platform->order,
            'status' => $platform->status
        );

        $id = (int) $platform->id;
        if($id == 0){
            $data['order'] = $this->getMaxOrder($data['parent']) + 1;
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        }else{
            if($this->getPlatform($id)){

                if($data['order'] > $oldOrder){
                    $this->incrementOrder($oldOrder,$data['parent']);
                }elseif($data['order'] < $oldOrder){
                    $this->decrementOrder($oldOrder,$data['parent']);
                }

                $this->tableGateway->update($data,array('id' => $id));
            }else{
                throw new \Exception("platform {$id} not found");
            }
        }

        return $id;
    }

    // move up
    private function decrementOrder($oldOrder,$parent)
    {
        $select = $this->tableGateway->getSql()->select();
        $where = new Where();
        $where->lessThan('order',$oldOrder);
        $where->equalTo('parent',$parent);
        $select->where($where);
        $select->order('order DESC');
        $select->limit(1);

        $rowset = $this->tableGateway->selectWith($select);
        $beforeRow = $rowset->current();

        if($beforeRow){
            $beforeRow->order = $oldOrder;
            $this->tableGateway->update(array('order' => $oldOrder),array('id' => $beforeRow->id));
        }

    }
    // move down
    private function incrementOrder($oldOrder,$parent)
    {
        $select = $this->tableGateway->getSql()->select();
        $where = new Where();
        $where->greaterThan('order',$oldOrder);
        $where->equalTo('parent',$parent);
        $select->where($where);
        $select->order('order ASC');
        $select->limit(1);

        $rowset = $this->tableGateway->selectWith($select);
        $afterRow = $rowset->current();
        if($afterRow){
            $afterRow->order = $oldOrder;
            $this->tableGateway->update(array('order' => $oldOrder),array('id' => $afterRow->id));
        }

    }

    public function getMaxOrder($parent)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array(
            'order' => new Expression('MAX(`order`)')
        ));
        $where = new Where();
        $where->equalTo('parent',$parent);
        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();
        return (int) $row->order;
    }

    public function deletePlatform($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

    /**
     * Fill Full Path for platform icon image
     * @param  string  $icon icon image name
     * @param  string $pathsArray rate Card Upload Images core Paths used to concatenate with images name to get full path
     * @return string full path icon image
     */
    protected function fillPathForIconImage($icon, $pathsArray)
    {
        if(!empty($icon)){
            return Cdn::jsPlugins($pathsArray['path'].$icon);
        }
        return $icon;
    }

    /**
     * Fill Full Path for platform icon active image
     * @param  string  $icon icon active image name
     * @param  string $pathsArray rate Card Upload Images core Paths used to concatenate with images name to get full path
     * @return string full path icon active image
     */
    protected function fillPathForIconActiveImage($icon, $pathsArray)
    {
        if(!empty($icon)){
            return Cdn::jsPlugins($pathsArray['path'].$icon);
        }
        return $icon;
    }

    /**
     * Fill Full Path for platform logo image
     * @param  string $logo logo image name
     * @param  string $pathsArray rate Card Upload Images core Paths used to concatenate with images name to get full path
     * @return string full path icon image
     */
    protected function fillPathForLogoImage($logo, $pathsArray)
    {
        if(!empty($logo)){
            return Cdn::jsPlugins($pathsArray['path'].$logo);
        }
        return $logo;
    }

}