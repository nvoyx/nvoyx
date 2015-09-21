<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * creates a new ajax file, path definition and redirects to it's path edit page
 */

/* add a blank path entry */
$nvDb->clear(array("ALL"));
$pid = $nvDb->query("INSERT","INTO `path` (`id`,`url`,`access`) " . 
							"VALUES (NULL,'/settings/ajax/{$nvBoot->fetch_entry("timestamp")}','s')");

/* create the ajax file */
touch($nvBoot->fetch_entry("blocks")."/private/ajax/".$nvBoot->fetch_entry("timestamp").".php");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry added',
	'type'=>'success'
);

/* redirect to the new ajaxmanager page */
$nvBoot->header(array("LOCATION"=>"/settings/ajaxmanager/edit/{$pid}"));
