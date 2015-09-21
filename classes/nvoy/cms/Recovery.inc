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
 * Collection of recovery bumf
 */

namespace nvoy\cms;

class Recovery{
	
	protected static $instance;
	protected static $boot;
	protected static $db;
	protected static $user;
	protected static $group;
	protected static $dept;
	protected static $html;

	
	/* --- PUBLIC METHODS --- */
	
	
	public static function connect($nvBoot,$nvDb,$nvUser,$nvGroup,$nvDept) {
		if( !self::$instance ) {
			self::$boot = $nvBoot;
			self::$db = $nvDb;
			self::$user = $nvUser;
			self::$group = $nvGroup;
			self::$dept = $nvDept;
			self::$instance = new self();		
		}
		return self::$instance;
	}
	
	
	/* return the html associated with the url called method */
	public function fetch_html(){ return self::$html; }

	
	/* --- PRIVATE METHODS --- */
	
	
	private function __construct() {
		$rs = "_".strtolower(self::$boot->fetch_entry("breadcrumb",2));
		if(method_exists(get_class(),$rs)){
			self::$html=$this->$rs();
		}
	}
		

	/* list all previously deleted nodes */
	private function _list(){
		return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
	}
		
	
	/* restore the node */
	private function _restore(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3)) && is_numeric(self::$boot->fetch_entry("breadcrumb",4))){
			return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
		}
	}
}