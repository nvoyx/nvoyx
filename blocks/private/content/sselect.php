<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* cycle through the values stored for this field */
foreach($field["fid-{$outline["fid"]}"] as $iteration=>$values){

	/* do we have a selected value */
	if(key_exists("selected",$values)){$v=$values["selected"];}else{$v="[none]";}

	/* reset the results variable */
	$rs=array();

	/* cycle through any select options associated with this field */
	foreach($outline["content"] as $key => $value){

		/* add the two values to the results array */
		$rs[] = array("INTERNAL"=>$value,"EXTERNAL"=>$key);
	}

	?>

	<!-- SINGLE SELECT -->
	<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b40'>
		<label class='col all100 fs13 c-white pad-b5'><?=ucwords($outline['name']);?></label>
		<select class='col all100 fs14 ss' name='<?="sselect-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-option";?>' id='<?="sselect-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-option";?>' placeholder="Please Select">
			<?php 
			foreach ($rs as $r){
			if($v==$r["INTERNAL"]){$flg = " selected";} else {$flg="";} ?>
			<option<?=$flg;?> value="<?=$r["INTERNAL"];?>"><?=$r["EXTERNAL"];?></option>
			<?php } ?>
		</select>
	</div>

	<?php
}


