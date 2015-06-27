<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * creates a new member and redirects to their edit page
 */

/* add a blank member entry */
$NVX_DB->DB_CLEAR(array("ALL"));

/* encrypted timestamp */
$r = $NVX_BOOT->CYPHER(array("TYPE"=>"encrypt","STRING"=>$NVX_BOOT->FETCH_ENTRY("timestamp")));

/* joined date  is now*/
$j = date('Y-m-d H:i:00',$NVX_BOOT->FETCH_ENTRY("timestamp"));

$mid = $NVX_DB->DB_QUERY("INSERT","INTO `member` (`id`,`username`,`password`,`joined`) " . 
							"VALUES (NULL,'{$r}','{$r}','{$j}')");

/* redirect to the new member edit */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/member/edit/{$mid}"));