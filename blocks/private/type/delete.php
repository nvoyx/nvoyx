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
$NVX_DB->CLEAR(array("ALL"));
$NVX_DB->SET_FILTER("`id`={$NVX_BOOT->FETCH_ENTRY("breadcrumb",3)}");
$NVX_DB->QUERY("DELETE","FROM `type`");

/* grab an array of all pages of this type */
$NVX_DB->CLEAR(array("ALL"));
$NVX_DB->SET_FILTER("`tid`={$NVX_BOOT->FETCH_ENTRY("breadcrumb",3)}");
$rs = $NVX_DB->QUERY("SELECT","`page`.`id` FROM `page`");

/* create an array of field types */
$fts = array("datebox","filelist","heirarchy","imagelist","mselect","sselect","textarea","textbox","heirarchy","tagbox");

/* if we have any pages of this type */
if($rs){
	
	/* cycle through the pages */
	foreach($rs as $r){
		
		/* cycle through the field types */
		foreach($fts as $ft){
	
				/* remove any field entries for the current node */
				$NVX_DB->CLEAR(array("ALL"));
				$NVX_DB->SET_FILTER("`nid`={$r["page.id"]}");
				$NVX_DB->QUERY("DELETE","FROM `{$ft}`");
		}
	}	
}

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);


/* redirect to the type list */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/type/list"));