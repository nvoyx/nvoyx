<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * deletes a redirect and redirects to the list page
 */

/* delete a redirects entry */
$nvDb->clear(array("ALL"));
$nvDb->set_filter("`id`={$nvBoot->fetch_entry("breadcrumb",3)}");
$nvDb->query("DELETE","FROM `redirects`");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);

/* redirect to the redirects listings */
$nvBoot->header(array("LOCATION"=>"/settings/redirects/list"));