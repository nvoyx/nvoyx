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
$nvDb->clear(array("ALL"));
$pid = $nvDb->query("INSERT","INTO `block` (`id`,`name`,`tid`,`access`,`params`) " . 
							"VALUES (NULL,'{$nvBoot->fetch_entry("timestamp")}','[\"\"]','s','[\"\"]')");

/* path to public blocks */
$pb = $nvBoot->fetch_entry("blocks")."/public";
							
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
		$r .= '$bid = pathinfo(__FILE__, PATHINFO_FILENAME);'."\n\n".'/* grab the params */'."\n".'$p = $nvBlock->fetch_params($bid);'."\n\n";
		
		/* put the string in the file */
		file_put_contents("{$pb}/{$pid}.php", $r);
	}
}

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry added',
	'type'=>'success'
);

/* redirect to the new block edit page */
$nvBoot->header(array("LOCATION"=>"/settings/block/edit/{$pid}"));