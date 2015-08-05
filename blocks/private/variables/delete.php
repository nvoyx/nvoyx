<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * deletes a variable and redirects to the listings page
 */

/* delete a variable entry */
$NVX_DB->CLEAR(array("ALL"));
$NVX_DB->SET_FILTER("`id`={$NVX_BOOT->FETCH_ENTRY("breadcrumb",3)}");
$NVX_DB->QUERY("DELETE","FROM `variables`");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);

/* redirect to the variable listings */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/variables/list"));