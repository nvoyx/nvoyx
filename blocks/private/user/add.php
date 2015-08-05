<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * creates a new user and redirects to it's edit page
 */

/* add a blank user entry */
$NVX_DB->CLEAR(array("ALL"));
$date = date('Y-m-d',$NVX_BOOT->FETCH_ENTRY("timestamp"));
$pid = $NVX_DB->QUERY("INSERT","INTO `user` (`id`,`username`,`password`,`type`,`filter`,`dept`,`date`,`last`) " . 
							"VALUES (NULL,'{$NVX_BOOT->CYPHER(array("TYPE"=>"encrypt","STRING"=>$NVX_BOOT->FETCH_ENTRY("timestamp")))}',".
											"'{$NVX_BOOT->CYPHER(array("TYPE"=>"encrypt","STRING"=>$NVX_BOOT->FETCH_ENTRY("timestamp")))}','a',0,1,'{$date}','0000-00-00 00:00:00')");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry added',
	'type'=>'success'
);
											
/* redirect to the new user-edit */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/user/edit/{$pid}"));
