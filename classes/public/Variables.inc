<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * @class
 * Collection of variable bumf
 */
class Variables{
	
	protected static $instance;
	protected static $DB;
	protected static $BOOT;
	static $VAR = array();
	
	
	/* --- PUBLIC METHODS --- */
	
		
    /*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($db,$boot) {
		if( !self::$instance ) {
			self::$DB = $db;
			self::$BOOT = $boot;
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
	/*
	 * FETCH_ARRAY()
	 * returns the variable array
	 */
	public function FETCH_ARRAY(){ return self::$VAR; }
	
	
	/*
	 * FETCH_ENTRY()
	 * returns the requested variable entry
	 */
	public function FETCH_ENTRY($n){ return self::$VAR["{$n}"]["value"]; }
	
	/*
	 * UPDATE_ENTRY()
	 * returns the requested variable entry
	 */
	public function UPDATE_ENTRY($n,$r){
		
		/* does the variable exist */
		if(array_key_exists($n,self::$VAR)){
			
			/* have we not been passed an array */
			if(!is_array($r)){
				
				/* convert to an array */
				$r=array($r);
			}
			
			/* update the stored value */
			self::$VAR["{$n}"]["value"] = $r;
						
			/* store the updated variable array to the cache */
			self::$BOOT->SET_CACHE("variables",self::$VAR);

			/* json encode the array */
			$r = self::$BOOT->JSON($r,"encode");
			
			/* update the variables table */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_FILTER("`variables`.`name`='$n'");
			$vars = self::$DB->DB_QUERY("UPDATE","`variables` SET `variables`.`value`='{$r}'");
		}
	}
	
	
	/*
	 * BUILD_ARRAY()
	 * build an array based on the available variables
	 */
	public function BUILD_ARRAY($use_cache=true){
		
		/* should the apc user array be used to retrieve variable details (if it exists) */
		if($use_cache){$cache = self::$BOOT->GET_CACHE("variables");}else{$cache=false;}
		
		/* if we don't have a cache for whatever reason */
		if(!$cache){
			
			/* reset the variable array */
			self::$VAR="";
			
			/* fetch the variables */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_ORDER(array("`variables`.`name`"=>"ASC"));
			$vars = self::$DB->DB_QUERY("SELECT","* FROM `variables`");
			
			/* reset the stored group variable to empty */
			self::$VAR = array();
			
			/* if at least one variable has been founrd */
			if(isset($vars)){
			
				/* cycle through the variables */
				foreach($vars as $var){
				
					/* convert the variable to an array */
					$var["variables.value"] = self::$BOOT->JSON($var["variables.value"],"decode");
				
					/* store the variable */
					self::$VAR[$var["variables.name"]] = array("id"=>$var["variables.id"],"name"=>$var["variables.name"],"notes"=>$var["variables.notes"],"value"=>$var["variables.value"]);
				}
			
				/* store the newly created variable array to the cache */
				self::$BOOT->SET_CACHE("variables",self::$VAR);	
			}
			
		} else {
			
			/* retrieve the variable cache */
			self::$VAR = $cache;
		}
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance, trigger the creation / retrieval of the variable cache
	 */
	private function __construct() { $this->BUILD_ARRAY(true); }

}