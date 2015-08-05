<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns list all archives currently associated with the passed node
 */

/*grab the page id we are interested in */
$nid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* grab the rollback we are interested in */
$rid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",4);

/* array of tables */
$dtypes = array(
	"datebox",
	"filelist",
	"heirarchy",
	"imagelist",
	"mselect",
	"sselect",
	"tagbox",
	"textarea",
	"textbox"
);

/* grab the system data from the archive */
$data=file_get_contents("zip://".$NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid."/".$rid.".zip"."#record/script/db.json");

/* decrypt the data */
$data = $NVX_BOOT->JSON($data,"decode");

/* create an updated modified date entry */
$mod = date('Y-m-d H:i:00',$NVX_BOOT->FETCH_ENTRY("timestamp"));

/* ttp and ttc are sometimes NULL, which is returned by the db as an empty string - the following fixes that */
if($data["node"]["ttp"]==""){$data["node"]["ttp"]="NULL";}
if($data["node"]["ttc"]==""){$data["node"]["ttc"]="NULL";}

/* update the node */
$NVX_DB->CLEAR(array("ALL"));
$NVX_DB->SET_FILTER("`page`.`id`={$nid}");
$NVX_DB->QUERY("UPDATE","`page` SET "
		. "`page`.`title`='{$data["node"]["title"]}', "
		. "`page`.`heading`='{$data["node"]["heading"]}',"
		. "`page`.`teaser`='{$data["node"]["teaser"]}',"
		. "`page`.`alias`='{$data["node"]["alias"]}',"
		. "`page`.`description`='{$data["node"]["description"]}',"
		. "`page`.`body`='{$data["node"]["body"]}',"
		. "`page`.`importance`={$data["node"]["importance"]},"
		. "`page`.`sttp`={$data["node"]["sttp"]},"
		. "`page`.`sttc`={$data["node"]["sttc"]},"
		. "`page`.`published`={$data["node"]["published"]},"
		. "`page`.`ttp`={$data["node"]["ttp"]},"
		. "`page`.`ttc`={$data["node"]["ttc"]},"
		. "`page`.`date`='{$data["node"]["date"]}',"
		. "`page`.`modified`='{$mod}',"
		. "`page`.`by`={$_SESSION["id"]}");

		
/* connect to the zip archive */
$zip = new \ZipArchive;
$zip->open($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid."/".$rid.".zip");

/* cycle over the dtypes */
foreach($dtypes as $dt){
	
	/* clear any entries for this dtype and this node */
	$NVX_DB->CLEAR(array("ALL"));
	$NVX_DB->SET_FILTER("`nid`={$nid}");
	$NVX_DB->QUERY("DELETE","FROM `{$dt}`");
	

	/* do we have any data for the current dtype */
	if(array_key_exists($dt,$data)){
		
		/* cycle through the entries for this dtype */
		foreach($data[$dt] as $d){
			
			/* put the entry into the database */
			$NVX_DB->CLEAR(array("ALL"));
			$NVX_DB->QUERY("INSERT","INTO `{$dt}` (`id`,`nid`,`gid`,`vid`,`pid`,`fid`,`values`) " . 
							"VALUES ({$d["id"]},{$d["nid"]},{$d["gid"]},{$d["vid"]},{$d["pid"]},{$d["fid"]},'{$d["values"]}')");
							
			switch ($dt):
				
				case "filelist":
					
					/* decode the entry */
					$d["values"] = $NVX_BOOT->JSON(stripslashes($d["values"]),"decode");
					
					/* cycle through the entries to grab the file reference */
					foreach($d["values"] as $f){
							
						
						/* check we have a valid file name */
						if($f["name"]!=""){
							$zip->extractTo($NVX_BOOT->FETCH_ENTRY("documents")."/","record/documents/".$f["name"]);
							rename($NVX_BOOT->FETCH_ENTRY("documents")."/record/documents/".$f["name"],$NVX_BOOT->FETCH_ENTRY("documents")."/".$f["name"]);
						}
					}
					break;
					
				case "imagelist":
					
					/* decode the entry */
					$d["values"] = $NVX_BOOT->JSON(stripslashes($d["values"]),"decode");
					
					/* cycle through the entries to grab the file reference */
					foreach($d["values"] as $f){
							
						
						/* check we have a valid file name */
						if($f["name"]!=""){

							/* if not present, append .webp */
							if(!stristr($f['name'],'.webp')){
								$f['name'].='.webp';
							}
							$zip->extractTo($NVX_BOOT->FETCH_ENTRY("images")."/","record/cms/".$f["name"]);
							rename($NVX_BOOT->FETCH_ENTRY("images")."/record/cms/".$f["name"],$NVX_BOOT->FETCH_ENTRY("images")."/".$f["name"]);							
						}
					}
					break;					
					
			endswitch;
		}
	}
}

/* directory cleanup */
if(file_exists($NVX_BOOT->FETCH_ENTRY("documents")."/record/documents")){
	$NVX_BOOT->DEL_TREE($NVX_BOOT->FETCH_ENTRY("documents")."/record");
}

if(file_exists($NVX_BOOT->FETCH_ENTRY("images")."/record/cms")){
	$NVX_BOOT->DEL_TREE($NVX_BOOT->FETCH_ENTRY("images")."/record");
}


/* close the zip */
$zip->close();

/* copy the rollback to a new archive */
copy($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid."/".$rid.".zip",$NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid."/".$NVX_BOOT->FETCH_ENTRY("timestamp").".zip");

/* grab a list of the archives that exist for this page */
$files = glob($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid."/*.zip");
				
/* do we have twenty-one backups */
if(count($files)==21){
					
	/* cycle over the files */
	for($a=0;$a<count($files);$a++){

		/* strip the path and filename leaving only the timestamp*/
		$files[$a] = pathinfo($files[$a], PATHINFO_FILENAME);
	}

	/* sort the timestamps lowest to highest numerically */
	sort($files,SORT_NUMERIC);
					
	/* delete the oldest archive */
	unlink($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid."/{$files[0]}.zip");
}

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry rolled',
	'type'=>'success'
);

/* redirect to the rolledback content-edit */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/content/edit/{$nid}"));