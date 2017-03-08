<?php
namespace Career\Form;

use Zend\Form\Form;

class vacancyForm extends Form {
    public function __construct($name = null){
        parent::__construct('vacancy');

        $this->setAttribute('method','post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden'
            )
        ));

        $this->add(array(
            'name' => 'nameEn',
            'attributes' => array(
                'type' => 'text'
            ),
            'options' => array(
                'label' => 'Name En'
            )
        ));

        $this->add(array(
            'name' => 'nameAr',
            'attributes' => array(
                'type' => 'text'
            ),
            'options' => array(
                'label' => 'Name Ar'
            )
        ));

        $this->add(array(
            'name' => 'descriptionEn',
            'attributes' => array(
                'type' => 'textarea'
            ),
            'options' => array(
                'label' => 'Description En'
            )
        ));

        $this->add(array(
            'name' => 'descriptionAr',
            'attributes' => array(
                'type' => 'textarea'
            ),
            'options' => array(
                'label' => 'Description Ar'
            )
        ));

        $this->add(array(
            'name' => 'availableVacancies',
            'attributes' => array(
                'type' => 'text'
            ),
            'options' => array(
                'label' => 'Number of Vacancy'
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
