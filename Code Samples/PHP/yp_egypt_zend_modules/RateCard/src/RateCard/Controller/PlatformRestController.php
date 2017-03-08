<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Test for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace RateCard\Controller;

use Application\Model\FtpUploader;
use RateCard\Exceptions\RateCardException;
use RateCard\Model\Platform;
use Zend\Debug\Debug;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Header\Cookie;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;
use RateCard\Form\PlatformForm;

class PlatformRestController extends AbstractRestfulController
{
    private $platformTable;

    /**
     * @return \RateCard\Model\PlatformTable;
     */
    private function getPlatformTable()
    {
        if(!$this->platformTable)
        {
            $sm = $this->getServiceLocator();
            $this->platformTable = $sm->get('RateCard\Model\PlatformTable');
        }
        return $this->platformTable;
    }

    public function getList()
    {
        $config = $this->getServiceLocator()->get('Config');
        $platforms = $this->getPlatformTable()->getParentPlatform(null,$config['rateCardUploadImagesPath']);
        return new JsonModel($platforms);
    }

    public function get($id)
    {
        try{
            $config = $this->getServiceLocator()->get('Config');
            $result = $this->getPlatformTable()->getPlatform($id,null,$config['rateCardUploadImagesPath']);
        }catch (\Exception $e){
            return new JsonModel(array(
                'data' => array(),
                'error' => $e->getMessage()
            ));
        }
        return new JsonModel($result);
    }

    public function create($data)
    {

        $platformId = $this->params()->fromRoute('id');

        $platform = $this->getPlatformTable()->getPlatformObj($platformId);

        if($platform instanceof \RateCard\Model\Platform)
        {
            return $this->__update($platform,$data);
        }else{
            return $this->__create($data);
        }

    }

    private function __create($data)
    {

        $form = new PlatformForm();
        $platform = new Platform();
        $form->setInputFilter($platform->getInputFilter());

        $files = $this->generateFileNameRand($this->getRequest()->getFiles()->toArray());
        $data['logo']       = (!empty($files['logo']['name'])) ? $files['logo']['name'] : null ;
        $data['icon'] = (!empty($files['icon']['name'])) ? $files['icon']['name'] : null ;
        $data['iconActive'] = (!empty($files['iconActive']['name'])) ? $files['iconActive']['name'] : null ;

        $form->setData($data);

        if($form->isValid()){
            // disable file upload
            $this->uploadPlatformImage($files);
            $platform->exchangeArray($form->getData());
            $id = $this->getPlatformTable()->savePlatform($platform);
        }else{
            $this->response->setStatusCode(400);
            return new JsonModel(array(
                'data' => array(),
                'error' => $form->getInputFilter()->getMessages()
            ));
        }

        return new JsonModel(array(
            'data' => array('id' => $id)
        ));
    }

    private function __update(Platform $platform,$data)
    {

        $data['id'] = $platform->id;
        $form = new PlatformForm();

        $files = $this->generateFileNameRand($this->getRequest()->getFiles()->toArray());
        $data['logo']       = (!empty($files['logo']['name'])) ? $files['logo']['name'] : $platform->logo ;
        $data['icon']       = (!empty($files['icon']['name'])) ? $files['icon']['name'] : $platform->icon ;
        $data['iconActive'] = (!empty($files['iconActive']['name'])) ? $files['iconActive']['name'] : $platform->iconActive ;
        $oldOrder = $platform->order;

        $form->bind($platform);
        $form->setInputFilter($platform->getInputFilter());
        $form->setData($data);

        if($form->isValid()){
            // disable file upload
            $this->uploadPlatformImage($files);
            $id = $this->getPlatformTable()->savePlatform($form->getData(),$oldOrder);
        }else{
            $this->response->setStatusCode(400);
            return new JsonModel(array(
                'data' => array(),
                'error' => $form->getInputFilter()->getMessages()
            ));
        }

        return new JsonModel(array(
            'id' => $id
        ));
    }

    public function update($id,$data)
    {
        /**
         * the create method will act as create and update
         * because the hassle to handle upload file using PUT request
         */
    }

    public function delete($id)
    {
        $this->getPlatformTable()->deletePlatform($id);
        return new JsonModel(array('id' => $id));
    }

    private function uploadPlatformImage($files)
    {
        $config = $this->getServiceLocator()->get('Config');

        if(!isset($config['rateCardUploadImagesPath']['ftp']) && $config['rateCardUploadImagesPath']['ftp'] === false){
            throw new \Exception("ftp config not found");
        }

        foreach ($files as $file) {

            try{
                $this->getFtpUploader()->ftpFileUpload($config['rateCardUploadImagesPath']['ftp'].$file['name'],$file['tmp_name']);
            }catch (\Exception $e){
                throw new RateCardException($e->getMessage().$config['rateCardUploadImagesPath']['ftp'].$file['name']);
            }
        }
    }

    /**
     * @return \Application\Model\FtpUploader;
     */
    private function getFtpUploader()
    {
        $config = $this->getServiceLocator()->get('Config');
        FtpUploader::setHost($config['ftp']['host']);
        FtpUploader::setUsername($config['ftp']['user']);
        FtpUploader::setPassword($config['ftp']['pass']);
        FtpUploader::setRoot($config['ftp']['root']);
        return FtpUploader::getInstance();
    }

    private function generateFileNameRand($files)
    {
        if(is_array($files)){
            array_walk($files,function(&$file,$value){
                $x = explode('.',$file['name']);
                $file['name'] = $x[0].'_'.mt_rand().'.'.$x[1];
            });
        }

        return $files;
    }
}
