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

namespace NVOYX\site;

class User{
	
	protected static $instance;
	protected static $DB;
	protected static $BOOT;
	protected static $VAR;
	static $USER = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($db,$boot,$var) {
        
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/db */
			self::$DB = $db;
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* link with public/var */
			self::$VAR = $var;
			
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
			
			case "s": return "!sacu";
			case "a": return "!acu";
			case "c": return "!cu";
			default: return "!u";
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
				
		/* have we received the nvx_unit cookie */
		if (isset($_COOKIE['nvx_unit'])){

			if($_COOKIE['nvx_unit']!='deleted'){
			
				/* grab a copy of the session data assigned to the unit test */
				$unit_session = $_SESSION;

				/* grab the session data from the passed reference - if genuine, this will be the superuser */
				$sess=file_get_contents('../session/sess_'.$_COOKIE['nvx_unit']);

				/* decode the alternate session data */
				session_decode($sess);

				/* unset the cookie unit code - we only need to synchronise these once */
				setcookie('nvx_unit',null,-1,'/');
				unset($_COOKIE['nvx_unit']);

				/* a captcha code cookie should also be present - compare this against the superuser */
				if($_COOKIE['nvx_cc']!=$_SESSION['captcha']){

					/* it didn't match, so revert to the original unit session - possible break-in attempt */
					$_SESSION=$unit_session;
				}
			} else {
				unset($_COOKIE['nvx_unit']);
			}
		}
		
		/* do we already have a session type and is the captcha check cookie present */
		if(isset($_SESSION['type']) && isset($_COOKIE['nvx_cc'])){
			
			
			/* compare the session captcha against the cookie captcha code */
			if($_SESSION['captcha']!=$_COOKIE['nvx_cc']){
				
				/* NOTE - we dont reset the session data as a valid user may be using this session also */
				
				/* this is a break-in attempt, so push the most basic permissions */
				self::$USER["type"] = "!u";
				
				/* unset the cookie captcha code */
				setcookie('nvx_cc', null,-1,'/');
				unset($_COOKIE['nvx_cc']);
			
			} else {
			
				/* grab the session id */
				self::$USER["id"] = $_SESSION['id'];
			
				/* grab the user access levels */
				self::$USER["type"] =  $this->ACCESS($_SESSION['type']);
			
				/* grab the user contact details */
				self::$USER["contact"] = $_SESSION['contact'];
			
				/* grab the user dept reference */
				self::$USER["dept"] = $_SESSION['dept'];
			}
		} else {
			
			/* don't know who this is, so give them the most basic permissions */
			self::$USER["type"] = "!u";
		}
	}
}