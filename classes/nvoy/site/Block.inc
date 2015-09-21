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

namespace nvoy\site;

class Block{
	
	protected static $instance;
	protected static $db;
	protected static $boot;
	protected static $page;
	protected static $current;
	static $block = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	public static function connect($nvDb,$nvBoot,$nvPage,$nvCurrent) {
		if( !self::$instance ) {
			self::$db = $nvDb;
			self::$boot = $nvBoot;
			self::$page = $nvPage;
			self::$current = $nvCurrent;
			self::$instance = new self();
		}
		return self::$instance;
	}

	
	/* Fetch block ids, may be filtered by PAGE tid and/or USER type */
	public function fetch_id($tid=false,$user=false) {
		if($user){$user = str_split($user);}
		foreach(self::$block as $b){
			$f=0;
			if($tid){if(!in_array($tid,$b["tid"])){$f=1;}}
			if($user){if(!in_array($b["access"],$user)){$f=1;}}
			if($f==0){$block[]=$b["id"];}
		}
		return (isset($block)) ? $block : false;
	}	

	
	/* fetch the blocks array */
	public function fetch_array() { return self::$block; }
	
	
	/* fetch the params associated with the specified block id */
	public function fetch_params($i) { return self::$block[$i]["params"]; }
	
	/* update the params associated with the specified block id - requires the full params array to be passed in */
	public function update_params($i,$a) {
		if(is_numeric($i) && is_array($a)){
			self::$block[$i]["params"]=$a;
			$r = self::$boot->json($a,"encode");
			self::$db->clear(array("ALL"));
			self::$db->query("UPDATE","`block` SET `block`.`params`='{$r}' WHERE `block`.`id`={$i}");
		}
	}
	
	/* load a block */
	public function loader($a,$i,$f="public") {
		if(is_array($a)){
			if(in_array($i,$a)) {
				$rs = self::$boot->test_include("block",$i,$f);
				if($rs){return $rs;}
			}
		}
	}
	
	
	/* --- PRIVATE METHODS --- */

	private function __construct() {
		$cache = self::$boot->get_cache("blocks");
		if(!$cache){
			self::$db->clear(array("ALL"));
			self::$db->set_order(array("`block`.`name`"=>"ASC"));
			$rs = self::$db->query("SELECT","* FROM `block`");
			foreach($rs as $r){
			self::$block[$r["block.id"]] = array("id"=>$r["block.id"],
									"name"=>$r["block.name"],
									"tid"=>self::$boot->json($r["block.tid"],"decode"),
									"access"=>$r["block.access"],
									"params"=>self::$boot->json($r["block.params"],"decode"));
			}
			self::$boot->set_cache("blocks",self::$block);			
		} else {
			self::$block = $cache;
		}
	}
}