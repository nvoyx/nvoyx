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

namespace nvoy\site;

class Redirects{
	
	protected static $instance;
	protected static $db;
	static $redirects = array();
	

	/* --- PUBLIC METHODS --- */
	
	
    public static function connect($nvDb) {
        if( !self::$instance ) {
			self::$db = $nvDb;
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	
	/* return the redirect array */
	public function fetch_array(){ if(array_keys(self::$redirects)){return self::$redirects;} }
	
	
	/* return the requested redirect array entry */
	public function fetch_entry($n){
		if(isset(self::$redirects["{$n}"])){
			return self::$redirects["{$n}"];
		} else {return false;}
	}
	
	/* build an array of the individual redirect details */
	public function build_array(){
		self::$db->clear(array("ALL"));
		self::$db->set_order(array("`redirects`.`old`"=>"ASC"));
		$redirects = self::$db->query("SELECT","* FROM `redirects`");
		$r=array();
		if($redirects){
			foreach($redirects as $redirect){
				$r[$redirect["redirects.old"]] = array("id"=>$redirect["redirects.id"],
												"old"=>$redirect["redirects.old"],
												"new"=>$redirect["redirects.new"]);	
			}
		}
		self::$redirects = $r;
	}
	
	/* does the requested missing page have an associated redirect */
	public function resolve($url){
		if(array_key_exists($url,self::$redirects)){
			$r=$this->resolve(self::$redirects[$url]['new']);
			return $r;
		} else {
			return $url;
		}
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	private function __construct() { $this->build_array(); }
}