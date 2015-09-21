<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* create two arrays, one to hold the html tags and one to hold the json tags */
$v=array();
$jsonv = array();

/* cycle through the values stored for this field */
foreach($field["fid-{$outline["fid"]}"] as $iteration=>$values){

	/* do we have a tag */
	if(key_exists("tag",$values)){

			/* wrap each of the tags in a-tags */ 
			$v[] = "<span class='tag'><a class=\"fs14 c-white pad-r10\" onclick=\"deleteTag(this,'#tagbox-{$group["id"]}-{$vari}-{$outline["fid"]}-0-tags','{$values["tag"]}')\">{$values["tag"]}</a></span>";
			$jsonv[] = $values["tag"];
	}
}

/* we have found no tags */
if(count($v)==0){

	/* set the two arrays to empty */
	$v=array("");
	$jsonv = "[\"\"]";
} else {

	/* encode the json array */
	$jsonv = $nvBoot->json($jsonv,"encode");
}

?>

<!-- TAG LOOKUP -->
<div class='col all100 pad-b20'>
	<label class='col all100 fs13 c-white pad-b5'><?=ucwords($outline['name']);?></label>
	<input class='col all100 fs14 tb' onkeyup='fetchTags(this,<?=$type['id'];?>,<?="\"#tagbox-{$group["id"]}-{$vari}-{$outline["fid"]}-0-addtags\""; ?>);' name="<?php echo "ignore-{$group["id"]}-{$vari}-{$outline["fid"]}-0-tags";?>" id="<?php echo "ignore-{$group["id"]}-{$vari}-{$outline["fid"]}-0-tags";?>" type="text" value="">
	<input name="<?="tagbox-{$group["id"]}-{$vari}-{$outline["fid"]}-0-tags";?>" id="<?="tagbox-{$group["id"]}-{$vari}-{$outline["fid"]}-0-tags";?>" type="hidden" value='<?=$jsonv;?>'>
</div>

<!-- CURRENT TAGS -->
<div class='col all50 sml100 med50 lge50 pad-r10 sml-pad-r0 pad-b40'>
	<label class='col all100 fs13 c-white pad-b5'>Current Tags</label>
	<div id='<?="tagbox-{$group["id"]}-{$vari}-{$outline["fid"]}-0-deletetags";?>' class='col all100 fs14 ta current-tags'><?php echo implode("",$v); ?></div>
</div>

<!-- AVAILABLE TAGS -->
<div class='col all50 sml100 med50 lge50 pad-b40'>
	<label class='col all100 fs13 c-white pad-b5'>Available Tags</label>
	<div id='<?="tagbox-{$group["id"]}-{$vari}-{$outline["fid"]}-0-addtags";?>' class='col all100 fs14 ta current-tags'></div>
</div>