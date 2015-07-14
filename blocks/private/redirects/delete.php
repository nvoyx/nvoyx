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
$NVX_DB->CLEAR(array("ALL"));
$NVX_DB->SET_FILTER("`id`={$NVX_BOOT->FETCH_ENTRY("breadcrumb",3)}");
$NVX_DB->QUERY("DELETE","FROM `redirects`");

/* redirect to the redirects listings */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/redirects/list"));