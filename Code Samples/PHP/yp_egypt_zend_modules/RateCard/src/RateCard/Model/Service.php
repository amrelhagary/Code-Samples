<?php
namespace RateCard\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;

class Service implements InputFilterAwareInterface {

    public $id;
    public $platform_id;
    public $title;
    public $media;
    public $content;
    public $script;
    public $packages_table;
    public $service_order;
    public $status;

    protected $inputFilter;

    public function exchangeArray($data) {

        $this->id             = (isset($data['id'])) ? $data['id'] : null;
        $this->platform_id    = (isset($data['platform_id'])) ? $data['platform_id'] : null;
        $this->title          = (isset($data['title'])) ? $data['title'] : null;
        $this->media          = (isset($data['media'])) ? $data['media'] : null;
        $this->content        = (isset($data['content'])) ? $data['content'] : null;
        $this->script         = (isset($data['script'])) ? $data['script'] : null;
        $this->packages_table = (isset($data['packages_table'])) ? $data['packages_table'] : null;
        $this->service_order  = (isset($data['service_order'])) ? $data['service_order'] : 0;
        $this->status         = (isset($data['status'])) ? $data['status'] : StatusModel::$INACTIVE;
    }

    public function getArrayCopy() {

        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {

    }

    public function getInputFilter() {

        if (!$this->inputFilter) {

            $inputFilter  = new InputFilter();
            $factory      = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int')
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'platform_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int')
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'title',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'media',
                'required' => true
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'content',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'Null')
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'script',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'Null')
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'packages_table',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'Null')
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'service_order',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int')
                )
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}