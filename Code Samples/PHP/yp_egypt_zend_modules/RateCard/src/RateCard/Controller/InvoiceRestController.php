<?php

namespace RateCard\Controller;

use RateCard\Form\InvoiceForm;
use RateCard\Model\ClientTable;
use RateCard\Model\Invoice;
use RateCard\Model\InvoiceTable;
use RateCard\Model\UserTable;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

/**
 * InvoiceRestController Restful Controller for Invoice
 * @package RateCard\Controller
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class InvoiceRestController extends  AbstractRestfulController {

    private $invoiceTable;
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

    /**
     * Get All Invoice Objects into InvoiceTable
     * @return JsonModel
     */
    public function getList() {

        $userId = $this->params()->fromRoute('userId');

        if($userId && $userId == $this->getUserId())
        {
            $invoices = $this->getInvoiceTable()->getInvocieByUserId($userId);
        }else{
            $invoices = $this->getInvoiceTable()->fetchAll();
        }

        $invoicesArray = array();

        foreach ($invoices as $invoice) {

            $invoicesArray[]  = $invoice;
        }

        return new JsonModel(
            $invoicesArray
        );

    }

    /**
     * Get Invoice Object using his id into InvoiceTable
     * @param int $id Invoice`s id
     * @return JsonModel
     */
    public function get($id) {

        try {

            $id      = (int)$id;
            $invoice = $this->getInvoiceTable()->getInvoice($id);
        } catch (\Exception $e) {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $e->getMessage()
                )
            );
        }

        return new JsonModel(
            $invoice
        );

    }

    /**
     * Create new Invoice
     * @param array $data Invoice`s data
     * @return JsonModel
     */
    public function create($data) {

        $data['id']         = 0;
        $data['created_at'] = $this->getCurrentDataTime();
        $data['updated_at'] = $this->getCurrentDataTime();

        $invoice     = new Invoice();
        $invoiceForm = new InvoiceForm();
            $invoiceForm->setInputFilter($invoice->getInputFilter());
            $invoiceForm->setData($data);

        if ($invoiceForm->isValid()) {

            try {

                $invoice->exchangeArray($data);
                //check if user which create his inVoice is exists in yp_RateCard_user Table or not
                $this->getUserTable()->getUser($invoice->user_id);
                //check if client who make his inVoice is exists in yp_RateCard_client Table or not
                $this->getClientTable()->getClient($invoice->client_id);

                $invoiceId = $this->getInvoiceTable()->saveInvoice($invoice);
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
                    "error" => $invoiceForm->getInputFilter()->getMessages()
                )
            );
        }

        return new JsonModel(
            $this->getInVoiceTable()->getInVoice($invoiceId)
        );

    }

    /**
     * Update Exists Invoice
     * @param int $id Invoice`s id
     * @param array $data updated Invoice`s data
     * @return JsonModel
     */
    public function update($id, $data) {

        try {

            $invoice = new Invoice();
                $invoice->exchangeArray($this->getInvoiceTable()->getInvoice($id));

            $data['id']         = (int)$id;
            $data['created_at'] = $invoice->created_at;
            $data['updated_at'] = $this->getCurrentDataTime();

            $invoiceForm = new InvoiceForm();
                $invoiceForm->bind($invoice);
                $invoiceForm->setInputFilter($invoice->getInputFilter());
                $invoiceForm->setData($data);

            if ($invoiceForm->isValid()) {

                //check if user which update his deal is exists in yp_RateCard_user Table or not
                $this->getUserTable()->getUser($invoice->user_id);
                //check if client who update his deal is exists in yp_RateCard_client Table or not
                $this->getClientTable()->getClient($invoice->client_id);

                $invoiceId = $this->getInvoiceTable()->saveInvoice($invoice);
            } else {

                $this->response->setStatusCode(400);
                return new JsonModel(array(
                        "data"  => array(),
                        "error" => $invoiceForm->getInputFilter()->getMessages()
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
            $this->getInvoiceTable()->getInvoice($invoiceId)
        );

    }

    /**
     * Delete Exists Invoice
     * @param int $id Invoice`s id
     * @return JsonModel
     */
    public function delete($id) {

        try {

            $this->getInvoiceTable()->getInvoice($id);
            $this->getInvoiceTable()->deleteInvoice($id);
        } catch (\Exception $e) {

            $this->response->setStatusCode(400);
            return new JsonModel(array(
                    "data"  => array(),
                    "error" => $e->getMessage()
                )
            );
        }

        return new JsonModel(array(
                "data" => "Invoice had been deleted"
            )
        );

    }

    private function getUserId()
    {
        $session = new Container('user');
        return $session->user_id;
    }
} 