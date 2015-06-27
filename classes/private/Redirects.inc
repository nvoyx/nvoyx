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
 * Collection of redirects bumf
 */
  
class _Redirects{
	
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
	 * grab any content associated with the redirects / list page
	 */
	private function _list(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}

	
	/*
	 * _add()
	 * grab any content associated with the redirects / add page
	 */
	private function _add(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}	
	
	
	/*
	 * _edit()
	 * process and grab any content associated with the redirects / edit page
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
	 *  grab any content associated with the redirects / delete page
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
							case "old":
								
								/* make sure that the the urls have opening slashes */
								if($value==''){
									$value=self::$BOOT->FETCH_ENTRY("timestamp");
								} else {
									if(substr($value,0,1)!='/'){$value = '/'.$value;}
								}
								
								/* store the urls */
								$v[$key]=$value;
								break;
							
							case "new":
								
								/* make sure that the the urls have opening slashes */
								if($value==''){
									$value=self::$BOOT->FETCH_ENTRY("timestamp");
								} else {
									if(substr($value,0,1)!='/'){$value = '/'.$value;}
								}
								
								/* make sure the urls do not have closing slashes */
								if($value!='/'){
									if(substr($value,-1,1)=='/'){
										$value=substr($value,0,-1);
									}
								}
								
								/* make sure the url is lowercase */
								$value=strtolower($value);
								
								/* store the urls */
								$v[$key]=$value;
								break;
						endswitch;
					}
					
					/* do we have an array of values */
					if(is_array($v)){
				
						/* push changes into redirects table */
						self::$DB->DB_CLEAR(array("ALL"));
						self::$DB->DB_SET_FILTER("`redirects`.`id`={$rid}");
						self::$DB->DB_QUERY("UPDATE","`redirects` SET `redirects`.`old`='{$v["old"]}',`redirects`.`new`='{$v["new"]}'");
					}
					break;
			endswitch;
		}
	}
}