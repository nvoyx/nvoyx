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
 * Collection of page bumf
 */

namespace nvoy\site;

class Page{
	
	protected static $instance;
	protected static $db;
	protected static $front;
	protected static $field;
	protected static $boot;
	protected static $with_fields;
	static $page = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	
	public static function connect($nvDb,$nvFront,$nvField,$nvBoot) {
		if( !self::$instance ) {
			self::$db = $nvDb;
			self::$front = $nvFront;
			self::$field = $nvField;
			self::$boot = $nvBoot;
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
	/* return the page array (may contain one or more pages) */
	public function fetch_array(){ if(array_keys(self::$page)){return self::$page;} }
	
	
	/* return a page fromthe page array based on the requested node id */
	public function fetch_entry($n){
		if(is_numeric($n)){$n = "nid-".$n;}
		if(array_key_exists($n,self::$page)){
			return self::$page["{$n}"];
		}
	}
	
	/* should any pages added into the array contain field data */
	public function set_with_fields($enabled=true){ self::$with_fields = $enabled; }
	
	
	/* clear the page array */
	public function clear(){ self::$page=array();}
	
	/* removes the specified key and associated values from the page array */
	public function clear_entry($nid){
		unset(self::$page[$nid]);
	}
	
	
	/* Lookup page (an optionally field) data and push into the page array */
	public function find($a){
		$f = "";
		if(isset($a["FIELDS"])){$this->set_with_fields(true);} else {$this->set_with_fields(false);}
		if(isset($a["ALIAS"])){
			if($a["ALIAS"]!=""){
				$a['ALIAS'] = self::$db->escape($a['ALIAS']);
				$f.= sprintf('(`page`.`alias`="%s")',$a['ALIAS']);
			} else {
				$f.= sprintf('(`page`.`id`=%u)',self::$front);
			}
		}
		
		if(isset($a["NID"])){
			if($f!=""){$and = " AND ";} else {$and="";}
			$f.= sprintf('%s(`page`.`id`=%u)',$and,$a["NID"]);
		}
		
		if($a["USER"]=="!u"){
			if($f!=""){$and = " AND ";} else {$and="";}
			$f.="{$and}(`page`.`published`=1)";
		}
		
		if(isset($a['TIDS']) || isset($a["NID"])){
			if(isset($a['TIDS'])){
				if(!is_array($a['TIDS'])){$a['TIDS']=array($a['TIDS']);}
				for($x=0;$x<count($a['TIDS']);$x++){
					if($x==0){
						if($f!=""){$and = " AND ";} else {$and="";}
						$f.="{$and}(";
					}
					
					$a['TIDS'][$x] = self::$db->escape($a['TIDS'][$x]);
					$f.= sprintf('( `page`.`tid`=%d ) ',$a['TIDS'][$x]);
					if($x==count($a['TIDS'])-1){$f.=") ";} else {$f.="OR ";}
				}
			}
			
			self::$db->clear(array("ALL"));
			self::$db->set_filter($f);
			$pages = self::$db->query("SELECT","* FROM `page`");
			
			if(isset($pages)){
				foreach($pages as $page){
					$tpage=array();
					$page['page.modified']=date('Y-m-d H:i',strtotime($page['page.modified']));
					$page['page.date']=date('Y-m-d H:i',strtotime($page['page.date']));
					$page['page.ttp']=date('Y-m-d H:i',strtotime($page['page.ttp']));
					$page['page.ttc']=date('Y-m-d H:i',strtotime($page['page.ttc']));
					$tpage = array(
						"id"=>$page["page.id"],
						"tid"=>$page["page.tid"],
						"nvids"=>self::$boot->json($page["page.nvids"],"decode"),
						"title"=>$page["page.title"],
						"heading"=>$page["page.heading"],
						"teaser"=>$page["page.teaser"],
						"alias"=>$page["page.alias"],
						"description"=>$page["page.description"],
						"body"=>$page["page.body"],
						"importance"=>$page["page.importance"],
						"sttp"=>$page["page.sttp"],
						"sttc"=>$page["page.sttc"],
						"published"=>$page["page.published"],
						"ttp"=>$page["page.ttp"],
						"ttc"=>$page["page.ttc"],
						"date"=>$page["page.date"],
						"modified"=>$page["page.modified"],
						"by"=>$page["page.by"]
					);
					if(self::$with_fields){
						self::$page["nid-{$tpage['id']}"] = array_merge($tpage,self::$field->fetch_fields($tpage['id'],$tpage['tid']));
					} else {
						self::$page["nid-{$tpage['id']}"] = $tpage;
					}
				}
			}
		}
	}
}