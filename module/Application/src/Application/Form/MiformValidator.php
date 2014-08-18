<?php

namespace Application\Form; 

use Zend\InputFilter\Factory as InputFactory; 
use Zend\InputFilter\InputFilter; 
use Zend\InputFilter\InputFilterAwareInterface; 
use Zend\InputFilter\InputFilterInterface; 

class MiformValidator implements InputFilterAwareInterface 
{               
    protected $inputFilter; 
                    
    public function setInputFilter(InputFilterInterface $inputFilter) 
    { 
        throw new \Exception("Not used"); 
    } 
            
    public function getInputFilter() 
    { 
        if (!$this->inputFilter) 
        { 
            $inputFilter = new InputFilter(); 
            $factory = new InputFactory(); 
            
        
        $inputFilter->add($factory->createInput([ 
            'name' => 'text',       
            'required' => 'required',                        
            'filters' => array( 
                array('name' => 'StripTags'), 
                array('name' => 'StringTrim'), 
            ), 
            'validators' => array( 
                array ( 
                    'name' => 'StringLength', 
                    'options' => array( 
                        'encoding' => 'UTF-8', 
                        'min' => '2', 
                        'max' => '6', 
                    ), 
                ), 
            ), 
        ])); 
 
        $inputFilter->add($factory->createInput([ 
            'name' => 'number', 
            'required' => false, 
            'filters' => array( 
                array('name' => 'StripTags'), 
                array('name' => 'StringTrim'), 
            ), 
            'validators' => array( 
                array ( 
                    'name' => 'digits', 
                ), 
 
            ), 
        ])); 
 
            $this->inputFilter = $inputFilter; 
        } 
        
        return $this->inputFilter; 
    } 
} 