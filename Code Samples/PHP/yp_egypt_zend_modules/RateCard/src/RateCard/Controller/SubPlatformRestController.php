<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Test for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace RateCard\Controller;

use RateCard\Model\Platform;
use Zend\Debug\Debug;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractRestfulController;
use RateCard\Form\PlatformForm;

class SubPlatformRestController extends AbstractRestfulController
{
    private $platformTable;
    private $serviceTable;

    /**
     * @return \RateCard\Model\SubPlatformTable
     */
    private function getPlatformTable()
    {
        if(!$this->platformTable)
        {
            $sm = $this->getServiceLocator();
            $this->platformTable = $sm->get('RateCard\Model\SubPlatformTable');
        }
        return $this->platformTable;
    }

    /**
     * @return \RateCard\Model\ServiceTable
     */
    private function getServiceTable()
    {
        if(!$this->serviceTable)
        {
            $sm = $this->getServiceLocator();
            $this->serviceTable = $serviceTable = $this->getServiceLocator()->get('RateCard\Model\ServiceTable');
        }
        return $this->serviceTable;
    }

    public function getList()
    {
        $platformId = $this->params()->fromRoute('pid');
        $config = $this->getServiceLocator()->get('Config');
        $results = $this->getPlatformTable()->getSubPlatformByParentId($platformId,$config['rateCardUploadImagesPath']);
        $data = array();
        foreach($results as $result){
            $data[] = $result;
        }
        return new JsonModel($data);
    }

    public function get($subPlatformId)
    {
        $platformId = $this->params()->fromRoute('pid');
        try{
            $config = $this->getServiceLocator()->get('Config');
            $subPlatform = $this->getPlatformTable()->getPlatform($subPlatformId,null,$config['rateCardUploadImagesPath']);
            if($subPlatform && is_array($subPlatform)){
                $service = $this->getServiceTable()->getPlatformService($subPlatformId);
                $subPlatform['service'] = $service;
            }
            $result = $subPlatform;
        }catch (\Exception $e){
            return new JsonModel(array(
                'data' => array(),
                'error' => $e->getMessage()
            ));
        }

        return new JsonModel($result);
    }

    public function create($data)
    {}

    public function update($id,$data)
    {}

    public function delete($id)
    {}
}
