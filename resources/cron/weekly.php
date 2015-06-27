<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/* automated tasks to be run every week */

/* create an array containing the tmp and session folders */
$folders = array("tmp","session");

/* cycle through the array */
foreach($folders as $folder){

	/* create a file iterator for the files folder */
	$i = new DirectoryIterator(self::$BOOT->FETCH_ENTRY($folder));

	/* cycle through the iterations */
	foreach ($i as $fileinfo) {

		/* if the file is not part of the OS navigation eg. not . or .. */
		if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
			
			/* if the file has not been modified in the last week */
			if($fileinfo->getMTime() < self::$BOOT->FETCH_ENTRY("timestamp") - 604800){
				
				/* delete the file */
				unlink($fileinfo->getPathname());
			}
		}
	}
}

/* do we have a php error log */
if(file_exists(self::$BOOT->FETCH_ENTRY("log")."/error.log")){
	
	/* delete the php error log */
	unlink(self::$BOOT->FETCH_ENTRY("log")."/error.log");
}