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
 * Collection of CMS redirect bumf
 */
class Redirects{
	
	protected static $instance;
	protected static $DB;
	static $REDIRECTS = array();
	

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
	 * return the redirect array
	 */
	public function FETCH_ARRAY(){ if(array_keys(self::$REDIRECTS)){return self::$REDIRECTS;} }
	
	
	/*
	 * FETCH_ENTRY()
	 * return the requested redirect array entry
	 */
	public function FETCH_ENTRY($n){
		
		/* does the requested entry exist */
		if(isset(self::$REDIRECTS["{$n}"])){
			
			/* return it */
			return self::$REDIRECTS["{$n}"];
		} else {return false;}
	}
	
	/*
	 * BUILD_ARRAY()
	 * build an array of the individual redirect details
	 */
	public function BUILD_ARRAY(){
		
		/* fetch the cms redirects */
		self::$DB->DB_CLEAR(array("ALL"));
		self::$DB->DB_SET_ORDER(array("`redirects`.`old`"=>"ASC"));
		$redirects = self::$DB->DB_QUERY("SELECT","* FROM `redirects`");
		
		/* cycle through the redirects */
		foreach($redirects as $redirect){
			
			/* organise the path details */
			$r[$redirect["redirects.old"]] = array("id"=>$redirect["redirects.id"],
											"old"=>$redirect["redirects.old"],
											"new"=>$redirect["redirects.new"]);	
		}
		
		/* store the redirect details */
		self::$REDIRECTS = $r;
	}
	
	public function RESOLVE($url){
		
		if(array_key_exists($url,self::$REDIRECTS)){
			
			$r=$this->RESOLVE(self::$REDIRECTS[$url]['new']);
			return $r;
		} else {
			return $url;
		}
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance and create the redirects array
	 */
	private function __construct() { $this->BUILD_ARRAY(); }
	
}