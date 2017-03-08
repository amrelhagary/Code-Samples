<?php

namespace RateCard\Form;

use Zend\Form\Form;

/**
 * ClientForm for Create and Edit Client Object
 * @package RateCard\Form
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class ClientForm extends Form {

    /**
     * ClientForm Constructor
     * @param string $name
     */
    public function __construct($name = null) {

        parent::__construct("Client");


        $this->add(array(
                'name' => 'id',
                'type' => 'hidden'
            )
        );

        $this->add(array(
                'name' => 'user_id',
                'type'   => 'hidden',
            )
        );

        $this->add(array(
                'name'   => 'account_name',
                'type'   => 'Text',
                'option' => array(
                    'label' => 'Account Name:'
                )
            )
        );

        $this->add(array(
                'name'   => 'contact_name',
                'type'   => 'Text',
                'option' => array(
                    'label' => 'Contact Name:'
                )
            )
        );

        $this->add(array(
                'name'   => 'mobile',
                'type'   => 'Text',
                'option' => array(
                    'label' => 'Mobile:'
                )
            )
        );

        $this->add(array(
                'name'   => 'email',
                'type'   => 'Email',
                'option' => array(
                    'label' => 'Email:'
                )
            )
        );

        $this->add(array(
                'name'   => 'meeting_objectives',
                'type'   => 'Text',
                'option' => array(
                    'label' => 'Meeting Objectives:'
                )
            )
        );

    }

} 