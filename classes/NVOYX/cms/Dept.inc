<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/** 
 * @class
 * Collection of Department bumf
 */

namespace NVOYX\cms;

class Dept{
	
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
			self::$GROUP = $dept;
			
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
	 * grab any content associated with the departments / list page
	 */
	private function _list(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}

	
	/*
	 * _add()
	 * grab any content associated with the departments / add page
	 */
	private function _add(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}	
	
	
	/*
	 * _edit()
	 * process and grab any content associated with the departments / edit page
	 */
	private function _edit(){
		
		/* check that we have a numeric reference for the redirect */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* process any fields that have been posted by this page */
			$this->POSTED("edit",self::$BOOT->FETCH_ENTRY("breadcrumb",3));
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * _delete()
	 *  grab any content associated with the departments / delete page
	 */
	private function _delete(){
		
		/* check that we have a numeric reference for the redirect */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}	
	
	
	/*
	 * POSTED()
	 */
	private function POSTED($form,$rid=false){
		
		/* do we have posted data */
		if($_POST){
			
			/* switch by html form */
			switch($form):
				
				
				case "edit":
					
					/* cycle through the posted values */
					foreach($_POST as $key=>$value ){
					
						/* sanitise the posted values */
						$value = self::$BOOT->TEXT($value);
						
						switch($key):
							
							case "access":
								
								/* encode the sanitised array */
								$v[$key] = self::$BOOT->JSON($value,"encode");
								break;

								
							case "name":
																
								/* store the urls */
								$v[$key]=$value;
								break;

						endswitch;
					}
					
					/* do we have an array of values */
					if(is_array($v)){
				
						/* push changes into redirects table */
						self::$DB->CLEAR(array("ALL"));
						self::$DB->SET_FILTER("`dept`.`id`={$rid}");
						self::$DB->QUERY("UPDATE","`dept` SET `dept`.`name`='{$v["name"]}',`dept`.`access`='{$v["access"]}'");
					}
					break;
			endswitch;
			
			/* issue a notification */
			$_SESSION['notify']=array(
				'message'=>'Success: entry updated',
				'type'=>'success'
			);
		}
	}
}