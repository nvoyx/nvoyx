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
$nid = $nvBoot->FETCH_ENTRY("breadcrumb",3);

/* select the page tid */
$nvBoot->clear(array("ALL"));
$nvDb->set_filter("`page`.`id`={$nid}");
$tid = $nvDb->query("SELECT","`page`.`tid` FROM `page`");

/* if we have a page type */
if($tid){
	
	/* grab information on the page type */
	$type = $nvType->fetch_by_tid($tid[0]['page.tid']);
	
	/* is the current user allowed to delete this page */
	if(stristr($nvUser->fetch_entry("type"),$type["createdelete"])){
		
		/* check that the rollback folder exists */
		if(file_exists($nvBoot->fetch_entry("rollback")."/".$nid)){
			
			/* cycle through the relevant rollback node folder and return a list of files */
			$files = glob($nvBoot->fetch_entry("rollback")."/".$nid."/*.zip");
			
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
				if(!file_exists($nvBoot->fetch_entry("recovery")."/".$tid[0]['page.tid'])){
					
					/* make it */
					mkdir($nvBoot->fetch_entry("recovery")."/".$tid[0]['page.tid']);
				}
				
				/* copy the latest rollback to its tid recovery folder  - name it by nid */
				if(end($files)!=''){
					copy($nvBoot->fetch_entry("rollback")."/".$nid."/".end($files).".zip", $nvBoot->fetch_entry("recovery")."/".$tid[0]['page.tid']."/".$nid.".zip");
				}
			}
		}
		
		/* delete the page reference */
		$nvDb->clear(array("ALL"));
		$nvDb->set_filter("`page`.`id`={$nid}");
		$nvDb->query("DELETE","FROM `page`");

		/* create an array of field types */
		$ftypes = array("datebox","filelist","imagelist","mselect","sselect","textarea","textbox","heirarchy","tagbox");

		/* cycle through the field types */
		foreach($ftypes as $ftype){
			
			/* delete any field references */
			$nvDb->clear(array("ALL"));
			$nvDb->set_filter("`{$ftype}`.`nid`={$nid}");
			$nvDb->query("DELETE","FROM `{$ftype}`");
		}		
	}
}

/* we need to delete the rollback folder */
if(file_exists($nvBoot->fetch_entry("rollback")."/".$nid)){
	$nvBoot->del_tree($nvBoot->fetch_entry("rollback")."/".$nid);
}

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry deleted',
	'type'=>'warning'
);

/* redirect to the content listing page */
$nvBoot->header(array("LOCATION"=>"/settings/content/list"));