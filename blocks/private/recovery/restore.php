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
$tid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* grab the page node */
$nid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",4);

/* grab the system data from the archive */
$data=file_get_contents("zip://".$NVX_BOOT->FETCH_ENTRY("recovery")."/".$tid."/".$nid.".zip"."#record/script/db.json");

/* decrypt the data */
$data = $NVX_BOOT->JSON($data,"decode");

/* create an updated modified date entry */
$mod = date('Y-m-d H:i:00',$NVX_BOOT->FETCH_ENTRY("timestamp"));

/* ttp and ttc are sometimes NULL, which is returned by the db as an empty string - the following fixes that */
if($data["node"]["ttp"]==""){$data["node"]["ttp"]="NULL";}
if($data["node"]["ttc"]==""){$data["node"]["ttc"]="NULL";}

/* update the node */
$NVX_DB->DB_CLEAR(array("ALL"));
$NVX_DB->DB_SET_FILTER("`page`.`id`={$nid}");
$NVX_DB->DB_QUERY("INSERT","INTO `page` (`id`,`tid`,`title`,`heading`,`teaser`,`alias`,`description`,`body`,`importance`,`sttp`,`sttc`,`published`,`ttp`,`ttc`,`date`,`modified`,`by`) " .
		"VALUES ({$nid},{$tid},'{$data["node"]["title"]}','{$data["node"]["heading"]}','{$data["node"]["teaser"]}','{$data["node"]["alias"]}',".
		"'{$data["node"]["description"]}','{$data["node"]["body"]}',{$data["node"]["importance"]},{$data["node"]["sttp"]},{$data["node"]["sttc"]},".
		"{$data["node"]["published"]},{$data["node"]["ttp"]},{$data["node"]["ttc"]},'{$data["node"]["date"]}','{$mod}',{$_SESSION["id"]})");
		
/* connect to the zip archive */
$zip = new ZipArchive;
$zip->open($NVX_BOOT->FETCH_ENTRY("recovery")."/".$tid."/".$nid.".zip");

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
	"textbox",
	"videolist"
);

/* cycle over the dtypes */
foreach($dtypes as $dt){

	/* do we have any data for the current dtype */
	if(array_key_exists($dt,$data)){
		
		/* cycle through the entries for this dtype */
		foreach($data[$dt] as $d){
			
			/* put the entry into the database */
			$NVX_DB->DB_CLEAR(array("ALL"));
			$NVX_DB->DB_QUERY("INSERT","INTO `{$dt}` (`id`,`nid`,`gid`,`vid`,`pid`,`fid`,`values`) " . 
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
							$zip->extractTo($NVX_BOOT->FETCH_ENTRY("images")."/","record/cms/".$f["name"]);
							rename($NVX_BOOT->FETCH_ENTRY("images")."/record/cms/".$f["name"],$NVX_BOOT->FETCH_ENTRY("images")."/".$f["name"]);							
						}
					}
					break;
					
				case "videolist":
					
					/* decode the entry */
					$d["values"] = $NVX_BOOT->JSON(stripslashes($d["values"]),"decode");
					
					/* cycle through the entries to grab the file reference */
					foreach($d["values"] as $f){
							
						/* check we have a valid file name */
						if($f["name"]!=""){
							
							/* grab the mp4 video from the archive */
							$zip->extractTo($NVX_BOOT->FETCH_ENTRY("videos")."/","record/videos/".$f["name"]);
							
							/* move the mp4 video to its proper location */
							rename($NVX_BOOT->FETCH_ENTRY("videos")."/record/videos/".$f["name"],$NVX_BOOT->FETCH_ENTRY("videos")."/".$f["name"]);
							
							/* grab the webm video from the archive */
							$zip->extractTo($NVX_BOOT->FETCH_ENTRY("videos")."/",   str_replace(".mp4".".webm","record/videos/".$f["name"]));
							
							/* move the webm video to its proper location */
							rename($NVX_BOOT->FETCH_ENTRY("videos")."/record/videos/".str_replace(".mp4",".webm",$f["name"]), $NVX_BOOT->FETCH_ENTRY("videos")."/".str_replace(".mp4",".webm",$f["name"]));
							
							/* does the thumbnail folder not exist */
							if(!file_exists($NVX_BOOT->FETCH_ENTRY("videos")."/".pathinfo($f["name"], PATHINFO_FILENAME))){
								
								/* make the thumbnail folder */
								mkdir($NVX_BOOT->FETCH_ENTRY("videos")."/".pathinfo($f["name"], PATHINFO_FILENAME));
								
								/* cycle through the potential thumbnails */
								for($i=0;$i<10;$i++){
									
									/* try and retrieve the thumbnail from the archive */
									if($zip->extractTo($NVX_BOOT->FETCH_ENTRY("videos")."/".pathinfo($f["name"], PATHINFO_FILENAME), "record/videos/".pathinfo($f["name"], PATHINFO_FILENAME)."/".$i.".png")){
										
										/* move the thumbnail to its proper location */
										rename($NVX_BOOT->FETCH_ENTRY("videos")."/".pathinfo($f["name"], PATHINFO_FILENAME)."/record/videos/".$i.".png",$NVX_BOOT->FETCH_ENTRY("videos")."/".pathinfo($f["name"], PATHINFO_FILENAME)."/".$i.".png");
									}
								}
								
								/* directory cleanup */
								if(file_exists($NVX_BOOT->FETCH_ENTRY("videos")."/".pathinfo($f["name"], PATHINFO_FILENAME)."/record/videos")){
									
									/* delete the record folder */
									$NVX_BOOT->DEL_TREE($NVX_BOOT->FETCH_ENTRY("videos")."/".pathinfo($f["name"], PATHINFO_FILENAME)."/record");
								}
								
							}
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

/* check that a rollback directory doesn't exist for this page */
if(!file_exists($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid)){
	
	/* make the rollback directory for this page */
	mkdir($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid);
}

/* copy the rollback to a new archive */
copy($NVX_BOOT->FETCH_ENTRY("recovery")."/".$tid."/".$nid.".zip",$NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid."/".$NVX_BOOT->FETCH_ENTRY("timestamp").".zip");

/* redirect to the restored content-edit */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/content/edit/{$nid}"));