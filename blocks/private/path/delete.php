<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * deletes a path and redirects to the list page
 */

/* delete a blank path entry */
$nvDb->clear(array("ALL"));
$nvDb->set_filter("`id`={$nvBoot->fetch_entry("breadcrumb",3)}");
$nvDb->query("DELETE","FROM `path`");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);

/* redirect to the path listings */
$nvBoot->header(array("LOCATION"=>"/settings/path/list"));