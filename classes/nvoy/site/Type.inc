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
 * Collection of type bumf
 */

namespace nvoy\site;

class Type{
	
	protected static $instance;
	protected static $db;
	protected static $boot;
	protected static $front;
	static $type = array();

	
	/* --- PUBLIC METHODS --- */
	
	
    public static function connect($nvDb,$nvBoot,$nvFront) {
		if( !self::$instance ) {
			self::$db = $nvDb;
			self::$boot = $nvBoot;
			self::$front = $nvFront;
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	
	/* return the page type array */
	public function fetch_array(){ return self::$type; }

	
	/* return a specific page type by type name */
	public function fetch_entry($n){
		if(array_key_exists($n,self::$type)){
			return self::$type["{$n}"];
		}
	}
	
	/* returns a specific page type by type id */
	public function fetch_by_tid($n){
		if(is_numeric($n)){
			foreach(self::$type as $type){
				if($type["id"]==$n){
					return $type;	
				}
			}
		}
	}
	
	 /* returns an array of possible type matches based on the requested URL / Node ID (NID) */
	public function fetch_matches($user,$url=false,$nid=false){		
		if(($url && $url[0]=="") || $nid){
			if(!$nid){$nid = self::$front;}
			self::$db->clear(array("ALL"));
			self::$db->set_filter("`page`.`id`={$nid}");
			$ptids = self::$db->query("SELECT","`page`.`tid` FROM `page`");
			if(isset($ptids)){
				$ptids = array($ptids[0]["page.tid"]);
			}
		} else {
			foreach(self::$type as $type){
				if(stristr($user,$type['view'])){
					$ptids[] = $type["id"];
				}
			}
		}
		return (isset($ptids)) ? $ptids : false;
	}
	
	
	/* resolve any tags associated with a type prefix */
	public function prefixer($a){
		$prefix = self::fetch_by_tid($a["tid"])["prefix"];
		if($prefix!=""){
			if(stristr($prefix,"[")){
					if(stristr($prefix,"[cd:")){
						$tag =  substr($prefix,strpos($prefix,'[cd:')+4,strpos($prefix,']') - (strpos($prefix,'[cd:')+4)   );
						$rtag = date($tag,strtotime($a["date"]));
						$prefix = str_replace("[cd:".$tag."]",$rtag,$prefix);
					}
					
					if(stristr($prefix,"[id:")){
						$prefix = str_replace("[id:]",$a["id"],$prefix);
					}
					
					if(stristr($prefix,"[ss:")){
						$tag = explode("-",substr($prefix,strpos($prefix,'[ss:')+4,strpos($prefix,']') - (strpos($prefix,'[ss:')+4)   ));
						self::$db->clear(array("ALL"));
						self::$db->set_filter("`sselect`.`nid`={$a['id']} AND `sselect`.`gid`={$tag[0]} AND `sselect`.`vid`={$tag[1]} AND `sselect`.`fid`={$tag[2]}");
						$rtag = self::$db->query("SELECT","`sselect`.`values` FROM `sselect`");
						if($rtag){
							$prefix= str_replace("[ss:".implode("-",$tag)."]",self::$boot->alias($rtag[0]['sselect.values']),$prefix);
						} 
					}

					if(stristr($prefix,"[ms:")){
						$tag = explode("-",substr($prefix,strpos($prefix,'[ms:')+4,strpos($prefix,']') - (strpos($prefix,'[ms:')+4)   ));
						self::$db->clear(array("ALL"));
						self::$db->set_filter("`mselect`.`nid`={$a['id']} AND `mselect`.`gid`={$tag[0]} AND `mselect`.`vid`={$tag[1]} AND `mselect`.`fid`={$tag[2]}");
						$rtag = self::$db->query("SELECT","`mselect`.`values` FROM `mselect`");
						if($rtag){
							$rtag = self::$boot->json($rtag[0]['mselect.values'],"decode");
							$prefix = str_replace("[ms:".implode("-",$tag)."]",self::$boot->alias($rtag[0]),$prefix);
						} 
					}

					return $prefix;
			} else {
				return $prefix;
			}
		} else {
			return false;
		}
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	private function __construct() {
		$cache = self::$boot->get_cache("types");
		if(!$cache){
			self::$db->clear(array("ALL"));
			self::$db->set_order(array("`type`.`name`"=>"ASC"));
			$types = self::$db->query("SELECT","* FROM `type`");
			foreach($types as $type){
				$t[$type["type.name"]] = array("id"=>$type["type.id"],
												"name"=>$type["type.name"],
												"parent"=>$type["type.parent"],
												"prefix"=>$type["type.prefix"],
												"view"=>$type["type.view"],
												"createdelete"=>$type["type.createdelete"],
												"rss"=>$type["type.rss"],
												"body"=>$type["type.body"],
												"template"=>$type["type.template"],
												"tags"=>self::$boot->json($type["type.tags"],"decode"));
			}
			self::$type = $t;
			self::$boot->set_cache("types",self::$type);
		} else {
			self::$type = $cache;
		}
	}
}