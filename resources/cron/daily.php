<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/* automated tasks to be run every day */

/*  grab the type class*/
$TYPE = \NVOYX\site\Type::CONNECT(self::$DB,
					self::$BOOT,
					self::$VAR->FETCH_ENTRY("front")[0]);

/* create an array containing filelist,imagelist */
$orphans = array(array("documents","filelist"),array("images","imagelist"));

/* cycle through the array */
foreach($orphans as $orphan){
	
	/* create an empty array */
	$r = array();

	/* create a file iterator for the files folder */
	$i = new DirectoryIterator(self::$BOOT->FETCH_ENTRY($orphan[0]));

	/* cycle through the iterations */
	foreach ($i as $fileinfo) {
	
		/* if the file is not part of the OS navigation eg. not . or ..  and is not a .webm file */
		if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
		
			/* add the file to the array as a key with a zero value */
			$r[$fileinfo->getFilename()] = 0;
		}
		
		/* are we examining the imagelist and is the current file actually a directory and is it not part of the OS navigation */
		if($orphan[1]=="imagelist" && $fileinfo->isDir() && !$fileinfo->isDot()){
			
			/* grab the name of the folder */
			$folders[] = $fileinfo->getFilename();
		}
	}

	/* grab all the list items */
	self::$DB->CLEAR(array("ALL"));
	$lists = self::$DB->QUERY("SELECT","* FROM `{$orphan[1]}`");

	/* if we have some db entries */
	if($lists){

		/* cycle through the list entries */
		foreach($lists as $list){
		
			/* set a flag to zero */
			$f=0;
		
			/* grab the list json array of files */
			$files = self::$BOOT->JSON($list["{$orphan[1]}.values"],"decode");
		
			/* create an empty array to store any valid files */
			$nfiles = array();

			/* cycle through the file outlines */
			foreach($files as $file){
				
				/* during the switchover, some imagelist db entries contain .webp, others do not */
				if($orphan[1]=='imagelist'){
					if(stristr($file['name'],'.webp')===false){
						$file['name'].='.webp';
					}
				}
			
				/* check whether the current file exists in the directory array */
				if(array_key_exists($file["name"],$r)){
				
					/* mark the directory array to confirm that we have a database entry */
					$r[$file["name"]]=1;
				
					/* add this valid file into the file array */
					$nfiles[] = $file;
			
				} else {
				
					/* set a flag to 1 */
					$f=1;
				}
			}
		
			/* if we have found an entry in the db that does not exist in the directory array */
			if($f===1){
			
				/* take the valid files array and json encode it */
				$nfiles = self::$BOOT->JSON($nfiles,"encode");
		
				/* update the db listing for these files */
				self::$DB->CLEAR(array("ALL"));
				self::$DB->SET_LIMIT(1);
				self::$DB->SET_FILTER("`{$orphan[1]}`.`id`={$list["{$orphan[1]}.id"]}");
				self::$DB->QUERY("UPDATE","`{$orphan[1]}` SET `{$orphan[1]}`.`values`='{$nfiles}'");
			}
		}
	}

	/* cycle over the directory array */
	foreach($r as $key=>$file){

		/* did we fail to find this listing in the db */
		if($file===0){

			/* remove this file from the directory as it is no longer within the database */
			unlink(self::$BOOT->FETCH_ENTRY("{$orphan[0]}") . "/" . $key);
			
			/* are we examining the imagelist */
			if($orphan[1]=="imagelist"){
				
				/* cycle through the imagecache folders */
				foreach($folders as $folder){
					
					/* if an imagecache version exists */
					if(file_exists(self::$BOOT->FETCH_ENTRY("{$orphan[0]}") . "/{$folder}/" . str_replace(".webp",".png",$key))){
					
						/* delete the imagecache version */
						unlink(self::$BOOT->FETCH_ENTRY("{$orphan[0]}") . "/{$folder}/" . str_replace(".webp",".png",$key));
					}
					
					/* if a 2x imagecache version exists */
					if(file_exists(self::$BOOT->FETCH_ENTRY("{$orphan[0]}") . "/{$folder}/@2x."  . str_replace(".webp",".png",$key))){
					
						/* delete the 2x imagecache version */
						unlink(self::$BOOT->FETCH_ENTRY("{$orphan[0]}") . "/{$folder}/@2x."  . str_replace(".webp",".png",$key));
					}
					
					/* if an imagecache version exists */
					if(file_exists(self::$BOOT->FETCH_ENTRY("{$orphan[0]}") . "/{$folder}/" . str_replace(".webp",".jpg",$key))){
					
						/* delete the imagecache version */
						unlink(self::$BOOT->FETCH_ENTRY("{$orphan[0]}") . "/{$folder}/" . str_replace(".webp",".jpg",$key));
					}
					
					/* if a 2x imagecache version exists */
					if(file_exists(self::$BOOT->FETCH_ENTRY("{$orphan[0]}") . "/{$folder}/@2x."  . str_replace(".webp",".jpg",$key))){
					
						/* delete the 2x imagecache version */
						unlink(self::$BOOT->FETCH_ENTRY("{$orphan[0]}") . "/{$folder}/@2x."  . str_replace(".webp",".jpg",$key));
					}
				}
			}			
		}
	}	
}

/* is the website set to live */
if(self::$VAR->FETCH_ENTRY("live")[0]==1){
	
	/* send the current sitemap to google */
	$rs = file_get_contents(self::$BOOT->FETCH_ENTRY("sitemap"));
	
	/* grab all pages modified within the last  24 hours */
	self::$DB->CLEAR(array("ALL"));
	self::$DB->SET_FILTER("`page`.`modified` > DATE_SUB(NOW(), INTERVAL 1 DAY) AND `page`.published=1 AND `page`.`importance` !=0.0");
	$rs  = self::$DB->QUERY("SELECT","`page`.`id`,`page`.`title`,`page`.`tid`,`page`.`alias` FROM `page`");
	
	/* do we have any modified pages */
	if($rs){
		
		/* cycle through the modified pages */
		foreach($rs as $r){
			
			/* strip the page. substring from the keys */
			$r = self::$BOOT->KEY_SUBSTR_STRIP($r,"page.");
			
			/* resolve the page prefix */
			$prefix = $TYPE->PREFIXER($r);
			
			/* tag the prefix onto the page alias */
			if($prefix){
														
				/* add the type prefix to the page alias */
				$r["alias"]="/".$prefix."/".$r["alias"];
			} else {
					
				/* add a preceding forward slash */
				$r["alias"]="/".$r["alias"];
			}

			/* are we looking at the homepage */
			if($r["id"]==self::$VAR->FETCH_ENTRY('front')[0]){
					
				/* set the alias to blank */
				$r["alias"]="";
			}
			
			/* add domain details to the page reference */
			$r["alias"]  = self::$BOOT->FETCH_ENTRY('domain').$r["alias"];
			
			$content='<?xml version="1.0"?>'.
				'<methodCall>'.
				' <methodName>weblogUpdates.ping</methodName>'.
				'  <params>'.
				'   <param>'.
				'    <value>'.$r["title"].'</value>'.
				'   </param>'.
				'  <param>'.
				'   <value>http://'.$r["alias"].'</value>'.
				'  </param>'.
				' </params>'.
				'</methodCall>';
			$headers="POST / HTTP/1.0\r\n".
			"User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1) Gecko/20090624 Firefox/3.5 (.NET CLR 3.5.30729)\r\n".
			"Host: rpc.pingomatic.com\r\n".
			"Content-Type: text/xml\r\n".
			"Content-length: ".strlen($content);
			$request=$headers."\r\n\r\n".$content;
			$response = "";
			$fs=fsockopen('rpc.pingomatic.com',80, $errno, $errstr);
			if ($fs) {
				fwrite ($fs, $request);
				while (!feof($fs)) $response .= fgets($fs);
				fclose ($fs);
				preg_match_all("/<(name|value|boolean|string)>(.*)<\/(name|value|boolean|string)>/U",$response,$ar, PREG_PATTERN_ORDER);
				for($i=0;$i<count($ar[2]);$i++) $ar[2][$i]= strip_tags($ar[2][$i]);
			}
		}
	}	
}

/* check the times that files were last accessed in the images/twitter folder and remove anything older than two days */

/* create an empty array */
$r = array();

/* create a file iterator for the twitter avatar folder */
$i = new DirectoryIterator(self::$BOOT->FETCH_ENTRY("twitter"));

/* cycle through the iterations */
foreach ($i as $fileinfo) {
	
	/* if the file is not part of the OS navigation eg. not . or .. */
	if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
		
		/* has it been over 2 days since the image was last accessed */
		if(fileatime(self::$BOOT->FETCH_ENTRY("twitter"). "/" . $fileinfo->getFilename())<self::$BOOT->FETCH_ENTRY("timestamp")-172800){
			
			/* delete the image */
			unlink(self::$BOOT->FETCH_ENTRY("twitter"). "/" . $fileinfo->getFilename());
		}
	}
}