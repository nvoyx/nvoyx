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

namespace nvoy\site;

class Path{
	
	protected static $instance;
	protected static $db;
	static $path = array();
	

	/* --- PUBLIC METHODS --- */
	
	
    public static function connect($nvDb) {
        if( !self::$instance ) {
			self::$db = $nvDb;
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	
	/* return the path array */
	public function fetch_array(){ if(array_keys(self::$path)){return self::$path;} }
	
	
	/* return the requested path array entry */
	public function fetch_entry($n){
		if(isset(self::$path["{$n}"])){
			return self::$path["{$n}"];
		} else {return false;}
	}
	
	/* build an array of the individual path details */
	public function build_array(){
		self::$db->clear(array("ALL"));
		self::$db->set_order(array("`path`.`url`"=>"ASC"));
		$paths = self::$db->query("SELECT","* FROM `path`");
		foreach($paths as $path){
			$p[$path["path.url"]] = array("id"=>$path["path.id"],
											"url"=>$path["path.url"],
											"access"=>$path["path.access"]);	
		}
		self::$path = $p;
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	private function __construct() { $this->build_array(); }
	
}