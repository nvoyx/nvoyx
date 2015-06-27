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
$tid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* convert the current microtime to MySQL datetime format */
$mt =  date('Y-m-d H:i:00',$NVX_BOOT->FETCH_ENTRY("microstamp"));

/* encode the microtime as an alias */
$alias = $NVX_BOOT->ALIAS($mt);

/* create an empty array of nvids */
$nvids = array();

/* cycle thru the groups */
foreach($NVX_GROUP->FETCH_ARRAY() as $GROUP){
	
	/* is this group associated with the current content type */
	if(in_array($tid,$GROUP["assoc"])){
		
		/* update nvids array */
		$nvids[$GROUP["id"]]=0;
	}
}

/* convert nvids array to a json string */
$nvids = $NVX_BOOT->JSON($nvids,"encode");

/* add a page to the database and grab its nid */
$NVX_DB->DB_CLEAR(array("ALL"));
$q = "INTO `page` (`id`,`tid`,`nvids`,`title`,`alias`,`heading`,`date`,`modified`,`by`) VALUES " .
		"(null,{$tid},'{$nvids}','nvoyxid{$mt}','nvoyxid{$alias}','{$mt}','{$mt}','{$mt}',{$NVX_USER->FETCH_ENTRY("id")})";
$nid = $NVX_DB->DB_QUERY("INSERT",$q);


/* check that a new page has been created, create a rollback folder then go visit it */
if(is_numeric($nid)){
	
	if(!file_exists($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid)){
		mkdir($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid);
	}
	
	$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/content/edit/".$nid));
}