<?php

namespace RateCard\Controller;

use Application\Model\FtpUploader;
use RateCard\Form\ServiceForm;
use RateCard\Model\PlatformTable;
use RateCard\Model\Service;
use RateCard\Model\ServiceTable;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * ServiceRestController Restful Controller for Service
 * @package RateCard\Controller
 * @author amr elhagary <a.elhagary@yellow.com.eg> , ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class ServiceRestController extends AbstractRestfulController {

    private $serviceTable;
    private $platformTable;

    /**
     * Get ServiceTable Instance
     * @return ServiceTable
     */
    private function getServiceTable() {

        if (!$this->serviceTable) {

            $sm = $this->getServiceLocator();
            $this->serviceTable = $sm->get('RateCard\Model\ServiceTable');
        }
        return $this->serviceTable;
    }

    /**
     * Get PlatformTable Instance
     * @return PlatformTable
     */
    private function getPlatformTable() {

        if (!$this->platformTable) {

            $sm = $this->getServiceLocator();
            $this->platformTable = $sm->get('RateCard\Model\PlatformTable');
        }

        return $this->platformTable;
    }

    /**
     * Check validation for media Key into service data object
     * @param array $media JSON array contains media objects
     * @return boolean
     */
    private function checkMediaKey($media) {

        //media must be an array
        if (is_array($media)) {

            // loop on media objects
             foreach ($media as $mediaObject) {

                 // every object must be an array and must have type, device, url [ tag key option]
                 if ( !(is_array($mediaObject) && array_key_exists('type', $mediaObject) && array_key_exists('device', $mediaObject)
                     && array_key_exists('url', $mediaObject) ) ) {
                    return false;
                 }

             }

            return true;
        }

        return false;
    }

    /**
     * Check validation for packages_table Key into service data object
     * @param array $packages_table JSON object contains packages table object
     * @return boolean
     */
    private function checkPackagesTableKey($packages_table) {

        //packages table must be an array
        if (is_array($packages_table)) {

            // packages table object must have type, row keys and row key is an array
            if ( !( array_key_exists('type', $packages_table) && array_key_exists('row', $packages_table)
                    && is_array($packages_table['row']) ) ) {
                return false;
            }

            // loop on row objects in packages_table objects
             foreach ($packages_table['row'] as $row) {

                 // every object must have mutable, cells [ hint keys option]
                 if (!( array_key_exists('mutable', $row) && array_key_exists('cells', $row) ) ) {

                    return false;
                 }

                 // every cells key in row object must has be an array
                 if (!is_array($row['cells'])) {

                     return false;
                 }

                 foreach ($row['cells'] as $cell) {

                     // every cell object must has type and content keys
                     if (! ( array_key_exists('type', $cell)
                              && array_key_exists('content', $cell) ) ) {
                         return false;
                     }
                 }

             }

            return true;
        }

        return false;
    }

    /**
     * get Media array after set uploaded files url for media objects
     * @param  array  $mediaArray media data array
     * @param  array | null  $files uploaded files array for media images
     * @return array media array
     */
    private function getMediaArray($mediaArray, $files) {

        $newMediaArray = array();

        if ($files) {

            $filesCounter  = 0;

            for ($counter = 0; $counter < count($mediaArray); $counter++) {

                $mediaObject = array();
                $mediaObject['type']   = $mediaArray[$counter]['type'];
                $mediaObject['device'] = $mediaArray[$counter]['device'];
                $mediaObject['tag']    = (!empty($mediaArray[$counter]['tag'])) ? $mediaArray[$counter]['tag'] : null;
                $mediaObject['url']    = $mediaArray[$counter]['url'];

                if (strcasecmp($mediaObject['type'], 'image') == 0 && is_array($mediaObject['url'])) {
                    // set file name as url key in media object
                    $mediaObject['url'] = $files[$filesCounter]['name'];
                    $filesCounter++;
                }

                $newMediaArray[] = $mediaObject;
            }
            // then call uploadMediaImages method to upload images
            $this->uploadMediaImages($files);
        } else {

            for ($counter = 0; $counter < count($mediaArray); $counter++) {

                $mediaObject = array();
                $mediaObject['type']   = $mediaArray[$counter]['type'];
                $mediaObject['device'] = $mediaArray[$counter]['device'];
                $mediaObject['tag']    = (!empty($mediaArray[$counter]['tag'])) ? $mediaArray[$counter]['tag'] : null;
                $mediaObject['url']    = $mediaArray[$counter]['url'];

                $newMediaArray[] = $mediaObject;
            }
        }

        return $newMediaArray;
    }

    /**
     * get Packages Table array after set uploaded files url for Cell objects for every Packages Table Row objects
     * @param  array  $packagesTableArray Packages Table data array
     * @param  array | null $files uploaded files array for packages_table images
     * @return array Packages Table array
     */
    private function getPackagesTableArray($packagesTableArray, $files) {

        $newPackagesTableArray = array();

        if ($files) {

            $filesCounter = 0;

            $newPackagesTableArray['type'] = $packagesTableArray['type'];
            $newPackagesTableArray['row']  = array();

            for ($rowCounter = 0; $rowCounter < count($packagesTableArray['row']); $rowCounter++) {

                $rowObject            = array();
                $rowObject['mutable'] = $packagesTableArray['row'][$rowCounter]['mutable'];
                $rowObject['hint']    = (!empty($packagesTableArray['row'][$rowCounter]['hint'])) ? $packagesTableArray['row'][$rowCounter]['hint'] : null;
                // loop on cells array from every row object
                for ($cellsCounter = 0; $cellsCounter < count($packagesTableArray['row'][$rowCounter]['cells']); $cellsCounter++) {

                    $cellObject            = array();
                    $cellObject['type']    = $packagesTableArray['row'][$rowCounter]['cells'][$cellsCounter]['type'];
                    $cellObject['content'] = $packagesTableArray['row'][$rowCounter]['cells'][$cellsCounter]['content'];
                    if (strcasecmp($cellObject['type'], 'preview') == 0 && is_array($cellObject['content'])) {
                        // set file name as url key in Cell object
                        $cellObject['content'] = $files[$filesCounter]['name'];
                        $filesCounter++;
                    }

                    $rowObject['cells'][] = $cellObject;
                }
                $newPackagesTableArray['row'][] = $rowObject;
            }
            // then call uploadMediaImages method to upload images
            $this->uploadMediaImages($files);
        } else {

            $newPackagesTableArray['type'] = $packagesTableArray['type'];
            $newPackagesTableArray['row']  = array();

            for ($rowCounter = 0; $rowCounter < count($packagesTableArray['row']); $rowCounter++) {

                $rowObject            = array();
                $rowObject['mutable'] = $packagesTableArray['row'][$rowCounter]['mutable'];
                $rowObject['hint']    = (!empty($packagesTableArray['row'][$rowCounter]['hint'])) ? $packagesTableArray['row'][$rowCounter]['hint'] : null;
                // loop on cells array from every row object
                for ($cellsCounter = 0; $cellsCounter < count($packagesTableArray['row'][$rowCounter]['cells']); $cellsCounter++) {

                    $cellObject            = array();
                    $cellObject['type']    = $packagesTableArray['row'][$rowCounter]['cells'][$cellsCounter]['type'];
                    $cellObject['content'] = $packagesTableArray['row'][$rowCounter]['cells'][$cellsCounter]['content'];

                    $rowObject['cells'][] = $cellObject;
                }
                $newPackagesTableArray['row'][] = $rowObject;
            }
        }


        return $newPackagesTableArray;
    }

    /**
     * Change files name by putting random value at last of file name before file extension
     * @param array $files array of files
     * @return array array of files after changing them
     */
    private function changeFilesName($files) {

        for ($counter = 0; $counter < count($files); $counter++) {

            $file          = $files[$counter];
            $fileName      = substr($file['name'], 0, strrpos($file['name'], '.'));
            $fileExtension = substr($file['name'], strrpos($file['name'], '.')+1);
            // then rename file name with putting random value at last of file name before file extension
            $file['name'] = $fileName.mt_rand().".".$fileExtension;
            // reSet file Object
            $files[$counter] = $file;
        }
        return $files;
    }

    /**
     * Upload Media Images Files Using FtpUploader Model
     * @param array $files media files array
     * @throws \Exception if there error happen in uploading file operation
     */
    private function uploadMediaImages($files) {

        $config = $this->getServiceLocator()->get('Config');

        if(!isset($config['rateCardUploadImagesPath']['ftp']) && $config['rateCardUploadImagesPath']['ftp'] === false){
            throw new \Exception("ftp config not found");
        }

        foreach ($files as $file) {

            if ( ($this->getFtpUploader()->ftpFileUpload($config['rateCardUploadImagesPath']['ftp'].$file['name'],$file['tmp_name'])) == false ) {
                $error = error_get_last();
                throw new \Exception($error['message']);
            }
        }

    }

    /**
     * Get FtpUploader Model instance
     * @return FtpUploader
     */
    private function getFtpUploader() {

        $config = $this->getServiceLocator()->get('Config');

        FtpUploader::setHost($config['ftp']['host']);
        FtpUploader::setUsername($config['ftp']['user']);
        FtpUploader::setPassword($config['ftp']['pass']);
        FtpUploader::setRoot($config['ftp']['root']);

        return FtpUploader::getInstance();
    }


    /**
     * Get All Service Objects into ServiceTable
     * @return JsonModel
     */
    public function getList() {

        $config  = $this->getServiceLocator()->get('Config');
        $results = $this->getServiceTable()->fetchAll($config['rateCardUploadImagesPath']);

        return new JsonModel(
            $results
        );
    }

    /**
     * Get Service Object using his id into ServiceTable
     * @param int $id
     * @return JsonModel
     */
    public function get($id) {

        try {

            $config = $this->getServiceLocator()->get('Config');
            $result = $this->getServiceTable()->getService($id, $config['rateCardUploadImagesPath']);
        } catch (\Exception $e) {

            $this->response->setStatusCode(400);
            return new JsonModel( array(
                    'data'  => array(),
                    'error' => $e->getMessage()
                )
            );
        }

        return new JsonModel(
           $result
        );
    }

    /**
     * Create new Service
     * @param array $data
     * @return JsonModel
     */
    public function create($data) {
        $serviceId = $this->params()->fromRoute('id');
        $service = $this->getServiceTable()->getServiceObj($serviceId);

        if ($service instanceof Service) {

            return $this->__update($service,$data);
        } else {

            return $this->__create($data);
        }
    }

    public function __create($data) {

        $files = $this->getRequest()->getFiles()->toArray();

        $mediaKeyIsValid         = false;
        $packagesTableKeyIsValid = true;

        if (isset($data['media']))  {

            $data['media']   = (is_array($data['media'])) ? $data['media'] : json_decode($data['media'], true);
            $mediaKeyIsValid = $this->checkMediaKey($data['media']);
            $mediaFiles      = (isset($files['media-files'])) ? $this->changeFilesName($files['media-files']) : null;

            // reSet media files after change file names
            if ($mediaFiles) {
                $files['media-files'] = $mediaFiles;
            }

            if ($mediaKeyIsValid) {
                $data['media']        = $this->getMediaArray($data['media'], $mediaFiles);
            }
        }

        if (isset($data['packages_table']))  {

            $data['packages_table']   = (is_array($data['packages_table'])) ? $data['packages_table'] : json_decode($data['packages_table'], true);
            $packagesTableKeyIsValid  = $this->checkPackagesTableKey($data['packages_table']);
            $packagesTableFiles       = (isset($files['row-files'])) ? $this->changeFilesName($files['row-files']) : null;

            // reSet package table files after change file names
            if ($packagesTableFiles) {

                $files['row-files']     = $packagesTableFiles;
            }

            if ($packagesTableKeyIsValid) {

                $data['packages_table'] = $this->getPackagesTableArray($data['packages_table'], $packagesTableFiles);
            }
        }

        $service = new Service();
        $form    = new ServiceForm();
            $form->setInputFilter($service->getInputFilter());
            $form->setData($data);

        $formIsValid = $form->isValid();

        if ($formIsValid && $mediaKeyIsValid && $packagesTableKeyIsValid) {

            try {

                $service->exchangeArray($form->getData());
                //check if Platform which update service under it is exists in yp_RateCard_platform Table or not
                $this->getPlatformTable()->getPlatform($service->platform_id);

                $id = $this->getServiceTable()->saveService($service);
            } catch (\Exception $e) {

                $this->response->setStatusCode(400);
                return new JsonModel(array(
                        "error" => $e->getMessage()
                    )
                );
            }
        } else {

            $error = array();

            if (!$formIsValid) {

                $error  = array_merge($form->getInputFilter()->getMessages(), $error);
            }

            if (!$mediaKeyIsValid) {

                $error["media Array"] = "Value must Contains Type, Device, Url and Tag keys";
            }

            if (!$packagesTableKeyIsValid) {

                $error["packages tables Array"] = "Value must Contains Type, Rows keys";
            }

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                'data'  => array(),
                'error' => $error
            ));
        }

        $config = $this->getServiceLocator()->get('Config');

        return new JsonModel(
            $this->getServiceTable()->getService($id, $config['rateCardUploadImagesPath'])
        );
    }

    public function __update(Service $service,$data) {

        try {

            $files = $this->getRequest()->getFiles()->toArray();

            $mediaKeyIsValid         = false;
            $packagesTableKeyIsValid = true;

            if (isset($data['media']))  {

                $data['media']   = (is_array($data['media'])) ? $data['media'] : json_decode($data['media'], true);
                $mediaKeyIsValid = $this->checkMediaKey($data['media']);
                $mediaFiles      = (isset($files['media-files'])) ? $this->changeFilesName($files['media-files']) : null;

                // reSet media files after change file names
                if ($mediaFiles) {
                    $files['media-files'] = $mediaFiles;
                }

                if ($mediaKeyIsValid) {
                    $data['media']        = $this->getMediaArray($data['media'], $mediaFiles);
                }
            }

            if (isset($data['packages_table']))  {

                $data['packages_table']   = (is_array($data['packages_table'])) ? $data['packages_table'] : json_decode($data['packages_table'], true);
                $packagesTableKeyIsValid  = $this->checkPackagesTableKey($data['packages_table']);
                $packagesTableFiles       = (isset($files['row-files'])) ? $this->changeFilesName($files['row-files']) : null;

                // reSet package table files after change file names
                if ($packagesTableFiles) {

                    $files['row-files']     = $packagesTableFiles;
                }

                if ($packagesTableKeyIsValid) {

                    $data['packages_table'] = $this->getPackagesTableArray($data['packages_table'], $packagesTableFiles);
                }
            }

            $form = new ServiceForm();
                $form->bind($service);
                $form->setInputFilter($service->getInputFilter());
                $form->setData($data);

            $formIsValid = $form->isValid();

            if ($formIsValid && $mediaKeyIsValid && $packagesTableKeyIsValid) {

                //$service->exchangeArray($form->getData());
                //check if Platform which update service under it is exists in yp_RateCard_platform Table or not
                $this->getPlatformTable()->getPlatform($service->platform_id);

                $id = $this->getServiceTable()->saveService($service);
            } else {

                $error = array();

                if (!$formIsValid) {

                    $error  = array_merge($form->getInputFilter()->getMessages(), $error);
                }

                if (!$mediaKeyIsValid) {

                    $error["media Array"] = "Value must Contains Type, Device, Url and Tag keys";
                }

                if (!$packagesTableKeyIsValid) {

                    $error["packages tables Array"] = "Value must Contains Type, Rows keys";
                }

                $this->response->setStatusCode(400);
                return new JsonModel(array(
                    'data'  => array(),
                    'error' => $error
                ));
            }
        } catch (\Exception $e) {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "error" => $e->getMessage()
                )
            );
        }

        $config = $this->getServiceLocator()->get('Config');

        return new JsonModel(
            $this->getServiceTable()->getService($id, $config['rateCardUploadImagesPath'])
        );
    }

    /**
     * Update Exists Service
     * @param int $id
     * @param array $data
     * @return JsonModel
     */
    public function update($id,$data)
    {}

    /**
     * Delete Exists Service
     * @param int $id
     * @return JsonModel
     */
    public function delete($id) {

        $this->getServiceTable()->deleteService($id);
        return new JsonModel( array(
                'data' => 'Service object had been Deleted'
            )
        );
    }

}
