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
	$gid = $nvBoot->text($_POST["gid"]);
	
	/* grab a sanitised version of the node id */
	$nid = $nvBoot->text($_POST["node"]);
		
	/* grab a sanitised version of the field id */
	$fid = $nvBoot->text($_POST["fid"]);
	
	/* decode the json array of nids */
	$nids = $nvBoot->json($_POST["parents"],"decode");
		
	/* was this a valid json array */
	if(is_array($nids)){
	
		/* sanitise the nids array */
		$nids = $nvBoot->text($nids);
	
		/* json encode the sanitised array */
		$nids = $nvBoot->json($nids,"encode");
		
		/* grab any suitable pages */
		$nvDb->clear(array("ALL"));
		$nvDb->set_filter("`heirarchy`.`gid`={$gid} AND `heirarchy`.`fid`={$fid} AND `heirarchy`.`values` LIKE '%{$nids}%' AND `page`.`id`=`heirarchy`.`nid` AND `page`.`id`!={$nid}");
		$pages = $nvDb->query("SELECT","DISTINCT(`page`.`id`),`page`.`title` FROM `heirarchy`,`page`");
		
		/* if we have suitable entries */
		if($pages){
			/* json encode the array */
			$pages = $nvBoot->json($pages,"encode");
			echo $pages;
		} else {
			echo "empty";
		}
	}
}