<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* cycle through the values stored for this field */
foreach($field["fid-{$outline["fid"]}"] as $iteration=>$values){

	/* do we have some text */
	if(key_exists("text",$values)){$v=$values["text"];}else{$v="";}

	?>

	<!-- TEXTBOX -->
	<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b40'>
		<label class='col all100 fs13 c-white pad-b5'><?=ucwords($outline['name']);?></label>
		<input class='col all100 fs14 tb' name='<?="textbox-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-text";?>' id='<?="textbox-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-text";?>' type='text' maxlength='<?=$outline["maxlength"];?>' value='<?=$v;?>'>
	</div>
	<?php
}