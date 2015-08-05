<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * creates a new page type and redirects to it's edit page
 */

/* add a blank type entry */
$NVX_DB->CLEAR(array("ALL"));
$tid = $NVX_DB->QUERY("INSERT","INTO `type` (`id`,`name`,`parent`,`prefix`,`view`,`createdelete`,`rss`,`body`,`template`,`tags`) " . 
							"VALUES (NULL,'{$NVX_BOOT->FETCH_ENTRY("timestamp")}',-1,'','s','s',0,0,3,'[]')");
							
/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry added',
	'type'=>'success'
);

/* redirect to the new type-edit */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/type/edit/{$tid}"));