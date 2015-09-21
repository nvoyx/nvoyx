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

namespace nvoy\cms;

class Type{
	
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

	
	/* grab any content associated with the type / list page */
	private function _list(){
		return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
	}

	
	/* grab any content associated with the type / add page */
	private function _add(){
		return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
	}	
	
	
	/* process and grab any content associated with the type / edit page */
	private function _edit(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3))){
			$this->posted(self::$boot->fetch_entry("breadcrumb",3));
			return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
		}
	}
	
	
	/* grab any content associated with the type / delete page */
	private function _delete(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3))){
			return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
		}
	}

	
	/* handle posted data */
	private function posted($tid){
		if($_POST){
			foreach($_POST as $key=>$value ){
				switch($key):
					case "prefix":
						$v[$key] = rtrim(self::$boot->text($value), '/');
						$v[$key] = ltrim($v[$key],"/");
						break;
					case "name":
					case "parent":
					case "view":
					case "createdelete":
					case "rss":
					case "body":
					case "template":
						$v[$key] = self::$boot->text($value);
						break;

					case "tags":
						$v[$key] = str_replace(array("\r\n","\r","\n"),"*",$value);
						$v[$key] = self::$boot->text($v[$key]);
						$v[$key] = explode("*",$v[$key]);
						$v[$key] = self::$boot->json($v[$key],"encode");
						break;	
				endswitch;
			}
			if(is_array($v)){
				self::$db->clear(array("ALL"));
				self::$db->set_filter("`type`.`id`={$tid}");
				self::$db->query("UPDATE","`type` SET `type`.`name`='{$v["name"]}',`type`.`prefix`='{$v["prefix"]}',`type`.`parent`='{$v["parent"]}',`type`.`view`='{$v["view"]}',`type`.`createdelete`='{$v["createdelete"]}',`type`.`rss`='{$v["rss"]}',`type`.`body`='{$v["body"]}',`type`.`template`='{$v["template"]}',`type`.`tags`='{$v["tags"]}'");
				$_SESSION['notify']=array(
					'message'=>'Success: entry updated',
					'type'=>'success'
				);
			}
		}
	}
}