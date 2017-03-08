<?php

namespace RateCard\Model;

use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Client Model
 * @package RateCard\Model
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class Client implements  InputFilterAwareInterface {

    public $id;
    public $user_id;
    public $account_name;
    public $contact_name;
    public $mobile;
    public $email;
    public $meeting_objectives;
    public $created_at;
    public $updated_at;

    private $inputFilter;

    /**
     * Coping Client entity`s properties From $data array
     *
     * @param array $data array which contains Client entity`s properties
     */
    public function exchangeArray($data) {

        $this->id                 = ( !empty($data['id']) ) ? $data['id'] : null;
        $this->user_id            = ( !empty($data['user_id']) ) ? $data['user_id'] : null;
        $this->account_name       = ( !empty($data['account_name']) ) ? $data['account_name'] : null;
        $this->contact_name       = ( !empty($data['contact_name']) ) ? $data['contact_name'] : null;
        $this->mobile             = ( !empty($data['mobile']) ) ? $data['mobile'] : null;
        $this->email              = ( !empty($data['email']) ) ? $data['email'] : null;
        $this->meeting_objectives = ( !empty($data['meeting_objectives']) ) ? $data['meeting_objectives'] : null;
    }

    /**
     * Get array Copy form Client Model
     * @return array
     */
    public function getArrayCopy() {

        return get_object_vars($this);
    }

    /**
     * Set Input Filter For ClientForm
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {

        throw new \Exception("Not Used");
    }

    /**
     * Get Input Filter For ClientForm
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
                        'name'     => 'account_name',
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
                                    'min'      => 1,
                                    'max'      => 100,
                                ),
                            ),
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'contact_name',
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
                                    'min'      => 1,
                                    'max'      => 100,
                                ),
                            ),
                        )
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'mobile',
                        'required' => true
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'email',
                        'required' => true
                    )
                )
            );

            $inputFilter->add($factory->createInput(array(
                        'name'     => 'meeting_objectives',
                        'required' => false
                    )
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

} 