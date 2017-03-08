<?php

namespace RateCard\Model;

use Application\Model\Cdn;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

/**
 * ServiceTable class is Model/Service`s TableGetaway class
 * @package RateCard\Model
 * @author amr elhagary <a.elhagary@yellow.com.eg> , ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class ServiceTable extends TableGateway {

    protected $tableGateway;

    /**
     * ServiceTable Constructor
     * @param TableGateway $tableGateway tableGateway object
     */
    public function __construct(TableGateway $tableGateway) {

        $this->tableGateway = $tableGateway;
    }

    /**
     * Get All Service Objects in yp_RateCard_service Table
     * @param array|null $pathsArray rate Card Upload Images core Path used to concatenate with images name to get full path
     * @return ResultSet array of Service`s Objects
     */
    public function fetchAll($pathsArray = null) {

        $resultSet = $this->tableGateway->select();
        $services  = array();

        foreach ($resultSet as $result) {

            $service = array();
            $service['id']             = $result->id;
            $service['platform_id']    = $result->platform_id;
            $service['title']          = $result->title;

            if (!is_null($result->media)) {
                //decode string json stored into Database for media fields
                $service['media'] = $this->fillPathForMediaUrls(json_decode($result->media, true), $pathsArray);
            }

            if (!is_null($result->content)) {

                $service['content'] = $result->content;
            }

            if (!is_null($result->script)) {

                $service['script'] = $result->script;
            }

            if (!is_null($result->packages_table)) {
                //decode string json stored into Database for packages_table fields
                $service['packages_table'] = $this->fillPathForPreviewImages(json_decode($result->packages_table, true), $pathsArray);
            }

            $service['service_order']  = $result->service_order;

            $services[] = $service;
        }
        return $services;
    }

    /**
     * Get Service Object in yp_RateCard_service Table
     * @param int $id Service`s id
     * @param array|null $pathsArray rate Card Upload Images core Path used to concatenate with images name to get full path
     * @return array | \ArrayObject Service Object
     * @throws \Exception if Service`s id which used to get Service object does not exist in yp_RateCard_service Table
     */
    public function getService($id, $pathsArray = null , $status = null) {

        $id     = (int) $id;
        $rowSet = $this->tableGateway->select(function(Select $select)use($id,$status){
            $select->where->equalTo('id',$id);

            if($status != null)
                $select->where->equalTo('status',$status);
        });
        $row    = $rowSet->current();

        if (!$row) {
            throw new \Exception("Service Does not exists");
        }

        $service = array();

        $service['id']             = $row->id;
        $service['platform_id']    = $row->platform_id;
        $service['title']          = $row->title;

        if (!is_null($row->media)) {
            //decode string json stored into Database for media fields
            $service['media'] = $this->fillPathForMediaUrls(json_decode($row->media, true), $pathsArray);
        }

        if (!is_null($row->content)) {

            $service['content'] = $row->content;
        }

        if (!is_null($row->script)) {

            $service['script'] = $row->script;
        }

        if (!is_null($row->packages_table)) {
            //decode string json stored into Database for packages_table fields
            $service['packages_table'] = $this->fillPathForPreviewImages(json_decode($row->packages_table, true), $pathsArray);
        }

        $service['service_order']  = $row->service_order;

        return $service ;
    }

    public function getServiceObj($id) {
        $id = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if(!$row){
            return false;
        }

        return $row;
    }

    /**
     * Get all Services under the same platform according to service`s platform_id
     * @param $platformId Platform` id
     * @param array|null $pathsArray rate Card Upload Images core Path used to concatenate with images name to get full path
     * @return array array of Service`s Objects
     */
    public function getPlatformService($platformId, $pathsArray = null,$status = null)
    {
        $platformId = (int) $platformId;
        // query statement using Zend\Db\Sql\Select and Zend\Db\Sql\Where
        $select =  $this->tableGateway->getSql()->select();
        $where  =  $select->where;
        $where->equalTo('platform_id', $platformId);

        if($status != null)
            $where->equalTo('status',$status);

        $select->order('service_order ASC');
        $resultSet  = $this->tableGateway->selectWith($select);

        $services   = array();

        if (!empty($resultSet)) {

            foreach ($resultSet as $result) {

                $service = array();

                $service['id']             = $result->id;
                $service['platform_id']    = $result->platform_id;
                $service['title']          = $result->title;

                if (!is_null($result->media)) {

                    $service['media'] = $this->fillPathForMediaUrls(json_decode($result->media, true), $pathsArray);
                }

                if (!is_null($result->content)) {

                    $service['content'] = $result->content;
                }

                if (!is_null($result->script)) {

                    $service['script'] = $result->script;
                }

                if (!is_null($result->packages_table)) {

                    $service['packages_table'] = $this->fillPathForPreviewImages(json_decode($result->packages_table, true), $pathsArray);
                }

                $service['service_order']  = $result->service_order;
                $service['status']         = (int) $result->status;

                $services[] = $service;
            }
        }

        return $services;
    }

    /**
     * Save Service Object in yp_RateCard_service Table if is not exists
     * OR
     * Update Service Object with new values if is already exists in yp_RateCard_service Table
     * @param Service $service Service object
     * @return int saved/updated Service`s Id
     * @throws \Exception in case Update Service Object is not exists in yp_RateCard_service Table
     */
    public function saveService(Service $service) {

        $data = array(
            'platform_id'    => $service->platform_id,
            'title'          => $service->title,
            'media'          => (is_null($service->media)) ? null : json_encode($this->removePathForMediaUrls($service->media)),
            'content'        => $service->content,
            'script'         => $service->script,
            'packages_table' => (is_null($service->packages_table)) ? null : json_encode($this->removePathForPreviewImages($service->packages_table)),
            'service_order'  => $service->service_order,
            'status'         => $service->status
        );

        $id = (int) $service->id;
        if ($id == 0) {


            $data['service_order'] = $this->getMaxServiceOrder($data['platform_id']) + 1;
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {

            if($this->getService($id)){
                $this->tableGateway->update($data, array('id' => $id));
            }else{
                throw new \Exception("Service Does not exists");
            }
        }

        return $id;
    }

    /**
     * Delete Service Object from yp_RateCard_service Table
     * @param int $id Service`s id
     */
    public function deleteService($id) {

        $this->tableGateway->delete(array('id' => (int) $id));
    }

    /**
     * Get Max Order for Service under same Parent Platform
     * @param int $platformId parent Platform id
     * @return int max order of service under platformId or -1 in case there isn`t service had been added under platformId
     * @throws \Exception
     */
    private function getMaxServiceOrder($platformId) {

        $platformId = (int)$platformId;
        $select = $this->tableGateway->getSql()->select();
            $select->columns(array(
                 'service_order' => new Expression('MAX(service_order)'),
                 'id'            => new Expression('Count(id)')
            ));
        $where  = $select->where;
        $where->equalTo('platform_id', $platformId);
        $select->limit(1);
        $rowSet = $this->tableGateway->selectWith($select);
        $row    = $rowSet->current();

        if (!$row) {
            throw new \Exception("Error happened when get max order");
        }

        // max service order under platform_id
        $maxServiceOrder = (int)$row->service_order;
        // count of services  under platform_id
        $servicesCount   = (int)$row->id;
        if ( $maxServiceOrder == 0 &&  $servicesCount== 0) {
            return -1;
        }

        return $maxServiceOrder;
    }

    /**
     * Fill Full Path for media images URLs
     * @param  array  $media media data array
     * @param  array $pathsArray rate Card Upload Images core Path used to concatenate with images name to get full path
     * @return array media array
     */
    private function fillPathForMediaUrls($media, $pathsArray) {

        $corePath = $pathsArray['path'];

        for ($counter = 0; $counter < count($media); $counter++) {

            if (strcasecmp($media[$counter]['type'], 'image') == 0) {

                $media[$counter]['url'] = Cdn::jsPlugins($corePath.$media[$counter]['url']);
            }
        }

        return $media;
    }

    /**
     * Fill Full Path for Preview images URLs into packages-table cells
     * @param  array $table packages table data array
     * @param  array $pathsArray rate Card Upload Images core Path used to concatenate with images name to get full path
     * @return array package table data array
     */
    private function fillPathForPreviewImages($table, $pathsArray) {

        $corePath = $pathsArray['path'];

        $mutableRow = $singularRow = 0;
        for ($rowCounter = 0; $rowCounter < count($table['row']); $rowCounter++) {


            if($table['row'][$rowCounter]['mutable'] == true)
            {
                $mutableRow++;
            }else{
                $singularRow++;
            }
            $table['countMutable']= $mutableRow;
            $table['countSingular'] = $singularRow;

            for ($celCounter = 0; $celCounter < count($table['row'][$rowCounter]['cells']); $celCounter++) {

                if (strcasecmp($table['row'][$rowCounter]['cells'][$celCounter]['type'], 'preview') == 0) {

                    $table['row'][$rowCounter]['cells'][$celCounter]['content'] = Cdn::jsPlugins($corePath.$table['row'][$rowCounter]['cells'][$celCounter]['content']);
                }
            }

        }

        return $table;
    }

    /**
     * Remove Full Path for media images URLs before saving/update image name into database
     * @param  array  $media media data array
     * @return array media array
     */
    private function removePathForMediaUrls($media) {

        for ($counter = 0; $counter < count($media); $counter++) {

            if (strcasecmp($media[$counter]['type'], 'image') == 0 && preg_match('/(http:)/',$media[$counter]['url']) == 1) {

                $imageUrl = $media[$counter]['url'];
                // check if image url with cacheKiller, remove cacheKiller if exists with image url
                if (preg_match('/(?)/', $imageUrl) == 1) {

                    $imageUrl =  substr($imageUrl, 0, stripos($imageUrl, '?'));
                }
                $media[$counter]['url'] = substr($imageUrl, strrpos($imageUrl, '/') +1);
            }
        }

        return $media;
    }

    /**
     * Remove Full Path for Preview images URLs into packages-table cells
     * @param  array $table packages table data array
     * @return array package table data array
     */
    private function removePathForPreviewImages($table) {

        for ($rowCounter = 0; $rowCounter < count($table['row']); $rowCounter++) {

            for ($celCounter = 0; $celCounter < count($table['row'][$rowCounter]['cells']); $celCounter++) {

                if (strcasecmp($table['row'][$rowCounter]['cells'][$celCounter]['type'], 'preview') == 0 && preg_match('/(http:)/',$table['row'][$rowCounter]['cells'][$celCounter]['content']) == 1) {

                    $imageUrl = $table['row'][$rowCounter]['cells'][$celCounter]['content'];
                    // check if image url with cacheKiller, remove cacheKiller if exists with image url
                    if (preg_match('/(?)/', $imageUrl) == 1) {

                        $imageUrl =  substr($imageUrl, 0, stripos($imageUrl, '?'));
                    }
                    $table['row'][$rowCounter]['cells'][$celCounter]['content'] = substr($imageUrl, strrpos($imageUrl, '/') +1);
                }
            }

        }

        return $table;
    }

}