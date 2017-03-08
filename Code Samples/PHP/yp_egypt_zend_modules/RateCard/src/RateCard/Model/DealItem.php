<?php

namespace RateCard\Model;

use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * DealItem Model
 * @package RateCard\Model
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class DealItem implements  InputFilterAwareInterface {

    public $id;
    public $platform_id;
    public $service_id;
    public $invoice_id;
    public $amount;
    public $package_name;
    public $quantity;
    public $comment;
    public $created_at;
    public $updated_at;

    private $inputFilter;

    /**
     * Coping DealItem entity`s properties From $data array
     *
     * @param array $data array which contains DealItem entity`s properties
     */
    public function exchangeArray($data) {

        $this->id           = ( !empty($data['id']) ) ? $data['id'] : null;
        $this->platform_id  = ( !empty($data['platform_id']) ) ? $data['platform_id'] : null;
        $this->service_id   = ( !empty($data['service_id']) ) ? $data['service_id'] : null;
        $this->invoice_id   = ( !empty($data['invoice_id']) ) ? $data['invoice_id'] : null;
        $this->amount       = ( !empty($data['amount']) ) ? $data['amount'] : null;
        $this->package_name = ( !empty($data['package_name']) ) ? $data['package_name'] : null;
        $this->quantity     = ( !empty($data['quantity']) ) ? $data['quantity'] : null;
        $this->comment      = ( !empty($data['comment']) ) ? $data['comment'] : null;
        $this->created_at   = ( !empty($data['created_at']) ) ? $data['created_at'] : null;
        $this->updated_at   = ( !empty($data['updated_at']) ) ? $data['updated_at'] : null;
    }

    /**
     * Get array Copy form DealItem Model
     * @return array
     */
    public function getArrayCopy() {

        return get_object_vars($this);
    }

    /**
     * Set Input Filter For DealItemForm
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {

        throw new \Exception("Not Used");
    }

    /**
     * Get Input Filter For DealItemForm
     * @return InputFilter|InputFilterInterface
     */
    public function getInputFilter() {

        if (!$this->inputFilter) {

            $inputFilter = new InputFilter();
            $factory     = new Factory();

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'id',
                        'required' => true,
                        'filter'   => array(
                            array('name' => 'Int')
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'platform_id',
                        'required' => true,
                        'filter'   => array(
                            array('name' => 'Int')
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'service_id',
                        'required' => true,
                        'filter'   => array(
                            array('name' => 'Int')
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'invoice_id',
                        'required' => true,
                        'filter'   => array(
                            array('name' => 'Int')
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'amount',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'Int')
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Between',
                                'options' => array(
                                    'min' => 1
                                )
                            ),
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'package_name',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min'      => 1
                                ),
                            ),
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'quantity',
                        'required' => true,
                        'filter'   => array(
                            array('name' => 'Int')
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Between',
                                'options' => array(
                                    'min' => 1
                                )
                            ),
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'comment',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min'      => 1
                                ),
                            ),
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'created_at',
                        'required' => true
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'updated_at',
                        'required' => true
                    )
                )
            );

            $this->inputFilter = $inputFilter;

        }

        return $this->inputFilter;
    }

} 