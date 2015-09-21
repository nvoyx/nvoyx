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

namespace nvoy\site;

class Group{
	
	protected static $instance;
	protected static $db;
	protected static $boot;
	static $group = array();
	
	
	/* --- PUBLIC METHODS --- */
	
    public static function connect($nvDb,$nvBoot) {
        if( !self::$instance ) {
			self::$db = $nvDb;
			self::$boot = $nvBoot;
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	
	/* retrieve the group array */
	public function fetch_array(){return self::$group; }
	
	
	/* build an array based on the available groups */
	public function build_array($use_cache=true){
		if($use_cache){$cache = self::$boot->get_cache("groups");}else{$cache=false;}
		if(!$cache){
			self::$db->clear(array("ALL"));
			self::$db->set_order(array("`group`.`position`"=>"ASC"));
			$groups = self::$db->query("SELECT","* FROM `group`");
			self::$group = array();
			if(isset($groups)){
				foreach($groups as $g){
				self::$group["id-{$g['group.id']}"] = array("id"=>$g["group.id"],
												"position"=>$g["group.position"],
												"name"=>$g["group.name"],
												"assoc"=>self::$boot->json($g["group.assoc"],"decode"),
												"access"=>$g["group.access"],
												"variants"=>$g["group.variants"],
												"nfid"=>$g["group.nfid"],
												"outline"=>self::$boot->json($g["group.outline"],"decode")
												);
				}
				self::$boot->set_cache("groups",self::$group);
			}
		} else {
			self::$group = $cache;
		}
	}

	
	/* --- PRIVATE METHODS --- */
	
	
	private function __construct() { $this->build_array(true); }
}