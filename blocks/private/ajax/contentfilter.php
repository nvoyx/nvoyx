<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * pushes changes to the visitor filter onto the database
 */

/* sanitise any data passed by _POST */
$p = $nvBoot->text($_POST);

/* do we have an action */
if(array_key_exists("filter",$p) && array_key_exists("user",$p)){
	
	/* check that the passed variables are numeric */
	if(is_numeric($p["filter"]) && is_numeric($p["user"])){
		
		/* push filter choice onto database */
		$nvDb->clear(array("ALL"));
		$nvDb->set_filter("`user`.`id`={$p["user"]}");
		$nvDb->query("UPDATE","`user` SET `user`.`filter`={$p["filter"]}");
	}
}