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
$p = $NVX_BOOT->TEXT($_POST);

/* do we have an action */
if(array_key_exists("filter",$p) && array_key_exists("user",$p)){
	
	/* check that the passed variables are numeric */
	if(is_numeric($p["filter"]) && is_numeric($p["user"])){
		
		/* push filter choice onto database */
		$NVX_DB->DB_CLEAR(array("ALL"));
		$NVX_DB->DB_SET_FILTER("`user`.`id`={$p["user"]}");
		$NVX_DB->DB_QUERY("UPDATE","`user` SET `user`.`filter`={$p["filter"]}");
	}
}