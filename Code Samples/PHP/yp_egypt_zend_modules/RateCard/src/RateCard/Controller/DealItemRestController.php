<?php

namespace RateCard\Controller;

use RateCard\Form\DealForm;
use RateCard\Form\DealItemForm;
use RateCard\Model\DealItem;
use RateCard\Model\DealItemTable;
use RateCard\Model\InVoiceTable;
use RateCard\Model\PlatformTable;
use RateCard\Model\ServiceTable;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * DealItemRestController Restful Controller for DealItem
 * @package RateCard\Controller
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class DealItemRestController extends  AbstractRestfulController {

    private $dealItemTable;
    private $platformTable;
    private $serviceTable;
    private $invoiceTable;

    /**
     * Get DealItemTable Instance
     * @return DealItemTable
     */
    private function getDealItemTable() {

        if (!$this->dealItemTable) {

            $sm = $this->getServiceLocator();
            $this->dealItemTable = $sm->get('RateCard\Model\DealItemTable');
        }

        return $this->dealItemTable;
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
     * Get InvoiceTable Instance
     * @return InvoiceTable
     */
    private function getInvoiceTable() {

        if (!$this->invoiceTable) {

            $sm = $this->getServiceLocator();
            $this->invoiceTable = $sm->get('RateCard\Model\InvoiceTable');
        }

        return $this->invoiceTable;
    }

    /**
     * Get Current DateTime
     * @return string datetime
     */
    private function  getCurrentDataTime() {

        $date = new \DateTime();
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get All DealItem Objects into DealItemTable
     * @return JsonModel
     */
    public function getList() {

        $dealItems = $this->getDealItemTable()->fetchAll();
        $dealItemsArray = array();

        foreach ($dealItems as $dealItem) {

            $dealItemsArray[]  = $dealItem;
        }

        return new JsonModel(
            $dealItemsArray
        );

    }

    /**
     * Get DealItem Object using his id into DealItemTable
     * @param int $id DealItem`s id
     * @return JsonModel
     */
    public function get($id) {

        try {

            $id       = (int)$id;
            $dealItem = $this->getDealItemTable()->getDealItem($id);
        } catch (\Exception $e) {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $e->getMessage()
                )
            );
        }

        return new JsonModel(
            $dealItem
        );

    }

    /**
     * Create new DealItem
     * @param array $data DealItem`s data
     * @return JsonModel
     */
    public function create($data) {

        $data['id']         = 0;
        $data['created_at'] = $this->getCurrentDataTime();
        $data['updated_at'] = $this->getCurrentDataTime();

        $dealItem     = new DealItem();
        $dealItemForm = new DealItemForm();
            $dealItemForm->setInputFilter($dealItem->getInputFilter());
            $dealItemForm->setData($data);

        if ($dealItemForm->isValid()) {

            try {

                $dealItem->exchangeArray($data);
                //check if Platform which create dealItem under it is exists in yp_RateCard_platform Table or not
                $this->getPlatformTable()->getPlatform($dealItem->platform_id);
                //check if Service which create dealItem under it is exists in yp_RateCard_service Table or not
                $this->getServiceTable()->getService($dealItem->service_id);
                //check if Invoice which create dealItem under it is exists in yp_RateCard_invoice Table or not
                $this->getInvoiceTable()->getInvoice($dealItem->invoice_id);

                $dealItemId = $this->getDealItemTable()->saveDealItem($dealItem);
            } catch (\Exception $e) {

                $this->response->setStatusCode(400);
                return new JsonModel(array(
                        "data"  => array(),
                        "error" => $e->getMessage()
                    )
                );
            }
        } else {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $dealItemForm->getInputFilter()->getMessages()
                )
            );
        }

        return new JsonModel(
            $this->getDealItemTable()->getDealItem($dealItemId)
        );

    }

    /**
     * Update Exists DealItem
     * @param int $id DealItem`s id
     * @param array $data updated DealItem`s data
     * @return JsonModel
     */
    public function update($id, $data) {

        try {

            $dealItem = new DealItem();
                $dealItem->exchangeArray($this->getDealItemTable()->getDealItem($id));

            $data['id']         = (int)$id;
            $data['created_at'] = $dealItem->created_at;
            $data['updated_at'] = $this->getCurrentDataTime();

            $dealItemForm = new DealItemForm();
                $dealItemForm->bind($dealItem);
                $dealItemForm->setInputFilter($dealItem->getInputFilter());
                $dealItemForm->setData($data);

            if ($dealItemForm->isValid()) {

                //check if Platform which update deal under it is exists in yp_RateCard_platform Table or not
                $this->getPlatformTable()->getPlatform($dealItem->platform_id);
                //check if Service which update deal under it is exists in yp_RateCard_service Table or not
                $this->getServiceTable()->getService($dealItem->service_id);
                //check if Invoice which create dealItem under it is exists in yp_RateCard_invoice Table or not
                $this->getInvoiceTable()->getInvoice($dealItem->invoice_id);

                $dealItemId = $this->getDealItemTable()->saveDealItem($dealItem);
            } else {

                $this->response->setStatusCode(400);
                return new JsonModel(array(
                        "data"  => array(),
                        "error" => $dealItemForm->getInputFilter()->getMessages()
                    )
                );
            }
        } catch (\Exception $e) {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $e->getMessage()
                )
            );
        }

        return new JsonModel(
            $this->getDealItemTable()->getDealItem($dealItemId)
        );

    }

    /**
     * Delete Exists DealItem
     * @param int $id DealItem`s id
     * @return JsonModel
     */
    public function delete($id) {

        try {

            $this->getDealItemTable()->getDealItem($id);
            $this->getDealItemTable()->deleteDealItem($id);
        } catch (\Exception $e) {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $e->getMessage()
                )
            );
        }

        return new JsonModel(array(
                "data" => "Deal Item had been deleted"
            )
        );

    }

} 