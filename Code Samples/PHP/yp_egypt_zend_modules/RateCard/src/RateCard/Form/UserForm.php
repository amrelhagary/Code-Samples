<?php

namespace RateCard\Form;

use Zend\Form\Form;

/**
 * UserForm for Create and Edit User Object
 * @package RateCard\Form
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class UserForm extends Form {

    /**
     * UserForm Constructor
     * @param string $name
     */
    public function __construct($name = null) {

        parent::__construct("User");


        $this->add(array(
                'name' => 'id',
                'type' => 'hidden'
            )
        );

        $this->add(array(
                'name'   => 'username',
                'type'   => 'Text',
                'option' => array(
                    'label' => 'Email:'
                )
            )
        );

        $this->add(array(
                'name'   => 'name',
                'type'   => 'Text',
                'option' => array(
                    'label' => 'User Name:'
                )
            )
        );

        $this->add(array(
                'name' => 'token',
                'type' => 'hidden'
            )
        );

    }

} 