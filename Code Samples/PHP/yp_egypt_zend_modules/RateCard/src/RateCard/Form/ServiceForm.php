<?php
namespace RateCard\Form;

use Zend\Form\Form;

class ServiceForm extends Form {

    public function __construct($name = null) {

        parent::__construct('platform');

        $this->setAttribute('method','post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden'
            )
        ));

        $this->add(array(
            'name' => 'platform_id',
            'attributes' => array(
                'type' => 'hidden'
            )
        ));

        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type' => 'text'
            ),
            'options' => array(
                'label' => 'Title:'
            )
        ));

        $this->add(array(
            'name' => 'media',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'content',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'script',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'packages_table',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'service_order',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'status',
            'attributes' => array(
                'type' => 'text'
            )
        ));


    }
}