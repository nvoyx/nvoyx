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

namespace nvoy\site;

class ImageCache{
	
	protected static $instance;
	protected static $db;
	protected static $boot;
	static $imagecache = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	
    public static function connect($nvDb,$nvBoot) {
        if( !self::$instance ) {
			self::$db = $nvDb;
			self::$boot = $nvBoot;
            self::$instance = new self();
        }
        return self::$instance;
    }

	
	/* returns the imagecache array */
	public function fetch_array(){ return self::$imagecache; }
	
	
	/* return a name specified imagecache entry */
	public function fetch_entry($n){return self::$imagecache["{$n}"];}


	/* return url paths to the images */
	public function path($icn,$ss,$i){
		$ic=self::FETCH_ENTRY($icn);
		if($ss){
			return '/settings/resources/files/images/cms/'.$ic['x'].'x'.$ic['y'].'/'.$i.'.'.$ic['mime'].' 1x, /settings/resources/files/images/cms/'.$ic['x'].'x'.$ic['y'].'/@2x.'.$i.'.'.$ic['mime'].' 2x';
		} else {
			return '/settings/resources/files/images/cms/'.$ic['x'].'x'.$ic['y'].'/'.$i.'.'.$ic['mime'];
		}
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	private function __construct() {
		$cache = self::$boot->get_cache("imagecache");
		if(!$cache){
			self::$db->clear(array("ALL"));
			self::$db->set_order(array("`imagecache`.`name`"=>"ASC"));
			$imagecaches = self::$db->query("SELECT","* FROM `imagecache`");
			if(isset($imagecaches)){
				foreach($imagecaches as $imagecache){
					$ic[$imagecache["imagecache.name"]] = array("id"=>$imagecache["imagecache.id"],
																"name"=>$imagecache["imagecache.name"],
																"x"=>$imagecache["imagecache.x"],
																"y"=>$imagecache["imagecache.y"],
																"mime"=>$imagecache["imagecache.mime"],
																"effects"=>self::$boot->json($imagecache["imagecache.effects"],"decode"));	
				}
				self::$imagecache = $ic;
				self::$boot->set_cache("imagecache",self::$imagecache);
			}
		} else {
			self::$imagecache = $cache;
		}
	}
}