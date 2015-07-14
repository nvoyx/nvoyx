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
 * Collection of type bumf
 */

namespace NVOYX\cms;

class Type{
	
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
	private function POSTED($tid){
		
		/* do we have posted data */
		if($_POST){
			
			/* iterate over the posted data */
			foreach($_POST as $key=>$value ){
				switch($key):
					
	
					case "prefix":
						
						/* remove any unwanted characters from the end of the file and sanitise */
						$v[$key] = rtrim(self::$BOOT->TEXT($value), '/');
						
						/* remove any unwanted characters from the start of the file and store in value array */
						$v[$key] = ltrim($v[$key],"/");
						break;
					
					
					case "name":
					case "parent":
					case "view":
					case "createdelete":
					case "rss":
					case "comments":
					case "comeditor":
					case "body":
					case "template":
						
						/* sanitise value and store in array */
						$v[$key] = self::$BOOT->TEXT($value);
						break;
					
					
					case "tags":
						
						/* remove any newlines from the string */
						$v[$key] = str_replace(array("\r\n","\r","\n"),"*",$value);
												
						/* sanitise the array */
						$v[$key] = self::$BOOT->TEXT($v[$key]);
						
						/* convert the string to an array */
						$v[$key] = explode("*",$v[$key]);
						
						/* encode the sanitised array */
						$v[$key] = self::$BOOT->JSON($v[$key],"encode");
						
						break;
						
				endswitch;
			}
			
			
			/* do we have valid values to update */
			if(is_array($v)){
				
				/* push changes into type table */
				self::$DB->CLEAR(array("ALL"));
				self::$DB->SET_FILTER("`type`.`id`={$tid}");
				self::$DB->QUERY("UPDATE","`type` SET `type`.`name`='{$v["name"]}',`type`.`prefix`='{$v["prefix"]}',`type`.`parent`='{$v["parent"]}',`type`.`view`='{$v["view"]}',`type`.`createdelete`='{$v["createdelete"]}',`type`.`rss`='{$v["rss"]}',`type`.`comments`='{$v["comments"]}',`type`.`comeditor`='{$v["comeditor"]}',`type`.`body`='{$v["body"]}',`type`.`template`='{$v["template"]}',`type`.`tags`='{$v["tags"]}'");
			}
		}
	}
}