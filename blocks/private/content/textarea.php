<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* cycle through the values stored for this field */
foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){

	/* should this textarea contain plain or html text (disable ckeditor if plain) */
	if($OUTLINE["plain"]==1){$r="plain";$OUTLINE["editor"]="";}else{$r="html";}

	/* if a maxlength of zero has been specified then return max size allowable by mysql field */
	if($OUTLINE["maxlength"]==0){$OUTLINE["maxlength"]="16777215";}

	/* do we have some text */
	if(key_exists("text",$VALUES)){$v=$VALUES["text"];}else{$v="";}

	?>

	<!-- TEXTAREA -->
	<div class='col all100 pad-b40'>
		<label class='col all100 fs13 c-white pad-b5'><?=ucwords($OUTLINE['name']);?></label>
		<div class='col all100'>
			<textarea data-editor="<?=$OUTLINE["editor"];?>" class='col all100 fs14 ta <?="{$OUTLINE["editor"]} {$r}";?>' name='<?="textarea-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>' id='<?="textarea-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>' maxlength='<?=$OUTLINE["maxlength"];?>'><?=$v;?></textarea>
		</div>
	</div>
	<?php
}