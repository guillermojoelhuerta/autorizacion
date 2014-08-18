<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller; 

use Zend\Mvc\Controller\AbstractActionController; 
use Zend\View\Model\ViewModel; 
use Application\Form\Miform;		 
use Application\Form\MiformValidator; 
//use Application\Model\; 	
						
				
class IndexController extends AbstractActionController
{
	
	
    public function indexAction() 
	{ 
	    $form = new Miform(); 						
	    $request = $this->getRequest(); 
	    $val = "error";

	    if($request->isPost()) 
	    { 
	        //$user = new (); 
	        											
	        $formValidator = new MiformValidator(); 
	        
	        $form->setInputFilter($formValidator->getInputFilter()); 
	        $form->setData($this->request->getPost()); 
	        																				 
	        print_r($this->getRequest()->getPost()); 
	        echo print_r($form->getMessages()); //
			if ($form->isValid()) {
									
				//print_r($form->getData());    

			}else{				
														
				echo print_r($form->getMessages()); //error messages
				//echo print_r($form->getErrors()); //error codes
				//echo print_r($form->getErrorMessages()); //any custom error messages
			}								


			//$product = $form->getData();

												
	    
	    //return ['form' => $form]; 
	    }else{
	    	$val = "nunca entra";
	    }														
	    return new ViewModel(array('form'=>$form,"val"=>$val));	
	}	
		 

    public function pruebaAction()
    {
    	return new ViewModel();
    }						
}
