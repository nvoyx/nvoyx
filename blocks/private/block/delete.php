<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * deletes an existing block and redirects to the listing page
 */

/* delete a block entry */
$nvDb->clear(array("ALL"));
$nvDb->set_filter("`id`={$nvBoot->fetch_entry("breadcrumb",3)}");
$nvDb->query("DELETE","FROM `block`");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);

/* redirect to block listings */
$nvBoot->header(array("LOCATION"=>"/settings/block/list"));