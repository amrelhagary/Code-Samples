<?php
namespace Authenticate\Form;

use Zend\Form\Form;

class LoginForm extends Form {
    public function __construct($name = null){
        parent::__construct('login');

        $this->setAttribute('method','post');

        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type' => 'text'
            ),
            'options' => array(
                'label' => 'Username'
            )
        ));

        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password'
            ),
            'options' => array(
                'label' => 'Password'
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Login',
                'class' => 'btn btn-default'
            )
        ));
    }
}
