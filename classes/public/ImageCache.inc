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
class ImageCache{
	
	protected static $instance;
	protected static $DB;
	protected static $BOOT;
	static $IMAGECACHE = array();
	
	
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
	 * returns the imagecache array
	 */
	public function FETCH_ARRAY(){ return self::$IMAGECACHE; }
	
	
	/*
	 * FETCH_ENTRY()
	 * return a name specified imagecache entry
	 */
	public function FETCH_ENTRY($n){return self::$IMAGECACHE["{$n}"];}


	/*
	 * PATH()
	 * return url paths to the images
	 */
	public function PATH($icn,$ss,$i){
		$ic=self::FETCH_ENTRY($icn);
		if($ss){
			return '/settings/resources/files/images/cms/'.$ic['x'].'x'.$ic['y'].'/'.$i.'.'.$ic['mime'].' 1x, /settings/resources/files/images/cms/'.$ic['x'].'x'.$ic['y'].'/@2x.'.$i.'.'.$ic['mime'].' 2x';
		} else {
			return '/settings/resources/files/images/cms/'.$ic['x'].'x'.$ic['y'].'/'.$i.'.'.$ic['mime'];
		}
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct
	 * build an instance
	 */
	private function __construct() {
		
		/* should the apc user array be used to retrieve imagecache details (if it exists) */
		$cache = self::$BOOT->GET_CACHE("imagecache");
		
		/* if we don't have a cache for whatever reason */
		if(!$cache){
			
			/* fetch the imagecache details from the database */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_ORDER(array("`imagecache`.`name`"=>"ASC"));
			$imagecaches = self::$DB->DB_QUERY("SELECT","* FROM `imagecache`");
			
			/* if at least one imagecache has been found */
			if(isset($imagecaches)){
			
				/* cycle through the imagecaches */
				foreach($imagecaches as $imagecache){
				
					/* build a multi-dimensional array based on the imagecache data */
					$ic[$imagecache["imagecache.name"]] = array("id"=>$imagecache["imagecache.id"],
																"name"=>$imagecache["imagecache.name"],
																"x"=>$imagecache["imagecache.x"],
																"y"=>$imagecache["imagecache.y"],
																"mime"=>$imagecache["imagecache.mime"],
																"effects"=>self::$BOOT->JSON($imagecache["imagecache.effects"],"decode"));	
				}
			
				/* store the imagecache details */
				self::$IMAGECACHE = $ic;
			
				/* store the newly created imagecache array to the cache */
				self::$BOOT->SET_CACHE("imagecache",self::$IMAGECACHE);
			}
		} else {
			
			/* store the cached imagecache array */
			self::$IMAGECACHE = $cache;
		}
	}
}