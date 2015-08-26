<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* clear the image entries */
$i = "";

/* reset the image count */
$icount = 0;

?>
<ul class='sortable col all100' id='<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-list"; ?>'>
<?php

/* if a maximum number of images wasn't stipulated, fix this at 100 */
if($OUTLINE["total"]==0){$OUTLINE["total"]=100;}

/* cycle through the values stored */
foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){

	/* reset the results variable */
	$rs="";

	/* do this iteration have a "name" entry in its array */
	if(key_exists("name",$VALUES)){

		/* increment the image counter by one */
		$icount++;

		/* have we added fewer than the maximum allowed files */
		if($icount<=$OUTLINE["total"]){

			?><li class='col all100 pad10 <?=$bc;?>'><?php
			
			/* grab the image name (convert .png to .webp) */
			$v = $VALUES["name"];

			?>
			<input type='hidden' name='<?="imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-name";?>' id='<?="imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-name";?>' value='<?=$v;?>' >
			<?php

			/* grab the image description */
			if(key_exists("desc",$VALUES)){$v=$VALUES["desc"];}else{$v="";}

			?>
			
			<!-- IMAGE THUMBNAIL -->
			<div class='col sml100 med33 lge33 pad-r10 sml-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-white pad-b5 grip bw'>&#8597;&nbsp;&nbsp;Thumbnail</label>
				<a target='_blank' href='/settings/resources/files/images/cms/0x0/<?=$VALUES['name'];?>.png'>
					<img class="col hgt30 brd-blue" src="/settings/resources/files/images/cms/0x30/<?=$VALUES['name'];?>.png">
				</a>
				<a onclick="deleteListItem(this);" class='fs14 c-white pad-l10' style="line-height:30px;">Delete</a>
			</div>
			
			<?php
				/* if the image doesn't have an associated link, we can make the description field 66% on lge view */
				if($OUTLINE["link"]==1){
					$lge=33;
					$lgepad='';
				} else {
					$lge=66;
					$lgepad=' lge-pad-r0';
				}
			?>
			<!-- IMAGE DESCRIPTION -->
			<div class='col sml100 med66 lge<?=$lge;?> pad-r10 sml-pad-r0 med-pad-r0<?=$lgepad;?> pad-b20'>
				<label class='col all100 fs13 c-white pad-b5'>Description</label>
				<input class='col all100 fs14 tb' name='<?="imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>' id='<?="imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>' type='text' maxlength='1024' value='<?=$v;?>' placeholder='Description'>
			</div>

			<?php

			/* does this imagelist have the link field enabled */
			if($OUTLINE["link"]==1){

				/* grab the image link */
				if(key_exists("link",$VALUES)){$v=$VALUES["link"];}else{$v="";}

				?>
			
				<!-- IMAGE LINK -->
				<div class='col sml100 med100 lge33 pad-b20'>
					<label class='col all100 fs13 c-white pad-b5'>Link</label>
					<input class='col all100 fs14 tb' name='<?="imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-link";?>' id='<?="imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-link";?>' type='text' maxlength='255' value='<?=$v;?>' placeholder='Link'>
				</div>
				<?php
			}

			/* does this imagelist have an additional field associated with it */
			if($OUTLINE["extra-type"]!="none"){

				/* is the additional textarea for plain or html text */
				if($OUTLINE["extra-type"]=="plain"){$r="plain";$OUTLINE["extra-editor"]="";}else{$r="html";}

				/* grab the extra text */
				if(key_exists("text",$VALUES)){$v=$VALUES["text"];}else{$v="";}

				?>
				
				<!-- IMAGE ADDITIONAL TEXT -->
				<div class='col all100 pad-b20'>
					<label class='col all100 fs13 c-white pad-b5'><?=ucwords($OUTLINE['extra-name']);?></label>
					<div class='col all100'>
						<textarea class='col all100 fs14 ta <?=$OUTLINE["extra-editor"]." ".$r;?>' data-editor='<?=$OUTLINE["extra-editor"];?>' name='<?="imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>' id='<?="imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>' maxlength='100000'><?=$v;?></textarea>
					</div>
				</div>

				<?php

			}
			?></li><?php
		}
	}
}

?>
</ul>	

<?php

/* based on maximum number files allowed, hide or show the drop zone */
if($icount>=$OUTLINE["total"]){$r="drop hide";}else{$r="drop";}

/* create a list of mime types to be used by the label title */
$tooltip = ".jpeg .png .gif ";
?>

<!-- IMAGE DROP -->
<div class='col all100 pad-b40'>
	<label class='col all100 fs13 c-white pad-b5'><?=ucwords($OUTLINE['name']);?> (drag and drop)</label>
	<div class="col all100 pad-b10 <?php echo $r;?>" id="<?="imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-drop";?>" data-type="imagelist" data-allowed="image/jpeg,image/png,image/gif" data-maxsize="<?=round($OUTLINE["size"]/1024);?>" data-maxfiles="<?=$OUTLINE["total"];?>" data-nuid="<?=$icount;?>" data-eeditor="<?=$OUTLINE["extra-editor"];?>" data-link="<?=$OUTLINE["link"];?>" data-elabel="<?=$OUTLINE["extra-name"];?>" data-etype="<?=$OUTLINE["extra-type"];?>">
		<div class="col all100 progressbar-container">
			<div class="col hgt30 progressbar b-white"></div>
		</div>
		<?php if($NVX_BOOT->FETCH_ENTRY('mobtab')==1) { ?>
			<div class='col all100 pad-tb10'>
				<input id='<?="imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-dropfallback";?>' class='col all100 fs12 c-white' type="file" accept="image/*" capture="camera">
			</div>
		<?php } ?>
	</div>
	<p class='fs13 c-white pad0'>
		<span><?=$icount;?></span> of <?=$OUTLINE["total"];?> ( <?=round($OUTLINE["size"]/1024);?> Mb ) <?=$tooltip;?>
	</p>
</div>