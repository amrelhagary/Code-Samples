<?php
namespace RateCard\Controller;

use RateCard\Model\StatusModel;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class PlatformServiceRestController extends AbstractRestfulController
{
    public function get($id){}
    public function create($data){}
    public function update($id,$data){}
    public function delete($id){}


    public function getList()
    {
        $data = array();
        try {

            $platformId = $this->params()->fromRoute('pid');

            $sm = $this->getServiceLocator();
            $platformModel = $sm->get('RateCard\Model\SubPlatformTable');
            $serviceModel  = $sm->get('RateCard\Model\ServiceTable');
            $config        = $sm->get('Config');

            if (isset($platformId)) {

                $data = array($platformModel->getPlatform($platformId,'platform', $config['rateCardUploadImagesPath']),StatusModel::$ACTIVE);
            } else {

                $data = $platformModel->getParentPlatform('platform', $config['rateCardUploadImagesPath'],StatusModel::$ACTIVE);
            }

            for ($i=0;$i<count($data);$i++) {

                $subPlatforms = $platformModel->getSubPlatformByParentId($data[$i]['platform']['id'], $config['rateCardUploadImagesPath'],StatusModel::$ACTIVE);
                $data[$i]['platform']['subPlatforms'] = $subPlatforms;

                for ($j=0;$j<count($subPlatforms);$j++) {

                    $service =  $serviceModel->getPlatformService($subPlatforms[$j]['id'], $config['rateCardUploadImagesPath'],StatusModel::$ACTIVE);
                    $data[$i]['platform']['subPlatforms'][$j]['service'] = $service;
                }
            }
        } catch (\Exception $e) {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $e->getMessage()
                )
            );
        }
        return new JsonModel($data);
    }
}