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
 * Collection of block bumf
 */

namespace nvoy\cms;
  
class Block{
	
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
	
	
	/* perform any actions and grab any content associated with the block / list page */
	private function _list(){
		return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
	}
	
	
	/* perform any actions and grab any content associated with the block / add page */
	private function _add(){
		return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
	}	
	
	
	/* perform any actions and grab any content associated with the block / edit page */
	private function _edit(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3))){
			$this->posted(self::$boot->fetch_entry("breadcrumb",3));
			return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
		}
	}
	
	
	/* perform any actions and grab any content associated with the block / delete page */
	private function _delete(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3))){
			return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
		}
	}
	
	
	/* handle posted data */
	private function posted($bid){
		if($_POST){
			foreach($_POST as $key=>$value ){
				switch($key):
					case "name":
					case "access":
						$v[$key] = self::$boot->text($value);
						break;
					
					case "tid":
						$value = self::$boot->text($value);
						$v[$key] = self::$boot->json($value,"encode");
						break;
					
					case "params":
						$value = self::$boot->json($value,"decode");
						if($value!=null){
							$value = self::$boot->text($value);
							$v[$key] = self::$boot->json($value,"encode");
						} else {
							$v["$key"]="[\"\"]";
						}
						break;
				endswitch;
			}
			
			if(is_array($v)){
				self::$db->clear(array("ALL"));
				self::$db->set_filter("`block`.`id`={$bid}");
				self::$db->query("UPDATE","`block` SET `block`.`name`='{$v["name"]}',`block`.`tid`='{$v["tid"]}',`block`.`access`='{$v["access"]}',`block`.`params`='{$v["params"]}'");
				$_SESSION['notify']=array(
					'message'=>'Success: entry updated',
					'type'=>'success'
				);
			}
		}
	}
}