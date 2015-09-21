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
 * Collection of textbox bumf
 */

namespace nvoy\cms;

class Textarea{
	
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
	
	
	/* perform any actions associated with adding a textarea */
	private function _add(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3)) && is_numeric(self::$boot->fetch_entry("breadcrumb",4))){
			$gid = self::$boot->fetch_entry("breadcrumb",3);
			$fid = self::$boot->fetch_entry("breadcrumb",4);
			$nfid = $fid+1;
			self::$db->clear(array("ALL"));
			self::$db->set_filter("`group`.`id`={$gid}");
			self::$db->set_limit(1);
			$group = self::$db->query("SELECT","* FROM `group`");
			$outline = self::$boot->json($group[0]["group.outline"],"decode");
			$outline[] = array("fid"=>$fid,
							"type"=>"textarea",
							"name"=>self::$boot->fetch_entry("timestamp"),
							"plain"=>1,
							"editor"=>"",
							"maxlength"=>16777215
							);
			$outline = self::$boot->json($outline,"encode");
			self::$db->clear(array("ALL"));
			self::$db->set_filter("`group`.`id`={$gid}");
			self::$db->query("UPDATE","`group` SET `group`.`outline`='{$outline}', `group`.`nfid`={$nfid}");
			$_SESSION['notify']=array(
				'message'=>'Success: entry added',
				'type'=>'success'
			);
			self::$boot->header(array("LOCATION"=>"/settings/textarea/edit/{$gid}/{$fid}"));
		}
	}

	
	/* perform any actions and grab any content associated with the textarea / edit page */
	private function _edit(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3)) && is_numeric(self::$boot->fetch_entry("breadcrumb",4))){
			$this->posted(self::$boot->fetch_entry("breadcrumb",3),self::$boot->fetch_entry("breadcrumb",4));
			return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
		}
	}
	
	
	/* handle posted data */
	private function posted($gid,$fid){
		if($_POST){
			self::$db->clear(array("ALL"));
			self::$db->set_filter("`group`.`id`={$gid}");
			self::$db->set_limit(1);
			$group = self::$db->query("SELECT","* FROM `group`");			
			$outline = self::$boot->json($group[0]["group.outline"],"decode");
			for($a=0;$a<count($outline);$a++){if($outline[$a]["fid"]==$fid){break;}}
			foreach($_POST as $key=>$value ){
				$value = self::$boot->text($value);
				switch($key):
					case "name":
					case "plain":
					case "editor":
					case "maxlength":
						$outline[$a][$key] = $value;
						break;
				endswitch;
			}
			
			$outline = self::$boot->json($outline,"encode");
			self::$db->clear(array("ALL"));
			self::$db->set_filter("`group`.`id`={$gid}");
			self::$db->query("UPDATE","`group` SET `group`.`outline`='{$outline}'");
			$_SESSION['notify']=array(
				'message'=>'Success: entry updated',
				'type'=>'success'
			);
		}
	}
}