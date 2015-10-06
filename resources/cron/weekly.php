<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/* automated tasks to be run every week */

$folders = array("tmp","session");
foreach($folders as $folder){
	$i = new \DirectoryIterator(self::$boot->fetch_entry($folder));
	foreach ($i as $fileinfo) {
		if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
			if($fileinfo->getMTime() < self::$boot->fetch_entry("timestamp") - 604800){
				unlink($fileinfo->getPathname());
			}
		}
	}
}
if(file_exists(self::$boot->fetch_entry("log")."/error.log")){
	unlink(self::$boot->fetch_entry("log")."/error.log");
}