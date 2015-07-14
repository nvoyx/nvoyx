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
$NVX_DB->CLEAR(array("ALL"));
$NVX_DB->SET_FILTER("`id`={$NVX_BOOT->FETCH_ENTRY("breadcrumb",3)}");
$NVX_DB->QUERY("DELETE","FROM `dept`");

/* TODO should really check against the current users and fallback to the default dept where appropriate */

/* redirect to the dept listings */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/dept/list"));