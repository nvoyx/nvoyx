<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns apc info
 */

/* does the url contain a cache to display */
if(array_key_exists("cache",$_GET)){
	
	/* sanitise the cache name */
	$r = $NVX_BOOT->TEXT($_GET["cache"]);

/* default to the user cache if non specified */
} else {$r = "user";}

/* does the url contain a request regarding clearing the cache */
if(array_key_exists("clear",$_GET)){
	
	/* sanitise the clear request */
	$c = $NVX_BOOT->TEXT($_GET["clear"]);

	/* if the clear request is positive */
	if($c==1){
		
		/* delete the specified cache */
		$NVX_BOOT->DELETE_CACHE($r);
	}
}

/* if the cache request is for user data, build the user titles */
if($r=="user"){
	$columns = array("name",
				"hits",
				"ctime",
				"refcount",
				"size",
				"value");
	
/* else build the file data title */
} else {
	$columns = array("name",
				"hits",
				"ctime",
				"file_mtime",
				"refcount",
				"phprefcount",
				"file_size");
}

/* grab the cache array */
$entries = $NVX_BOOT->GET_CACHE_ARRAY($r);


/* has the user cache been requested */
if($r=="user"){
	
	/* cycle through the user caches */
	for($x=0;$x<xcache_count(XC_TYPE_VAR);$x++){
		
		/* grab information on the user cache */
		$info[$x]=xcache_info(XC_TYPE_VAR,$x);
	}
} else {
	
	/* cycle through the opcode caches */
	for($x=0;$x<xcache_count(XC_TYPE_PHP);$x++){
		
		/* grab information on the opcode cache */
		$info[$x]=xcache_info(XC_TYPE_PHP,$x);
	}
}

?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>NVOY - <?=$NVX_BOOT->FETCH_ENTRY("current");?></title>
		<meta name="Generator" content="NVOYX Open Source CMS">
		<link rel="icon" type="image/png" href="/favicon.png">
		<link href='//fonts.googleapis.com/css?family=Lato:300normal,400normal&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		<style>
			body {font-family:"Lato";}
			table {margin:0 0 20px 0;background-color:#fff;padding:0 10px 10px 10px;border:1px #425770 solid; width:100%;}
			th {background-color:#425770;color:#fff;font-weight:normal;font-size:1.0em;line-height:1.4em;font-family:"Lato";padding:15px;text-align:left;border-top:10px #fff solid;word-wrap:break-word;}
			tr {background-color:#fff;font-size:12px;}
			td {color:#425770;font-size:1.0em;line-height:1.4em;font-family:"Lato";padding:15px;border-bottom:1px #425770 solid;vertical-align:top;word-wrap:break-word;}
			pre {margin:0 0;padding:0;}
			pre p {margin:0;padding:0 0 10px 0;}
			label {display:block;color:#423770;padding:20px 0 10px 0;width:100%;}
			h3 {margin:0px;padding:0 0 10px 0;}
		</style>
	</head>
	<body>
		<div class='width:80%;overflow:hidden;margin:auto;display:table;'>
		<label>XCache global <?= strtoupper($r);?> usage</label>
		<table>
			<thead>
				<tr>
					<th>Slots</th>
					<th>Updates</th>
					<th>Misses</th>
					<th>Hits</th>
					<th>Errors</th>
					<th>Cached</th>
					<th>Size</th>
					<th>Availability</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($info as $i){ ?>
				<tr>
					<td><?=$i["slots"];?></td>
					<td><?=$i["updates"];?></td>
					<td><?=$i["misses"];?></td>
					<td><?=$i["hits"];?></td>
					<td><?=$i["errors"];?></td>
					<td><?=$i["cached"];?></td>
					<td><?=implode($NVX_BOOT->HUMAN_FILESIZE($i["size"]));?></td>
					<td><?=implode($NVX_BOOT->HUMAN_FILESIZE($i["avail"]));?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		
		<label>Select a cache to view.</label>
			
		<select onchange="window.location = '?cache=' + this.value + '&clear=0';">
			<option value="user"<?php if($r=="user"){echo" selected";}?>>User</option>
			<option value="file"<?php if($r=="file"){echo" selected";}?>>File</option>
		</select>
		
		<label><a onclick="window.location = '<?="?cache=".$r;?>&clear=1';">Click to delete this cache</a></label>
		
		<table>
			<thead>
				<tr>
					<?php foreach($columns as $column){ ?>
						<th><?=ucwords(str_replace("_"," ",$column));?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php $x=0;if($entries){foreach($entries as $entry){ $x++; ?>
				<tr>
					<?php foreach($columns as $column){
						if($column=="ctime" || $column=="atime" || $column=="file_mtime"){
							$entry[$column] = date("j/M/Y",$entry[$column])."<br>".date("H:i:s",$entry[$column]);
						}
						if($column=="size" || $column=="file_size"){
							$entry[$column] = implode($NVX_BOOT->HUMAN_FILESIZE($entry[$column]));
						}
						if($r=="user"){
							if($column=="ttl"){
								$entry[$column]=$NVX_BOOT->HUMAN_TIME($entry[$column]);
							}
						}
					?>
					<td><?=$entry[$column];?></td>
					<?php } ?>
				</tr>
				<?php if($x==10){ ?>
				<tr>
					<?php foreach($columns as $column){ ?>
					<th><?=$column;?></th>
					<?php } ?>
				</tr>
				<?php $x=0;} ?>
				<?php }} ?>
			</tbody>
		</table>
		</div>
	</body>
</html>