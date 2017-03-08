<?php
namespace Career\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;


class Vacancy implements InputFilterAwareInterface
{
    public $id;
    public $nameEn;
    public $nameAr;
    public $descriptionEn;
    public $descriptionAr;
    public $subFolder;
    public $availableVacancies;
    protected $inputFilter;

    public function exchangeArray($data){

        $this->id = (isset($data['id'])) ? $data['id']: null;
        $this->nameEn = (isset($data['nameEn']))? $data['nameEn'] : null;
        $this->nameAr = (isset($data['nameAr'])) ? $data['nameAr'] : null;
        $this->descriptionEn = (isset($data['descriptionEn'])) ? $data['descriptionEn'] : null;
        $this->descriptionAr = (isset($data['descriptionAr'])) ? $data['descriptionAr'] : null;
        $this->subFolder = (isset($data['subFolder'])) ? $data['subFolder'] : null;
        $this->availableVacancies = (isset($data['availableVacancies'])) ? $data['availableVacancies'] : null;
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
                'name' => 'nameEn',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'nameAr',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'descriptionEn',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 10,
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'descriptionAr',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 10
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'availableVacancies',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int')
                ),
                'validators' => array(
                    array(
                        'name' => 'Between',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100
                        )
                    )
                )
            )));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}