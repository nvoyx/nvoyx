<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* cycle through the values stored for this field */
foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){

	/* do we have a start date */
	if(key_exists("start",$VALUES)){$v=$VALUES["start"];}else{$v="";} ?>

	<!-- START -->
	<div class='col sml100 med50 lge50 pad-r10 sml-pad-r0 pad-b20'>
		<label class='col all100 fs13 c-white pad-b5'><?=ucwords($OUTLINE['name']);?></label>
		<input class='col all100 fs14 tb date <?=$OUTLINE['measure'];?>picker' name='<?="datebox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-start";?>' id='<?="datebox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-start";?>' type='text' value='<?=$v;?>' readonly placeholder='Start <?=ucwords($OUTLINE['measure']);?>'>
	</div>

	<?php

	/* does the field outline contain a finish date */
	if($OUTLINE["finish"]==1){

		/* does the page have a finish date stored */
		if(key_exists("finish",$VALUES)){$v = $VALUES["finish"];} else {$v = "";} ?>
	
		<!-- FINISH -->
		<div class='col sml100 med50 lge50 pad-r10 sml-pad-r0 med-pad-r0 lge-pad-r0 pad-b40'>
			<label class='col all100 fs13 c-white pad-b5'>Finish Date</label>
			<input class='col all100 fs14 tb date <?=$OUTLINE['measure'];?>picker' name='<?="datebox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-finish";?>' id='<?="datebox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-finish";?>' type='text' value='<?=$v;?>' readonly placeholder='Finish <?=ucwords($OUTLINE['measure']);?>'>
		</div>
		<?php
	}
}

