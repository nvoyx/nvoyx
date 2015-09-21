<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/* add a blank department entry */
$nvDb->clear(array("ALL"));
$pid = $nvDb->query("INSERT","INTO `dept` (`id`,`name`,`access`) VALUES (NULL,'{$nvBoot->fetch_entry("timestamp")}','[]')");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry added',
	'type'=>'success'
);

/* redirect to the new department-edit */
$nvBoot->header(array("LOCATION"=>"/settings/dept/edit/{$pid}"));