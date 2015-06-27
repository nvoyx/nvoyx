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
class Type{
	
	protected static $instance;
	protected static $DB;
	protected static $BOOT;
	protected static $FRONT;
	static $TYPE = array();

	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
    public static function CONNECT($db,$boot,$front) {
        
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/db */
			self::$DB = $db;
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* homepage id */
			self::$FRONT = $front;
			
			/* create a new instance */
            self::$instance = new self();
			
        }
		
		/* return instance */
        return self::$instance;
    }
	
	
	/*
	 * FETCH_ARRAY()
	 * return the page type array
	 */
	public function FETCH_ARRAY(){ return self::$TYPE; }
	
	
	/*
	 * FETCH_ENTRY()
	 * return a specific page type by type name
	 */
	public function FETCH_ENTRY($n){
					
		/* does this page type actually exist in the type array */
		if(array_key_exists($n,self::$TYPE)){
				
			/* return the requested page type */
			return self::$TYPE["{$n}"];
		}
	}
	
	/*
	 * FETCH_BY_TID()
	 * returns a specific page type by type id
	 */
	public function FETCH_BY_TID($n){
		
		/* have we been passed a numeric tid */
		if(is_numeric($n)){

			/* cycle through the stored page types */
			foreach(self::$TYPE as $type){
				
				/* if we have a match for the passed tid */
				if($type["id"]==$n){
					
					/* return the details */
					return $type;	
				}
			}
		}
	}
	
	/*
	 * 
	 * returns an array of possible type matches based on the requested URL / Node ID (NID)
	 */
	public function FETCH_MATCHES($a){
		
		/* have we been passed either a node id or the homepage */
		if((key_exists("URL",$a) && $a["URL"][0]=="") || isset($a["NID"])){
			
			/* if a node id isn't present, assume its the homepage */
			if(key_exists("NID",$a)){$nid = $a["NID"];} else {$nid = self::$FRONT;}
			
			/* grab page where id equals the node id */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_FILTER("`page`.`id`={$nid}");
			$ptids = self::$DB->DB_QUERY("SELECT","`page`.`tid` FROM `page`");
			
			/* do we have a type for the page */
			if(isset($ptids)){
				
				/* convert the results to a single entry array */
				$ptids = array($ptids[0]["page.tid"]);
			}
		} else {
			
			/* cycle through the page types */
			foreach(self::$TYPE as $type){
				
				/* is the user allowed to view this page type */
				if(stristr($a["USER"],$type['view'])){
					
					/* add this type to the list of possible types */
					$ptids[] = $type["id"];
				}
			}
		}
		
		/* do we have any possible page types, if so return them */
		return (isset($ptids)) ? $ptids : false;
	}
	
	
	/*
	 * PREFIXER()
	 * resolve any tags associated with a type prefix
	 */
	public function PREFIXER($a){
		
		/* grab the potentially tagged prefix */
		$prefix = self::FETCH_BY_TID($a["tid"])["prefix"];
		
		/* does the prefix have a length */
		if($prefix!=""){
			
			/* does the prefix have any tags */
			if(stristr($prefix,"[")){
								
					/* does the prefix contain a created date tag */
					if(stristr($prefix,"[cd:")){
						
						/* substitute the tag for the page created date */
						$tag =  substr($prefix,strpos($prefix,'[cd:')+4,strpos($prefix,']') - (strpos($prefix,'[cd:')+4)   );
						$rtag = date($tag,strtotime($a["date"]));
						$prefix = str_replace("[cd:".$tag."]",$rtag,$prefix);
					}
					
					/* does the prefix contain an id tag */
					if(stristr($prefix,"[id:")){
						
						/* susbtitute the tag for the page id (node id) */
						$prefix = str_replace("[id:]",$a["id"],$prefix);
					}
					
					/* does the prefix contain a single select tag */
					if(stristr($prefix,"[ss:")){

						$tag = explode("-",substr($prefix,strpos($prefix,'[ss:')+4,strpos($prefix,']') - (strpos($prefix,'[ss:')+4)   ));
						
						/* grab the  sselect value */
						self::$DB->DB_CLEAR(array("ALL"));
						self::$DB->DB_SET_FILTER("`sselect`.`nid`={$a['id']} AND `sselect`.`gid`={$tag[0]} AND `sselect`.`vid`={$tag[1]} AND `sselect`.`fid`={$tag[2]}");
						$rtag = self::$DB->DB_QUERY("SELECT","`sselect`.`values` FROM `sselect`");
						
						/* we have an sselect entry */
						if($rtag){
							
							/* run the sselect internal value through boot->alias then replace the tag in the page prefix */
							$prefix= str_replace("[ss:".implode("-",$tag)."]",self::$BOOT->ALIAS($rtag[0]['sselect.values']),$prefix);
						} 
					}

					/* does the prefix contain a multi select tag */
					if(stristr($prefix,"[ms:")){

						$tag = explode("-",substr($prefix,strpos($prefix,'[ms:')+4,strpos($prefix,']') - (strpos($prefix,'[ms:')+4)   ));
						
						/* grab the  mselect value */
						self::$DB->DB_CLEAR(array("ALL"));
						self::$DB->DB_SET_FILTER("`mselect`.`nid`={$a['id']} AND `mselect`.`gid`={$tag[0]} AND `mselect`.`vid`={$tag[1]} AND `mselect`.`fid`={$tag[2]}");
						$rtag = self::$DB->DB_QUERY("SELECT","`mselect`.`values` FROM `mselect`");
						
						/* we have an mselect entry */
						if($rtag){
							
							$rtag = self::$BOOT->JSON($rtag[0]['mselect.values'],"decode");
							
							/* we will only submit the first tag (others would be discarded anyway by google)*/
							$prefix = str_replace("[ms:".implode("-",$tag)."]",self::$BOOT->ALIAS($rtag[0]),$prefix);
						} 
					}

					/* return the fixed prefix */
					return $prefix;
			} else {
				
				/* prefix has no tags, so return the prefix unaltered */
				return $prefix;
			}
		} else {
			
			/* no prefix so return false */
			return false;
		}
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __connect()
	 * build an instance
	 */
	private function __construct() {
		
		/* should the apc user array be used to retrieve type details (if it exists) */
		$cache = self::$BOOT->GET_CACHE("types");
		
		/* if we don't have a cache for whatever reason */
		if(!$cache){
			
			/* fetch the type details from the database */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_ORDER(array("`type`.`name`"=>"ASC"));
			$types = self::$DB->DB_QUERY("SELECT","* FROM `type`");
			
			/* cycle through the page types */
			foreach($types as $type){
				
				/* build an array of page types */
				$t[$type["type.name"]] = array("id"=>$type["type.id"],
												"name"=>$type["type.name"],
												"parent"=>$type["type.parent"],
												"prefix"=>$type["type.prefix"],
												"view"=>$type["type.view"],
												"createdelete"=>$type["type.createdelete"],
												"rss"=>$type["type.rss"],
												"body"=>$type["type.body"],
												"comments"=>$type["type.comments"],
												"comeditor"=>$type["type.comeditor"],
												"template"=>$type["type.template"],
												"tags"=>self::$BOOT->JSON($type["type.tags"],"decode"));
			}
			
			/* store the page types */
			self::$TYPE = $t;
			
			/* update the page type cache */
			self::$BOOT->SET_CACHE("types",self::$TYPE);
		
			
		} else {
			
			/* retrieve the page type cache */
			self::$TYPE = $cache;
		}
	}
}