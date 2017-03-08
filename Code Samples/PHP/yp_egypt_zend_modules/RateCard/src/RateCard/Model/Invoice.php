<?php

namespace RateCard\Model;

use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Invoice Model
 * @package RateCard\Model
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class Invoice implements  InputFilterAwareInterface {

    public $id;
    public $user_id;
    public $client_id;
    public $total_amount;
    public $comment;
    public $created_at;
    public $updated_at;

    private $inputFilter;

    /**
     * Coping Invoice entity`s properties From $data array
     *
     * @param array $data array which contains Invoice entity`s properties
     */
    public function exchangeArray($data) {

        $this->id           = ( !empty($data['id']) ) ? $data['id'] : null;
        $this->user_id      = ( !empty($data['user_id']) ) ? $data['user_id'] : null;
        $this->client_id    = ( !empty($data['client_id']) ) ? $data['client_id'] : null;
        $this->total_amount = ( !empty($data['total_amount']) ) ? $data['total_amount'] : null;
        $this->comment      = ( !empty($data['comment']) ) ? $data['comment'] : null;
        $this->created_at   = ( !empty($data['created_at']) ) ? $data['created_at'] : null;
        $this->updated_at   = ( !empty($data['updated_at']) ) ? $data['updated_at'] : null;
    }

    /**
     * Get array Copy form Invoice Model
     * @return array
     */
    public function getArrayCopy() {

        return get_object_vars($this);
    }

    /**
     * Set Input Filter For InvoiceForm
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {

        throw new \Exception("Not Used");
    }

    /**
     * Get Input Filter For InVoiceForm
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
                        'name'     => 'user_id',
                        'required' => true,
                        'filter'   => array(
                            array('name' => 'Int')
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'client_id',
                        'required' => true,
                        'filter'   => array(
                            array('name' => 'Int')
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'total_amount',
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