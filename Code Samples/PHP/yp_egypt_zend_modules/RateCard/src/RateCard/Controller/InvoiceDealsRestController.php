<?php

namespace RateCard\Controller;

use RateCard\Exceptions\NotFoundException;
use RateCard\Exceptions\UnauthorizedException;
use RateCard\Form\DealItemForm;
use RateCard\Form\InvoiceForm;
use RateCard\Model\ClientTable;
use RateCard\Model\DealItem;
use RateCard\Model\DealItemTable;
use RateCard\Model\Invoice;
use RateCard\Model\InvoiceTable;
use RateCard\Model\PlatformTable;
use RateCard\Model\ServiceTable;
use RateCard\Model\UserTable;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;

/**
 * InVoiceDealsRestController Restful Controller for Final InVoice with Deals
 * @package RateCard\Controller
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class InVoiceDealsRestController extends AbstractRestfulController{

    private $invoiceTable;
    private $dealItemTable;
    private $platformTable;
    private $serviceTable;
    private $userTable;
    private $clientTable;

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
     * Get UserTable Instance
     * @return UserTable
     */
    private function getUserTable() {

        if (!$this->userTable) {

            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('RateCard\Model\UserTable');
        }

        return $this->userTable;
    }

    /**
     * Get ClientTable Instance
     * @return ClientTable
     */
    private function getClientTable() {

        if (!$this->clientTable) {

            $sm = $this->getServiceLocator();
            $this->clientTable = $sm->get('RateCard\Model\ClientTable');
        }

        return $this->clientTable;
    }

    /**
     * Get Current DateTime
     * @return string datetime
     */
    private function  getCurrentDataTime() {

        $date = new \DateTime();
        return $date->format('Y-m-d H:i:s');
    }

    public function getList() {}

    public function get($id) {}

    public function create($data) {
        // first collect all Data for Invoice
        $invoiceData = array();
        $invoiceData['id']           = 0;
        $invoiceData['user_id']      = $this->getUserId();
        $invoiceData['client_id']    = $this->getClientId();
        $invoiceData['comment']      = (!empty($data['comment'])) ? $data['comment'] : null;
        $invoiceData['created_at']   = $this->getCurrentDataTime();
        $invoiceData['updated_at']   = $this->getCurrentDataTime();
        $invoiceData['total_amount'] = 0;

        // loop on all Deals then collect the total_amount for invoice
        $dealItems = $data['items'];
        foreach ($dealItems as $dealItem) {

            $amountForDealItem = 0;

            if (isset($dealItem['amount']) && isset($dealItem['quantity'])) {

                $amountForDealItem = $dealItem['amount'] * $dealItem['quantity'];
            } else if (isset($dealItem['amount'])) {

                $amountForDealItem = $dealItem['amount'];
            }
            $invoiceData['total_amount'] += $amountForDealItem;
        }


        $invoice     = new Invoice();
        $invoiceForm = new InvoiceForm();
            $invoiceForm->setInputFilter($invoice->getInputFilter());
            $invoiceForm->setData($invoiceData);

        if ($invoiceForm->isValid()) {

                try {

                    $invoice->exchangeArray($invoiceData);
                    //check if user which create his inVoice is exists in yp_RateCard_user Table or not
                    $this->getUserTable()->getUser($invoice->user_id);
                    //check if client who make his inVoice is exists in yp_RateCard_client Table or not
                    $this->getClientTable()->getClient($invoice->client_id);

                    // Save Invoice Object in DataBase
                    $invoiceId = $this->getInvoiceTable()->saveInvoice($invoice);
                } catch (\Exception $e) {

                    $this->response->setStatusCode(400);
                    return new JsonModel(array(
                            "data"  => array(),
                            "error" => $e->getMessage()
                        )
                    );
                }

                $dealItemObjects = array();
                // loop again on all deals to save them into database
                foreach ($dealItems as $dealItem) {

                    // first collect Data for Deal
                    $dealItemData = array();
                    $dealItemData['id']           = 0;
                    $dealItemData['platform_id']  = (isset($dealItem['platform_id'])) ? $dealItem['platform_id'] : null;
                    $dealItemData['service_id']   = (isset($dealItem['service_id'])) ? $dealItem['service_id'] : null;
                    $dealItemData['invoice_id']   = $invoiceId;
                    $dealItemData['amount']       = (isset($dealItem['amount'])) ? $dealItem['amount'] : null;
                    $dealItemData['package_name'] = (isset($dealItem['package_name'])) ? $dealItem['package_name'] : null;
                    $dealItemData['quantity']     = (isset($dealItem['quantity'])) ? $dealItem['quantity'] : null;
                    $dealItemData['comment']      = (isset($dealItem['comment'])) ? $dealItem['comment'] : null;
                    $dealItemData['created_at']   = $this->getCurrentDataTime();
                    $dealItemData['updated_at']   = $this->getCurrentDataTime();

                    $dealItemObject = new DealItem();
                    $dealItemForm   = new DealItemForm();
                        $dealItemForm->setInputFilter($dealItemObject->getInputFilter());
                        $dealItemForm->setData($dealItemData);

                    // check validation for every Deal Item
                    if ($dealItemForm->isValid()) {

                        try {

                            $dealItemObject->exchangeArray($dealItemData);
                            //check if Platform which create dealItem under it is exists in yp_RateCard_platform Table or not
                            $this->getPlatformTable()->getPlatform($dealItemObject->platform_id);
                            //check if Service which create dealItem under it is exists in yp_RateCard_service Table or not
                            $this->getServiceTable()->getService($dealItemObject->service_id);

                            // after finish checking for deal item data , Store it`s object in array until finish to checking another deal items
                            $dealItemObjects[] = $dealItemObject;
                        } catch (\Exception $e) {

                            // in case one of deal item data is not valid then we must remove invoice object from database
                            $this->getInvoiceTable()->deleteInvoice($invoiceId);

                            $this->response->setStatusCode(400);
                            return new JsonModel(array(
                                    "data"  => array(),
                                    "error" => $e->getMessage()
                                )
                            );
                        }
                    } else {

                        // in case one of deal item data is not valid then we must remove invoice object from database
                        $this->getInvoiceTable()->deleteInvoice($invoiceId);

                        $this->response->setStatusCode(400);
                        return new JsonModel(array(
                                "data"  => array(),
                                "error" => $dealItemForm->getInputFilter()->getMessages()
                            )
                        );
                    }

                }

                // after checking all deal items data , Save them into database
                foreach ($dealItemObjects as $dealItemObject) {

                    $this->getDealItemTable()->saveDealItem($dealItemObject);
                }

        } else {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $invoiceForm->getInputFilter()->getMessages()
                )
            );
        }

        return new JsonModel(array(
                "invoiceId" => $invoiceId
            )
        );

    }

    public function update($id, $data) {}

    public function delete($id) {}

    private function getClientId()
    {
        $session = new Container('user');
        $clientId = $session->clientId;

        return $clientId;
    }

    private function getUserId()
    {
        $session = new Container('user');

        if (!empty($session->userId)) {

            return $session->userId;
        } else {

            throw new UnauthorizedException("user not found");
        }
    }

} 