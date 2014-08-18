<?php 
namespace AutorizarLicenciaturas\Form;
                                       
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Factory;
                                
class UsuariosForm extends Form
{                                      
    public function __construct($name = null)
    {
        parent::__construct($name);
                                            
        //$this->setInputFilter(new \Modulo\Form\AddUsuarioValidator());
                                                  
        $this->add(array(
            'name' => 'login',
            'attributes' => array(
                'type' => 'text',       
                'class' => 'input form-control',
                'required'=>'required'
            )                                                            
        ));                                                                                 
                                                               
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'class' => 'input form-control',
                'required'=>'required'
            )                                
        ));                              
                                                                            
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(    
                'type' => 'submit',
                'value' => 'Entrar',
                'class' => 'btn btn-primary'
            ),
        ));             
    }       
}
