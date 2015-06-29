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
class User{
	
	protected static $instance;
	protected static $DB;
	static $USER = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($db) {
        
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/db */
			self::$DB = $db;
			
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
	public function FETCH_ARRAY(){ return self::$USER;	}
	
	
	/*
	 * FETCH_ENTRY()
	 * return an entry from the user array
	 */
	public function FETCH_ENTRY($n){ return self::$USER["{$n}"]; }
	
	
	/*
	 * GRANTED()
	 * is this user allowed access to this file/resource/page/etc
	 */
	public function GRANTED($req){ if(stristr(self::$USER["type"],$req)){return true;}else{return false;} }

	
	/*
	 * ACCESS()
	 * which permissions is this user allowed to make use of for access purposes
	 */
	private function ACCESS($id){
		
		/* switch by user level and return all levels that the user can access */
		switch($id):
			
			case "s": return "!sacu"; break;
			case "a": return "!acu"; break;
			case "c": return "!cu"; break;
			default: return "!u"; break;
		endswitch;
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance
	 */
	private function __construct(){
		
		/* set the session name for the user */
		session_name("NVOYX");
		
		/* start a session */
		session_start();
		
		/* do we already have a session type */
		if(isset($_SESSION['type'])){
			
			/* grab the session id */
			self::$USER["id"] = $_SESSION['id'];
			
			/* grab the user access levels */
			self::$USER["type"] =  $this->ACCESS($_SESSION['type']);
			
			/* grab the user contact details */
			self::$USER["contact"] = $_SESSION['contact'];
			
		} else {
			
			/* don't know who this is, so give them the most basic permissions */
			self::$USER["type"] = "!u";
		}
	}
}