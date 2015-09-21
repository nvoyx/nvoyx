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
 * Collection of field bumf
 */

namespace nvoy\site;

class Field{
	
	protected static $instance;
	protected static $db;
	protected static $group;
	protected static $boot;
	static $field = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	
    public static function connect($nvDb,$nvGroup,$nvBoot) {
		if( !self::$instance ) {
			self::$db = $nvDb;
			self::$group = $nvGroup;
			self::$boot = $nvBoot;
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	
	/* grab the actual field values for a given page */
	public function fetch_entry($a,$gid,$vid,$fid,$iteration){
		if(isset($a["gid-{$gid}"]["vid-{$vid}"]["fid-{$fid}"][$iteration])){
			return $a["gid-{$gid}"]["vid-{$vid}"]["fid-{$fid}"][$iteration];
		}
	}	
	
	/* takes a page array and adds any associated fields - need to remove the passing of the page array (this could get massive) */
	public function fetch_fields($nid,$tid) {
		$page=array();
		foreach(self::$group->fetch_array() as $g){
			if(!in_array($tid,$g["assoc"])){continue;}

			foreach($g["outline"] as $o){
				self::$db->clear(array("ALL"));
				self::$db->set_filter("`{$o['type']}`.`gid`={$g['id']} AND `{$o['type']}`.`nid`={$nid} AND `{$o['type']}`.`fid`={$o['fid']}");
				self::$db->set_order(array("{$o['type']}.pid"=>"ASC"));
				$rs = self::$db->query("SELECT", "* FROM `{$o['type']}`");
				if(!$rs){continue;}

				foreach($rs as $r){
					switch($o["type"]):
						
						case "ajaxbox":
							$ajax = self::$boot->json($r["ajaxbox.values"]);
							$page["gid-{$g['id']}"]["vid-{$r['ajaxbox.vid']}"]["fid-{$o['fid']}"][0]["results"] = $ajax;
							break;
						
						case "imagelist":
							$imgs = self::$boot->json($r["imagelist.values"]);
							$x=0;
							$page["gid-{$g['id']}"]["vid-{$r['imagelist.vid']}"]["fid-{$o['fid']}"] = array();
							foreach($imgs as $i){
								$page["gid-{$g['id']}"]["vid-{$r['imagelist.vid']}"]["fid-{$o['fid']}"][$x]["name"] = str_replace(".webp","",$i["name"]);
								$page["gid-{$g['id']}"]["vid-{$r['imagelist.vid']}"]["fid-{$o['fid']}"][$x]["desc"] = $i["desc"];
								if(array_key_exists("link",$i)){
									$page["gid-{$g['id']}"]["vid-{$r['imagelist.vid']}"]["fid-{$o['fid']}"][$x]["link"] = $i["link"];
								}
								if(array_key_exists("text",$i)){
									$page["gid-{$g['id']}"]["vid-{$r['imagelist.vid']}"]["fid-{$o['fid']}"][$x]["text"] = $i["text"];
								}

								$x++;		
							}
							break;

						case "filelist":

							$files = self::$boot->json($r["filelist.values"]);
							$x=0;
							$page["gid-{$g['id']}"]["vid-{$r['filelist.vid']}"]["fid-{$o['fid']}"] = array();
							foreach($files as $f){
								$page["gid-{$g['id']}"]["vid-{$r['filelist.vid']}"]["fid-{$o['fid']}"][$x]["name"] = $f["name"];
								$page["gid-{$g['id']}"]["vid-{$r['filelist.vid']}"]["fid-{$o['fid']}"][$x]["desc"] = $f["desc"];
								$page["gid-{$g['id']}"]["vid-{$r['filelist.vid']}"]["fid-{$o['fid']}"][$x]["type"] = $f["type"];
								$page["gid-{$g['id']}"]["vid-{$r['filelist.vid']}"]["fid-{$o['fid']}"][$x]["size"] = $f["size"];
								$x++;
							}
							break;

						case "datebox":

							$dates = self::$boot->json($r["datebox.values"]);
							if(empty($dates[0]["finish"])){$dates[0]["finish"]="";}
							$page["gid-{$g['id']}"]["vid-{$r['datebox.vid']}"]["fid-{$o['fid']}"][0]["start"] = $dates[0]["start"];
							$page["gid-{$g['id']}"]["vid-{$r['datebox.vid']}"]["fid-{$o['fid']}"][0]["finish"] = $dates[0]["finish"];
							break;

						case "textbox":

							$page["gid-{$g['id']}"]["vid-{$r['textbox.vid']}"]["fid-{$o['fid']}"][0]["text"] = $r["textbox.values"];
							break;

						case "textarea":
							if($o["plain"]==1){$r["textarea.values"] = str_replace("<br>","[format:newline]",$r['textarea.values']);}
							$page["gid-{$g['id']}"]["vid-{$r['textarea.vid']}"]["fid-{$o['fid']}"][0]["text"] = $r["textarea.values"];
							break;

						case "sselect":

							$page["gid-{$g['id']}"]["vid-{$r['sselect.vid']}"]["fid-{$o['fid']}"][0]["selected"] = $r["sselect.values"];
							break;

						case "mselect":
							$selections = self::$boot->json($r["mselect.values"]);
							$x=0;
							$page["gid-{$g['id']}"]["vid-{$r['mselect.vid']}"]["fid-{$o['fid']}"] = array();
							foreach($selections as $s){
								$page["gid-{$g['id']}"]["vid-{$r['mselect.vid']}"]["fid-{$o['fid']}"][$x]["selected"] = $s;
								$x++;
							}
							break;

						case "heirarchy":
							$parents = self::$boot->json($r["heirarchy.values"]);
							$x=0;
							$page["gid-{$g['id']}"]["vid-{$r['heirarchy.vid']}"]["fid-{$o['fid']}"] = array();
							foreach($parents as $p){
								$page["gid-{$g['id']}"]["vid-{$r['heirarchy.vid']}"]["fid-{$o['fid']}"][$x]["parent"] = $p;
								$x++;
							}
							break;

							case "tagbox":
							$tags = self::$boot->json($r["tagbox.values"]);
							$x=0;
							$page["gid-{$g['id']}"]["vid-{$r['tagbox.vid']}"]["fid-{$o['fid']}"] = array();
							foreach($tags as $t){
								$page["gid-{$g['id']}"]["vid-{$r['tagbox.vid']}"]["fid-{$o['fid']}"][$x]["tag"] = $t;
								$x++;
							}
							break;

					endswitch;
				}
			}
		}
		return $page;
	}
}