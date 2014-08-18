<?php

namespace AutorizarLicenciaturas\Form;
																	
use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Captcha;
use Zend\Form\Factory;	
use Zend\Filter;	
					
//Incluir componentes de validación
/*
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\I18n\Validator as I18nValidator;		
*/
						
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\I18n\Validator as I18nValidator;											
						
class AutorizarForm extends Form{

	public function __construct($name){
																	 		
		parent::__construct($name);

		$this->setInputFilter(new \AutorizarLicenciaturas\Form\AutorizarValidator());

		$entidad = new Element\Select('id_entidad');
		$entidad->setLabel('Entidad:');			
		$entidad->setEmptyOption('Seleccione');
		$entidad->setAttributes(array(				
			'id'=>'id_entidad',
			'required'=>'required'									
			)
		);																								
																	
		$this->add($entidad);

		$escuela = new Element\Select('id_escuela');
		$escuela->setLabel('Escuela:');			
		$escuela->setEmptyOption('Seleccione');
		$escuela->setDisableInArrayValidator(true);
		$escuela->setAttributes(array(				
			'id'=>'id_escuela'		
			)			
		);											
														
					
		$this->add($escuela);	

		$licenciatura = new Element\Select('id_licenciatura');
		$licenciatura->setLabel('Licenciatura:');			
		$licenciatura->setEmptyOption('Seleccione');
		$licenciatura->setDisableInArrayValidator(true);				
		$licenciatura->setAttributes(array(
			'id'=>'id_licenciatura',
			'required'=>false	
			)										
		);						

							
		$this->add($licenciatura);


		$autorizar = new Element\Checkbox('autorizar');
        $autorizar->setLabel('Autorizar:');
        $autorizar->setCheckedValue("1");
		$autorizar->setUncheckedValue("0");
        $this->add($autorizar);													

        $periodo = new Element\Text('periodo');
        $periodo->setLabel('Periodo:');
        $periodo->setAttributes(array(
    			'required' => 'required',
    			'size'	   => 10						
    		)																
        );																												
       																									
        $this->add($periodo);	
   											

        $oficio = new Element\Text('oficio');
        $oficio->setLabel('Oficio:');
        $oficio->setAttributes(array(
    			'required'=>'required'
    		)						
        );

        $this->add($oficio);	

		$enviar = new Element\Submit('enviar');	
		$enviar->setAttributes(array(
			"value"=>"Enviar"
			)						
		);																	
        $this->add($enviar);			
	}
}