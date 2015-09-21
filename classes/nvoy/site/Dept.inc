<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/** 
 * @class
 * Collection of dept bumf
 */

namespace nvoy\site;
  
class Dept{
	
	protected static $instance;
	protected static $user;
	protected static $db;
	protected static $boot;
	static $dept = array();
	
	
	/* --- PUBLIC METHODS --- */
	
		
	public static function connect($nvBoot,$nvDb,$nvUser) {
		if( !self::$instance ) {
			self::$boot = $nvBoot;
			self::$db = $nvDb;
			self::$user = $nvUser;
			self::$instance = new self();		
		}
		return self::$instance;
	}
	
	
	/* return the user array */
	public function fetch_array(){ return self::$dept;}
	
	
	/* return an entry from the dept array */
	public function fetch_entry($n){
		foreach(self::$dept as $r){
			if($r['id']==$n){
				return self::$dept[$r['name']];
			}
		}
		return false;
	}
	
	
	/* is this dept a member of the default Web department */
	public function granted($id,$tid=false){
		if($id==1){
			return true;
		}
		if($tid){
			$rs=$this->fetch_entry($id)['access'];
			if(in_array($tid,$rs)){
				return true;
			}
		}
		return false;
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	private function __construct(){
		$cache = self::$boot->get_cache("depts");
		if(!$cache){
			self::$db->clear(array("ALL"));
			self::$db->set_order(array("`dept`.`name`"=>"ASC"));
			$depts = self::$db->query("SELECT","* FROM `dept`");
			foreach($depts as $dept){
				$d[$dept["dept.name"]] = array("id"=>$dept["dept.id"],
												"name"=>$dept["dept.name"],
												"access"=>self::$boot->json($dept["dept.access"],"decode"));
			}
			self::$dept = $d;
			self::$boot->set_cache("depts",self::$dept);
		} else {
			self::$dept = $cache;
		}
	}
}