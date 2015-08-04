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

namespace NVOYX\site;

class Field{
	
	protected static $instance;
	protected static $DB;
	protected static $GROUP;
	protected static $BOOT;
	static $FIELD = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
    public static function CONNECT($db,$group,$boot) {
        
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/db */
			self::$DB = $db;
			
			/* link with public/group */
			self::$GROUP = $group;
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* create a new instance */
            self::$instance = new self();
        }
		
		/* return instance */
        return self::$instance;
    }
	
	
	/*
	 * FETCH_ENTRY()
	 * grab the actual field values for a given page
	 */
	public function FETCH_ENTRY($a,$gid,$vid,$fid,$iteration){
		 
		/* if the group -> variation -> field -> iteration exists within the passed page array */
		if(isset($a["gid-{$gid}"]["vid-{$vid}"]["fid-{$fid}"][$iteration])){
			
			/* return the array of response fields */
			return $a["gid-{$gid}"]["vid-{$vid}"]["fid-{$fid}"][$iteration];
		}
	}	
	
	/*
	 * FETCH_FIELDS()
	 * takes a page array and adds any associated fields - need to remove the passing of the page array (this could get massive)
	 */
	public function FETCH_FIELDS($nid,$tid) {
		
		/* empty array to hold the fields */
		$page=array();
						
		/* cycle through all groups defined within the cms */
		foreach(self::$GROUP->FETCH_ARRAY() as $g){
			
			/* checks against the received page array, to see if the page of interest is of a type associated with this group, skips if not */
			if(!in_array($tid,$g["assoc"])){continue;}

			/* cycle through the current group outline (fields) */
			foreach($g["outline"] as $o){
				
				/* clear the db statement */
				self::$DB->CLEAR(array("ALL"));

				/* build a filter based on the field type table, group id, node id and field id */
				self::$DB->SET_FILTER("`{$o['type']}`.`gid`={$g['id']} AND `{$o['type']}`.`nid`={$nid} AND `{$o['type']}`.`fid`={$o['fid']}");

				/* order the results by their position id */
				self::$DB->SET_ORDER(array("{$o['type']}.pid"=>"ASC"));

				/* fetch the field results */
				$rs = self::$DB->QUERY("SELECT", "* FROM `{$o['type']}`");

				/* if we have no results for this field, move on */
				if(!$rs){continue;}

				/* cycle through the results */

				foreach($rs as $r){

					/* what type of field are we looking at */
					switch($o["type"]):

						case "imagelist":

							/* convert any stored image details to an array (stored as JSON encoded string) */
							$imgs = self::$BOOT->JSON($r["imagelist.values"]);

							/* set an increment flag of images found to zero */
							$x=0;

							/* create an empty field array and place it inside the page array */
							$page["gid-{$g['id']}"]["vid-{$r['imagelist.vid']}"]["fid-{$o['fid']}"] = array();

							/* cycle through each of the images found */
							foreach($imgs as $i){

								/* add the field values to the the page array */
								$page["gid-{$g['id']}"]["vid-{$r['imagelist.vid']}"]["fid-{$o['fid']}"][$x]["name"] = str_replace(".webp","",$i["name"]);
								$page["gid-{$g['id']}"]["vid-{$r['imagelist.vid']}"]["fid-{$o['fid']}"][$x]["desc"] = $i["desc"];

								/* link is an optional field under imagelist, test if it exists before adding to page array */
								if(array_key_exists("link",$i)){
									$page["gid-{$g['id']}"]["vid-{$r['imagelist.vid']}"]["fid-{$o['fid']}"][$x]["link"] = $i["link"];
								}

								/* text is an optional field under imagelist, test if it exists before adding to page array */
								if(array_key_exists("text",$i)){
									$page["gid-{$g['id']}"]["vid-{$r['imagelist.vid']}"]["fid-{$o['fid']}"][$x]["text"] = $i["text"];
								}

								/* bump the field incrementor */
								$x++;		
							}
							break;


						case "filelist":

							/* convert any stored file details to an array (stored as JSON encoded string) */
							$files = self::$BOOT->JSON($r["filelist.values"]);

							/* set an increment flag of files found to zero */
							$x=0;

							/* create an empty field array and place it inside the page array */
							$page["gid-{$g['id']}"]["vid-{$r['filelist.vid']}"]["fid-{$o['fid']}"] = array();

							/* cycle through each of the files found */
							foreach($files as $f){

								/* add the field values to the the page array */
								$page["gid-{$g['id']}"]["vid-{$r['filelist.vid']}"]["fid-{$o['fid']}"][$x]["name"] = $f["name"];
								$page["gid-{$g['id']}"]["vid-{$r['filelist.vid']}"]["fid-{$o['fid']}"][$x]["desc"] = $f["desc"];
								$page["gid-{$g['id']}"]["vid-{$r['filelist.vid']}"]["fid-{$o['fid']}"][$x]["type"] = $f["type"];
								$page["gid-{$g['id']}"]["vid-{$r['filelist.vid']}"]["fid-{$o['fid']}"][$x]["size"] = $f["size"];

								/* bump the field incrementor */
								$x++;
							}
							break;


						case "datebox":

							/* convert any stored date details to an array (stored as JSON encoded string) */
							$dates = self::$BOOT->JSON($r["datebox.values"]);

							/* datboxes may contain a range field, set to empty if not */
							if(empty($dates[0]["finish"])){$dates[0]["finish"]="";}

							/* add the field values to the the page array */
							$page["gid-{$g['id']}"]["vid-{$r['datebox.vid']}"]["fid-{$o['fid']}"][0]["start"] = $dates[0]["start"];
							$page["gid-{$g['id']}"]["vid-{$r['datebox.vid']}"]["fid-{$o['fid']}"][0]["finish"] = $dates[0]["finish"];
							break;


						case "textbox":

							/* add the field values to the the page array */
							$page["gid-{$g['id']}"]["vid-{$r['textbox.vid']}"]["fid-{$o['fid']}"][0]["text"] = $r["textbox.values"];
							break;


						case "textarea":

							/* if the field is in plain text format, replace the newline tag with an html line break */
							if($o["plain"]==1){$r["textarea.values"] = str_replace("<br>","[format:newline]",$r['textarea.values']);}

							/* add the field values to the the page array */
							$page["gid-{$g['id']}"]["vid-{$r['textarea.vid']}"]["fid-{$o['fid']}"][0]["text"] = $r["textarea.values"];
							break;


						case "sselect":

							/* add the field values to the the page array */
							$page["gid-{$g['id']}"]["vid-{$r['sselect.vid']}"]["fid-{$o['fid']}"][0]["selected"] = $r["sselect.values"];
							break;


						case "mselect":

							/* convert any stored mselect details to an array (stored as JSON encoded string) */
							$selections = self::$BOOT->JSON($r["mselect.values"]);

							/* set an increment flag of selections found to zero */
							$x=0;

							/* create an empty field array and place it inside the page array */
							$page["gid-{$g['id']}"]["vid-{$r['mselect.vid']}"]["fid-{$o['fid']}"] = array();

							/* cycle through each of the selection found */
							foreach($selections as $s){

								/* add the field values to the the page array */
								$page["gid-{$g['id']}"]["vid-{$r['mselect.vid']}"]["fid-{$o['fid']}"][$x]["selected"] = $s;

								/* bump the field incrementor */
								$x++;
							}
							break;


						case "heirarchy":

							/* convert any stored heirarchy details to an array (stored as JSON encoded string) */
							$parents = self::$BOOT->JSON($r["heirarchy.values"]);

							/* set an increment flag of heirarchies found to zero */
							$x=0;

							/* create an empty field array and place it inside the page array */
							$page["gid-{$g['id']}"]["vid-{$r['heirarchy.vid']}"]["fid-{$o['fid']}"] = array();

							/* cycle through each of the heirarchies found */
							foreach($parents as $p){

								/* add the field values to the the page array */
								$page["gid-{$g['id']}"]["vid-{$r['heirarchy.vid']}"]["fid-{$o['fid']}"][$x]["parent"] = $p;

								/* bump the field incrementor */
								$x++;
							}
							break;


							case "tagbox":

							/* convert any stored tags to an array (stored as JSON encoded string) */
							$tags = self::$BOOT->JSON($r["tagbox.values"]);

							/* set an increment flag of tags found to zero */
							$x=0;

							/* create an empty field array and place it inside the page array */
							$page["gid-{$g['id']}"]["vid-{$r['tagbox.vid']}"]["fid-{$o['fid']}"] = array();

							/* cycle through each of the tags found */
							foreach($tags as $t){

								/* add the field values to the the page array */
								$page["gid-{$g['id']}"]["vid-{$r['tagbox.vid']}"]["fid-{$o['fid']}"][$x]["tag"] = $t;

								/* bump the field incrementor */
								$x++;
							}
							break;

					endswitch;
				}
			}
		}
		
		/* return the page fields */
		return $page;
	}

}