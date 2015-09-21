<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* cycle through the values stored for this field */
foreach($field["fid-{$outline["fid"]}"] as $iteration=>$values){

	/* should this textarea contain plain or html text (disable ckeditor if plain) */
	if($outline["plain"]==1){$r="plain";$outline["editor"]="";}else{$r="html";}

	/* if a maxlength of zero has been specified then return max size allowable by mysql field */
	if($outline["maxlength"]==0){$outline["maxlength"]="16777215";}

	/* do we have some text */
	if(key_exists("text",$values)){$v=$values["text"];}else{$v="";}

	?>

	<!-- TEXTAREA -->
	<div class='col all100 pad-b40'>
		<label class='col all100 fs13 c-white pad-b5'><?=ucwords($outline['name']);?></label>
		<div class='col all100'>
			<textarea data-editor="<?=$outline["editor"];?>" class='col all100 fs14 ta <?="{$outline["editor"]} {$r}";?>' name='<?="textarea-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-text{$r}";?>' id='<?="textarea-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-text{$r}";?>' maxlength='<?=$outline["maxlength"];?>'><?=$v;?></textarea>
		</div>
	</div>
	<?php
}