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
use Zend\View\Model\JsonModel;                                
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;   
                                                                    
//Incluir componentes de validación
use Zend\Validator;
use Zend\I18n\Validator as I18nValidator;    

//Componentes de autenticación      
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Session\Container;                                                              
                                            
        
use AutorizarLicenciaturas\Model\Entity\AutorizarModel;
use AutorizarLicenciaturas\Form\AutorizarForm; 

                                                                                              
class AutorizarController extends AbstractActionController
{                          
    private $model;
    private $auth;      
    private $autorizacionesPorPagina  = 6;               

    public function __construct()
    { 
        $this->auth = new AuthenticationService();                                                                
    }                                                                            

    protected function attachDefaultListeners()
    {       
        parent::attachDefaultListeners();

        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');   
        $this->model = new AutorizarModel($dbAdapter);                
    }                                                                                                                                           

    public function indexAction()
    {                                       
        $identi = $this->auth->getStorage()->read();
        if($identi==false && $identi==null)
        {           
            $this->auth->clearIdentity();       
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/usuarios/login');
        }                                                    
                                                                                                                                                                                                                 
        $form  = new AutorizarForm("Autorizacion");
        $form->setAttributes(array(              
                'action' => $this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/Autorizar/buscar',
                'method' => 'post'
        ));                                          

        $entidades = $this->model->getEntidades();  
                                                                                                                                                                                                                              
        $form->get('id_entidad')->setValueOptions(          
            $entidades                             
        );                       

        $view = array(
            'flashMessages' => $this->flashMessenger()->getMessages(),
            'form' => $form,
            'url'  => $this->getRequest()->getBaseUrl()
        );                                                            
                                                                                 
        return new ViewModel($view);    
    }             


    public function formAction()
    {       
        $identi = $this->auth->getStorage()->read(); 
        if($identi==false && $identi==null)
        {       
            $this->auth->clearIdentity();       
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/usuarios/login');
        }                                                                          
                                      
        $form  = new AutorizarForm("Autorizacion");
        $form->setAttributes(array(         
                'action' => $this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/Autorizar/form',
                'method' => 'post',
                'role'   => 'form',
                'class'=>'form-horizontal'
        ));                                                                                            

        $entidades = $this->model->getEntidades();                                                          
        $form->get('id_entidad')->setValueOptions(          
            $entidades                             
        );                        

        $periodos = $this->model->getPeriodos();                                                                                                                                                  
        $form->get('periodo1')->setValueOptions(          
            $periodos                             
        );                  

                                                                       
        if($this->getRequest()->isPost()) {
         
            $data = $this->getRequest()->getPost();
            //Repoblamos el formulario con los datos pasados por el usuario
            $form->setData($data);  
                                        
            //Si el formulario es valido que haga algo      
            if($form->isValid()){ 

                $this->flashMessenger()->addMessage('<div class="msj-true">Autorización agregada correctamente.</div>');                                                                                
                if($id = $this->model->insertAutorizacion($form->getData())){
                    $this->redirect()->toUrl(           
                        $this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/Autorizar/updateForm/'.$id
                    );                                                       
                }                                                                                                        

            }else{                                             

                if($data['id_entidad']){
                                                
                    $escuelas = $this->model->getEscuelas($data['id_entidad']); 
                                            
                    if(!empty($escuelas)){ 
                                                                                                                                                                                                             
                        $form->get('id_escuela')->setValueOptions(          
                            $escuelas                             
                        );  

                        $form->get('id_escuela')->setAttributes(
                            array("disabled"=>false)
                        );                           
                    }  
                }                                                                                  

                if($data['id_escuela']){

                    $licenciaturas = $this->model->getLicenciaturas($data['id_escuela']); 
                                                                
                    if(!empty($licenciaturas)){                                                                                                                                                       
                                                                                                                                    
                        $form->get('id_licenciatura')->setValueOptions(          
                            $licenciaturas                             
                        );    
                        $form->get('id_licenciatura')->setAttributes(
                            array("disabled"=>false)
                        );                                         
                    }                                                     
                }                                   

                if($data['id_licenciatura']){

                    $especialidad = $this->model->getEspecialidad($data['id_licenciatura']); 
                                                                  
                    if(!empty($especialidad)){                                                                                                                                                           
                                                                                                                                         
                        $form->get('id_especialidad')->setValueOptions(          
                            $especialidad                                          
                        );  
                        $form->get('id_especialidad')->setAttributes(
                            array("disabled"=>false)
                        );                  
                    }                                                                                      
                }                     

                if($data['periodo1']){

                    $periodo = $this->model->periodo($data['periodo1']);                                  

                    $getPeriodosMayor = $this->model->getPeriodosMayor($periodo[0]["periodo"]); 
                                                                      
                    if(!empty($getPeriodosMayor)){

                        foreach ($getPeriodosMayor as $key => $value) {
                            $periodosMayor[$value["id"]] = $value["periodo"];        
                        }                                                                                                                                                                                             
                                                                                                                                                 
                        $form->get('periodo2')->setValueOptions(          
                            $periodosMayor                                          
                        );                  
                    }                                                
                    
                    $form->get('periodo2')->setAttributes(
                        array("disabled"=>false)
                    );                                        
                }                                                                        

                //echo print_r($form->getMessages());                                              
                                                                                                                                                         
            }     
        }        


        $vista = array(
            "form" => $form,
            "url"  => $this->getRequest()->getBaseUrl(),
            'flashMessages' => $this->flashMessenger()->getMessages()
        );                                                                            
                                                                         

        return new ViewModel($vista);                                                           
    }                                  


    public function updateFormAction()
    {          
        $identi = $this->auth->getStorage()->read();
        if($identi==false && $identi==null)
        {           
            $this->auth->clearIdentity();       
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/usuarios/login');
        }                       

        $form  = new AutorizarForm("Autorizacion");             
                                                                                             
        if($this->getRequest()->isPost()){      

            $data = $this->getRequest()->getPost();     
            $form->setData($data);

            if($form->isValid()){       
                                                                                                
                $this->flashMessenger()->addMessage('<div class="msj-true">Autorización modificada correctamente.</div>');                                                                                
                if($this->model->updateAutorizacion($form->getData()))
                {                                     
                    $this->redirect()->toUrl(                                                               
                        $this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/Autorizar/updateForm/'.$data["id"]
                    );                                                                          
                }                                                                                                                   

            }else{                                      

                //echo print_r($form->getMessages()); 

            }                                                                                          

        }else{       

                $id = (int)$this->params()->fromRoute('id', 0);

                if(!$id){                    

                    $this->redirect()->toUrl(                                                               
                        $this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/Autorizar/form'
                    );                                                                                    
                }                                                    

                $autorizacion = $this->model->getAutorizacionUpdate($id);  

                $form->setAttributes(array(
                    'action' => $this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/Autorizar/updateForm',
                    'method' => 'post'
                ));                                                                                                                                                  
                                                                                         
                $values = array(        
                    "id"        => $autorizacion["id"],                   
                    "autorizar" => $autorizacion["autorizar"],
                    "oficio"    => $autorizacion["oficio"]
                );                                        
                                                                                     
                $form->setData($values);      
                
                $form->get('id_entidad')->setEmptyOption(null)
                                        ->setValueOptions(
                    array(                                                      
                        $autorizacion["id_entidad"] => $autorizacion["entidad"]
                    )                                                                                
                );              
                //->setValue($getAutorizacion[0]["id_entidad"]);        

                $form->get('id_escuela')->setEmptyOption(null)
                                        ->setAttributes(array("disabled"=>false))
                                        ->setValueOptions(
                    array(                                          
                        $autorizacion["id_escuela"] => $autorizacion["escuela"]
                    )                                                                                                                           
                ); 

                $form->get('id_licenciatura')->setEmptyOption(null)
                                        ->setAttributes(array("disabled"=>false))
                                        ->setValueOptions(
                    array(                                          
                        $autorizacion["id_licenciatura"] => $autorizacion["licenciatura"]
                    )                                                                                                                                         
                );           

                $form->get('id_especialidad')->setEmptyOption(null)
                                        ->setAttributes(array("disabled"=>false))
                                        ->setValueOptions(
                    array(                                                 
                        $autorizacion["id_especialidad"] => $autorizacion["especialidad"]
                    )                                                                                                                                                              
                );              

                $form->get('periodo1')->setEmptyOption(null)
                                    ->setAttributes(array("disabled"=>false))
                                    ->setValueOptions(
                    array(                                                         
                        $autorizacion["periodo1"] => $autorizacion["periodo_year"]
                    )                                                                                                                                                      
                );                     

                $form->get('periodo2')->setEmptyOption(null)
                                    ->setAttributes(array("disabled"=>false))
                                    ->setValueOptions(      
                    array(                                                                           
                        $autorizacion["periodo2"] => $autorizacion["periodo2_year"]
                    )                                                                                                                                                                                                                              
                );              
        }                                                                 
                                                                                                                                                             
        $view = new ViewModel(array(
            'form'          => $form,
            'flashMessages' => $this->flashMessenger()->getMessages()
        ));
        $view->setTemplate('autorizar-licenciaturas/autorizar/form.phtml');  

        return $view;                                                                                                  
    }                                                  

    private function paginasTotales($autorizacionesTotales)
    {                                                                                                                                            
        return ceil($autorizacionesTotales/$this->autorizacionesPorPagina);         
    }                                                                     

    private function offset($pagina)
    {
        return ($pagina-1)*$this->autorizacionesPorPagina;  
    }

    private function enlacesPaginacion($pagina, $paginas_totales)
    {                                                          
        if($pagina < $paginas_totales){   
            $enlaces['class_sig'] = 'mostrar-sig';
            $enlaces['siguiente_pag'] = $pagina+1;                                
        }else{                               
            $enlaces['class_sig'] = 'ocultar-sig';
            $enlaces['siguiente_pag'] = '#';  
        }                                                                                                                                  
                                             
        if($pagina>1){   
            $enlaces['class_ant'] = 'mostrar-ant';
            $enlaces['anterior_pag'] = $pagina-1;                                
        }else{                                                                     
            $enlaces['class_ant'] = 'ocultar-ant';
            $enlaces['anterior_pag'] = '#';  
        }                                     
        return $enlaces;                    
    }                                                                                

    public function getFiltrosEntidadAction()
    {                                                                                                                                                                                                                                                      
        $parameters  = $this->request->getPost();                                
        $escuelas    = $this->model->getEscuelasV1($parameters['id_entidad']); 
                                                                                                                                                                                                               
        $escuelasView = new ViewModel();        
        $escuelasView->setTerminal(true)                                                                        
               ->setTemplate('autorizar-licenciaturas/autorizar/get-escuelas.phtml')                                                                           
               ->setVariables(array(                                                               
                  'getEscuelas' => $escuelas
        ));                                                                        
                                     
        $escuelasView = $this->getServiceLocator()
                  ->get('viewrenderer')
                  ->render($escuelasView);                                                              

        $where["autorizacion_licenciaturas.id_entidad"] = $parameters['id_entidad'];                            
        $autorizaciones_totales = $this->model->countAutorizacionesV2($where);                      
                                                                         
        $pagina = ($parameters['page'])?$parameters['page']:1;           
        $paginas_totales = $this->paginasTotales($autorizaciones_totales); 
        $offset = $this->offset($pagina);                             

        $autorizaciones = $this->model->getAutorizacionEntidad($where,$this->autorizacionesPorPagina,$offset);                                                                                                                                    
                                                                                     

        $autorizacionesView = new ViewModel();        
        $autorizacionesView->setTerminal(true)                                                                        
               ->setTemplate('autorizar-licenciaturas/autorizar/autorizacionesV2.phtml')                                                                           
               ->setVariables(array(                                                               
                  'url'  => $this->getRequest()->getBaseUrl(),                                                                     
                  'autorizaciones'        => $autorizaciones,                     
                  'enlaces'               => $this->enlacesPaginacion($pagina,$paginas_totales),    
                  'pagina'                => $pagina,                                                        
                  'offset'                => $offset,           
                  'autorizaciones_totales'=> $autorizaciones_totales,     
                  'paginas_totales'       => $paginas_totales    
        ));                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                               
        $autorizacionesView = $this->getServiceLocator()
                  ->get('viewrenderer')
                  ->render($autorizacionesView);                                                 
                

        $jsonModel = new JsonModel();           
        $jsonModel->setVariables(array(                                                          
            'filtro_autorizaciones'=>array(
                    'html' => $escuelasView
                ),                                                                          
            'autorizaciones' =>array(
                    'html' => $autorizacionesView
                )                                                                                                   
            )                                                       
        );    

        return $jsonModel;              
    }                                                                

    public function getFiltrosEscuelaAction()
    {                                                                                                                                                                                                                                
        $parameters  = $this->request->getPost();                                
        $getLicenciaturas = $this->model->getLicenciaturasV1($parameters['id_escuela']);                

        $escuelasView = new ViewModel();        
        $escuelasView->setTerminal(true)                                                                        
               ->setTemplate('autorizar-licenciaturas/autorizar/get-licenciaturas.phtml')                                                                           
               ->setVariables(array(                                                               
                  'getLicenciaturas' => $getLicenciaturas
        ));                                                             
                                                                                                                                                                                                                               
        $escuelasView = $this->getServiceLocator()
                ->get('viewrenderer')
                ->render($escuelasView);                                                              


        $where["autorizacion_licenciaturas.id_entidad"]      = $parameters['id_entidad'];         
        $where["autorizacion_licenciaturas.id_escuela"]      = $parameters['id_escuela']; 

        $autorizaciones_totales = $this->model->countAutorizacionesV2($where);                      
                                                                                       
        $pagina = ($parameters['page'])?$parameters['page']:1;           
        $paginas_totales = $this->paginasTotales($autorizaciones_totales); 
        $offset = $this->offset($pagina);                             

        $autorizaciones = $this->model->getAutorizacionEntidad($where,$this->autorizacionesPorPagina,$offset);                                                                                                                                 

                                 
        $autorizacionesView = new ViewModel();        
        $autorizacionesView->setTerminal(true)                                                                        
               ->setTemplate('autorizar-licenciaturas/autorizar/autorizacionesV2.phtml')                                                                           
               ->setVariables(array(                                                               
                  'url'  => $this->getRequest()->getBaseUrl(),                                                                     
                  'autorizaciones'        => $autorizaciones,                     
                  'enlaces'               => $this->enlacesPaginacion($pagina,$paginas_totales),    
                  'pagina'                => $pagina,                                                        
                  'offset'                => $offset,           
                  'autorizaciones_totales'=> $autorizaciones_totales,     
                  'paginas_totales'       => $paginas_totales    
        ));                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                               
        $autorizacionesView = $this->getServiceLocator()
                  ->get('viewrenderer')
                  ->render($autorizacionesView);                                                 
                

        $jsonModel = new JsonModel();           
        $jsonModel->setVariables(array(                                                          
            'filtro_autorizaciones'=>array(
                    'html' => $escuelasView
                ),                                                                          
            'autorizaciones' =>array(
                    'html' => $autorizacionesView
                )                                                                                                   
            )                                                       
        );          


        return $jsonModel;              
    }   

    public function getFiltrosLicenciaturaAction()
    {                                                                                                                                                                                                                           
        $parameters  = $this->request->getPost();                                       
        $getEspecialidad = $this->model->getEspecialidadV1($parameters['id_licenciatura']);                

        $escuelasView = new ViewModel();        
        $escuelasView->setTerminal(true)                                                                                       
               ->setTemplate('autorizar-licenciaturas/autorizar/get-especialidad.phtml')                                                                           
               ->setVariables(array(                                                                     
                  'getEspecialidad' => $getEspecialidad
        ));                                                                                     
                                                                                                                                                                                                                                                             
        $escuelasView = $this->getServiceLocator()
                  ->get('viewrenderer')
                  ->render($escuelasView);                                      

        $where["autorizacion_licenciaturas.id_entidad"]      = $parameters['id_entidad'];         
        $where["autorizacion_licenciaturas.id_escuela"]      = $parameters['id_escuela'];                                    
        $where["autorizacion_licenciaturas.id_licenciatura"] = $parameters['id_licenciatura'];   

        $autorizaciones_totales = $this->model->countAutorizacionesV2($where);                      
                                                                                       
        $pagina = ($parameters['page'])?$parameters['page']:1;           
        $paginas_totales = $this->paginasTotales($autorizaciones_totales); 
        $offset = $this->offset($pagina);                             

        $autorizaciones = $this->model->getAutorizacionEntidad($where,$this->autorizacionesPorPagina,$offset);                                                                                                                              
                  

        $autorizacionesView = new ViewModel();        
        $autorizacionesView->setTerminal(true)                                                                        
               ->setTemplate('autorizar-licenciaturas/autorizar/autorizacionesV2.phtml')                                                                           
               ->setVariables(array(                                                               
                  'url'  => $this->getRequest()->getBaseUrl(),                                                                     
                  'autorizaciones'        => $autorizaciones,                     
                  'enlaces'               => $this->enlacesPaginacion($pagina,$paginas_totales),    
                  'pagina'                => $pagina,                                                        
                  'offset'                => $offset,           
                  'autorizaciones_totales'=> $autorizaciones_totales,     
                  'paginas_totales'       => $paginas_totales    
        ));                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                               
        $autorizacionesView = $this->getServiceLocator()
                  ->get('viewrenderer')
                  ->render($autorizacionesView);                                                 
                

        $jsonModel = new JsonModel();           
        $jsonModel->setVariables(array(                                                          
            'filtro_autorizaciones'=>array(
                    'html' => $escuelasView
                ),                                                                          
            'autorizaciones' =>array(
                    'html' => $autorizacionesView
                )                                                                                                   
            )                                                       
        );          
                                
        return $jsonModel;              
    }   

    public function getFiltrosEspecialidadAction()
    {                     
        $parameters  = $this->request->getPost();                                
        $getEspecialidad = $this->model->getEspecialidadV1($parameters['id_especialidad']);                
                            
                                                                                                               
        $where["autorizacion_licenciaturas.id_entidad"]      = $parameters['id_entidad'];         
        $where["autorizacion_licenciaturas.id_escuela"]      = $parameters['id_escuela'];                                    
        $where["autorizacion_licenciaturas.id_licenciatura"] = $parameters['id_licenciatura']; 
        $where["autorizacion_licenciaturas.id_especialidad"] = $parameters['id_especialidad'];                              

        $autorizaciones_totales = $this->model->countAutorizacionesV2($where);                      
                                                                                       
        $pagina = ($parameters['page'])?$parameters['page']:1;           
        $paginas_totales = $this->paginasTotales($autorizaciones_totales); 
        $offset = $this->offset($pagina);                             

        $autorizaciones = $this->model->getAutorizacionEntidad($where,$this->autorizacionesPorPagina,$offset);   

        
        $autorizacionesView = new ViewModel();        
        $autorizacionesView->setTerminal(true)                                                                        
               ->setTemplate('autorizar-licenciaturas/autorizar/autorizacionesV2.phtml')                                                                           
               ->setVariables(array(                                                               
                  'url'  => $this->getRequest()->getBaseUrl(),                                                                     
                  'autorizaciones'        => $autorizaciones,                     
                  'enlaces'               => $this->enlacesPaginacion($pagina,$paginas_totales),    
                  'pagina'                => $pagina,                                                        
                  'offset'                => $offset,           
                  'autorizaciones_totales'=> $autorizaciones_totales,     
                  'paginas_totales'       => $paginas_totales    
        ));                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                               
        $autorizacionesView = $this->getServiceLocator()
                  ->get('viewrenderer')
                  ->render($autorizacionesView);                                                 
                         

        $jsonModel = new JsonModel();           
        $jsonModel->setVariables(array(                                                                                                                                       
            'autorizaciones' =>array(
                    'html' => $autorizacionesView
                )                                                                                                   
            )                                                                     
        );                               

        return $jsonModel;              
    } 

    public function buscarAutorizacionAction()
    {                                       
        $parameters  = $this->request->getPost();                                
                                                                       
        $autorizaciones_totales = $this->model->countAutorizacionesLike($parameters['buscar']);           
                                                                                                                                                                                                                                                                                                                                                                      
        $pagina = ($parameters['page'])?$parameters['page']:1;           
        $paginas_totales = $this->paginasTotales($autorizaciones_totales['count']); 
        $offset = $this->offset($pagina);                             
               
        $autorizaciones = $this->model->getAutorizacionLike($parameters['buscar'],$this->autorizacionesPorPagina,$offset);                                                                                                                                    
                                                     

        $autorizacionesView = new ViewModel();        
        $autorizacionesView->setTerminal(true)                                                                        
               ->setTemplate('autorizar-licenciaturas/autorizar/autorizacionesV2.phtml')                                                                           
               ->setVariables(array(                                                               
                  'url'  => $this->getRequest()->getBaseUrl(),                                                                     
                  'autorizaciones'        => $autorizaciones,                     
                  'enlaces'               => $this->enlacesPaginacion($pagina,$paginas_totales),    
                  'pagina'                => $pagina,                                                        
                  'offset'                => $offset,           
                  'autorizaciones_totales'=> $autorizaciones_totales['count'],     
                  'paginas_totales'       => $paginas_totales    
        ));                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                                       
        $autorizacionesView = $this->getServiceLocator()
                  ->get('viewrenderer')
                  ->render($autorizacionesView);                                                 
                
                                    
        $jsonModel = new JsonModel();           
        $jsonModel->setVariables(array(
            'autorizaciones'=>array(
                'html'=> $autorizacionesView  
                )                                                                                                                                   
            )                                                       
        );  

        return $jsonModel;                              
    }                   

    public function eliminarAutorizacionAction()
    {
        $parameters = $this->request->getPost();                                                                                            
        $this->model->eliminarAutorizacion($parameters['id']);    
                        
        $result = new ViewModel();  
        $result->setTerminal(true);                                                                                   
        return $result;                             
    }   

    public function getAutorizacionAction()
    {                                                 
        $parameters = $this->request->getPost();                                                                                     
        $getAutorizacion = $this->model->getAutorizacion($parameters['id']);                                                                       
        $result = new ViewModel(array('autorizacion'=>$getAutorizacion));  
        $result->setTerminal(true);                                                                                   
        return $result;                                                               
    }                                                    

    public function getEscuelasAction()
    {                                                                                                                                                                                       
        $parameters = $this->request->getPost();                                                       
        $getEscuelas = $this->model->getEscuelasV1($parameters['id_entidad']);                                                         
        $result = new ViewModel(array('getEscuelas'=>$getEscuelas));  
        $result->setTerminal(true);                                       
        return $result;                             
    }                                                                                                                                                                                                 

    public function getLicenciaturasAction()
    {                                                                                                                                    
        $parameters = $this->request->getPost();                                
        $getLicenciaturas = $this->model->getLicenciaturasV1($parameters['id_escuela']);                                                         
        $result = new ViewModel(array('getLicenciaturas'=>$getLicenciaturas));  
        $result->setTerminal(true);                                       
        return $result;                             
    }                

    public function getEspecialidadAction()
    {                                                                                                                                                                           
        $parameters = $this->request->getPost();                                
        $getEspecialidad = $this->model->getEspecialidadV1($parameters['id_licenciatura']);                                                         
        $result = new ViewModel(array('getEspecialidad'=>$getEspecialidad));  
        $result->setTerminal(true);                                            
        return $result;                             
    }           

    public function getPeriodosAction()
    {                                                                                                                                 
        $parameters  = $this->request->getPost();                                                                 
        $getPeriodos = $this->model->getPeriodosMayor($parameters['periodo1']);                               
        $result = new ViewModel(array('getPeriodos'=>$getPeriodos));  
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
