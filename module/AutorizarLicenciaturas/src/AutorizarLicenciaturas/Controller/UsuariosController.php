<?php
namespace AutorizarLicenciaturas\Controller;
											
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Validator;
use Zend\I18n\Validator as I18nValidator;
use Zend\Db\Adapter\Adapter;
//use Zend\Crypt\Password\Bcrypt;				
use Zend\Authentication\Adapter\DbTable as AuthAdapter;

//Componentes de autenticación
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Session\Container;
 
//Incluir modelos
//use Modulo\Model\Entity\UsuariosModel;
 				
//Incluir formularios
use AutorizarLicenciaturas\Form\UsuariosForm;
																			
class UsuariosController extends AbstractActionController{
    private $dbAdapter;
    private $auth;		
					
    public function __construct(){	
        //Cargamos el servicio de autenticación en el constructor
        $this->auth = new AuthenticationService();
    }																							

    public function indexAction(){
    	//Vamos a utilizar otros métodos
        return new ViewModel();
    }						

    public function loginAction()
    {
        $auth   = $this->auth;
        $identi = $auth->getStorage()->read();
        					
        if($identi!=false && $identi!=null){
        	return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/usuarios/dentro');
        }						
        							
		$this->layout("layout/login.phtml");						
       
        //DbAdapter
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
         				
        //Creamos el formulario de login
        $form = new UsuariosForm("form");
       					 		 				
        //Si nos llegan datos por post
        if($this->getRequest()->isPost()){

	        $authAdapter = new AuthAdapter($this->dbAdapter,
	                                           'aut_usuarios',
	                                           'login',	
	                                           'password');		
	        				     								                               
	        //Establecemos como datos a autenticar los que nos llegan del formulario
	        $authAdapter->setIdentity($this->getRequest()->getPost("login"))		
	                    ->setCredential(sha1($this->getRequest()->getPost("password")));
	         	    		
	        																	    		
	        //Le decimos al servicio de autenticación que el adaptador
	        $auth->setAdapter($authAdapter);
	            
	        //Le decimos al servicio de autenticación que lleve a cabo la identificacion
	        $result=$auth->authenticate();		
	         		 
	        //Si el resultado del login es falso, es decir no son correctas las credenciales
	        if($authAdapter->getResultRowObject()==false){
	        								    						
	            //Crea un mensaje flash y redirige	
	            $this->flashMessenger()->addMessage("Usuario ó Contraseña incorrectas.");
	            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/usuarios/login');
	        }else{																
	            								
	            // Le decimos al servicio que guarde en una sesión
	            // el resultado del login cuando es correcto
	            $auth->getStorage()->write($authAdapter->getResultRowObject());
	        																																						     							 																	
	            //Nos redirige a una pagina interior												
	            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/Autorizar/index');
	            																													
	        }																																
        }		  																				   
         
        return new ViewModel(array("form"=>$form,
        						   "url" => $this->getRequest()->getBaseUrl())
        );
    }													
     					
    public function cerrarAction()
    {
        //Cerramos la sesión borrando los datos de la sesión.
        $this->auth->clearIdentity();
        return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/AutorizarLicenciaturas/usuarios/login');
    }


}

?>