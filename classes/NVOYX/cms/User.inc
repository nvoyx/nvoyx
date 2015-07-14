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
 * Collection of user bumf
 */

namespace NVOYX\cms;

class User{
	
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
	 * grab any content associated with the user / list page
	 */
	private function _list(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}

	
	/*
	 * _add()
	 * grab any content associated with the user / add page
	 */
	private function _add(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}	
	
	
	/*
	 * _edit()
	 * process and grab any content associated with the user / edit page
	 */
	private function _edit(){
		
		/* check that we have a numeric reference for the user */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* process any fields that have been posted by this page */
			$this->POSTED("edit",self::$BOOT->FETCH_ENTRY("breadcrumb",3));
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * _delete()
	 *  grab any content associated with the user / delete page
	 */
	private function _delete(){
		
		/* check that we have a numeric reference for the user */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * _login()
	 *  process ad grab any content associated with the user / login page
	 */
	private function _login(){
		
		/* is the current user is at least an admin */
		if(stristr(self::$USER->FETCH_ENTRY("type"),"a")){
			
			/* redirect to the content listings */
			self::$BOOT->HEADER(array("LOCATION"=>"/settings/content/list"));
		}
		
		/* process any fields that have been posted by this page */
		$this->POSTED("login");
		
		/* if the login form has been completed */
		if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["captcha"])){
			
			/* grab the previously issued captcha */
			if($_POST["captcha"] == $_SESSION["captcha"]){
				
				/* encrypt the posted username */
				$_POST["username"] = self::$BOOT->CYPHER(array("TYPE"=>"encrypt","STRING"=>$_POST["username"]));
				
				/* encrypt the posted password */
				$_POST["password"] = self::$BOOT->CYPHER(array("TYPE"=>"encrypt","STRING"=>$_POST["password"]));
				
				/* look for a user matching this record */
				self::$DB->CLEAR(array("ALL"));
				self::$DB->SET_FILTER("`user`.`username`='{$_POST["username"]}' AND `user`.`password`='{$_POST["password"]}'");
				$rs = self::$DB->QUERY("SELECT","* FROM `user`");
				
				/* we have a user record */
				if(isset($rs)){
					
					/* clear the old session id */
					session_regenerate_id();
					
					/* populate the session variable with some handy user info */
					$_SESSION["type"]=$rs[0]["user.type"];
					$_SESSION["id"]=$rs[0]["user.id"];
					$_SESSION["contact"]=self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$rs[0]["user.contact"]));
					$_SESSION["dept"]=$rs[0]["user.dept"];
					
					/* redirect to the content listings */
					self::$BOOT->HEADER(array("LOCATION"=>"/settings/content/list"));
				}
			}	
		}
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}
	
	
	/*
	 * _logout()
	 *  process grab any content associated with the user / logout page
	 */
	public function _logout(){
		
		/* clean the session variable */
		$_SESSION = array();
		
		/* create a new session id */
		session_regenerate_id();
		
		/* destroy the session */
		session_destroy();
		
		/* redirect to the website homepage */
		self::$BOOT->HEADER(array("LOCATION"=>"http://" . self::$BOOT->FETCH_ENTRY("domain") ."/"));
	}
	
	
	/*
	 * POSTED()
	 */
	private function POSTED($form,$uid=false){
		
		/* do we have posted data */
		if($_POST){
			
			/* switch by html form */
			switch($form):
				
	
				case "login":
					
					/* trim the post values */
					array_filter($_POST, create_function('$v','return trim($v);'));
					
					/* ccycle through the posted fields */
					foreach($_POST as $key=>$value ){
						
						/* sanitise the posted values */
						$_POST[$key] = self::$BOOT->TEXT($_POST[$key]);
					}
					break;
					
				
				
				case "edit":
					
					/* cycle through the posted values */
					foreach($_POST as $key=>$value ){
					
						/* sanitise the posted values */
						$value = self::$BOOT->TEXT($value);
						
						switch($key):
							case "username":
							case "password":
							case "contact":
							case "telephone":
							case "email":
								
								/* cypher encrypt the field values */
								$v[$key] = self::$BOOT->CYPHER(array("TYPE"=>"encrypt","STRING"=>$value));
								break;
							
							
							case "type":
								
								/* store the value (default to "s") */
								if($value!=""){$v[$key]=$value;}else{$v[$key]="s";}
								break;
								
							case "dept":
							case "filter":
								
								/* store the content type filter for this user */
								$v[$key]=$value;
								break;
						endswitch;
					}
					
					/* do we have an array of values */
					if(is_array($v)){
				
						/* push changes into user table */
						self::$DB->CLEAR(array("ALL"));
						self::$DB->SET_FILTER("`user`.`id`={$uid}");
						self::$DB->QUERY("UPDATE","`user` SET `user`.`username`='{$v["username"]}',`user`.`password`='{$v["password"]}',`user`.`contact`='{$v["contact"]}',`user`.`telephone`='{$v["telephone"]}',`user`.`email`='{$v["email"]}',`user`.`type`='{$v["type"]}',`user`.`filter`={$v["filter"]},`user`.`dept`={$v["dept"]}");
					}
					break;
			endswitch;
		}
	}
}