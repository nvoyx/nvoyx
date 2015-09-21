<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/* automated tasks to be run every day */

$type = \nvoy\site\Type::connect(self::$db,
					self::$boot,
					self::$var->fetch_entry("front")[0]);

$orphans = array(array("documents","filelist"),array("images","imagelist"));

foreach($orphans as $orphan){
	$r = array();
	$i = new DirectoryIterator(self::$boot->fetch_entry($orphan[0]));
	foreach ($i as $fileinfo) {
		if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
			$r[$fileinfo->getFilename()] = 0;
		}
		if($orphan[1]=="imagelist" && $fileinfo->isDir() && !$fileinfo->isDot()){
			$folders[] = $fileinfo->getFilename();
		}
	}

	self::$db->clear(array("ALL"));
	$lists = self::$db->query("SELECT","* FROM `{$orphan[1]}`");
	if($lists){
		foreach($lists as $list){
			$f=0;
			$files = self::$boot->json($list["{$orphan[1]}.values"],"decode");
			$nfiles = array();
			foreach($files as $file){
				if($orphan[1]=='imagelist'){
					if(stristr($file['name'],'.webp')===false){
						$file['name'].='.webp';
					}
				}
				if(array_key_exists($file["name"],$r)){
					$r[$file["name"]]=1;
					$nfiles[] = $file;
				} else {
					$f=1;
				}
			}
			if($f===1){
				$nfiles = self::$boot->json($nfiles,"encode");
				self::$db->clear(array("ALL"));
				self::$db->set_limit(1);
				self::$db->set_filter("`{$orphan[1]}`.`id`={$list["{$orphan[1]}.id"]}");
				self::$db->query("UPDATE","`{$orphan[1]}` SET `{$orphan[1]}`.`values`='{$nfiles}'");
			}
		}
	}
	foreach($r as $key=>$file){
		if($file===0){
			unlink(self::$boot->fetch_entry("{$orphan[0]}") . "/" . $key);
			if($orphan[1]=="imagelist"){
				foreach($folders as $folder){
					if(file_exists(self::$boot->fetch_entry("{$orphan[0]}") . "/{$folder}/" . str_replace(".webp",".png",$key))){
						unlink(self::$boot->fetch_entry("{$orphan[0]}") . "/{$folder}/" . str_replace(".webp",".png",$key));
					}
					if(file_exists(self::$boot->fetch_entry("{$orphan[0]}") . "/{$folder}/@2x."  . str_replace(".webp",".png",$key))){
						unlink(self::$boot->fetch_entry("{$orphan[0]}") . "/{$folder}/@2x."  . str_replace(".webp",".png",$key));
					}
					if(file_exists(self::$boot->fetch_entry("{$orphan[0]}") . "/{$folder}/" . str_replace(".webp",".jpg",$key))){
						unlink(self::$boot->fetch_entry("{$orphan[0]}") . "/{$folder}/" . str_replace(".webp",".jpg",$key));
					}
					if(file_exists(self::$boot->fetch_entry("{$orphan[0]}") . "/{$folder}/@2x."  . str_replace(".webp",".jpg",$key))){
						unlink(self::$boot->fetch_entry("{$orphan[0]}") . "/{$folder}/@2x."  . str_replace(".webp",".jpg",$key));
					}
				}
			}			
		}
	}	
}

if(self::$var->fetch_entry("live")[0]==1){
	$rs = file_get_contents(self::$boot->fetch_entry("sitemap"));
	self::$db->clear(array("ALL"));
	self::$db->set_filter("`page`.`modified` > DATE_SUB(NOW(), INTERVAL 1 DAY) AND `page`.published=1 AND `page`.`importance` !=0.0");
	$rs  = self::$db->query("SELECT","`page`.`id`,`page`.`title`,`page`.`tid`,`page`.`alias` FROM `page`");
	if($rs){
		foreach($rs as $r){
			$r = self::$boot->key_substr_strip($r,"page.");
			$prefix = $type->prefixer($r);
			if($prefix){
				$r["alias"]="/".$prefix."/".$r["alias"];
			} else {
				$r["alias"]="/".$r["alias"];
			}
			if($r["id"]==self::$var->fetch_entry('front')[0]){
				$r["alias"]="";
			}
			$r["alias"]  = self::$boot->fetch_entry('domain').$r["alias"];
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
				while (!feof($fs)){
					$response .= fgets($fs);
				}
				fclose ($fs);
				preg_match_all("/<(name|value|boolean|string)>(.*)<\/(name|value|boolean|string)>/U",$response,$ar, PREG_PATTERN_ORDER);
				for($i=0;$i<count($ar[2]);$i++){
					$ar[2][$i]= strip_tags($ar[2][$i]);
				}
			}
		}
	}	
}