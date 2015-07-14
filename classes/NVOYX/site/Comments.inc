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
 * Collection of comment bumf
 */

namespace NVOYX\site;

class Comments{
	
	protected static $instance;
	protected static $DB;
	protected static $BOOT;
	protected static $MEMBERS;
	protected static $COMMENTS;
	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($db,$boot) {
		
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/db */
			self::$DB = $db;
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* create a new instance */
			self::$instance = new self();
			}
		
		/* return instance */
		 return self::$instance;
	}
	
	
	/*
	 * FETCH_ARRAY()
	 * returns the comments array
	 */
	public function FETCH_ARRAY(){ return self::$COMMENTS; }
	
	/*
	 * FETCH_BY_NID()
	 * 
	 */
	public function FETCH_BY_NID($nid){
		
		/* fetch all top level comments  in descending date order ie no replies relating to the requested page */
		self::$DB->DB_CLEAR(array("ALL"));
		self::$DB->DB_SET_ORDER(array("`comments`.`date`"=>"ASC"));
		self::$DB->DB_SET_FILTER("`comments`.`rid`=0 AND `comments`.`nid`={$nid} AND `comments`.`approved`=1");
		$comments = self::$DB->DB_QUERY("SELECT","* FROM `comments`");
		
		/* if we have no comments pass a false back */
		if(!$comments){return false;}
		
		/* create an empty comment array*/
		$c=array();
		
		/* cycle over the comments */
		foreach($comments as $comment){
			
			/* fetch all of the replies to the current comment in descending date order */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_ORDER(array("`comments`.`date`"=>"ASC"));
			self::$DB->DB_SET_FILTER("`comments`.`rid`={$comment["comments.id"]} AND `comments`.`approved`=1");
			$replies = self::$DB->DB_QUERY("SELECT","* FROM `comments`");
			
			/* create an empty reply array*/
			$r=array();
			
			/* if the current comment is approved */
			if($comment["comments.approved"]==1){
				
				/* if we have replies to this comment */
				if($replies){
					
					/* cycle over the replies */
					foreach($replies as $reply){
												
						/* grab details regarding the member whom replied */
						self::$DB->DB_CLEAR(array("ALL"));
						self::$DB->DB_SET_LIMIT(1);
						self::$DB->DB_SET_FILTER("`member`.`id`={$reply["comments.mid"]}");
						$member = self::$DB->DB_QUERY("SELECT","* FROM `member`");
						
						/* populate the reply array */
						$r[]=array("id"=>$reply["comments.id"],
									"nid"=>$reply["comments.nid"],
									"rid"=>$reply["comments.rid"],
									"date"=>$reply["comments.date"],
									"approved"=>$reply["comments.approved"],
									"values"=>$reply["comments.values"],
									"mid"=>$reply["comments.mid"],
									"title"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.title"])),
									"firstname"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.firstname"])),
									"lastname"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.lastname"])),
									"position"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.position"])),
									"company"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.company"])),
									"email"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.email"])),
									"dob"=>$member[0]["member.dob"],
									"web"=>$member[0]["member.web"],
									"username"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.username"])),
									"sex"=>$member[0]["member.sex"],
									"avatar"=>$member[0]["member.avatar"],
									"state"=>$member[0]["member.state"],
									"joined"=>$member[0]["member.joined"],
									"comments"=>$member[0]["member.comments"]
									);
					}
				}
			}
			
			/* fetch the member details for the original member cornment */
			self::$DB->DB_CLEAR(array("ALL"));
			self::$DB->DB_SET_LIMIT(1);
			self::$DB->DB_SET_FILTER("`member`.`id`={$comment["comments.mid"]}");
			$member = self::$DB->DB_QUERY("SELECT","* FROM `member`");
			
			/* update the comment array with the original comment and replies */
			$c[]=array("comment"=>array("id"=>$comment["comments.id"],
										"nid"=>$comment["comments.nid"],
										"rid"=>$comment["comments.rid"],
										"date"=>$comment["comments.date"],
										"approved"=>$comment["comments.approved"],
										"values"=>$comment["comments.values"],
										"mid"=>$comment["comments.mid"],
										"title"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.title"])),
										"firstname"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.firstname"])),
										"lastname"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.lastname"])),
										"position"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.position"])),
										"company"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.company"])),
										"email"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.email"])),
										"dob"=>$member[0]["member.dob"],
										"web"=>$member[0]["member.web"],
										"username"=>self::$BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member[0]["member.username"])),
										"sex"=>$member[0]["member.sex"],
										"avatar"=>$member[0]["member.avatar"],
										"state"=>$member[0]["member.state"],
										"joined"=>$member[0]["member.joined"],
										"comments"=>$member[0]["member.comments"]
										),
						"replies"=>$r
						);
		}
		return $c;
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * Returns an array with the public page comments ordered by date (DESC)
	 */
	private function __construct() {
	}	
}