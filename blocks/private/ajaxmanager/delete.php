<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * deletes an ajax file - both physical file and db reference (held in the path table) and redirects to the ajaxmanager list page
 */


/* cycle through the stored paths */
foreach($nvPath->fetch_array() as $r){

	/* do we have an id match */
	if($r["id"]==$nvBoot->fetch_entry("breadcrumb",3)){
		
		/* does the referenced file actually exist */
		if(file_exists($nvBoot->fetch_entry("blocks")."/private/ajax/".str_replace("/settings/ajax/","",$r["url"]).".php")){
			
			/* delete the file */
			unlink($nvBoot->fetch_entry("blocks")."/private/ajax/".str_replace("/settings/ajax/","",$r["url"]).".php");
		}
	}
}

/* delete the path entry */
$nvDb->clear(array("ALL"));
$nvDb->set_filter("`id`={$nvBoot->fetch_entry("breadcrumb",3)}");
$nvDb->query("DELETE","FROM `path`");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);

/* redirect to the ajaxmanager listings */
$nvBoot->header(array("LOCATION"=>"/settings/ajaxmanager/list"));