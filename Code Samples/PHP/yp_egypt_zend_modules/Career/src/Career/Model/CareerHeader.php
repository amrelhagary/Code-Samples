<?php
namespace Career\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;


class CareerHeader implements InputFilterAwareInterface
{
    public $id;
    public $headerEn;
    public $headerAr;
    protected $inputFilter;

    public function exchangeArray($data){

        $this->id = (isset($data['id'])) ? $data['id']: null;
        $this->headerEn = (isset($data['headerEn']))? $data['headerEn'] : null;
        $this->headerAr = (isset($data['headerAr']))? $data['headerAr'] : null;
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
                'name' => 'headerEn',
                'required' => true,
                'filters' => array(
                    //array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 20
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'headerAr',
                'required' => true,
                'filters' => array(
                    //array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 20
                        )
                    )
                )
            )));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}