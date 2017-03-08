<?php
namespace Career\Form;

use Zend\Form\Form;

class careerHeaderForm extends Form {
    public function __construct($name = null){
        parent::__construct('careerHeader');

        $this->setAttribute('method','post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden'
            )
        ));

        $this->add(array(
            'name' => 'headerEn',
            'attributes' => array(
                'type' => 'textarea'
            ),
            'options' => array(
                'label' => 'Name En'
            )
        ));

        $this->add(array(
            'name' => 'headerAr',
            'attributes' => array(
                'type' => 'textarea'
            ),
            'options' => array(
                'label' => 'Name Ar'
            )
        ));


        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'add',
                'class' => 'btn btn-default'
            )
        ));
    }
}
