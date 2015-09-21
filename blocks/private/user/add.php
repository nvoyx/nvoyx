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
$nvDb->clear(array("ALL"));
$date = date('Y-m-d',$nvBoot->fetch_entry("timestamp"));
$pid = $nvDb->query("INSERT","INTO `user` (`id`,`username`,`password`,`type`,`filter`,`dept`,`date`,`last`) " . 
							"VALUES (NULL,'{$nvBoot->cypher('encrypt',$nvBoot->fetch_entry("timestamp"))}',".
											"'{$nvBoot->cypher('encrypt',$nvBoot->fetch_entry("timestamp"))}','a',1,1,'{$date}','0000-00-00 00:00:00')");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry added',
	'type'=>'success'
);
											
/* redirect to the new user-edit */
$nvBoot->header(array("LOCATION"=>"/settings/user/edit/{$pid}"));
