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
$NVX_DB->CLEAR(array("ALL"));
$NVX_DB->SET_FILTER("`group`.`id`={$gid}");
$NVX_DB->QUERY("DELETE","FROM `group`");

/* create an array of field types */
$fts = array("ajaxbox","datebox","filelist","heirarchy","imagelist","mselect","sselect","textarea","textbox","heirarchy","tagbox");

/* cycle through the field types */
foreach($fts as $ft){
	
	/* delete any data held for this field */
	$NVX_DB->CLEAR(array("ALL"));
	$NVX_DB->SET_FILTER("`{$ft}`.`gid`={$gid}");
	$NVX_DB->QUERY("DELETE","FROM `{$ft}`");
}

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);

/* redirect to the group listings */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/group/list"));