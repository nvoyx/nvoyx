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
foreach($NVX_PATH->FETCH_ARRAY() as $r){
	
	/* do we have an id match */
	if($r["id"]==$NVX_BOOT->FETCH_ENTRY("breadcrumb",3)){
		
		/* does the referenced file actually exist */
		if(file_exists($NVX_BOOT->FETCH_ENTRY("blocks")."/private/ajax/".str_replace("/settings/ajax/","",$r["url"]).".php")){
			
			/* delete the file */
			unlink($NVX_BOOT->FETCH_ENTRY("blocks")."/private/ajax/".str_replace("/settings/ajax/","",$r["url"]).".php");
		}
	}
}

/* delete the path entry */
$NVX_DB->DB_CLEAR(array("ALL"));
$NVX_DB->DB_SET_FILTER("`id`={$NVX_BOOT->FETCH_ENTRY("breadcrumb",3)}");
$NVX_DB->DB_QUERY("DELETE","FROM `path`");

/* redirect to the ajaxmanager listings */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/ajaxmanager/list"));