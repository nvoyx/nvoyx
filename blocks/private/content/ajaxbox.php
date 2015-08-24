<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* cycle through the values stored for this field */
foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){

	/* do we have a response */
	if(key_exists("results",$VALUES)){
		$v=$NVX_BOOT->JSON($VALUES["results"],'encode');
		if($v=='[{"ok":"0"}]'){
			$v='';
		}
	}else{$v='';}
	?>

	<!-- AJAXBOX -->
	<div class='col all100 pad-b40'>
		<label class='col all100 fs13 c-white pad-b5'><?=ucwords($OUTLINE['name']);?></label>
		<input onkeyup='ajaxbox(this,"<?=$OUTLINE['file'];?>")' class='col all100 fs14 tb mar-b10' name='<?="ajaxbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-lookup";?>' id='<?="ajaxbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-lookup";?>' type='text' autocomplete='off' value=''>
		<textarea class='col all100 fs14 ta plain' name='<?="ajaxbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-results";?>' id='<?="ajaxbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-results";?>' readonly tabindex="-1"><?=$v;?></textarea>
	</div>
	<?php
}