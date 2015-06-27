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
class Block{
	
	protected static $instance;
	protected static $DB;
	protected static $BOOT;
	protected static $PAGE;
	protected static $CURRENT;
	static $BLOCK = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($db,$boot,$page,$current) {
        
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/db */
			self::$DB = $db;
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* link with public/page */
			self::$PAGE = $page;
			
			/* link with the current page */
			self::$CURRENT = $current;
			
			/* create a new instance */
			self::$instance = new self();
		}
		
		/* return instance */
		return self::$instance;
	}

	
	/*
	 * FETCH_ID()
	 * Fetch block ids, may be filtered by PAGE tid and/or USER type
	 */
	public function FETCH_ID($a = false) {
		
		/* if we have been passed a user type, split the string */
		if(isset($a["USER"])){$a["USER"] = str_split($a["USER"]);}
		
		/* cycle through the blocks */
		foreach(self::$BLOCK as $b){
			
			/* reset the not valid flag */
			$f=0;
			
			/* do we have page types */
			if(isset($a["TID"])){
				
				/* if the page type is not associated with this block, flag it */
				if(!in_array($a["TID"],$b["tid"])){$f=1;}
			}
			
			/* if we have a valid user array */
			if(isset($a["USER"])){
				
				/* if the user type is not associated with this block, flag it */
				if(!in_array($b["access"],$a["USER"])){$f=1;}
			}
			
			/* if the flag is not set, grab the block id */
			if($f==0){$block[]=$b["id"];}
		}
		
		/* if we have at least one appropriate block, return it */
		return (isset($block)) ? $block : false;
	}	

	
	/**
	 * FETCH_ARRAY()
	 * fetch the blocks array
	 */
	public function FETCH_ARRAY() { return self::$BLOCK; }
	
	
	/*
	 * FETCH_PARAMS()
	 * fetch the params associated with the specified block id
	 */
	public function FETCH_PARAMS($i) { return self::$BLOCK[$i]["params"]; }
	
	/*
	 * UPDATE_PARAMS()
	 * update the params associated with the specified block id - requires the full params array to be passed in
	 */
	public function UPDATE_PARAMS($i,$a) {
		
		/* validate the data before proceeding */
		if(is_numeric($i) && is_array($a)){
			
			/* update the local block records (held in an array) */
			self::$BLOCK[$i]["params"]=$a;
			
			/* json encode the passed array */
			$r = self::$BOOT->JSON($a,"encode");
		
			/* update the specified block details on the database */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_QUERY("UPDATE","`block` SET `block`.`params`='{$r}' WHERE `block`.`id`={$i}");
		}
	}
	
	/*
	 * LOADER()
	 * load a block
	 */
	public function LOADER($a,$i,$f="public") {
		
		/* do we have a block array */
		if(is_array($a)){
		
			/* if the passed associated blocks for this page $a, contains the requested block id $i */
			if(in_array($i,$a)) {
			
				/* test whether the requested include file exists */
				$rs = self::$BOOT->TEST_INCLUDE(array("TYPE"=>"block","FACING"=>$f,"VALUE"=>$i));
			
				/* if the file exists, return it */
				if($rs){return $rs;}
			}
		}
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance
	 */
	private function __construct() {
		
		/* try to grab the cached array of blocks */
		$cache = self::$BOOT->GET_CACHE("blocks");
		
		/* if there isn't a cached array of blocks */
		if(!$cache){
			
			/* grab the block details from the database */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_ORDER(array("`block`.`name`"=>"ASC"));
			$rs = self::$DB->DB_QUERY("SELECT","* FROM `block`");
			
			/* for each db block */
			foreach($rs as $r){
				
				
			/* build an array of page types */
			self::$BLOCK[$r["block.id"]] = array("id"=>$r["block.id"],
									"name"=>$r["block.name"],
									"tid"=>self::$BOOT->JSON($r["block.tid"],"decode"),
									"access"=>$r["block.access"],
									"params"=>self::$BOOT->JSON($r["block.params"],"decode"));
			}
			
			/* cache the block array */
			self::$BOOT->SET_CACHE("blocks",self::$BLOCK);
			
		} else {
			
			/* store the cached version of the block array */
			self::$BLOCK = $cache;
		}
	}
}