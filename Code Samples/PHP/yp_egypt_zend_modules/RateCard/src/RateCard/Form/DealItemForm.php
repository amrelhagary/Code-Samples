<?php

namespace RateCard\Form;

use Zend\Form\Form;

/**
 * DealItemForm for Create and Edit DealItem Object
 * @package RateCard\Form
 * @author ahmed hamdy <a.abdelhak@yellow.com.eg>
 */
class DealItemForm extends Form {

    /**
     * DealItemForm Constructor
     * @param string $name
     */
    public function __construct($name = null) {

        parent::__construct("DealItem");


        $this->add(array(
                'name' => 'id',
                'type' => 'hidden'
            )
        );

        $this->add(array(
                'name' => 'platform_id',
                'type'   => 'hidden',
            )
        );

        $this->add(array(
                'name' => 'service_id',
                'type'   => 'hidden',
            )
        );

        $this->add(array(
                'name' => 'invoice_id',
                'type'   => 'hidden',
            )
        );

        $this->add(array(
                'name'   => 'amount',
                'type'   => 'Text',
                'option' => array(
                    'label' => 'Deal Amount:'
                )
            )
        );

        $this->add(array(
                'name'   => 'package_name',
                'type'   => 'Text',
                'option' => array(
                    'label' => 'Package Name For Deal:'
                )
            )
        );

        $this->add(array(
                'name'   => 'quantity',
                'type'   => 'Text'
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