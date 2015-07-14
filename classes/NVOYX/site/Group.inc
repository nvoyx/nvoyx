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

namespace NVOYX\site;

class Group{
	
	protected static $instance;
	protected static $DB;
	protected static $BOOT;
	static $GROUP = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
    public static function CONNECT($db,$boot) {
		
		/* is this the first time the class has been connected to */
        if( !self::$instance ) {
			
			/* link with public/db */
			self::$DB = $db;
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* create a new instance */
            self::$instance = new self();
        }
		
		/* return instance */
        return self::$instance;
    }
	
	
	/*
	 * FETCH_ARRAY()
	 * retrieve the group array
	 */
	public function FETCH_ARRAY(){return self::$GROUP; }
	
	
	/*
	 * BUILD_ARRAY()
	 * build an array based on the available groups
	 */
	public function BUILD_ARRAY($use_cache=true){
		
		/* should the apc user array be used to retrieve group details (if it exists) */
		if($use_cache){$cache = self::$BOOT->GET_CACHE("groups");}else{$cache=false;}
		
		/* if we don't have a cache for whatever reason */
		if(!$cache){
						
			/* fetch the group details from the database */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_ORDER(array("`group`.`position`"=>"ASC"));
			$groups = self::$DB->DB_QUERY("SELECT","* FROM `group`");
			
			/* reset the stored group variable to empty */
			self::$GROUP = array();
			
			/* if at least one group has been found */
			if(isset($groups)){
				
				/* cycle through the groups */
				foreach($groups as $g){
					
					
				/* build an array of grouops */
				self::$GROUP["id-{$g['group.id']}"] = array("id"=>$g["group.id"],
												"position"=>$g["group.position"],
												"name"=>$g["group.name"],
												"assoc"=>self::$BOOT->JSON($g["group.assoc"],"decode"),
												"access"=>$g["group.access"],
												"variants"=>$g["group.variants"],
												"nfid"=>$g["group.nfid"],
												"outline"=>self::$BOOT->JSON($g["group.outline"],"decode")
												);
				}
				
				/* store the newly created group array to the cache */
				self::$BOOT->SET_CACHE("groups",self::$GROUP);
			}
		} else {
			
			/* store the cached group array */
			self::$GROUP = $cache;
		}
	}

	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance, trigger the creation / retrieval of the group cache
	 */
	private function __construct() { $this->BUILD_ARRAY(true); }
}