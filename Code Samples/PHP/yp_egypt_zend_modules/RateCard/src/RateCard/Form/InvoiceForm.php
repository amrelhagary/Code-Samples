<?php

namespace RateCard\Form;

use Zend\Form\Form;

/**
 * InvoiceForm for Create and Edit InVoice Object
 * @package RateCard\Form
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */

class InvoiceForm extends Form {

    /**
     * InVoiceForm Constructor
     * @param string $name
     */
    public function __construct($name = null) {

        parent::__construct("Invoice");


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
                'name' => 'client_id',
                'type'   => 'hidden',
            )
        );

        $this->add(array(
                'name'   => 'total_amount',
                'type'   => 'Text',
                'option' => array(
                    'label' => 'Total Amount:'
                )
            )
        );

        $this->add(array(
                'name'   => 'comment',
                'type'   => 'Text',
                'option' => array(
                    'label' => 'Comment:'
                )
            )
        );

        $this->add(array(
                'name' => 'created_at',
                'type' => 'Text'
            )
        );

        $this->add(array(
                'name' => 'updated_at',
                'type' => 'Text'
            )
        );

    }

} 