<?php

namespace AutorizarLicenciaturas\Form;
                																	
use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Captcha;
use Zend\Form\Factory;	
use Zend\Filter;	
	            				
//Incluir componentes de validación                      						
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\I18n\Validator as I18nValidator;											
	                       					
class AutorizarForm extends Form{
                
	public function __construct($name){
			             														 		
		parent::__construct($name);

		$this->setInputFilter(new \AutorizarLicenciaturas\Form\AutorizarValidator());

        $this->add(array(        
            'name' => 'id',              
            'type' => 'Zend\Form\Element\Hidden', 
            'attributes' => array(      
                'id'=>'id',        
            )                                                        
        ));                                                                             
            
		$this->add(array( 
            'name' => 'id_entidad', 
            'type' => 'Zend\Form\Element\Select', 
            'attributes' => array( 
            	'id'=>'id_entidad',
                'required' => 'required',
                'class'=>'form-control input-sm'                                      
            ),                                       
            'options' => array( 
                'label' => 'Entidad:',	                      	            	                                                                                   
                'empty_option' => 'Selecccione:',
                'disable_inarray_validator' => true
            )       									      
        )); 	                                   		                 												

		$this->add(array(                     
            'name' => 'id_escuela', 
            'type' => 'Zend\Form\Element\Select', 
            'attributes' => array( 
            	'id'=>'id_escuela',
                'required' => 'required',               
                'disabled'=>'disabled',                
                'class'=>'form-control input-sm'                                        
            ),   			   				              				  
            'options' => array( 				
                'label' => 'Escuela:',			 
                'empty_option' => 'Selecccione:',
                'disable_inarray_validator' => true
            ) 																			
        )); 				                                						

        $this->add(array( 						
            'name' => 'id_licenciatura', 
            'type' => 'Zend\Form\Element\Select', 
            'attributes' => array( 
            	'id'=>'id_licenciatura',
                'required' => 'required',
                'disabled'=>'disabled',   
                'class'=>'form-control input-sm'                                       
            ),                    						  
            'options' => array( 				
                'label' => 'Licenciatura:',			 
                'empty_option' => 'Selecccione:',
                'disable_inarray_validator' => true
            )									
        ));              

        $this->add(array(                                         
            'name' => 'id_especialidad', 
            'type' => 'Zend\Form\Element\Select', 
            'attributes' => array(      
                'id'=>'id_especialidad',
                'required' => 'required',       
                'disabled'=>'disabled',   
                'class'=>'form-control input-sm'                                    
            ),                                                                    
            'options' => array(                 
                'label' => 'Especialidad:',          
                'empty_option' => 'Selecccione:',
                'disable_inarray_validator' => true
            )                                 
        )); 


        $this->add(array(        						
            'name' => 'autorizar', 			
            'type' => 'Zend\Form\Element\Checkbox', 
            'attributes' => array( 
            	'id'=>'autorizar',     
                'class'=>'checkbox' 

            ),                       		        			       						  
            'options' => array( 				
                'label' => 'Autorizar:',			 
                'checked_value' => '1',
                'unchecked_value' => '0'
            ) 																
        )); 						      	             		

        $this->add(array(                       
            'name' => 'periodo1', 
            'type' => 'Zend\Form\Element\Select', 
            'attributes' => array( 
                'id'=>'periodo1',         
                'required' => 'required',
                'class'=>'form-control input-sm'                                               
            ),                                            
            'options' => array(                                    
                'label' => 'Periodo:',          
                'empty_option' => 'Selecccione:',
                'disable_inarray_validator' => true
            )                                  
        )); 

        $this->add(array(                       
            'name' => 'periodo2', 
            'type' => 'Zend\Form\Element\Select', 
            'attributes' => array( 
                'id'=>'periodo2',                                        
                'disabled'=>'disabled',   
                'required' => 'required',  
                'class'=>'form-control input-sm'                                                 
            ),                                                                          
            'options' => array(                         
                'empty_option' => 'Selecccione:',
                'disable_inarray_validator' => true
            )                                  
        )); 								


   		$this->add(array( 
            'name' => 'oficio', 
            'type' => 'Zend\Form\Element\Text', 
            'attributes' => array( 
                'required' => 'required',
                'class'=>'form-control input-sm'                                       
            ),                      
            'options' => array( 											
                'label' => 'Oficio:', 
                 'label_attributes' => array(
                    'class' => 'control-label',
                ),                                                  
            ) 
        ));		

        $this->add(array( 
            'name' => 'buscar',                 
            'type' => 'Zend\Form\Element\Text', 
            'attributes' => array( 
                'required' => 'required', 
                'placeholder' => 'Buscar',  
                'id'=>'buscar',
                'class'=>'form-control input-sm'                                        
            ),                                                                                                                                                                                 
            'options' => array(                                             
                'label' => 'Buscar:', 
            )               
        ));                                                     													 				

   		$this->add(array(              
            'name' => 'enviar', 
            'type' => 'Zend\Form\Element\Submit', 
            'attributes' => array( 
                'value' => 'Enviar',
                'class' => 'btn btn-primary center-block'                                       
            )          					            
        ));			
	
	}
}