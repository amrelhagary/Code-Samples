<?php
namespace RateCard\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;

class Platform implements InputFilterAwareInterface
{
    public $id;
    public $title;
    public $icon;
    public $iconActive;
    public $logo;
    public $bgColor;
    public $bgColorActive;
    public $parent;
    public $lang;
    public $order;
    public $status;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->title = (isset($data['title'])) ? $data['title'] : null;
        $this->icon = (isset($data['icon'])) ? $data['icon'] : null;
        $this->iconActive = (isset($data['iconActive'])) ? $data['iconActive'] : null;
        $this->logo = (isset($data['logo'])) ? $data['logo'] : null;
        $this->bgColor = (isset($data['bgColor'])) ? $data['bgColor'] : null;
        $this->bgColorActive = (isset($data['bgColorActive'])) ? $data['bgColorActive'] : null;
        $this->parent = (isset($data['parent'])) ? $data['parent'] : 0;
        $this->order = (isset($data['order'])) ? $data['order'] : 0;
        $this->status = (isset($data['status'])) ? $data['status'] : StatusModel::$INACTIVE;
        $this->lang = 'en';
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter){}

    public function getInputFilter(){
        if(!$this->inputFilter){
            $inputFilter  = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int')
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'title',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'message' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Title is Required'
                            )
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'icon',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'Null')
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'message' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Icon is Required'
                            )
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'iconActive',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'Null')
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'message' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Image is Required'
                            )
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'logo',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'Null')
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'message' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Logo is Required'
                            )
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'bgColor',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'Null')
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'message' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Background Color is Required'
                            )
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'bgColorActive',
                'required' => false,
                'filters' => array(
                   array('name' => 'StripTags'),
                   array('name' => 'StringTrim'),
                   array('name' => 'Null')
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'message' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Active Background Color is Required'
                            )
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'parent',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'message' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Parent is Required'
                            )
                        )
                    )
                )
            )));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}