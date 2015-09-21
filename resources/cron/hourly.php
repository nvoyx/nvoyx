<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/* automated tasks to be run every hour */

/* create an array of the auto publish and close columns within the page table */
$options = array(array("sttp","ttp",1),array("sttc","ttc",0));

/* grab the current date */
$now = date('Y-m-d H:i:00.00',self::$boot->fetch_entry("timestamp"));

/* cycle over the auto options */
foreach($options as $option){
	
	/* clear any db query settings  */
	self::$db->clear(array("ALL"));
			
	/* filter by any pages that should now be either published or closed */
	self::$db->set_filter("`page`.`{$option[0]}`=1 && `page`.`{$option[1]}` <= '{$now}'");
			
	/* fetch the query results */
	$rs = self::$db->query("SELECT","`page`.`id`,`page`.`{$option[1]}` FROM `page`");

	/* do we have any pages with auto information */
	if($rs){
		
		/* cycle through the pages */
		foreach($rs as $r){
			
				/* update the db listing for these files */
				self::$db->clear(array("ALL"));
				self::$db->set_limit(1);
				self::$db->set_filter("`page`.`id`={$r["page.id"]}");
				self::$db->query("UPDATE","`page` SET `page`.`{$option[0]}`=0,`page`.`{$option[1]}`=NULL,`page`.`published`={$option[2]}");
		}
		
		/* we need to clear all cached data */
		self::$boot->delete_cache();
		
	}
}