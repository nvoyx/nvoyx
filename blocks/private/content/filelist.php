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
<ul class='sortable col all100' id='<?="filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-list"; ?>'>
<?php

/* if a maximum number of files wasn't stipulated, fix this at 100 */
if($OUTLINE["total"]==0){$OUTLINE["total"]=100;}

/* cycle through the values stored */
foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){

	/* reset the results variable */
	$rs="";

	/* do this iteration have a "name" entry in its array */
	if(key_exists("name",$VALUES)){

		/* increment the file count by one */
		$fcount++;

		/* have we added fewer than the maximum allowed files */
		if($fcount<=$OUTLINE["total"]){

		?><li class='col all100 pad10 <?=$bc;?>'><?php

		/* grab the file name */
		$v = $VALUES["name"];

		?>
		<input type='hidden' name='<?="filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-name";?>' id='<?="filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-name";?>' value='<?=$v;?>' >
		<?php

		/* grab the file size */
		if(key_exists("size",$VALUES)){$v=$VALUES["size"];}else{$v="";}

		?>
		<input type='hidden' name='<?="filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-size";?>' id='<?="filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-size";?>' value='<?=$v;?>' >
		<?php

		/* grab the file type */
		if(key_exists("type",$VALUES)){$v=$VALUES["type"];}else{$v="";}

		?>
		<input type='hidden' name='<?="filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-type";?>' id='<?="filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-type";?>' value='<?=$v;?>' >
		<?php

		/* grab the file description */
		if(key_exists("desc",$VALUES)){$v=$VALUES["desc"];}else{$v="";}

		?>
		
		<!-- FILE -->
		<div class='col all100 pad-b20'>
			<label class='col all100 fs13 c-white pad-b5 grip bw'>&#8597;&nbsp;&nbsp;Description</label>
			<input class='col all100 fs14 tb mar-b5' name='<?="filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>' id='<?="filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>' type='text' maxlength='1024' value='<?=$v;?>' placeholder='Description'>
			<a class='fs14 c-white' target='_blank' href='<?="/settings/resources/files/documents/".$VALUES['name'];?>'>Download</a>&nbsp;&nbsp;
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
if($fcount>=$OUTLINE["total"]){$r="drop hide";}else{$r="drop";}

/* create a list of mime types to be used by the label title */
$tooltip = "";
foreach($OUTLINE["filetypes"] as $ft){$tooltip .= ".".substr($ft,strpos($ft,"/")+1)." ";}

?>

<!-- FILE DROP -->
<div class='col all100 pad-b40'>
	<label class='col all100 fs13 c-white pad-b5'><?=ucwords($OUTLINE['name']);?> (drag and drop)</label>
	<div class="col all100 pad-b10 <?=$r;?>" id="<?="filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-drop";?>" data-type="filelist" data-allowed="<?=implode(",",$OUTLINE["filetypes"]);?>" data-maxsize="<?=round($OUTLINE["size"]/1024);?>" data-maxfiles="<?=$OUTLINE["total"];?>" data-nuid="<?=$fcount;?>">
		<div class="col all100 progressbar-container">
			<div class="col hgt30 progressbar b-white"></div>
		</div>
	</div>
	<p class='fs13 c-white pad0'>
		<?=$fcount;?> of <?=$OUTLINE["total"];?> ( <?=round($OUTLINE["size"]/1024);?> Mb ) <?=$tooltip;?>
	</p>
</div>