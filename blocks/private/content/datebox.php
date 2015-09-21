<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* cycle through the values stored for this field */
foreach($field["fid-{$outline["fid"]}"] as $iteration=>$values){

	/* do we have a start date */
	if(key_exists("start",$values)){$v=$values["start"];}else{$v="";} ?>

	<!-- START -->
	<div class='col sml100 med50 lge50 pad-r10 sml-pad-r0 pad-b20'>
		<label class='col all100 fs13 c-white pad-b5'><?=ucwords($outline['name']);?></label>
		<input class='col all100 fs14 tb date <?=$outline['measure'];?>picker' name='<?="datebox-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-start";?>' id='<?="datebox-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-start";?>' type='text' value='<?=$v;?>' readonly placeholder='Start <?=ucwords($outline['measure']);?>'>
	</div>

	<?php

	/* does the field outline contain a finish date */
	if($outline["finish"]==1){

		/* does the page have a finish date stored */
		if(key_exists("finish",$values)){$v = $values["finish"];} else {$v = "";} ?>
	
		<!-- FINISH -->
		<div class='col sml100 med50 lge50 pad-r10 sml-pad-r0 med-pad-r0 lge-pad-r0 pad-b40'>
			<label class='col all100 fs13 c-white pad-b5'>Finish Date</label>
			<input class='col all100 fs14 tb date <?=$outline['measure'];?>picker' name='<?="datebox-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-finish";?>' id='<?="datebox-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-finish";?>' type='text' value='<?=$v;?>' readonly placeholder='Finish <?=ucwords($outline['measure']);?>'>
		</div>
		<?php
	}
}

