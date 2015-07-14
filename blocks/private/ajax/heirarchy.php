<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns heirarchy options
 */

/* have parents been posted */
if(array_key_exists("parents",$_POST)){
	
	/* grab a sanitised version of the group id */
	$gid = $NVX_BOOT->TEXT($_POST["gid"]);
	
	/* grab a sanitised version of the node id */
	$nid = $NVX_BOOT->TEXT($_POST["node"]);
		
	/* grab a sanitised version of the field id */
	$fid = $NVX_BOOT->TEXT($_POST["fid"]);
	
	/* decode the json array of nids */
	$nids = $NVX_BOOT->JSON($_POST["parents"],"decode");
	
	/* was this a valid json array */
	if(is_array($nids)){
	
		/* sanitise the nids array */
		$nids = $NVX_BOOT->TEXT($nids);
	
		/* json encode the sanitised array */
		$nids = $NVX_BOOT->JSON($nids,"encode");
		
		/* grab any suitable pages */
		$NVX_DB->CLEAR(array("ALL"));
		$NVX_DB->SET_FILTER("`heirarchy`.`gid`={$gid} AND `heirarchy`.`fid`={$fid} AND `heirarchy`.`values` LIKE '%{$nids}%' AND `page`.`id`=`heirarchy`.`nid` AND `page`.`id`!={$nid}");
		$pages = $NVX_DB->QUERY("SELECT","DISTINCT(`page`.`id`),`page`.`title` FROM `heirarchy`,`page`");
		
		/* if we have suitable entries */
		if($pages){
			/* json encode the array */
			$pages = $NVX_BOOT->JSON($pages,"encode");
			echo $pages;
		} else {
			echo "empty";
		}

	}
}