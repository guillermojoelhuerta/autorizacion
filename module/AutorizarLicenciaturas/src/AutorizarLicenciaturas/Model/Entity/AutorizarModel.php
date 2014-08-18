<?php
										
namespace AutorizarLicenciaturas\Model\Entity;
													
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;	
use Zend\Db\Sql\Predicate;	       
	       					      								
class AutorizarModel					
{									
	public $dbAdapter;
                      
	public function __construct($dbAdapter = null)
  {	
		$this->dbAdapter = $dbAdapter;													
	}		  

  /*
	public function getEntidades()
  {
        $result = $this->dbAdapter->query("select id,entidad from cat_entidades order by id asc",Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray(); 		 							
	} */        
                   					     
  
	public function getEscuelasV1($id_entidad)
  {	
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()												
        			        ->columns(array('id','escuela'))						
                      ->from('escuelas')				
                      ->where(array('id_entidad'=>$id_entidad))		
                      ->order('id asc'); 				          
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();      		 							
	}      

       
  public function getLicenciaturasV1($id_escuela)
  {             
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                                    
                      ->columns(array('id_licenciatura'))                   
                      ->from('escuelas_licenciaturas')                                                                                  
                      ->join('cat_licenciaturas', 'escuelas_licenciaturas.id_licenciatura = cat_licenciaturas.id',array('licenciatura'))                      
                      ->where(array('escuelas_licenciaturas.id_escuela' => $id_escuela))  
                      ->group(array('escuelas_licenciaturas.id_licenciatura'))            
                      ->order('cat_licenciaturas.id asc');                                                                                                                                             
        $selectString = $sql->getSqlStringForSqlObject($select);          
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();                        
  }        

                  
  public function getEspecialidadV1($id_licenciatura)
  {                                
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                                       
                      ->columns(array('id_especialidad'))                   
                      ->from('escuelas_licenciaturas')                                                                                                 
                      ->join('cat_especialidades','escuelas_licenciaturas.id_especialidad = cat_especialidades.id',array('especialidad'))                      
                      ->where(array('escuelas_licenciaturas.id_licenciatura' => $id_licenciatura))  
                      ->group(array('escuelas_licenciaturas.id_especialidad'))            
                      ->order('escuelas_licenciaturas.id asc');                                                                                                                                                                                    
        $selectString = $sql->getSqlStringForSqlObject($select);          
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();                        
  }       

  public function escuela($id)
  {                           
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                          
                      ->columns(array('id','escuela'))            
                      ->from('escuelas')            
                      ->where(array('id'=>$id));                     
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();                        
  }   

  public function getEscuelas($id_entidad)
  {    
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                        
                      ->columns(array('id','escuela'))            
                      ->from('escuelas')        
                      ->where(array('id_entidad'=>$id_entidad))   
                      ->order('id asc');                   
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
               
        $escuelas = array();                                                                                       
        foreach($result->toArray() as $key => $value){
            $escuelas[$value["id"]] = $value["escuela"];        
        }                                                                                            
        return $escuelas;                                       
  }                     

  public function getEntidades()
  {                         
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                  
                      ->columns(array('id','entidad'))            
                      ->from('cat_entidades')                                                              
                      ->order('id asc');                                                           
        $selectString = $sql->getSqlStringForSqlObject($select);       
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);

        $entidades = array();                         
        foreach($result->toArray() as $key => $value){
            $entidades[$value["id"]] = $value["entidad"]; 
        } 
        return $entidades;                                                             
  }       

  public function eliminarAutorizacion($id)
  {
      $sql = new Sql($this->dbAdapter);
      $select = $sql->delete()                                  
                    ->from('autorizacion_licenciaturas') 
                    ->where(array('id'=>$id));                                                                      
      $selectString = $sql->getSqlStringForSqlObject($select);
      /*echo $selectString;                                          
      die();*/                                                                       
      return $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
  }                                                                                     

  public function getAutorizacion($id)
  {      
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                                                                                      
                      ->columns(array('id','autorizar','oficio'))                                            
                      ->from('autorizacion_licenciaturas')                                                                                                                                                            
                      ->join('cat_entidades','autorizacion_licenciaturas.id_entidad = cat_entidades.id',array('entidad'))
                      ->join('escuelas','autorizacion_licenciaturas.id_escuela = escuelas.id',array('escuela'))   
                      ->join('cat_licenciaturas','autorizacion_licenciaturas.id_licenciatura = cat_licenciaturas.id',array('licenciatura'))                                             
                      ->join('cat_especialidades','autorizacion_licenciaturas.id_especialidad = cat_especialidades.id',array('especialidad'))                   
                      ->join(array("periodos_licenciaturas1" => 'periodos_licenciaturas'),'autorizacion_licenciaturas.periodo1 = periodos_licenciaturas1.id',array('periodo1'=>'periodo'))        
                      ->join(array("periodos_licenciaturas2" => 'periodos_licenciaturas'),'autorizacion_licenciaturas.periodo2 = periodos_licenciaturas2.id',array('periodo2'=>'periodo'))                                                                                          
                      ->where(array('autorizacion_licenciaturas.id' => $id));                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  
        $selectString = $sql->getSqlStringForSqlObject($select);                                        
        /*echo $selectString;                                          
        die();*/                                                                                                                                             
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();                                     
  }      

  public function getAutorizacionUpdate($id)
  {      
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()    
                      ->columns(array('id','autorizar','oficio','id_entidad','id_escuela','id_licenciatura','id_especialidad','periodo1','periodo2'))                                            
                      ->from('autorizacion_licenciaturas')                                                                                                                                                                   
                      ->join('cat_entidades','autorizacion_licenciaturas.id_entidad = cat_entidades.id',array('entidad'))
                      ->join('escuelas','autorizacion_licenciaturas.id_escuela = escuelas.id',array('escuela'))   
                      ->join('cat_licenciaturas','autorizacion_licenciaturas.id_licenciatura = cat_licenciaturas.id',array('licenciatura'))                                             
                      ->join('cat_especialidades','autorizacion_licenciaturas.id_especialidad = cat_especialidades.id',array('especialidad'))                                     
                      ->join(array("periodos_licenciaturas1" => 'periodos_licenciaturas'),'autorizacion_licenciaturas.periodo1 = periodos_licenciaturas1.id',array('periodo_year'=>'periodo'))        
                      ->join(array("periodos_licenciaturas2" => 'periodos_licenciaturas'),'autorizacion_licenciaturas.periodo2 = periodos_licenciaturas2.id',array('periodo2_year'=>'periodo'))                                                                                          
                      ->where(array('autorizacion_licenciaturas.id' => $id));                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  
        $selectString = $sql->getSqlStringForSqlObject($select);                                        
        /*echo $selectString;        
        die();    */                                                                                                                                                                   
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray()[0];                                                     
  }                 

  public function autorizacionValues($id)
  {                 
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                                                                                      
                      ->columns(array('id','autorizar','oficio','id_entidad','id_escuela','id_licenciatura','id_especialidad','periodo1','periodo2'))                                            
                      ->from('autorizacion_licenciaturas')                                                                                                                                                            
                      ->where(array('id' => $id));                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
        $selectString = $sql->getSqlStringForSqlObject($select);                                        
        /*echo $selectString;                                          
        die();*/                                                                                                                                             
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();                                     
  }             
       


  public function periodo($id)
  {                                     
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                           
                      ->columns(array('periodo'))               
                      ->from('periodos_licenciaturas')  
                      ->where(array('id'=>$id));                                                                    
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();                        
  }     

  public function getPeriodos()
  {             
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                        
                      ->columns(array('id','periodo'))            
                      ->from('periodos_licenciaturas')        
                      ->order('id asc');                                                              
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);

        $periodos = array();                                  
        foreach($result->toArray() as $key => $value){
            $periodos[$value["id"]] = $value["periodo"]; 
        }               
        return $periodos;                        
  }             

  public function getPeriodosMayor($periodo)
  {             
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                        
                      ->columns(array('id','periodo'))            
                      ->from('periodos_licenciaturas') 
                      ->where("periodo > {$periodo}",null)          
                      ->order('id asc');                                                                                                                                                                                
        $selectString = $sql->getSqlStringForSqlObject($select);                
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();                             
  }                        

  public function getLicenciaturas($id_escuela)
  {             
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                                    
                      ->columns(array('id_licenciatura'))                   
                      ->from('escuelas_licenciaturas')                                                                                  
                      ->join('cat_licenciaturas', 'escuelas_licenciaturas.id_licenciatura = cat_licenciaturas.id',array('licenciatura'))                      
                      ->where(array('escuelas_licenciaturas.id_escuela' => $id_escuela))  
                      ->group(array('escuelas_licenciaturas.id_licenciatura'))            
                      ->order('cat_licenciaturas.id asc');                                                                                                                                      
        $selectString = $sql->getSqlStringForSqlObject($select);          
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        
        $licenciaturas = array(); 
        foreach($result->toArray() as $key => $value) {
              $licenciaturas[$value["id_licenciatura"]] = $value["licenciatura"];        
        }
        return $licenciaturas;                        

  }       

  public function getEspecialidad($id_licenciatura)
  {                                   
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                                       
                      ->columns(array('id_especialidad'))                   
                      ->from('escuelas_licenciaturas')                                                                                                 
                      ->join('cat_especialidades','escuelas_licenciaturas.id_especialidad = cat_especialidades.id',array('especialidad'))                      
                      ->where(array('escuelas_licenciaturas.id_licenciatura' => $id_licenciatura))  
                      ->group(array('escuelas_licenciaturas.id_especialidad'))            
                      ->order('escuelas_licenciaturas.id asc');                                                                                                                                                                                    
        $selectString = $sql->getSqlStringForSqlObject($select);          
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE); 

        $especialidad = array(); 
        foreach($result->toArray() as $key => $value) {
              $especialidad[$value["id_especialidad"]] = $value["especialidad"];        
        }          
        return $especialidad;                           
  }                      
                            
  public function getAutorizacionEntidad($where = array(),$limit,$offset)
  {                                              
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                                                                              
                      ->columns(array('id','autorizar','oficio'))                                            
                      ->from('autorizacion_licenciaturas')                                                                                                                                                            
                      ->join('cat_entidades','autorizacion_licenciaturas.id_entidad = cat_entidades.id',array('entidad'))
                      ->join('escuelas','autorizacion_licenciaturas.id_escuela = escuelas.id',array('escuela'))   
                      ->join('cat_licenciaturas','autorizacion_licenciaturas.id_licenciatura = cat_licenciaturas.id',array('licenciatura'))                                             
                      ->join('cat_especialidades','autorizacion_licenciaturas.id_especialidad = cat_especialidades.id',array('especialidad'))                   
                      ->join(array("periodos_licenciaturas1" => 'periodos_licenciaturas'),'autorizacion_licenciaturas.periodo1 = periodos_licenciaturas1.id',array('periodo1'=>'periodo'))        
                      ->join(array("periodos_licenciaturas2" => 'periodos_licenciaturas'),'autorizacion_licenciaturas.periodo2 = periodos_licenciaturas2.id',array('periodo2'=>'periodo'))                                                                                          
                      //->where(array('autorizacion_licenciaturas.id_entidad' => $entidad))                                                                          
                      ->where($where)     
                      ->order('autorizacion_licenciaturas.id asc')             
                      ->limit($limit) // always takes an integer/numeric
                      ->offset($offset);                                                                                                                                                                                                                                                                                                                                                                                                                                            
        $selectString = $sql->getSqlStringForSqlObject($select);                    
        /*echo $selectString;                                       
        die();*/                                                                                                                                             
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();                    
  }      

  public function getAutorizacionLike($like,$limit,$offset)
  {                                                               
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                                                                                    
                      ->columns(array('id','autorizar','oficio'))                                            
                      ->from('autorizacion_licenciaturas')                                                                                                                                                            
                      ->join('cat_entidades','autorizacion_licenciaturas.id_entidad = cat_entidades.id',array('entidad'))
                      ->join('escuelas','autorizacion_licenciaturas.id_escuela = escuelas.id',array('escuela'))   
                      ->join('cat_licenciaturas','autorizacion_licenciaturas.id_licenciatura = cat_licenciaturas.id',array('licenciatura'))                                             
                      ->join('cat_especialidades','autorizacion_licenciaturas.id_especialidad = cat_especialidades.id',array('especialidad'))                   
                      ->join(array("periodos_licenciaturas1" => 'periodos_licenciaturas'),'autorizacion_licenciaturas.periodo1 = periodos_licenciaturas1.id',array('periodo1'=>'periodo'))        
                      ->join(array("periodos_licenciaturas2" => 'periodos_licenciaturas'),'autorizacion_licenciaturas.periodo2 = periodos_licenciaturas2.id',array('periodo2'=>'periodo'))                                                                                                     
                      ->where(array(                                         
                          // Other conditions...
                          new Predicate\PredicateSet(
                              array(   
                                  new Predicate\Like('escuelas.escuela', '%'.$like.'%'),
                                  new Predicate\Like('cat_licenciaturas.licenciatura ', '%'.$like.'%'),
                                  new Predicate\Like('cat_licenciaturas.licenciatura ', '%'.$like.'%')
                              ),                                                   
                              Predicate\PredicateSet::COMBINED_BY_OR
                          )          
                      ))                       
                      ->limit($limit) // always takes an integer/numeric
                      ->offset($offset);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
        $selectString = $sql->getSqlStringForSqlObject($select);                    
        /*echo $selectString;                                       
        die();     */                                                                                                                                                                             
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();                    
  }                           
  

  public function countAutorizacionesLike($like)
  {     /*                                                                
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                                                                                                                                 
                      ->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')))                                      
                      ->from('autorizacion_licenciaturas')                                                                                                                                                                         
                      ->join('cat_entidades','autorizacion_licenciaturas.id_entidad = cat_entidades.id')
                      ->join('escuelas','autorizacion_licenciaturas.id_escuela = escuelas.id')   
                      ->join('cat_licenciaturas','autorizacion_licenciaturas.id_licenciatura = cat_licenciaturas.id')                                             
                      ->join('cat_especialidades','autorizacion_licenciaturas.id_especialidad = cat_especialidades.id')                   
                      ->join(array("periodos_licenciaturas1" => 'periodos_licenciaturas'),'autorizacion_licenciaturas.periodo1 = periodos_licenciaturas1.id')        
                      ->join(array("periodos_licenciaturas2" => 'periodos_licenciaturas'),'autorizacion_licenciaturas.periodo2 = periodos_licenciaturas2.id')                                                                                         
                      //->where(array('autorizacion_licenciaturas.id_entidad' => $entidad))                                                                          
                      ->where('escuelas.escuela LIKE "%'.$like.'%"');                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
        $selectString = $sql->getSqlStringForSqlObject($select);                                                          
        echo $selectString;                                                                                                    
        die();                                                                                                                                                                                                               
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();          
        */   

        $result = $this->dbAdapter->query("SELECT COUNT(*) AS count
            FROM autorizacion_licenciaturas
            INNER JOIN cat_entidades ON autorizacion_licenciaturas.id_entidad = cat_entidades.id
            INNER JOIN escuelas ON autorizacion_licenciaturas.id_escuela = escuelas.id
            INNER JOIN cat_licenciaturas ON autorizacion_licenciaturas.id_licenciatura = cat_licenciaturas.id
            INNER JOIN cat_especialidades ON autorizacion_licenciaturas.id_especialidad = cat_especialidades.id
            INNER JOIN periodos_licenciaturas AS periodos_licenciaturas1 ON autorizacion_licenciaturas.periodo1 = periodos_licenciaturas1.id
            INNER JOIN periodos_licenciaturas AS periodos_licenciaturas2 ON autorizacion_licenciaturas.periodo2 = periodos_licenciaturas2.id                   
            WHERE escuelas.escuela LIKE '%".$like."%'                                 
            OR cat_licenciaturas.licenciatura LIKE '%".$like."%' 
            OR cat_especialidades.especialidad LIKE '%".$like."%'",Adapter::QUERY_MODE_EXECUTE);
        $count = $result->toArray();                                                                                
        return $count[0];                                                                                                                                                              
  }          


  public function countAutorizaciones($where = array())
  {                                                        
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                                                                                                                          
                      ->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')))                                      
                      ->from('autorizacion_licenciaturas')                                                                                                                                                                         
                      ->where($where);                                                                                                                                                                                                                                                                                                                                                                                                               
        $selectString = $sql->getSqlStringForSqlObject($select);                                                                                                                                                                                             
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();                                   
  }                     

  public function countAutorizacionesV2($where = array())
  {                                                            
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select()                                                                                                                                          
                      ->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')))                                      
                      ->from('autorizacion_licenciaturas')                                                                                                                                                                         
                      ->where($where);                                                                                                                                                                                                                                                                                                                                                                                                               
        $selectString = $sql->getSqlStringForSqlObject($select);  
        /*echo $selectString;                                       
        die();  */                                                                                                                                                                                            
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray()[0]["count"];                              
          
  }        

  public function insertAutorizacion($data = array())
  {                                                          
        $values = array(            
          'id_entidad'=> $data['id_entidad'],
          'id_escuela'=> $data['id_escuela'],
          'id_licenciatura'=> $data['id_licenciatura'],
          'id_especialidad'=> $data['id_especialidad'],
          'autorizar'=> $data['autorizar'],
          'periodo1'=> $data['periodo1'],
          'periodo2'=> $data['periodo2'],
          'oficio'=> $data['oficio']
        );                                              

        $sql = new Sql($this->dbAdapter);               
        $insert = $sql->insert('autorizacion_licenciaturas')                                     
                      ->values($values);                                                                                                                                                              
        $selectString = $sql->getSqlStringForSqlObject($insert);        
        $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
        $lastId = $this->dbAdapter->getDriver()->getLastGeneratedValue();       
        return $lastId;                                                                                 
  }                     
      
  public function updateAutorizacion($data = array())
  {                                                           
        $values = array(                          
          'autorizar'=> $data['autorizar'],
          'oficio'   => $data['oficio']
        );                                                                          

        $sql = new Sql($this->dbAdapter);               
        $insert = $sql->update('autorizacion_licenciaturas')                                     
                      ->set($values)    
                      ->where(array("id"=>$data['id']));                                                                                                                                                                 
        $selectString = $sql->getSqlStringForSqlObject($insert); 
        /* echo $selectString;                                       
        die(); */                                                      
        return $result = $this->dbAdapter->query($selectString,Adapter::QUERY_MODE_EXECUTE);
  }    


																		
}