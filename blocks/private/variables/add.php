<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * creates a new variable and redirects to it's edit page
 */

/* add a blank variable entry */
$NVX_DB->CLEAR(array("ALL"));
$pid = $NVX_DB->QUERY("INSERT","INTO `variables` (`id`,`name`,`notes`,`value`) " . 
							"VALUES (NULL,'{$NVX_BOOT->FETCH_ENTRY("timestamp")}','','[\"\"]')");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry added',
	'type'=>'success'
);
							
/* redirect to the new variable edit */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/variables/edit/{$pid}"));