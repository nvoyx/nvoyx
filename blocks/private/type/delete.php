<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * deletes a type and redirects to the list page
 */

/* remove the page */
$NVX_DB->DB_CLEAR(array("ALL"));
$NVX_DB->DB_SET_FILTER("`id`={$NVX_BOOT->FETCH_ENTRY("breadcrumb",3)}");
$NVX_DB->DB_QUERY("DELETE","FROM `type`");

/* grab an array of all pages of this type */
$NVX_DB->DB_CLEAR(array("ALL"));
$NVX_DB->DB_SET_FILTER("`tid`={$NVX_BOOT->FETCH_ENTRY("breadcrumb",3)}");
$rs = $NVX_DB->DB_QUERY("SELECT","`page`.`id` FROM `page`");

/* create an array of field types */
$fts = array("datebox","filelist","heirarchy","imagelist","mselect","sselect","textarea","textbox","videolist","heirarchy","tagbox");

/* if we have any pages of this type */
if($rs){
	
	/* cycle through the pages */
	foreach($rs as $r){
		
		/* cycle through the field types */
		foreach($fts as $ft){
	
				/* remove any field entries for the current node */
				$NVX_DB->DB_CLEAR(array("ALL"));
				$NVX_DB->DB_SET_FILTER("`nid`={$r["page.id"]}");
				$NVX_DB->DB_QUERY("DELETE","FROM `{$ft}`");
		}
	}	
}


/* redirect to the type list */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/type/list"));