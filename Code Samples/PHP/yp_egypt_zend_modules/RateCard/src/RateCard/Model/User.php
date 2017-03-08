<?php

namespace RateCard\Model;

use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * User Model
 * @package RateCard\Model
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class User implements  InputFilterAwareInterface {

    public $id;
    public $username;
    public $name;
    public $token;
    public $role;

    private $inputFilter;

    /**
     * Coping User entity`s properties From $data array
     * @param array $data array which contains User entity`s properties
     */
    public function exchangeArray($data) {

        $this->id       = ( !empty($data['id']) ) ? $data['id'] : null;
        $this->username = ( !empty($data['username']) ) ? $data['username'] : null;
        $this->name     = ( !empty($data['name']) ) ? $data['name'] : null;
        $this->token    = ( !empty($data['token']) ) ? $data['token'] : null;
        $this->role     = ( !empty($data['role']) ) ? $data['role']: "user";
    }

    /**
     * Get array Copy form User Model
     * @return array
     */
    public function getArrayCopy() {

        return get_object_vars($this);
    }

    /**
     * Set Input Filter For UserForm
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {

        throw new \Exception("Not Used");
    }

    /**
     * Get Input Filter For UserForm
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
                         'name'     => 'username',
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
                         'name'     => 'name',
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
                         'name'     => 'token',
                         'required' => false,
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

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

} 