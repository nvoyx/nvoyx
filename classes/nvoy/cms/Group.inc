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
 * Collection of group bumf
 */

namespace nvoy\cms;

class Group{
	
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
	
	
	/* grab any content associated with the group / list page */
	private function _list(){
		$this->posted("list",-1);
		return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
	}

	
	/* perform any actions associated with adding a group */
	private function _add(){
		$t = self::$boot->fetch_entry("timestamp");
		self::$db->clear(array("ALL"));
		$gid = self::$db->query("INSERT","INTO `group` (`id`,`position`,`name`,`assoc`,`access`,`variants`,`nfid`,`outline`) " . 
									"VALUES (NULL,999,'{$t}','[]','a',1,0,'[]')");
		$_SESSION['notify']=array(
			'message'=>'Success: entry added',
			'type'=>'success'
		);
		self::$boot->header(array("LOCATION"=>"/settings/group/edit/{$gid}"));
	}	
	
	
	/* perform any actions and grab any content associated with the group / edit page */
	private function _edit(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3))){
			$this->posted("edit",self::$boot->fetch_entry("breadcrumb",3));
			return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
		}
	}
	
	
	/* perform any actions and grab any content associated with the group / delete page */
	private function _delete(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3))){
			return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
		}
	}

	
	/* handle posted data */
	private function posted($form,$gid){
		if($_POST){
			switch($form):
				case "list":
					$a=-1;
					foreach($_POST as $key=>$value ){
						$value = self::$boot->text($value);
						if(stristr($key,"group-")){
							$a++;
							$gid = str_replace("group-","",$key);
							self::$db->clear(array("ALL"));
							self::$db->set_filter("`group`.`id`={$gid}");
							self::$db->query("UPDATE","`group` SET `group`.`position`={$a}");
						}
					}					
					break;
					
				case "edit":
					foreach($_POST as $key=>$value ){
						$value = self::$boot->text($value);
						switch($key):
							case "fields":
								self::$db->clear(array("ALL"));
								self::$db->set_filter("`group`.`id`={$gid}");
								$outline = self::$db->query("SELECT","`group`.`outline` FROM `group`");
								$outline = self::$boot->json($outline[0]["group.outline"],"decode");
								foreach($value as $val){
									foreach($outline as $o){
										if($o["fid"]==$val){
											$noutline[]=$o;
											break;
										}
									}
								}								
								if(isset($noutline)){
									$v["outline"] = self::$boot->json($noutline,"encode");
								} else {
									$v["outline"]='[]';
								}
								break;
							
							case "assoc":
								$v["assoc"] = self::$boot->json($value,"encode");
								break;
							case "variants":
							case "access":
							case "name":
								$v[$key] = $value;
								break;
						endswitch;
					}
					if(!key_exists("assoc",$v)){$v["assoc"]="[]";}
					$fts= array("datebox",
								"filelist",
								"imagelist",
								"mselect",
								"sselect",
								"textarea",
								"textbox",
								"heirarchy",
								"tagbox");
					if(!array_key_exists("outline",$v)){
						$v["outline"]="[]";
						foreach($fts as $ft){
							self::$db->clear(array("ALL"));
							self::$db->set_filter("`{$ft}`.`gid`={$gid}");
							self::$db->query("DELETE","FROM `{$ft}`");
						}
					} else {
						foreach($fts as $ft){
							$fids = self::$boot->json($v["outline"],"decode");
							$r="";
							foreach($fids as $fid){
								if($fid["type"]==$ft){
									$r[] = $fid["fid"]; 
								}
							}
							
							if($r!=""){
								$r = "`{$ft}`.`fid` !=" . implode(" AND `{$ft}`.`fid` !=",$r);
								self::$db->clear(array("ALL"));
								self::$db->set_filter("`{$ft}`.`gid`={$gid} AND ({$r})");
								$rs = self::$db->query("SELECT","DISTINCT(`{$ft}`.`fid`) FROM `{$ft}`");
								if(isset($rs)){
									foreach($rs as $r){
										self::$db->clear(array("ALL"));
										self::$db->set_filter("`{$ft}`.`gid`={$gid} AND `{$ft}`.`fid`={$r["{$ft}.fid"]}");
										self::$db->query("DELETE","FROM `{$ft}`");
									}
								}
							}
						}
					}					
					self::$db->clear(array("ALL"));
					self::$db->set_filter("`group`.`id`={$gid}");
					self::$db->query("UPDATE","`group` SET `group`.`name`='{$v["name"]}',`group`.`assoc`='{$v["assoc"]}',`group`.`access`='{$v["access"]}',`group`.`outline`='{$v["outline"]}',`group`.`variants`='{$v["variants"]}'");
					break;
			endswitch;
			$_SESSION['notify']=array(
				'message'=>'Success: entry updated',
				'type'=>'success'
			);
		}
	}
}