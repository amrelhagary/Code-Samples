<?php
namespace RateCard\Controller;

use RateCard\Exceptions\UnauthorizedException;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

class InvoiceUserRestController extends AbstractRestfulController
{
    private $invoiceTable;

    private function getInvoiceTable()
    {
        if(!$this->invoiceTable)
        {
            $this->invoiceTable = $this->getServiceLocator()->get('RateCard\Model\InvoiceTable');
        }
        return $this->invoiceTable;

    }

    public function get($userId)
    {
    }

    public function getList()
    {
        $session = new Container('user');
        $userId = $session->userId;
        if($userId)
        {
            $invoices = $this->getInvoiceTable()->getInvoiceByUserId($userId);
            return new JsonModel(array(
                'invoices' => $invoices
            ));
        }else{
            throw new UnauthorizedException("user not found");
        }
    }
    public function create($data){}
    public function update($id,$data){}
    public function delete($id){}
}