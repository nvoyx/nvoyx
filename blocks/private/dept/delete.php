<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * deletes a dept and redirects to the dept page
 */

/* delete a dept entry */
$nvDb->clear(array("ALL"));
$nvDb->set_filter("`id`={$nvBoot->fetch_entry("breadcrumb",3)}");
$nvDb->query("DELETE","FROM `dept`");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);

/* redirect to the dept listings */
$nvBoot->header(array("LOCATION"=>"/settings/dept/list"));