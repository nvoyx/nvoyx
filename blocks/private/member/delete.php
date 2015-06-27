<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * deletes a member and redirects to the listings page
 */

/* delete a member entry */
$NVX_DB->DB_CLEAR(array("ALL"));
$NVX_DB->DB_SET_FILTER("`id`={$NVX_BOOT->FETCH_ENTRY("breadcrumb",3)}");
$NVX_DB->DB_QUERY("DELETE","FROM `member`");

/* redirect to the member listings */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/member/list"));