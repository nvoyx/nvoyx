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
 * Collection of CMS path bumf
 */

namespace NVOYX\site;

class Path{
	
	protected static $instance;
	protected static $DB;
	static $PATH = array();
	

	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
    public static function CONNECT($db) {
		
		/* is this the first time the class has been connected to */
        if( !self::$instance ) {
			
			/* link with public/db */
			self::$DB = $db;
			
			/* create a new instance */
            self::$instance = new self();
			
        }
		
		/* return instance */
        return self::$instance;
    }
	
	
	/*
	 * FETCH_ARRAY()
	 * return the path array
	 */
	public function FETCH_ARRAY(){ if(array_keys(self::$PATH)){return self::$PATH;} }
	
	
	/*
	 * FETCH_ENTRY()
	 * return the requested path array entry
	 */
	public function FETCH_ENTRY($n){
		
		/* does the requested entry exist */
		if(isset(self::$PATH["{$n}"])){
			
			/* return it */
			return self::$PATH["{$n}"];
		} else {return false;}
	}
	
	/*
	 * BUILD_ARRAY()
	 * build an array of the individual path details
	 */
	public function BUILD_ARRAY(){
		
		/* fetch the cms paths */
		self::$DB->DB_CLEAR(array("ALL"));
		self::$DB->DB_SET_ORDER(array("`path`.`url`"=>"ASC"));
		$paths = self::$DB->DB_QUERY("SELECT","* FROM `path`");
		
		/* cycle through the paths */
		foreach($paths as $path){
			
			/* organise the path details */
			$p[$path["path.url"]] = array("id"=>$path["path.id"],
											"url"=>$path["path.url"],
											"access"=>$path["path.access"]);	
		}
		
		/* store the pathdtails */
		self::$PATH = $p;
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance and create the path array
	 */
	private function __construct() { $this->BUILD_ARRAY(); }
	
}