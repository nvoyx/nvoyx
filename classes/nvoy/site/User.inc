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

namespace nvoy\site;

class User{
	
	protected static $instance;
	protected static $db;
	protected static $boot;
	protected static $var;
	static $user = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	
	public static function connect($nvDb,$nvBoot,$nvVar) {
		if( !self::$instance ) {
			self::$db = $nvDb;
			self::$boot = $nvBoot;
			self::$var = $nvVar;
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/* return the user array */
	public function fetch_array(){ return self::$user;	}
	
	
	/* return an entry from the user array */
	public function fetch_entry($n){ return self::$user["{$n}"]; }
	
	
	/* is this user allowed access to this file/resource/page/etc */
	public function granted($req){ if(stristr(self::$user["type"],$req)){return true;}else{return false;} }

	
	/* which permissions is this user allowed to make use of for access purposes */
	private function access($id){
		switch($id):
			case "s": return "!sacu";
			case "a": return "!acu";
			case "c": return "!cu";
			default: return "!u";
		endswitch;
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	private function __construct(){
		session_name("NVOYX");
		session_start();
		if (isset($_COOKIE['nvx_unit'])){
			if($_COOKIE['nvx_unit']!='deleted'){
				$unit_session = $_SESSION;
				$sess=file_get_contents('../session/sess_'.$_COOKIE['nvx_unit']);
				session_decode($sess);
				setcookie('nvx_unit',null,-1,'/');
				unset($_COOKIE['nvx_unit']);
				if($_COOKIE['nvx_cc']!=$_SESSION['captcha']){
					$_SESSION=$unit_session;
				}
			} else {
				unset($_COOKIE['nvx_unit']);
			}
		}
		
		if(isset($_SESSION['type']) && isset($_COOKIE['nvx_cc'])){
			if($_SESSION['captcha']!=$_COOKIE['nvx_cc']){
				self::$USER["type"] = "!u";
				setcookie('nvx_cc', null,-1,'/');
				unset($_COOKIE['nvx_cc']);
			} else {
				self::$user["id"] = $_SESSION['id'];
				self::$user["type"] =  $this->access($_SESSION['type']);
				self::$user["contact"] = $_SESSION['contact'];
				self::$user["dept"] = $_SESSION['dept'];
			}
		} else {
			self::$user["type"] = "!u";
		}
	}
}