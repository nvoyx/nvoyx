<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * creates a new block and redirects to it's edit page
 */

/* add a blank block entry */
$NVX_DB->CLEAR(array("ALL"));
$pid = $NVX_DB->QUERY("INSERT","INTO `block` (`id`,`name`,`tid`,`access`,`params`) " . 
							"VALUES (NULL,'{$NVX_BOOT->FETCH_ENTRY("timestamp")}','[\"\"]','s','[\"\"]')");

/* path to public blocks */
$pb =  $NVX_BOOT->FETCH_ENTRY("blocks")."/public";
							
/* does a file already exist */
if(!file_exists("{$pb}/{$pid}.php")){
	
	/* create an empty file */
	touch("{$pb}/{$pid}.php");
	
	/* check that touch was able to create the file */
	if(file_exists("{$pb}/{$pid}.php")){
		
		/* build a string to copy into the file */
		$r = "<";
		$r .= "?php\n\n\n";
		$r .= "/*\n* @block {$pid} ()\n* param \n* returns  \n*/\n\n/* current block id */\n";
		$r .= '$bid = pathinfo(__FILE__, PATHINFO_FILENAME);'."\n\n".'/* grab the params */'."\n".'$p = $NVX_BLOCK->FETCH_PARAMS($bid);'."\n\n";
		
		/* put the string in the file */
		file_put_contents("{$pb}/{$pid}.php", $r);
	}
}

/* redirect to the new block edit page */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/block/edit/{$pid}"));