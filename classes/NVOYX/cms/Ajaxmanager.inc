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
 * Collection of ajaxmanager bumf
 */

namespace NVOYX\cms;
  
class Ajaxmanager{
	
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
	 * grab any content associated with the type / list page
	 */
	private function _list(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}

	
	/*
	 * _add()
	 * grab any content associated with the type / add page
	 */
	private function _add(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}	
	
	
	/*
	 * _edit()
	 * process and grab any content associated with the type / edit page
	 */
	private function _edit(){
		
		/* check that we have a numeric reference for the type */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* process any fields that have been posted by this page */
			$this->POSTED(self::$BOOT->FETCH_ENTRY("breadcrumb",3));
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * _list()
	 * grab any content associated with the type / delete page
	 * 
	 */
	private function _delete(){
		
		/* check that we have a numeric reference for the type */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}

	
	/*
	 * POSTED()
	 * handle posted data
	 */
	private function POSTED($id){
		
		/* do we have posted data */
		if($_POST){
			
			/* iterate over the posted data and update the relevant fields in the temporary outline array */
			foreach($_POST as $key=>$value ){
							
				/* sanitise the field values */
				$value = self::$BOOT->TEXT($value);
								
				switch($key):
					
					case "url":
					case "orig_url":
						
						/* grab the current sanitised field value */
						$v[$key] = "/settings/ajax/".self::$BOOT->ALIAS($value);
						break;
				
					case "access":
						/* grab the current sanitised field value (default to "s") */
						if($value!=""){$v[$key]=$value;}else{$v[$key]="s";}
						break;
				endswitch;
			}
			
			/* if we have field data */
			if(is_array($v)){
				
				/* push changes into path table */
				self::$DB->CLEAR(array("ALL"));
				self::$DB->SET_FILTER("`path`.`id`={$id}");
				self::$DB->QUERY("UPDATE","`path` SET `path`.`url`='{$v["url"]}',`path`.`access`='{$v["access"]}'");
				
				/* has the file name changed */
				if($v["url"] != $v["orig_url"]){
					
					/* the file name has changed, so update it */
					rename(str_replace("/settings/ajax/",self::$BOOT->FETCH_ENTRY("blocks")."/private/ajax/",$v["orig_url"]).".php",str_replace("/settings/ajax/",self::$BOOT->FETCH_ENTRY("blocks")."/private/ajax/",$v["url"]).".php");
				}
			}
		}
	}
}