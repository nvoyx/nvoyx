<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * creates new page, then redirects page edit
 */

/* the page type to be created */
$tid = $nvBoot->fetch_entry("breadcrumb",3);

/* convert the current microtime to MySQL datetime format */
$mt =  date('Y-m-d H:i',$nvBoot->fetch_entry("microstamp"));

/* encode the microtime as an alias */
$alias = $nvBoot->alias($mt);

/* create an empty array of nvids */
$nvids = array();

/* cycle thru the groups */
foreach($nvGroup->fetch_array() as $group){
	
	/* is this group associated with the current content type */
	if(in_array($tid,$group["assoc"])){
		
		/* update nvids array */
		$nvids[$group["id"]]=0;
	}
}

/* convert nvids array to a json string */
$nvids = $nvBoot->json($nvids,"encode");

/* add a page to the database and grab its nid */
$nvDb->clear(array("ALL"));
$q = "INTO `page` (`id`,`tid`,`nvids`,`title`,`alias`,`heading`,`date`,`modified`,`ttc`,`ttp`,`by`) VALUES " .
		"(null,{$tid},'{$nvids}','nvoyxid{$mt}','nvoyxid{$alias}','{$mt}','{$mt}','{$mt}','{$mt}','{$mt}',{$nvUser->fetch_entry("id")})";
$nid = $nvDb->query("INSERT",$q);


/* check that a new page has been created, create a rollback folder then go visit it */
if(is_numeric($nid)){
	
	if(!file_exists($nvBoot->fetch_entry("rollback")."/".$nid)){
		mkdir($nvBoot->fetch_entry("rollback")."/".$nid);
		$nvBoot->sync($nid,'newrollbackfolder');
	}
	
	/* issue a notification */
	$_SESSION['notify']=array(
		'message'=>'Success: entry added',
		'type'=>'success'
	);
	
	$nvBoot->header(array("LOCATION"=>"/settings/content/edit/".$nid));
}