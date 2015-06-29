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
  
class _Imagecache{
	
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
	 * __construct
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
	 * _delete()
	 * perform any actions associated with the imagecache / delete page
	 */
	private function _delete(){
		
		/* check that we have a numeric reference for the imagecache */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* grab the imagecache id */
			$iid=self::$BOOT->FETCH_ENTRY("breadcrumb",3);
			
			/* fetch the imagecache to be deleted */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_FILTER("`imagecache`.`id`={$iid}");
			self::$DB->DB_SET_LIMIT(1);
			$imagecache = self::$DB->DB_QUERY("SELECT","* FROM `imagecache`");
			
			/* cycle through the old imagecache folder */
			foreach(array_diff(scandir(self::$BOOT->FETCH_ENTRY("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}"),array('.','..')) as $f){
				
				/* check we have a file and not a folder */
				if(is_file(self::$BOOT->FETCH_ENTRY("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}".'/'.$f)){
					
					/* delete the file */
					unlink(self::$BOOT->FETCH_ENTRY("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}".'/'.$f);
				}
			}
			
			/* check we have a valid imagecache directory */
			if(is_dir(self::$BOOT->FETCH_ENTRY("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}")){
				
				/* delete the directory */
				rmdir(self::$BOOT->FETCH_ENTRY("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}");
			}
			
			
			/* delete the imagecache */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_FILTER("`id`={$iid}");
			self::$DB->DB_QUERY("DELETE","FROM `imagecache`");

			/* redirect to the _list */
			self::$BOOT->HEADER(array("LOCATION"=>"/settings/imagecache/list"));
		}
	}
	
	
	/*
	 * _list()
	 * grab any content associated with the imagecache / list page
	 */
	private function _list(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}
	
	
	/*
	 * _add()
	 * perform any actions associated with the imagecache / add page
	 */
	private function _add(){
		
			/* grab the current timestamp */
			$t = self::$BOOT->FETCH_ENTRY("timestamp");
			
			/* insert an empty imagecache record */
			self::$DB->DB_CLEAR(array("ALL"));
			$iid = self::$DB->DB_QUERY("INSERT","INTO `imagecache` (`id`,`name`,`x`,`y`,`mime`) " . 
										"VALUES (NULL,'{$t}',999,999,'jpg')");
										
			/* create the required 999x999 cache directory */
			mkdir(self::$BOOT->FETCH_ENTRY("images")."/999x999",0755);

			/* redirect to the new imagecache-edit */
			self::$BOOT->HEADER(array("LOCATION"=>"/settings/imagecache/edit/{$iid}"));
	}
	
	
	/*
	 * _edit()
	 * process and grab any content associated with the imagecache / edit page
	 */
	private function _edit(){
		
		/* check that we have a numeric reference for the imagecache */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* process any fields that have been posted by this page */
			$this->POSTED(self::$BOOT->FETCH_ENTRY("breadcrumb",3));
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}

	
	/*
	 * POSTED()
	 * handle posted data
	 */
	private function POSTED($iid){
		
		/* do we have posted data */
		if($_POST){
			
			/* grab the details for the current imagecache */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_FILTER("`imagecache`.`id`={$iid}");
			self::$DB->DB_SET_LIMIT(1);
			$imagecache = self::$DB->DB_QUERY("SELECT","* FROM `imagecache`");
			
			/* iterate over the posted data */
			foreach($_POST as $key=>$value ){
				
				/* sanitise the field values */
				//$value = self::$BOOT->TEXT($value);
				
				switch($key):
					case "name":
					case "x":
					case "y":
					case "mime":
						
						/* sanitise the field values */
						$value = self::$BOOT->TEXT($value);
						
						/* grab the current sanitised field value */
						$v[$key] = $value;
						break;
					case "effects":
						
						
						/* convert the posted JSON encoded string to an array */
						$value = self::$BOOT->JSON($value,"decode");
						
						/* sanitise the field values */
						$value = self::$BOOT->TEXT($value);
						
						/* if we have a an array */
						if($value!=null){
							
							/* sanitise the contents of the array */
							//$value = self::$BOOT->TEXT($value);
							
							/* JSON encode the values */
							$v[$key] = self::$BOOT->JSON($value,"encode");
						
							
						} else {
							
							/* manually create an empty JSON array */
							$v["$key"]="[\"\"]";
						}
						break;
				endswitch;
			}
			
			/* check that we found a current imagecache */
			if(isset($imagecache)){
							
				/* check whether the imagecache dims have been updated */
				if($imagecache[0]["imagecache.x"]!=$v["x"] || $imagecache[0]["imagecache.y"]!=$v["y"]){

					/* cycle through the contents in the old imagecache folder */
					foreach(array_diff(scandir(self::$BOOT->FETCH_ENTRY("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}"),array('.','..')) as $f){
						
						/* check that what we are looking at is a file */
						if(is_file(self::$BOOT->FETCH_ENTRY("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}".'/'.$f)){
							
							/* delete the file */
							unlink(self::$BOOT->FETCH_ENTRY("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}".'/'.$f);
						}
					}
			
					/* check that we have a directory for the old imagecache */
					if(is_dir(self::$BOOT->FETCH_ENTRY("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}")){
						
						/* delete the directory */
						rmdir(self::$BOOT->FETCH_ENTRY("images")."/{$imagecache[0]["imagecache.x"]}x{$imagecache[0]["imagecache.y"]}");
					}
				
					/* create the required cache directory */
					mkdir(self::$BOOT->FETCH_ENTRY("images")."/{$v["x"]}x{$v["y"]}",0755);
				}
			}

			/* update the database */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_FILTER("`imagecache`.`id`={$iid}");
			self::$DB->DB_QUERY("UPDATE","`imagecache` SET `imagecache`.`name`='{$v["name"]}',`imagecache`.`x`={$v["x"]},`imagecache`.`y`={$v["y"]}, `imagecache`.`mime`='{$v['mime']}', `imagecache`.`effects`='{$v["effects"]}'");
		}
	}
}