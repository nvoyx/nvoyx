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
 * Collection of member bumf
 */

namespace NVOYX\cms;

class Member{
	
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
	 * grab any content associated with the member / list page
	 */
	private function _list(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}

	
	/*
	 * _add()
	 * grab any content associated with the member / add page
	 */
	private function _add(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}	
	
	
	/*
	 * _edit()
	 * process and grab any content associated with the member / edit page
	 */
	private function _edit(){
		
		/* check that we have a numeric reference for the member */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* process any fields that have been posted by this page */
			$this->POSTED(self::$BOOT->FETCH_ENTRY("breadcrumb",3));
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * _delete()
	 * grab any content associated with the member / delete page
	 */
	private function _delete(){
		
		/* check that we have a numeric reference for the member */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * POSTED()
	 */
	private function POSTED($mid){
		
		/* do we have posted data */
		if($_POST){
			
			/* cycle through the posted fields */
			foreach($_POST as $key=>$value ){
				
				/* sanitise the posted value */
				$value = self::$BOOT->TEXT($value);
				
				switch($key):
					case "title":
					case "firstname":
					case "lastname":
					case "position":
					case "company":
					case "email":
					case "username":
					case "password":
						
						/* encrypt the sanitised value */
						$v[$key] = self::$BOOT->CYPHER(array("TYPE"=>"encrypt","STRING"=>$value));
						break;
					
					case "dob":
												
						/* convert the posted date of birth to a mysql date format */
						$v[$key] = date('Y-m-d',strtotime($value));
						break;
					
					case "joined":
												
						/* convert the posted joined date to a mysql datetime format */
						$v[$key] = date('Y-m-d H:i:00',strtotime($value));
						break;
					
					case "comments":
						
						$v[$key] = $value;
						
						/* check through existing comments and replies, updating the approved field as appropriate */
						self::$DB->CLEAR(array("ALL"));
						self::$DB->SET_FILTER("`comments`.`mid`={$mid}");
						self::$DB->QUERY("UPDATE","`comments` SET `comments`.`approved`={$v[$key]}",true);
						break;
					
					default:
						
						$v[$key] = $value;
						break;

				endswitch;
			}
			
			/* if we have any values */
			if(is_array($v)){
				
				/* push changes into member table */
				self::$DB->CLEAR(array("ALL"));
				self::$DB->SET_LIMIT(1);
				self::$DB->SET_FILTER("`member`.`id`={$mid}");
				self::$DB->QUERY("UPDATE","`member` SET `member`.`title`='{$v["title"]}',".
									"`member`.`firstname`='{$v["firstname"]}',".
									"`member`.`lastname`='{$v["lastname"]}',".
									"`member`.`position`='{$v["position"]}',".
									"`member`.`company`='{$v["company"]}',".
									"`member`.`email`='{$v["email"]}',".
									"`member`.`dob`='{$v["dob"]}',".
									"`member`.`web`='{$v["web"]}',".
									"`member`.`username`='{$v["username"]}',".
									"`member`.`password`='{$v["password"]}',".
									"`member`.`sex`='{$v["sex"]}',".
									"`member`.`avatar`='{$v["avatar"]}',".
									"`member`.`state`={$v["state"]},".
									"`member`.`joined`='{$v["joined"]}',".
									"`member`.`comments`={$v["comments"]}");
			}
		}
	}
}