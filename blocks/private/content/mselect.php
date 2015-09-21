<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* holder for any select options to be selected for this page */
$v=array();

/* cycle through the available select options (one in each array entry) */
foreach($field["fid-{$outline["fid"]}"] as $iteration=>$values){

	/* if we have a selected entry */
	if(key_exists("selected",$values)){

		/* add the selected item to an array */
		$v[]=$values["selected"];
	}
}

/* reset the results variable */
$rs=array();

foreach($outline["content"] as $key => $value){

	/* cycle through any select options associated with this field */
	$rs[] = array("INTERNAL"=>$value,"EXTERNAL"=>$key);
}

?>

<!-- MULTIPLE SELECT -->
<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b40'>
	<label class='col all100 fs13 c-white pad-b5'><?=ucwords($outline['name']);?></label>
	<select class='col all100 fs14 ms' name='<?="mselect-{$group["id"]}-{$vari}-{$outline["fid"]}-0-options[]";?>' id='<?="mselect-{$group["id"]}-{$vari}-{$outline["fid"]}-0-options";?>' placeholder="Please Select" multiple>
		<?php 
		foreach ($rs as $r){
		if(in_array($r["INTERNAL"],$v)){$flg = " selected";} else {$flg="";} ?>
		<option<?=$flg;?> value="<?=$r["INTERNAL"];?>"><?=$r["EXTERNAL"];?></option>
		<?php } ?>
	</select>
</div>