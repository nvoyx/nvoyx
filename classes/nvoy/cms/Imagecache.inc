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
 * Collection of imagecache bumf
 */

namespace nvoy\cms;

class Imagecache{
	
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
			
	
	/* perform any actions associated with the imagecache / delete page */
	private function _delete(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3))){
			$iid=self::$boot->fetch_entry("breadcrumb",3);
			self::$db->clear(array("ALL"));
			self::$db->set_filter("`imagecache`.`id`={$iid}");
			self::$db->set_limit(1);
			$imagecache = self::$db->query("SELECT","* FROM `imagecache`");
			foreach(array_diff(scandir(self::$boot->fetch_entry("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}"),array('.','..')) as $f){
				if(is_file(self::$boot->fetch_entry("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}".'/'.$f)){
					unlink(self::$boot->fetch_entry("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}".'/'.$f);
				}
			}			
			if(is_dir(self::$boot->fetch_entry("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}")){
				rmdir(self::$boot->fetch_entry("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}");
			}
			self::$db->clear(array("ALL"));
			self::$db->set_filter("`id`={$iid}");
			self::$db->query("DELETE","FROM `imagecache`");
			$_SESSION['notify']=array(
				'message'=>'Success: entry deleted',
				'type'=>'warning'
			);
			self::$boot->header(array("LOCATION"=>"/settings/imagecache/list"));
		}
	}
	
	
	/* grab any content associated with the imagecache / list page */
	private function _list(){
		return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
	}
	
	
	/* perform any actions associated with the imagecache / add page */
	private function _add(){
			$t = self::$boot->fetch_entry("timestamp");
			self::$db->clear(array("ALL"));
			$iid = self::$db->query("INSERT","INTO `imagecache` (`id`,`name`,`x`,`y`,`mime`,`effects`) " . 
										"VALUES (NULL,'{$t}',999,999,'jpg','[\"\"]')");
			mkdir(self::$boot->fetch_entry("images")."/999x999",0755);
			$_SESSION['notify']=array(
				'message'=>'Success: entry added',
				'type'=>'success'
			);
			self::$boot->header(array("LOCATION"=>"/settings/imagecache/edit/{$iid}"));
	}
	
	
	/* process and grab any content associated with the imagecache / edit page */
	private function _edit(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3))){
			$this->posted(self::$boot->fetch_entry("breadcrumb",3));
			return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
		}
	}

	
	/* handle posted data */
	private function posted($iid){
		if($_POST){
			self::$db->clear(array("ALL"));
			self::$db->set_filter("`imagecache`.`id`={$iid}");
			self::$db->set_limit(1);
			$imagecache = self::$db->query("SELECT","* FROM `imagecache`");
			foreach($_POST as $key=>$value ){
				switch($key):
					case "name":
					case "x":
					case "y":
					case "mime":
						$value = self::$boot->text($value);
						$v[$key] = $value;
						break;
					case "effects":
						$value = self::$boot->json($value,"decode");
						$value = self::$boot->text($value);
						if($value!=null){
							$v[$key] = self::$boot->json($value,"encode");
						} else {
							$v["$key"]="[\"\"]";
						}
						break;
				endswitch;
			}
			if(isset($imagecache)){
				if($imagecache[0]["imagecache.x"]!=$v["x"] || $imagecache[0]["imagecache.y"]!=$v["y"]){
					foreach(array_diff(scandir(self::$boot->fetch_entry("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}"),array('.','..')) as $f){
						if(is_file(self::$boot->fetch_entry("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}".'/'.$f)){
							unlink(self::$boot->fetch_entry("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}".'/'.$f);
						}
					}
					if(is_dir(self::$boot->fetch_entry("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}")){
						rmdir(self::$boot->fetch_entry("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}");
					}
					mkdir(self::$boot->fetch_entry("images")."/{$v["x"]}x{$v["y"]}",0755);
				}
			}
			self::$db->clear(array("ALL"));
			self::$db->set_filter("`imagecache`.`id`={$iid}");
			self::$db->query("UPDATE","`imagecache` SET `imagecache`.`name`='{$v["name"]}',`imagecache`.`x`={$v["x"]},`imagecache`.`y`={$v["y"]}, `imagecache`.`mime`='{$v['mime']}', `imagecache`.`effects`='{$v["effects"]}'");
			$_SESSION['notify']=array(
				'message'=>'Success: entry updated',
				'type'=>'success'
			);
		}
	}
}