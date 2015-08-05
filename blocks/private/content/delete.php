<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * delete existing page, then redirect to content list
 */

/* the page to be deleted */
$nid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* select the page tid */
$NVX_DB->CLEAR(array("ALL"));
$NVX_DB->SET_FILTER("`page`.`id`={$nid}");
$tid = $NVX_DB->QUERY("SELECT","`page`.`tid` FROM `page`");

/* if we have a page type */
if($tid){
	
	/* grab information on the page type */
	$type = $NVX_TYPE->FETCH_BY_TID($tid[0]['page.tid']);
	
	/* is the current user allowed to delete this page */
	if(stristr($NVX_USER->FETCH_ENTRY("type"),$type["createdelete"])){
		
		/* check that the rollback folder exists */
		if(file_exists($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid)){
			
			/* cycle through the relevant rollback node folder and return a list of files */
			$files = glob($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid."/*.zip");
			
			/* if  we have an array */
			if(is_array($files)){
				
				/* cycle over the files */
				for($a=0;$a<count($files);$a++){

					/* strip the path and filename leaving only the timestamp*/
					$files[$a] = pathinfo($files[$a], PATHINFO_FILENAME);
				}

				/* sort the timestamps highest to lowest numerically */
				rsort($files,SORT_NUMERIC);
				
				/* if we don't already have a recovery folder for pages of this type */
				if(!file_exists($NVX_BOOT->FETCH_ENTRY("recovery")."/".$tid[0]['page.tid'])){
					
					/* make it */
					mkdir($NVX_BOOT->FETCH_ENTRY("recovery")."/".$tid[0]['page.tid']);
				}
				
				/* copy the latest rollback to its tid recovery folder  - name it by nid */
				if(end($files)!=''){
					copy($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid."/".end($files).".zip", $NVX_BOOT->FETCH_ENTRY("recovery")."/".$tid[0]['page.tid']."/".$nid.".zip");
				}
			}
		}
		
		/* delete the page reference */
		$NVX_DB->CLEAR(array("ALL"));
		$NVX_DB->SET_FILTER("`page`.`id`={$nid}");
		$NVX_DB->QUERY("DELETE","FROM `page`");

		/* create an array of field types */
		$ftypes = array("datebox","filelist","imagelist","mselect","sselect","textarea","textbox","heirarchy","tagbox");

		/* cycle through the field types */
		foreach($ftypes as $ftype){
			
			/* delete any field references */
			$NVX_DB->CLEAR(array("ALL"));
			$NVX_DB->SET_FILTER("`{$ftype}`.`nid`={$nid}");
			$NVX_DB->QUERY("DELETE","FROM `{$ftype}`");
		}		
	}
}

/* we need to delete the rollback folder */
if(file_exists($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid)){
	$NVX_BOOT->DEL_TREE($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid);
}

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);

/* redirect to the content listing page */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/content/list"));