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
$gid = $nvBoot->fetch_entry("breadcrumb",3);

/* delete the group */
$nvDb->clear(array("ALL"));
$nvDb->set_filter("`group`.`id`={$gid}");
$nvDb->query("DELETE","FROM `group`");

/* create an array of field types */
$fts = array("ajaxbox","datebox","filelist","heirarchy","imagelist","mselect","sselect","textarea","textbox","heirarchy","tagbox");

/* cycle through the field types */
foreach($fts as $ft){
	
	/* delete any data held for this field */
	$nvDb->clear(array("ALL"));
	$nvDb->set_filter("`{$ft}`.`gid`={$gid}");
	$nvDb->query("DELETE","FROM `{$ft}`");
}

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);

/* redirect to the group listings */
$nvBoot->header(array("LOCATION"=>"/settings/group/list"));