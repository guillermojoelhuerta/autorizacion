<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
            
namespace AutorizarLicenciaturas\Controller;
        
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;               
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;   

//Incluir componentes de validación
use Zend\Validator;
use Zend\I18n\Validator as I18nValidator;                                                             
                
use AutorizarLicenciaturas\Model\Entity\AutorizarModel;
use AutorizarLicenciaturas\Form\AutorizarForm; 

                                                   
class AutorizarController extends AbstractActionController
{           
    private $model;

    protected function attachDefaultListeners()
    {       
        parent::attachDefaultListeners();

        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');   
        $this->model = new AutorizarModel($dbAdapter);                
    }                                                                                                  

    public function indexAction()
    {               
       return new ViewModel();                   
    }                                                                                                                   

    public function formAction()
    {                                                                   
        $form  = new AutorizarForm("Autorizacion");
        $getEntidades = $this->model->getEntidades();


        foreach ($getEntidades as $key => $value) {
            $entidades[$value["id"]] = $value["entidad"];        
        }                                                                 
                                                                
        $form->get('id_entidad')->setValueOptions(          
            $entidades                             
        );    

        $vista = array(
            "form" => $form,
            "url"  => $this->getRequest()->getBaseUrl()
        );                                                            
                                                                      
        if($this->getRequest()->isPost()) {
         
            //Repoblamos el formulario con los datos pasados por el usuario
            $form->setData($this->getRequest()->getPost());

            $vista = array(
                "form" => $form,
                "url"  => $this->getRequest()->getBaseUrl()
            ); 

            //print_r($this->getRequest()->getPost());      
                                                                                     
            //Si el formulario es valido que haga algo
            if($form->isValid()){
                                                                                       
                //print_r($form->getData());      
                $this->model->insertAutorizacion($form->getData());

            }else{

                echo print_r($form->getMessages()); //error messages
                //echo print_r($form->getErrors()); //error codes
                //echo print_r($form->getErrorMessages()); //any custom error messages                                                                                                           
            }     
        }                                                  

        return new ViewModel($vista);                                                           
    }       


    public function getEscuelasAction()
    {                                                                                                                  
        $parameters  = $this->request->getPost();                                
        $getEscuelas = $this->model->getEscuelas($parameters['id_entidad']);                               
        $result = new ViewModel(array('getEscuelas'=>$getEscuelas));  
        $result->setTerminal(true);                     
        return $result;
    }                                                              

    public function getLicenciaturasAction()
    {                                                                                                                                    
        $parameters = $this->request->getPost();                                
        $getLicenciaturas = $this->model->getLicenciaturas($parameters['id_escuela']);                                                         
        $result = new ViewModel(array('getLicenciaturas'=>$getLicenciaturas));  
        $result->setTerminal(true);                                       
        return $result;                             
    }                                   


    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /module-specific-root/skeleton/foo
        return new ViewModel();   
    }
}
