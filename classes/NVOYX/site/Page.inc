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

namespace NVOYX\site;

class Page{
	
	protected static $instance;
	protected static $DB;
	protected static $FRONT;
	protected static $FIELD;
	protected static $BOOT;
	protected static $with_fields;
	static $PAGE = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($db,$front,$field,$boot) {
        
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/db */
			self::$DB = $db;
			
			/* id of the homepage */
			self::$FRONT = $front;
			
			/* field information */
			self::$FIELD = $field;
			
			/* boot stuff */
			self::$BOOT = $boot;
			
			/* create a new instance */
			self::$instance = new self();
		}
		
		/* return instance */
		return self::$instance;
	}
	
	
	/*
	 * FETCH_ARRAY()
	 * return the page array (may contain one or more pages)
	 */
	public function FETCH_ARRAY(){ if(array_keys(self::$PAGE)){return self::$PAGE;} }
	
	
	/*
	 * FETCH_ENTRY()
	 * return a page fromthe page array based on the requested node id
	 */
	public function FETCH_ENTRY($n){
		
		/* check that we have a numeric value for the page id */
		if(is_numeric($n)){$n = "nid-".$n;}
		
		/* chcek that the node exists in the page array */
		if(array_key_exists($n,self::$PAGE)){
			
			/* return the requested node */
			return self::$PAGE["{$n}"];
		}
	}
	
	/* WITH_FIELDS()
	 * should any pages added into the array contain field data
	 */
	public function SET_WITH_FIELDS($enabled=true){ self::$with_fields = $enabled; }
	
	
	/* CLEAR()
	 * clear the page array
	 */
	public function CLEAR(){ self::$PAGE=array();}
	
	/* CLEAR_ENTRY()
	 * removes the specified key and associated values from the page array
	 */
	public function CLEAR_ENTRY($nid){
	
		unset(self::$PAGE[$nid]);
	}
	
	
	/*
	 * FIND()
	 * Lookup page (an optionally field) data and push into the page array
	 */
	public function FIND($a){
		
		/* create an empty filter string */
		$f = "";
		
		/* set the state of the page fields (should they be included in page retrieval) */
		if(isset($a["FIELDS"])){$this->SET_WITH_FIELDS(true);} else {$this->SET_WITH_FIELDS(false);}
		
		/* have we been passed a page alias to go and find */
		if(isset($a["ALIAS"])){
			
			/* if the aliss is not for the homepage */
			if($a["ALIAS"]!=""){
				
				/* make the alias safe */
				$a['ALIAS'] = self::$DB->ESCAPE($a['ALIAS']);
				
				/* add the alias to the filter */
				$f.= sprintf('(`page`.`alias`="%s")',$a['ALIAS']);
			} else {
				
				/* add the id for the homepage to to the filter */
				$f.= sprintf('(`page`.`id`=%u)',self::$FRONT);
			}
		}
		
		/* do we have a node id */
		if(isset($a["NID"])){
			
			/* have we already started build a filter */
			if($f!=""){$and = " AND ";} else {$and="";}
			
			/* add the node id into the filter */
			$f.= sprintf('%s(`page`.`id`=%u)',$and,$a["NID"]);
		}
		
		/* is the active website user a member of the public */
		if($a["USER"]=="!u"){
			
			/* have we already started building a filter */
			if($f!=""){$and = " AND ";} else {$and="";}
			
			/* add a published reference to the filter */
			$f.="{$and}(`page`.`published`=1)";
		}
		
		/* are we requesting pages using either page types or a node id */
		if(isset($a['TIDS']) || isset($a["NID"])){
			
			/* if we have page types */
			if(isset($a['TIDS'])){
				
				/* if only one page type as a string, convert to a single entry array */
				if(!is_array($a['TIDS'])){$a['TIDS']=array($a['TIDS']);}
				
				/* cycle through the passed page types */
				for($x=0;$x<count($a['TIDS']);$x++){
					
					/* if this is the first page type */
					if($x==0){
						
						/* have we already started building a filter */
						if($f!=""){$and = " AND ";} else {$and="";}
						$f.="{$and}(";
					}
					
					/* make the type id safe */
					$a['TIDS'][$x] = self::$DB->ESCAPE($a['TIDS'][$x]);
					
					/* add type definitions to the filter */
					$f.= sprintf('( `page`.`tid`=%d ) ',$a['TIDS'][$x]);
					
					/* check whether this is the last type definition supplied */
					if($x==count($a['TIDS'])-1){$f.=") ";} else {$f.="OR ";}
				}
			}
			
			/* go fetch the appropriate pages */
			self::$DB->CLEAR(array("ALL"));
			self::$DB->SET_FILTER($f);
			$pages = self::$DB->QUERY("SELECT","* FROM `page`");
			
			/* do we have any pages of the requested type */
			if(isset($pages)){
				
				/* cycle through the pages */
				foreach($pages as $page){
					
					/* create a temporary array to hold the standard fields with formatted names and the associated fields */
					$tpage=array();
					
					/* add the page into the page array */					
					$tpage = array(
						"id"=>$page["page.id"],
						"tid"=>$page["page.tid"],
						"nvids"=>self::$BOOT->JSON($page["page.nvids"],"decode"),
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
					
					/* should the page include fields */
					if(self::$with_fields){
						
						/* go fetch the field data, merge it with the temporary array and pass the whole lot into the PAGE array */
						self::$PAGE["nid-{$tpage['id']}"] = array_merge($tpage,self::$FIELD->FETCH_FIELDS($tpage['id'],$tpage['tid']));
					} else {
						
						/* push the temporary page array without any fields, onto the PAGE array */
						self::$PAGE["nid-{$tpage['id']}"] = $tpage;
					}
				}
			}
		}
	}
}