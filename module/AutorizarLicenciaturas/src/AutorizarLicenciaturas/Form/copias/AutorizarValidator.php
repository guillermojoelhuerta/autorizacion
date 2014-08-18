<?php

namespace AutorizarLicenciaturas\Form;
 		
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\I18n\Validator as I18nValidator;

class AutorizarValidator extends InputFilter{
 																	
 	public function __construct(){


        
 		$this->add(array(				
            'name' => 'periodo',
            'required' => true,
            'filters' => array(
//Cuidado con StripTags y HtmlEntities puede ser que no nos validen texto con tildes o eñes
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array (
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => '5',
                        'max' => '15',
                        'messages' => array(
                        \Zend\Validator\StringLength::INVALID=>'Tu nombre esta mal',
                        \Zend\Validator\StringLength::TOO_SHORT=>'Tu nombre debe ser de más de 5 letras',
                        \Zend\Validator\StringLength::TOO_LONG=>'Tu nombre debe ser de menos de 15 letras',
                        ),
                    ),
                ),
                 array(
                    'name' => 'Alpha',
                     'options' => array(
                        'messages' => array(
                            I18nValidator\Alpha::INVALID=>'Tu nombre solo puede estar formado por letras',
                            I18nValidator\Alpha::NOT_ALPHA=>'Tu nombre solo puede estar formado por letras',
                            I18nValidator\Alpha::STRING_EMPTY=>'Tu nombre no puede estar vacio',
                            //I18nValidator\Alpha::NOT_ALNUM=>'Tu nombre esta mal',
                        ),
                     ),
                 ),    
        )));				

 	}  


}   