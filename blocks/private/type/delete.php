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
$nvDb->clear(array("ALL"));
$nvDb->set_filter("`id`={$nvBoot->fetch_entry("breadcrumb",3)}");
$nvDb->query("DELETE","FROM `type`");

/* grab an array of all pages of this type */
$nvDb->clear(array("ALL"));
$nvDb->set_filter("`tid`={$nvBoot->fetch_entry("breadcrumb",3)}");
$rs = $nvDb->query("SELECT","`page`.`id` FROM `page`");

/* create an array of field types */
$fts = array("ajaxbox","datebox","filelist","heirarchy","imagelist","mselect","sselect","textarea","textbox","heirarchy","tagbox");

/* if we have any pages of this type */
if($rs){
	
	/* cycle through the pages */
	foreach($rs as $r){
		
		/* cycle through the field types */
		foreach($fts as $ft){
	
				/* remove any field entries for the current node */
				$nvDb->clear(array("ALL"));
				$nvDb->set_filter("`nid`={$r["page.id"]}");
				$nvDb->query("DELETE","FROM `{$ft}`");
		}
	}	
}

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);


/* redirect to the type list */
$nvBoot->header(array("LOCATION"=>"/settings/type/list"));