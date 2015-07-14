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

namespace NVOYX\cms;

class Group{
	
	protected static $instance;
	protected static $BOOT;
	protected static $DB;
	protected static $USER;
	protected static $GROUP;
	protected static $DEPT;
	protected static $HTML;

	
	/* --- PUBLIC METHODS --- */
	
		
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($boot,$db,$user,$group,$dept) {
		
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* link with public/db */
			self::$DB = $db;
			
			/* link with public/user */
			self::$USER = $user;
			
			/* link with public/group */
			self::$GROUP = $group;
			
			/* link with public/dept */
			self::$DEPT = $dept;
			
			/* create a new instance */
			self::$instance = new self();		
		}
		
		/* return instance */
		return self::$instance;
	}
	
	
	/*
	 * FETCH_HTML()
	 * return the html associated with the url called method
	 */
	public function FETCH_HTML(){ return self::$HTML; }
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance 
	 * 
	 */
	private function __construct() {
		
		/* use the requested url to dictate which method to run */
		$rs = "_".strtolower(self::$BOOT->FETCH_ENTRY("breadcrumb",2));
		
		/* does the requested method exists */
		if(method_exists(get_class(),$rs)){
			
			/* store the output of the requested method */
			self::$HTML=$this->$rs();
		}
	}
	
	
	/*
	 * _list()
	 * grab any content associated with the group / list page
	 */
	private function _list(){
		
		/* process any fields that have been posted by this page (pass a negative value for the group id) */
		$this->POSTED("list",-1);
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}

	
	/*
	 * _add()
	 * perform any actions associated with adding a group
	 */
	private function _add(){
		
		/* grab the current timestamp */
		$t = self::$BOOT->FETCH_ENTRY("timestamp");
		
		/* add a blank group entry */
		self::$DB->CLEAR(array("ALL"));
		$gid = self::$DB->QUERY("INSERT","INTO `group` (`id`,`position`,`name`,`assoc`,`access`,`variants`,`nfid`,`outline`) " . 
									"VALUES (NULL,999,'{$t}','[]','a',1,0,'[]')");
									
		/* redirect to the new group-edit */
		self::$BOOT->HEADER(array("LOCATION"=>"/settings/group/edit/{$gid}"));
	}	
	
	
	/*
	 * _edit()
	 * perform any actions and grab any content associated with the group / edit page
	 */
	private function _edit(){
		
		/* check that we have a numeric reference for the group */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* process any fields that have been posted by this page */
			$this->POSTED("edit",self::$BOOT->FETCH_ENTRY("breadcrumb",3));
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * _delete()
	 * perform any actions and grab any content associated with the group / delete page
	 */
	private function _delete(){
		
		/* check that we have a numeric reference for the group */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}

	
	/*
	 * POSTED()
	 * handle posted data
	 */
	private function POSTED($form,$gid){
		
		/* do we have posted data */
		if($_POST){
			
			/* which page has sent us this form data */
			switch($form):
				case "list":
					
					/* group position counter */
					$a=-1;
					
					/* cycle through the list of groups */
					foreach($_POST as $key=>$value ){
						
						/* sanitise the field values */
						$value = self::$BOOT->TEXT($value);
						
						/* check we have a group reference */
						if(stristr($key,"group-")){
							
							/* increment the group position counter */
							$a++;
							
							/* grab the group id */
							$gid = str_replace("group-","",$key);
							
							/* update the group database */
							self::$DB->CLEAR(array("ALL"));
							self::$DB->SET_FILTER("`group`.`id`={$gid}");
							self::$DB->QUERY("UPDATE","`group` SET `group`.`position`={$a}");
						}
					}					
					break;
					
				case "edit":
					
					/* grab and process the form data */
					foreach($_POST as $key=>$value ){
					
						/* sanitise the field values */
						$value = self::$BOOT->TEXT($value);
						
						switch($key):
							case "fields":
								
								/* grab the group outline for this gid */
								self::$DB->CLEAR(array("ALL"));
								self::$DB->SET_FILTER("`group`.`id`={$gid}");
								$outline = self::$DB->QUERY("SELECT","`group`.`outline` FROM `group`");
								
								/* convert the JSON encoded group outline to an array */
								$outline = self::$BOOT->JSON($outline[0]["group.outline"],"decode");
								
								/* cycle through the value array */
								foreach($value as $val){
								
									/* cycle through the field outlines */
									foreach($outline as $o){
									
										/* if the field id is equal to the posted fields id */
										if($o["fid"]==$val){
										
											/* grab the field outline */
											$noutline[]=$o;
											break;
										}
									}
								}
								
								/* JSON encode the field outlines */
								
								if(isset($noutline)){
									$v["outline"] = self::$BOOT->JSON($noutline,"encode");
								} else {
									$v["outline"]='[]';
								}
								
								break;
							
								
							case "assoc":
								
								/* encode and grab the posted data */
								$v["assoc"] = self::$BOOT->JSON($value,"encode");
								break;
							
							
							case "variants":
							case "access":
							case "name":
								
								/* grab the current field value */
								$v[$key] = $value;
								break;
						endswitch;
					}
					
					/* assoc might be blank, which is no good, so push an empty array in */
					if(!key_exists("assoc",$v)){$v["assoc"]="[]";}
					
					/* array of field types */
					$fts= array("datebox",
								"filelist",
								"imagelist",
								"mselect",
								"sselect",
								"textarea",
								"textbox",
								"heirarchy",
								"tagbox");
					
					/* is the group outline blank */
					if(!array_key_exists("outline",$v)){
						
						/* push an empty array */
						$v["outline"]="[]";
						
						/* cycle over the field types */
						foreach($fts as $ft){
							
							/* we can delete all references to this group in all the field types */
							self::$DB->CLEAR(array("ALL"));
							self::$DB->SET_FILTER("`{$ft}`.`gid`={$gid}");
							self::$DB->QUERY("DELETE","FROM `{$ft}`");
						}
						
					} else {
						
						/* cycle over the fields */
						foreach($fts as $ft){
							
							/* grab the field outlines */
							$fids = self::$BOOT->JSON($v["outline"],"decode");
							
							/* reset the results variable */
							$r="";
							
							/* cycle through the field outlines */
							foreach($fids as $fid){
								
								/* if the current field outline equals the current field type array */
								if($fid["type"]==$ft){
									
									/* add the field id to the results array */
									$r[] = $fid["fid"]; 
								}
							}
							
							/* if we have results */
							if($r!=""){
								
								/* build a filter to select distinct rows from the field type data matching group and field type */
								$r = "`{$ft}`.`fid` !=" . implode(" AND `{$ft}`.`fid` !=",$r);
								self::$DB->CLEAR(array("ALL"));
								self::$DB->SET_FILTER("`{$ft}`.`gid`={$gid} AND ({$r})");
								$rs = self::$DB->QUERY("SELECT","DISTINCT(`{$ft}`.`fid`) FROM `{$ft}`");
								
								/* have we found any distinct entries */
								if(isset($rs)){
									
									/* cycle through the entries */
									foreach($rs as $r){
										
										/* delete all field type entries matching this group and field id */
										self::$DB->CLEAR(array("ALL"));
										self::$DB->SET_FILTER("`{$ft}`.`gid`={$gid} AND `{$ft}`.`fid`={$r["{$ft}.fid"]}");
										self::$DB->QUERY("DELETE","FROM `{$ft}`");
									}
								}
							}
						}
					}
					
					/* update the group table */
					self::$DB->CLEAR(array("ALL"));
					self::$DB->SET_FILTER("`group`.`id`={$gid}");
					self::$DB->QUERY("UPDATE","`group` SET `group`.`name`='{$v["name"]}',`group`.`assoc`='{$v["assoc"]}',`group`.`access`='{$v["access"]}',`group`.`outline`='{$v["outline"]}',`group`.`variants`='{$v["variants"]}'");
					break;
			endswitch;			
		}
	}
}