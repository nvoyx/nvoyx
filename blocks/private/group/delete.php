<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * delete existing group, then redirect to group list
 */

/* the group type to be deleted */
$gid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* delete the group */
$NVX_DB->DB_CLEAR(array("ALL"));
$NVX_DB->DB_SET_FILTER("`group`.`id`={$gid}");
$NVX_DB->DB_QUERY("DELETE","FROM `group`");

/* create an array of field types */
$fts = array("datebox","filelist","heirarchy","imagelist","mselect","sselect","textarea","textbox","heirarchy","tagbox");

/* cycle through the field types */
foreach($fts as $ft){
	
	/* delete any data held for this field */
	$NVX_DB->DB_CLEAR(array("ALL"));
	$NVX_DB->DB_SET_FILTER("`{$ft}`.`gid`={$gid}");
	$NVX_DB->DB_QUERY("DELETE","FROM `{$ft}`");
}

/* redirect to the group listings */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/group/list"));