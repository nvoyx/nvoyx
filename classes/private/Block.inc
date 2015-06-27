<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */
 
/** 
 * @class
 * Collection of block bumf
 */
  
class _Block{
	
	protected static $instance;
	protected static $BOOT;
	protected static $DB;
	protected static $USER;
	protected static $GROUP;
	protected static $HTML;
	
	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($boot,$db,$user,$group) {
		
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* link with public/db */
			self::$DB = $db;
			
			/* link with public/user */
			self::$USER = $user;
			
			/* link with public/group */
			self::$GROUP = $group;
			
			/* create a new instance */
			self::$instance = new self();		
		}
		
		/* return instance */
		return self::$instance;
	}
	
	
	/*
	 * FETCH_HTML()
	 * return the html associated with the url called method
	 */
	public function FETCH_HTML(){ return self::$HTML; }
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance
	 */
	private function __construct() {
		
		/* use the requested url to dictate which method to run */
		$rs = "_".strtolower(self::$BOOT->FETCH_ENTRY("breadcrumb",2));
		
		/* does the requested method exists */
		if(method_exists(get_class(),$rs)){
			
			/* store the output of the requested method */
			self::$HTML=$this->$rs();
		}
	}
	
	
	/*
	 * _list()
	 * perform any actions and grab any content associated with the block / list page
	 */
	private function _list(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}
	
	
	/*
	 * _add()
	 * perform any actions and grab any content associated with the block / add page
	 */
	private function _add(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}	
	
	
	/*
	 * _edit()
	 * perform any actions and grab any content associated with the block / edit page
	 */
	private function _edit(){
		
		/* check that we have a numeric reference for the block id */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* process any fields that have been posted by this page */
			$this->POSTED(self::$BOOT->FETCH_ENTRY("breadcrumb",3));
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * _delete()
	 * perform any actions and grab any content associated with the block / delete page
	 */
	private function _delete(){
		
		/* check that we have a numeric reference for the block id */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * POSTED()
	 * handle posted data
	 */
	private function POSTED($bid){
		
		/* do we have posted data */
		if($_POST){
			
			/* cycle through the posted data */
			foreach($_POST as $key=>$value ){
				
				/* grab the posted name */
				switch($key):
					case "name":
					case "access":
						
						/* santise the posted value */
						$v[$key] = self::$BOOT->TEXT($value);
						
						break;

					
					case "tid":
						
						/* sanitise the posted array */
						$value = self::$BOOT->TEXT($value);
						
						/* convert the sanitised array to a JSON string */
						$v[$key] = self::$BOOT->JSON($value,"encode");
						
						break;
					
					
					case "params":
						
						/* convert the JSON encoded string to an array */
						$value = self::$BOOT->JSON($value,"decode");
						
						/* check the array is valid */
						if($value!=null){
							
							/* sanitize the posted array */
							$value = self::$BOOT->TEXT($value);
							
							/* convert the sanitised array back to a JSON string */
							$v[$key] = self::$BOOT->JSON($value,"encode");
						
						} else {
							
							/* no params, so create an empy JSON entry */
							$v["$key"]="[\"\"]";
						}
						
						break;
				endswitch;
			}
			
			/* do we have an array of responses */
			if(is_array($v)){
				
				/* push changes into block table */
				self::$DB->DB_CLEAR(array("ALL"));
				self::$DB->DB_SET_FILTER("`block`.`id`={$bid}");
				self::$DB->DB_QUERY("UPDATE","`block` SET `block`.`name`='{$v["name"]}',`block`.`tid`='{$v["tid"]}',`block`.`access`='{$v["access"]}',`block`.`params`='{$v["params"]}'");
			}
		}
	}
}