<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* clear file entries */
$f="";

/* reset the file count to zero */
$fcount = 0;

?>
<ul class='sortable col all100' id='<?="filelist-{$group["id"]}-{$vari}-{$outline["fid"]}-list"; ?>'>
<?php

/* if a maximum number of files wasn't stipulated, fix this at 100 */
if($outline["total"]==0){$outline["total"]=100;}

/* cycle through the values stored */
foreach($field["fid-{$outline["fid"]}"] as $iteration=>$values){

	/* reset the results variable */
	$rs="";

	/* do this iteration have a "name" entry in its array */
	if(key_exists("name",$values)){

		/* increment the file count by one */
		$fcount++;

		/* have we added fewer than the maximum allowed files */
		if($fcount<=$outline["total"]){

		?><li class='col all100 pad10 <?=$bc;?>'><?php

		/* grab the file name */
		$v = $values["name"];

		?>
		<input type='hidden' name='<?="filelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-name";?>' id='<?="filelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-name";?>' value='<?=$v;?>' >
		<?php

		/* grab the file size */
		if(key_exists("size",$values)){$v=$values["size"];}else{$v="";}

		?>
		<input type='hidden' name='<?="filelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-size";?>' id='<?="filelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-size";?>' value='<?=$v;?>' >
		<?php

		/* grab the file type */
		if(key_exists("type",$values)){$v=$values["type"];}else{$v="";}

		?>
		<input type='hidden' name='<?="filelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-type";?>' id='<?="filelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-type";?>' value='<?=$v;?>' >
		<?php

		/* grab the file description */
		if(key_exists("desc",$values)){$v=$values["desc"];}else{$v="";}

		?>
		
		<!-- FILE -->
		<div class='col all100 pad-b20'>
			<label class='col all100 fs13 c-white pad-b5 grip bw'>&#8597;&nbsp;&nbsp;Description</label>
			<input class='col all100 fs14 tb mar-b5' name='<?="filelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-desc";?>' id='<?="filelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-desc";?>' type='text' maxlength='1024' value='<?=$v;?>' placeholder='Description'>
			<a class='fs14 c-white' target='_blank' href='<?="/settings/resources/files/documents/".$values['name'];?>'>Download</a>&nbsp;&nbsp;
			<a class='fs14 c-white' onclick="deleteListItem(this);">Delete</a>
		</div>

		</li>
		<?php
		}
	}
}

?>
</ul>


<?php
/* based on maximum number files allowed, hide or show the drop zone */
if($fcount>=$outline["total"]){$r="drop hide";}else{$r="drop";}

/* create a list of mime types to be used by the label title */
$tooltip = "";
foreach($outline["filetypes"] as $ft){$tooltip .= ".".substr($ft,strpos($ft,"/")+1)." ";}

?>

<!-- FILE DROP -->
<div class='col all100 pad-b40'>
	<label class='col all100 fs13 c-white pad-b5'><?=ucwords($outline['name']);?> (drag and drop)</label>
	<div class="col all100 pad-b10 <?=$r;?>" id="<?="filelist-{$group["id"]}-{$vari}-{$outline["fid"]}-drop";?>" data-type="filelist" data-allowed="<?=implode(",",$outline["filetypes"]);?>" data-maxsize="<?=round($outline["size"]/1024);?>" data-maxfiles="<?=$outline["total"];?>" data-nuid="<?=$fcount;?>">
		<div class="col all100 progressbar-container">
			<div class="col hgt30 progressbar b-white"></div>
		</div>
	</div>
	<p class='fs13 c-white pad0'>
		<?=$fcount;?> of <?=$outline["total"];?> ( <?=round($outline["size"]/1024);?> Mb ) <?=$tooltip;?>
	</p>
</div>