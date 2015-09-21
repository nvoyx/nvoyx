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
<ul class='sortable col all100' id='<?php echo "imagelist-{$group["id"]}-{$vari}-{$outline["fid"]}-list"; ?>'>
<?php

/* if a maximum number of images wasn't stipulated, fix this at 100 */
if($outline["total"]==0){$outline["total"]=100;}

/* cycle through the values stored */
foreach($field["fid-{$outline["fid"]}"] as $iteration=>$values){

	/* reset the results variable */
	$rs="";

	/* do this iteration have a "name" entry in its array */
	if(key_exists("name",$values)){

		/* increment the image counter by one */
		$icount++;

		/* have we added fewer than the maximum allowed files */
		if($icount<=$outline["total"]){

			?><li class='col all100 pad10 <?=$bc;?>'><?php
			
			/* grab the image name (convert .png to .webp) */
			$v = $values["name"];

			?>
			<input type='hidden' name='<?="imagelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-name";?>' id='<?="imagelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-name";?>' value='<?=$v;?>' >
			<?php

			/* grab the image description */
			if(key_exists("desc",$values)){$v=$values["desc"];}else{$v="";}

			?>
			
			<!-- IMAGE THUMBNAIL -->
			<div class='col sml100 med33 lge33 pad-r10 sml-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-white pad-b5 grip bw'>&#8597;&nbsp;&nbsp;Thumbnail</label>
				<a target='_blank' href='/settings/resources/files/images/cms/0x0/<?=$values['name'];?>.png'>
					<img class="col hgt30 brd-blue" src="/settings/resources/files/images/cms/0x30/<?=$values['name'];?>.png">
				</a>
				<a onclick="deleteListItem(this);" class='fs14 c-white pad-l10' style="line-height:30px;">Delete</a>
			</div>
			
			<?php
				/* if the image doesn't have an associated link, we can make the description field 66% on lge view */
				if($outline["link"]==1){
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
				<input class='col all100 fs14 tb' name='<?="imagelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-desc";?>' id='<?="imagelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-desc";?>' type='text' maxlength='1024' value='<?=htmlentities($v,ENT_QUOTES);?>' placeholder='Description'>
			</div>

			<?php

			/* does this imagelist have the link field enabled */
			if($outline["link"]==1){

				/* grab the image link */
				if(key_exists("link",$values)){$v=$values["link"];}else{$v="";}

				?>
			
				<!-- IMAGE LINK -->
				<div class='col sml100 med100 lge33 pad-b20'>
					<label class='col all100 fs13 c-white pad-b5'>Link</label>
					<input class='col all100 fs14 tb' name='<?="imagelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-link";?>' id='<?="imagelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-link";?>' type='text' maxlength='255' value='<?=htmlentities($v,ENT_QUOTES);?>' placeholder='Link'>
				</div>
				<?php
			}

			/* does this imagelist have an additional field associated with it */
			if($outline["extra-type"]!="none"){

				/* is the additional textarea for plain or html text */
				if($outline["extra-type"]=="plain"){$r="plain";$outline["extra-editor"]="";}else{$r="html";}

				/* grab the extra text */
				if(key_exists("text",$values)){$v=$values["text"];}else{$v="";}

				?>
				
				<!-- IMAGE ADDITIONAL TEXT -->
				<div class='col all100 pad-b20'>
					<label class='col all100 fs13 c-white pad-b5'><?=ucwords($outline['extra-name']);?></label>
					<div class='col all100'>
						<textarea class='col all100 fs14 ta <?=$outline["extra-editor"]." ".$r;?>' data-editor='<?=$outline["extra-editor"];?>' name='<?="imagelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-text{$r}";?>' id='<?="imagelist-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-text{$r}";?>' maxlength='100000'><?=$v;?></textarea>
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
if($icount>=$outline["total"]){$r="drop hide";}else{$r="drop";}

/* create a list of mime types to be used by the label title */
$tooltip = ".jpeg .png .gif ";
?>

<!-- IMAGE DROP -->
<div class='col all100 pad-b40'>
	<label class='col all100 fs13 c-white pad-b5'><?=ucwords($outline['name']);?> (drag and drop)</label>
	<div class="col all100 pad-b10 <?php echo $r;?>" id="<?="imagelist-{$group["id"]}-{$vari}-{$outline["fid"]}-drop";?>" data-type="imagelist" data-allowed="image/jpeg,image/png,image/gif" data-maxsize="<?=round($outline["size"]/1024);?>" data-maxfiles="<?=$outline["total"];?>" data-nuid="<?=$icount;?>" data-eeditor="<?=$outline["extra-editor"];?>" data-link="<?=$outline["link"];?>" data-elabel="<?=$outline["extra-name"];?>" data-etype="<?=$outline["extra-type"];?>">
		<div class="col all100 progressbar-container">
			<div class="col hgt30 progressbar b-white"></div>
		</div>
		<?php if($nvBoot->fetch_entry('mobtab')==1) { ?>
			<div class='col all100 pad-tb10'>
				<input id='<?="imagelist-{$group["id"]}-{$vari}-{$outline["fid"]}-dropfallback";?>' class='col all100 fs12 c-white' type="file" accept="image/*" capture="camera">
			</div>
		<?php } ?>
	</div>
	<p class='fs13 c-white pad0'>
		<span><?=$icount;?></span> of <?=$outline["total"];?> ( <?=round($outline["size"]/1024);?> Mb ) <?=$tooltip;?>
	</p>
</div>