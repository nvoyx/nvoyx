<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns list all recovery files from previously deleted nodes
 */

/* grab the page type */
$tid = $nvBoot->fetch_entry("breadcrumb",3);

/* grab the page node */
$nid = $nvBoot->fetch_entry("breadcrumb",4);

/* grab the system data from the archive */
$data=file_get_contents("zip://".$nvBoot->fetch_entry("recovery")."/".$tid."/".$nid.".zip"."#record/script/db.json");

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
$nvDb->query("INSERT","INTO `page` (`id`,`tid`,`title`,`heading`,`teaser`,`alias`,`description`,`body`,`importance`,`sttp`,`sttc`,`published`,`ttp`,`ttc`,`date`,`modified`,`by`) " .
		"VALUES ({$nid},{$tid},'{$data["node"]["title"]}','{$data["node"]["heading"]}','{$data["node"]["teaser"]}','{$data["node"]["alias"]}',".
		"'{$data["node"]["description"]}','{$data["node"]["body"]}',{$data["node"]["importance"]},{$data["node"]["sttp"]},{$data["node"]["sttc"]},".
		"{$data["node"]["published"]},{$data["node"]["ttp"]},{$data["node"]["ttc"]},'{$data["node"]["date"]}','{$mod}',{$_SESSION["id"]})");
		
/* connect to the zip archive */
$zip = new \ZipArchive;
$zip->open($nvBoot->fetch_entry("recovery")."/".$tid."/".$nid.".zip");

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

/* cycle over the dtypes */
foreach($dtypes as $dt){

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

/* check that a rollback directory doesn't exist for this page */
if(!file_exists($nvBoot->fetch_entry("rollback")."/".$nid)){
	
	/* make the rollback directory for this page */
	mkdir($nvBoot->fetch_entry("rollback")."/".$nid);
	$nvBoot->sync($nid,'newrollbackfolder');
}

/* copy the rollback to a new archive */
copy($nvBoot->fetch_entry("recovery")."/".$tid."/".$nid.".zip",$nvBoot->fetch_entry("rollback")."/".$nid."/".$nvBoot->fetch_entry("timestamp").".zip");
$nvBoot->sync($nid."/".$nvBoot->fetch_entry("timestamp").".zip",'addrollbackzip');


/* delete the recovery file */
unlink($nvBoot->fetch_entry("recovery")."/".$tid."/".$nid.".zip");
$nvBoot->sync($tid."/".$nid.".zip",'deleterecoveryzip');

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry restored',
	'type'=>'success'
);

/* redirect to the restored content-edit */
$nvBoot->header(array("LOCATION"=>"/settings/content/edit/{$nid}"));