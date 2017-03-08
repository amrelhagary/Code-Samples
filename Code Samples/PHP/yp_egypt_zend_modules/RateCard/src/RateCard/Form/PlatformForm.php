<?php
namespace RateCard\Form;
use Zend\Form\Form;
use Zend\Form\Element;

class PlatformForm extends Form
{
    public function __construct($name = null){
        parent::__construct('platform');

        $this->setAttribute('method','post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden'
            )
        ));

        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'icon',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'iconActive',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'logo',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'bgColor',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'bgColorActive',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'parent',
            'attributes' => array(
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'order',
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