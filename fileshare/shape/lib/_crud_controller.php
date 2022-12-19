<?php

/**
 * Crud controller
 */
abstract class CrudController extends shController {

    /**
     *
     * @param array $arguments
     */
	function __construct(array $arguments = array()){
		parent::__construct($arguments);
		
		
	}

    /**
     * synonym for update
     */
	function create(){
		$this->update();
	}

    /**
     * Update object function
     */
	function update(){
		try {

            $this->model = new $this->className();

            /**
             * Find the primary key, an if it is set, find object on its base
             */
            $primaryKey = $this->model->find_primary();
            if( $this->arguments->$primaryKey ){
                $this->model->find( $this->arguments->$primaryKey );
            }

            /**
             * set the attributes
             */
			foreach( $this->model->getproperties() as $key => $val ){
				if( isset($this->arguments->$key) ){
					$this->model->$key = $this->arguments->$key;
				}
			}
			if($this->model->$primaryKey){
//die('23232');
				$this->result = $this->model->save();
			} else {
//print "<pre>"; var_dump($this->model); exit;
				$this->result = $this->model->create();
			}
			
			
		}  catch (Exception $e){
            
			$_SESSION['errorstring'] = $e->getMessage();
			$module = $GLOBALS['_CONFIG']['DEFAULT_ERROR_MODULE'];
			$action = isset($GLOBALS['_CONFIG']['DEFAULT_ERROR_ACTION']) ? $GLOBALS['_CONFIG']['DEFAULT_ERROR_ACTION'] : 'index';		
		}
		return $this->model;
	}
	

	function select(){
		
		try {
			$this->model = new $this->className();
            
            $this->ormProperties = $this->model->getProperties();
			$where_clause = '';
			foreach( $this->model->getproperties() as $key => $val ){
				if ( isset($this->arguments->like[$key]) ){
					$where_clause .= $key . " like '%" . $this->arguments->like[$key] . "%' ";
				}
				if( $this->arguments->$key && $this->arguments->$key != ''){
					$where_clause .= $key . "= '" . $this->arguments->$key . "' ";
				}
			}
			$order_by			= $this->arguments->order_by	? $this->arguments->order_by	: NULL;
			$this->orderrule	= $this->arguments->orderrule	? $this->arguments->orderrule	: 'ASC';
			$this->result = $this->model->find_all($where_clause,NULL,0,$order_by,$this->orderrule);
			
			$this->orderrule	= $this->orderrule == 'ASC' ? 'DESC' : 'ASC';
			
		}  catch (Exception $e){
            error_log($e->getMessage());
			$_SESSION['errorstring'] = $e->getMessage();
			$module = $GLOBALS['_CONFIG']['DEFAULT_ERROR_MODULE'];
			$action = isset($GLOBALS['_CONFIG']['DEFAULT_ERROR_ACTION']) ? $GLOBALS['_CONFIG']['DEFAULT_ERROR_ACTION'] : 'index';		
		}
	}


	function delete(){
		try {
			$this->model = new $this->className();
			foreach( $this->model->getproperties() as $key => $val ){
				if( $this->arguments->$key && $this->arguments->$key != ''){
					$this->model->$key = $this->arguments->$key;
				}
			}
			$this->result = $this->model->destroy();
			
		} catch (Exception $e){
			$_SESSION['errorstring'] = $e->getMessage();
			$module = $GLOBALS['_CONFIG']['DEFAULT_ERROR_MODULE'];
			$action = isset($GLOBALS['_CONFIG']['DEFAULT_ERROR_ACTION']) ? $GLOBALS['_CONFIG']['DEFAULT_ERROR_ACTION'] : 'index';		
		}
	}

}
