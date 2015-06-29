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
 * Collection of variable bumf
 */
  
class _Variables{
	
	protected static $instance;
	protected static $BOOT;
	protected static $DB;
	protected static $USER;
	protected static $GROUP;
	protected static $DEPT;
	protected static $HTML;
	
	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($boot,$db,$user,$group,$dept) {
		
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
			
			/* link with public/dept */
			self::$DEPT = $dept;
			
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
	 * grab any content associated with the variables / list page
	 */
	private function _list(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}

	
	/*
	 * _add()
	 * grab any content associated with the variables / add page
	 */
	private function _add(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}	
	
	
	/*
	 * _edit()
	 * process and grab any content associated with the variables / edit page
	 */
	private function _edit(){
		
		/* check that we have a numeric reference for the variable */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* process any fields that have been posted by this page */
			$this->POSTED(self::$BOOT->FETCH_ENTRY("breadcrumb",3));
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * _delete()
	 * grab any content associated with the variables / delete page
	 */
	private function _delete(){
		
		/* check that we have a numeric reference for the variable */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * POSTED()
	 */
	private function POSTED($vid){
		
		/* do we have posted data */
		if($_POST){
			
			/* cycle through the posted fields */
			foreach($_POST as $key=>$value ){
				
				switch($key):
					case "name":
					case "notes":
						
						/* sanitise the posted value */
						$v[$key] = self::$BOOT->TEXT($value);
						break;
					
					case "value":
						
						/* convert the posted JSON encoded string to an array */
						$value = self::$BOOT->JSON($value,"decode");
						
						/* if we have a an array */
						if($value!=null){
							
							/* sanitise the contents of the array */
							$value = self::$BOOT->TEXT($value);
							
							/* JSON encode the values */
							$v[$key] = self::$BOOT->JSON($value,"encode");
						
							
						} else {
							
							/* mauually create an empty JSON array */
							$v["$key"]="[\"\"]";
						}
						break;
				endswitch;
			}
			
			/* if we have any values */
			if(is_array($v)){
				
				/* push changes into variables table */
				self::$DB->DB_CLEAR(array("ALL"));
				self::$DB->DB_SET_FILTER("`variables`.`id`={$vid}");
				self::$DB->DB_QUERY("UPDATE","`variables` SET `variables`.`name`='{$v["name"]}',`variables`.`notes`='{$v["notes"]}',`variables`.`value`='{$v["value"]}'");
			}
		}
	}
}