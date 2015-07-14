<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * pushes changes to the comments and members tables on the database
 */

/* sanitise any data passed by _POST */
$p = $NVX_BOOT->TEXT($_POST);

/* do we have an action */
if(array_key_exists("action",$p)){
	
	switch ($p["action"]):
		
		/* a potential member is attempting to login */
		case "login":
			
			/* do we have a username and password */
			if(array_key_exists("user",$p) && array_key_exists("pass",$p)){
				
				/* do both the username and password contain a string of at least one character in length */
				if($p["user"]!="" && $p["pass"]!=""){
					
					/* encrypt the username and password */
					$p["user"] = $NVX_BOOT->CYPHER(array("STRING"=>$p["user"],"TYPE"=>"encrypt"));
					$p["pass"] = $NVX_BOOT->CYPHER(array("STRING"=>$p["pass"],"TYPE"=>"encrypt"));
					
					/* attempt to fetch the requested member details */
					$NVX_DB->CLEAR(array("ALL"));
					$NVX_DB->SET_FILTER("`member`.`username`='{$p["user"]}' AND `member`.`password`='{$p["pass"]}'");
					$member = $NVX_DB->QUERY("SELECT","`member`.* FROM `member`");
					
					/* have we found a valid member */
					if($member){
						
						/* is this member enabled */
						if($member[0]["member.state"]==1){
							
							/* is this member allowed to leave comments */
							if($member[0]["member.comments"]==1){
						
								/* make a note of this member id (mid) in the session data */
								$_SESSION["mid"] = $member[0]["member.id"];
						
								/* return the member id */
								echo $member[0]["member.id"];
							}
						}
					}
				}
			}
			break;
			
		case "new":
			
			/* check that we have numeric values for both the member and the node */
			if(is_numeric($p["member"]) && is_numeric($p["node"])){
				
				/* does the current session id tally with the id being passed in the post */
				if($_SESSION["mid"]==$p["member"]){
					
					/* populate PAGE based on possible TIDs (array or integer as string) and lowest alias */
					$NVX_PAGE->FIND(array("NID" => $p["node"],
								"USER" => $NVX_USER->FETCH_ENTRY("type"),
								"FIELDS" => false
								));
					
					
					/* grab the page (if one was found) */
					$page = $NVX_PAGE->FETCH_ARRAY();
					
					/* does the requested page exist */
					if($page){

						/* are comments enabled for this node*/
						if($page["nid-{$p["node"]}"]["comments"]==1){
							
							$date = date("Y-m-d H:i:s",$NVX_BOOT->FETCH_ENTRY("timestamp"));
							
							$NVX_DB->CLEAR(array("ALL"));
							$cmt = $NVX_DB->QUERY("INSERT","INTO `comments` (`id`,`mid`,`nid`,`approved`,`date`,`values`) " . 
								"VALUES (NULL,{$p["member"]},{$p["node"]},1,'{$date}','{$p["comment"]}')");
								
							echo $cmt;
						}
					}
				}
			}
			
			break;
		
		case "go-reply":
			
			/* check that we have numeric values for the member , comment and the node */
			if(is_numeric($p["member"]) && is_numeric($p["node"]) && is_numeric($p["rid"])){
				
				/* does the current session id tally with the id being passed in the post */
				if($_SESSION["mid"]==$p["member"]){
					
					/* populate PAGE based on possible TIDs (array or integer as string) and lowest alias */
					$NVX_PAGE->FIND(array("NID" => $p["node"],
								"USER" => $NVX_USER->FETCH_ENTRY("type"),
								"FIELDS" => false
								));
					
					
					/* grab the page (if one was found) */
					$page = $NVX_PAGE->FETCH_ARRAY();
					
					/* does the requested page exist */
					if($page){

						/* are comments enabled for this node*/
						if($page["nid-{$p["node"]}"]["comments"]==1){
							
							$date = date("Y-m-d H:i:s",$NVX_BOOT->FETCH_ENTRY("timestamp"));
							
							$NVX_DB->CLEAR(array("ALL"));
							$rply = $NVX_DB->QUERY("INSERT","INTO `comments` (`id`,`mid`,`nid`,`rid`,`approved`,`date`,`values`) " . 
								"VALUES (NULL,{$p["member"]},{$p["node"]},{$p["rid"]},1,'{$date}','{$p["reply"]}')");
								
							echo $rply;
						}
					}
				}
			}
			break;
		
	endswitch;
}