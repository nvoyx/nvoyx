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
 * Collection of filelist bumf
 */

namespace NVOYX\cms;

class Filelist{
	
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
	 * _construct()
	 * build an instance
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
	 * _add()
	 * perform any actions associated with adding a fileist
	 */
	private function _add(){
		
		/* check that we have numeric references for the group and the new field id */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3)) && is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",4))){
			
			/* grab the group id */
			$gid = self::$BOOT->FETCH_ENTRY("breadcrumb",3);
			
			/* grab the field id */
			$fid = self::$BOOT->FETCH_ENTRY("breadcrumb",4);
			
			/* set the next field */
			$nfid = $fid+1;
			
			/* grab current details for this group */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_FILTER("`group`.`id`={$gid}");
			self::$DB->DB_SET_LIMIT(1);
			$group = self::$DB->DB_QUERY("SELECT","* FROM `group`");
						
			/* convert the outline from JSON to an array */
			$outline = self::$BOOT->JSON($group[0]["group.outline"],"decode");
			
			/* tag the new field onto the group outline array */
			$outline[] = array("fid"=>$fid,
							"type"=>"filelist",
							"name"=>self::$BOOT->FETCH_ENTRY("timestamp"),
							"total"=>0,
							"filetypes"=>array("image/jpeg","image/png","image/gif","application/zip"),
							"size"=>4096
							);
			
			/* JSON encode the outline array */
			$outline = self::$BOOT->JSON($outline,"encode");

			/* update the database with the new array and the iterated next-field-id */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_FILTER("`group`.`id`={$gid}");
			self::$DB->DB_QUERY("UPDATE","`group` SET `group`.`outline`='{$outline}', `group`.`nfid`={$nfid}");
			
			/* goto the edit page for the new field */
			self::$BOOT->HEADER(array("LOCATION"=>"/settings/filelist/edit/{$gid}/{$fid}"));
		}
	}

	
	/*
	 * _edit()
	 * perform any actions and grab any content associated with the filelist / edit page
	 */
	public function _edit(){
		
		/* check that we have numeric references for the group and the field id */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3)) && is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",4))){
			
			/* process any fields that have been posted by this page */
			$this->POSTED(self::$BOOT->FETCH_ENTRY("breadcrumb",3),self::$BOOT->FETCH_ENTRY("breadcrumb",4));
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * POSTED()
	 * handle posted data
	 */
	private function POSTED($gid,$fid){
		
		/* do we have posted data */
		if($_POST){
			
			/* grab the details for the current group */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_FILTER("`group`.`id`={$gid}");
			self::$DB->DB_SET_LIMIT(1);
			$group = self::$DB->DB_QUERY("SELECT","* FROM `group`");
			
			/* convert the outline from JSON to an array */
			$outline = self::$BOOT->JSON($group[0]["group.outline"],"decode");
			
			/* use $a to store the array index we are interested in updating */
			for($a=0;$a<count($outline);$a++){if($outline[$a]["fid"]==$fid){break;}}
			
			/* iterate over the posted data and update the relevant fields in the temporary outline array */
			foreach($_POST as $key=>$value ){
				
				/* convert the fieldtypes field value to an array */
				if($key=="filetypes"){$value = self::$BOOT->JSON($value,"decode");}
				
				/* sanitise the field values */
				$value = self::$BOOT->TEXT($value);
				
				switch($key):
					case "name":
					case "total":
					case "size":
					case "filetypes":
						
						/* add the sanitised field value into the group outline array */
						$outline[$a][$key] = $value;
						break;
				endswitch;
			}
			
			/* the temporary array has been updated, convert to JSON */
			$outline = self::$BOOT->JSON($outline,"encode");
			
			/* update the database */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_FILTER("`group`.`id`={$gid}");
			self::$DB->DB_QUERY("UPDATE","`group` SET `group`.`outline`='{$outline}'");
		}
	}
}