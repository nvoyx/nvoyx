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

namespace nvoy\site;

class Variables{
	
	protected static $instance;
	protected static $db;
	protected static $boot;
	static $var = array();
	
	
	/* --- PUBLIC METHODS --- */
	
		
	public static function connect($nvDb,$nvBoot) {
		if( !self::$instance ) {
			self::$db = $nvDb;
			self::$boot = $nvBoot;
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
	/* returns the variable array */
	public function fetch_array(){ return self::$var; }
	
	
	/* returns the requested variable entry */
	public function fetch_entry($n){ return self::$var["{$n}"]["value"]; }
	
	/* returns the requested variable entry */
	public function update_entry($n,$r){
		if(array_key_exists($n,self::$var)){
			if(!is_array($r)){
				$r=array($r);
			}
			
			self::$var["{$n}"]["value"] = $r;
			self::$boot->set_cache("variables",self::$var);
			$r = self::$boot->json($r,"encode");
			self::$db->clear(array("ALL"));
			self::$db->set_filter("`variables`.`name`='$n'");
			self::$db->query("UPDATE","`variables` SET `variables`.`value`='{$r}'");
		}
	}
	
	
	/* build an array based on the available variables */
	public function build_array($use_cache=true){
		if($use_cache){$cache = self::$boot->get_cache("variables");}else{$cache=false;}
		if(!$cache){
			self::$var="";
			self::$db->clear(array("ALL"));
			self::$db->set_order(array("`variables`.`name`"=>"ASC"));
			$vars = self::$db->query("SELECT","* FROM `variables`");
			self::$var = array();
			if(isset($vars)){
				foreach($vars as $var){
					$var["variables.value"] = self::$boot->json($var["variables.value"],"decode");
					self::$var[$var["variables.name"]] = array("id"=>$var["variables.id"],"name"=>$var["variables.name"],"notes"=>$var["variables.notes"],"value"=>$var["variables.value"]);
				}
				self::$boot->set_cache("variables",self::$var);	
			}
		} else {
			self::$var = $cache;
		}
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	private function __construct() { $this->build_array(true); }

}