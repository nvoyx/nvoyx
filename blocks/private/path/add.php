<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * creates a new path and redirects to it's edit page
 */

/* add a blank path entry */
$nvDb->clear(array("ALL"));
$pid = $nvDb->QUERY("INSERT","INTO `path` (`id`,`url`,`access`) " . 
							"VALUES (NULL,'/{$nvBoot->fetch_entry("timestamp")}','s')");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry added',
	'type'=>'success'
);
							
/* redirect to the new path-edit */
$nvBoot->header(array("LOCATION"=>"/settings/path/edit/{$pid}"));