<?php

namespace Application\Form; 

use Zend\Captcha; 
use Zend\Form\Element; 
use Zend\Form\Form; 
                
class  Miform extends Form 
{               
    public function __construct($name = null) 
    { 
        parent::__construct('application'); 
        
        $this->setAttribute('method', 'post'); 
        
        $this->add(array( 
            'name' => 'text', 
            'type' => 'Zend\Form\Element\Text', 
            'attributes' => array( 
                'placeholder' => 'Type something...', 
                'required' => 'required'                                       
            ),                      
            'options' => array( 
                'label' => 'Text', 
            ), 
        )); 
        
        $this->add(array( 
            'name' => 'number', 
            'type' => 'Zend\Form\Element\Text', 
            'attributes' => array( 
                'min' => '2', 
                'max' => '6', 
                'step' => '1', 
            ), 
            'options' => array( 
            ), 
        )); 
                            
        /*
        $this->add(array( 
            'name' => 'csrf', 
            'type' => 'Zend\Form\Element\Csrf', 
        ));  */ 

        $enviar = new Element\Submit('enviar'); 
        $enviar->setAttributes(array(
            "value"=>"Enviar"
            )                       
        );          

                                        
        $this->add(array(
            'name' => 'phone',
            'type' => 'Application\Form\Element\Phone',
            'attributes' => array(          
                'required' => 'required'                                       
            )                                                 
        ));  

        $this->add($enviar);         
    } 
} 