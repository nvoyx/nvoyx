<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns folder information
 */

/* array of cms folders with content to be viewed */
$folders = array("ckimages","documents","images","session","tmp","videos");

/* do we have a get variable folder */
if(array_key_exists("folder",$_GET)){
	
	/* grab the folder reference */
	$f=$NVX_BOOT->TEXT($_GET["folder"]);
} else {
	
	/* go with the first folder */
	$f = "ckimages";
}

$mf = $NVX_BOOT->FETCH_ENTRY("mimes");

/* create a file iterator for the files folder */
$i = new DirectoryIterator($NVX_BOOT->FETCH_ENTRY($f));

$sz = array(" Bytes","KB","MB","GB","TB","P");

/* cycle through the iterations */
$x=0;
foreach ($i as $fileinfo) {

	/* if the file is not part of the OS navigation eg. not . or .. */
	if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
		$docs[$x]["name"] = $fileinfo->getFilename();
		$docs[$x]["timestamp"] = $fileinfo->getMTime();
		$docs[$x]["date"] = date("jS M Y H:i:s",$docs[$x]["timestamp"]);
		$docs[$x]["extension"] = $fileinfo->getExtension();
		$docs[$x]["size"] = $NVX_BOOT->HUMAN_FILESIZE($fileinfo->getSize());
		if(file_exists($mf."/".$docs[$x]["extension"].".png")){
			$docs[$x]["mime"] = "/settings/resources/files/images/mimes/".$docs[$x]["extension"].".png";
		} else {
			$docs[$x]["mime"] = "/settings/resources/files/images/mimes/default.png";
		}
		$docs[$x]["owner"] = posix_getpwuid($fileinfo->getOwner())["name"];
		$docs[$x]["group"] = posix_getpwuid($fileinfo->getGroup())["name"];
		$x++;
	}
}


?>

<label style="color:#425770;">Choose A Folder<br></label>
<select onchange="window.location = '?folder=' + this.value;">
	<?php foreach($folders as $folder){ if($f==$folder){$selected="selected ";}else{$selected="";} ?>
	<option <?=$selected;?>value="<?=$folder;?>"><?=$folder;?></option>
	<?php } ?>
</select>

<?php
$im = array("png","gif","jpg","jpeg","bmp");
?>

<?php if(isset($docs)){foreach($docs as $doc){ ?>

<div class="blank" style="padding:10px 10px 0 10px;background-color:#fff;border:1px #425770 solid;margin:10px 0 0 0;">
	<div class="blank fl" style="width:50%">
		<div class="blank">
			<img class="fl" src="<?=$doc["mime"];?>" width="16" height="16">
			<h2 class="fl" style="padding:0 0 0 10px;margin:0;"><?=$doc["name"];?></h2>
		</div>
		<p>Modified <em><?=$doc["date"];?></em><br>File Size <em><?=implode("",$doc["size"]);?></em><br>Owner <em><?=$doc["owner"];?></em> : <em><?=$doc["group"];?></em></p>
	</div>
	<div class="blank fl" style="width:50%;text-align:right">
		<?php
		if(in_array($doc["extension"],$im)){
			
			/* convert the file path to an array */
			$r = explode("/",$NVX_BOOT->FETCH_ENTRY($f));
			?>
		<img src="/settings/resources/files/images/<?=end($r);?>/<?=$doc["name"];?>" height="86">
		<?php } ?>
	</div>
</div>
<?php }}