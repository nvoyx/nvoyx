<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* cycle through the values stored for this field */
foreach($field["fid-{$outline["fid"]}"] as $iteration=>$values){

	/* do we have a response */
	if(key_exists("results",$values)){
		$v=$nvBoot->json($values["results"],'encode');
		if($v=='[{"ok":"0"}]'){
			$v='';
		}
	}else{$v='';}
	?>

	<!-- AJAXBOX -->
	<div class='col all100 pad-b40'>
		<label class='col all100 fs13 c-white pad-b5'><?=ucwords($outline['name']);?></label>
		<input onkeyup='ajaxbox(this,"<?=$outline['file'];?>")' class='col all100 fs14 tb mar-b10' name='<?="ajaxbox-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-lookup";?>' id='<?="ajaxbox-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-lookup";?>' type='text' autocomplete='off' value=''>
		<textarea class='col all100 fs14 ta plain' name='<?="ajaxbox-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-results";?>' id='<?="ajaxbox-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-results";?>' readonly tabindex="-1"><?=$v;?></textarea>
	</div>
	<?php
}