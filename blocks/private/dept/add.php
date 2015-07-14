<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/* add a blank department entry */
$NVX_DB->CLEAR(array("ALL"));
$pid = $NVX_DB->QUERY("INSERT","INTO `dept` (`id`,`name`,`access`) VALUES (NULL,'{$NVX_BOOT->FETCH_ENTRY("timestamp")}','[]')");

/* redirect to the new department-edit */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/dept/edit/{$pid}"));