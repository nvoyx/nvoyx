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
 * Collection of dept bumf
 */
  
class Dept{
	
	protected static $instance;
	protected static $USER;
	protected static $DB;
	protected static $BOOT;
	static $DEPT = array();
	
	
	/* --- PUBLIC METHODS --- */
	
		
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($boot,$db,$user) {
		
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* link with public/db */
			self::$DB = $db;
			
			/* link with public/user */
			self::$USER = $user;
						
			/* create a new instance */
			self::$instance = new self();		
		}
		
		/* return instance */
		return self::$instance;
	}
	
	
	/*
	 * FETCH_ARRAY()
	 * return the user array
	 */
	public function FETCH_ARRAY(){ return self::$DEPT;	}
	
	
	/*
	 * FETCH_ENTRY()
	 * return an entry from the dept array
	 */
	public function FETCH_ENTRY($n){
		foreach(self::$DEPT as $r){
			if($r['id']==$n){
				return self::$DEPT[$r['name']];
			}
		}
		return false;
	}
	
	
	
	/*
	 * GRANTED()
	 * is this dept a member of the default Web department
	 */
	public function GRANTED($id,$tid=false){
		if($id==1){
			return true;
		}
		if($tid){
			$rs=$this->FETCH_ENTRY($id)['access'];
			if(in_array($tid,$rs)){
				return true;
			}
		}
		return false;
	}
	
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance
	 */
	private function __construct(){
	
		/* should the apc depts array be used to retrieve department details (if it exists) */
		$cache = self::$BOOT->GET_CACHE("depts");
		
		/* if we don't have a cache for whatever reason */
		if(!$cache){
			
			/* fetch the department details from the database */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_ORDER(array("`dept`.`name`"=>"ASC"));
			$depts = self::$DB->DB_QUERY("SELECT","* FROM `dept`");
			
			/* cycle through the department types */
			foreach($depts as $dept){
				
				/* build an array of departments */
				$d[$dept["dept.name"]] = array("id"=>$dept["dept.id"],
												"name"=>$dept["dept.name"],
												"access"=>self::$BOOT->JSON($dept["dept.access"],"decode"));
			}
			
			/* store the departments */
			self::$DEPT = $d;
			
			/* update the page type cache */
			self::$BOOT->SET_CACHE("depts",self::$DEPT);
		
			
		} else {
			
			/* retrieve the page type cache */
			self::$DEPT = $cache;
		}
	}
}