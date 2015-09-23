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
$nid = $nvBoot->fetch_entry("breadcrumb",3);

/* grab the rollback we are interested in */
$rid = $nvBoot->fetch_entry("breadcrumb",4);

/* array of tables */
$dtypes = array(
	"ajaxbox",
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
$data=file_get_contents("zip://".$nvBoot->fetch_entry("rollback")."/".$nid."/".$rid.".zip"."#record/script/db.json");

/* decrypt the data */
$data = $nvBoot->json($data,"decode");

/* create an updated modified date entry */
$mod = date('Y-m-d H:i:00',$nvBoot->fetch_entry("timestamp"));

/* ttp and ttc are sometimes NULL, which is returned by the db as an empty string - the following fixes that */
if($data["node"]["ttp"]==""){$data["node"]["ttp"]="NULL";}
if($data["node"]["ttc"]==""){$data["node"]["ttc"]="NULL";}

/* update the node */
$nvDb->clear(array("ALL"));
$nvDb->set_filter("`page`.`id`={$nid}");
$nvDb->query("UPDATE","`page` SET "
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
$zip->open($nvBoot->fetch_entry("rollback")."/".$nid."/".$rid.".zip");

/* cycle over the dtypes */
foreach($dtypes as $dt){
	
	/* clear any entries for this dtype and this node */
	$nvDb->clear(array("ALL"));
	$nvDb->set_filter("`nid`={$nid}");
	$nvDb->query("DELETE","FROM `{$dt}`");
	

	/* do we have any data for the current dtype */
	if(array_key_exists($dt,$data)){
		
		/* cycle through the entries for this dtype */
		foreach($data[$dt] as $d){
			
			/* put the entry into the database */
			$nvDb->clear(array("ALL"));
			$nvDb->query("INSERT","INTO `{$dt}` (`id`,`nid`,`gid`,`vid`,`pid`,`fid`,`values`) " . 
							"VALUES ({$d["id"]},{$d["nid"]},{$d["gid"]},{$d["vid"]},{$d["pid"]},{$d["fid"]},'{$d["values"]}')");
							
			switch ($dt):
				
				case "filelist":
					
					/* decode the entry */
					$d["values"] = $nvBoot->json(stripslashes($d["values"]),"decode");
					
					/* cycle through the entries to grab the file reference */
					foreach($d["values"] as $f){
							
						
						/* check we have a valid file name */
						if($f["name"]!=""){
							$zip->extractTo($nvBoot->fetch_entry("documents")."/","record/documents/".$f["name"]);
							rename($nvBoot->fetch_entry("documents")."/record/documents/".$f["name"],$nvBoot->fetch_entry("documents")."/".$f["name"]);
							$nvBoot->sync('documents/'.$f["name"],'file');
						}
					}
					break;
					
				case "imagelist":
					
					/* decode the entry */
					$d["values"] = $nvBoot->json(stripslashes($d["values"]),"decode");
					
					/* cycle through the entries to grab the file reference */
					foreach($d["values"] as $f){
							
						
						/* check we have a valid file name */
						if($f["name"]!=""){

							/* if not present, append .webp */
							if(!stristr($f['name'],'.webp')){
								$f['name'].='.webp';
							}
							$zip->extractTo($nvBoot->fetch_entry("images")."/","record/cms/".$f["name"]);
							rename($nvBoot->fetch_entry("images")."/record/cms/".$f["name"],$nvBoot->fetch_entry("images")."/".$f["name"]);
							$nvBoot->sync('images/cms'.$f["name"],'file');
						}
					}
					break;					
					
			endswitch;
		}
	}
}

/* directory cleanup */
if(file_exists($nvBoot->fetch_entry("documents")."/record/documents")){
	$nvBoot->del_tree($nvBoot->fetch_entry("documents")."/record");
}

if(file_exists($nvBoot->fetch_entry("images")."/record/cms")){
	$nvBoot->del_tree($nvBoot->fetch_entry("images")."/record");
}


/* close the zip */
$zip->close();

/* copy the rollback to a new archive */
copy($nvBoot->fetch_entry("rollback")."/".$nid."/".$rid.".zip",$nvBoot->fetch_entry("rollback")."/".$nid."/".$nvBoot->fetch_entry("timestamp").".zip");
$nvBoot->sync($nid."/".$nvBoot->fetch_entry("timestamp").".zip",'addrollbackzip');

/* grab a list of the archives that exist for this page */
$files = glob($nvBoot->fetch_entry("rollback")."/".$nid."/*.zip");
				
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
	unlink($nvBoot->fetch_entry("rollback")."/".$nid."/{$files[0]}.zip");
	$nvBoot->sync($nid."/{$files[0]}.zip",'deleterollbackzip');
}

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry rolled',
	'type'=>'success'
);

/* redirect to the rolledback content-edit */
$nvBoot->header(array("LOCATION"=>"/settings/content/edit/{$nid}"));