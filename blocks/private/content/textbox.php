<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* cycle through the values stored for this field */
foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){

	/* do we have some text */
	if(key_exists("text",$VALUES)){$v=$VALUES["text"];}else{$v="";}

	?>

	<!-- TEXTBOX -->
	<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b40'>
		<label class='col all100 fs13 c-white pad-b5'><?=ucwords($OUTLINE['name']);?></label>
		<input class='col all100 fs14 tb' name='<?="textbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text";?>' id='<?="textbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text";?>' type='text' maxlength='128' maxlength='<?=$OUTLINE["maxlength"];?>' value='<?=$v;?>'>
	</div>
	<?php
}