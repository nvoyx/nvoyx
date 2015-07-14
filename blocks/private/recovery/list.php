<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns recovery listings
 */

/* grab the "type filter variable" */
$NVX_DB->DB_CLEAR(array("ALL"));
$NVX_DB->DB_SET_FILTER("`user`.`id`={$_SESSION['id']}");
$type_filter = $NVX_DB->DB_QUERY("SELECT","* FROM `user`")[0]["user.filter"];

/* grab all currently registered users */
$NVX_DB->DB_CLEAR(array("ALL"));
$users = $NVX_DB->DB_QUERY("SELECT","* FROM `user`");

/* create an empty user array */
$u = array();

/* cycle over the fetched user array */
foreach($users as $user){

	/* create a new array based on the user id containing the decrypted user contact details */
	$u[$user["user.id"]] = $NVX_BOOT->CYPHER(array("STRING"=>$user["user.contact"],"TYPE"=>"decrypt"));
}

/* create an empty array to hold page type details */
$type = array();

/* create an array to hold the pages */
$pages = array();

/* cycle over the page types */
foreach($NVX_TYPE->FETCH_ARRAY() as $t){
	
	/* create key value pairs of type name and id */
	$type[$t["name"]]=$t["id"];
	
	/* create an empty array of pages based on the current type */
	$pages[$t["id"]] = array();
}

/* sort the type pairs by key */
ksort($type);

/* counter for the node types */
$tcnt=0;

/* cycle through the sorted types */
foreach($type as $t){
	
	/* update the current tfilter */
	if($tcnt==$type_filter){$tfilter = $t;}
	
	/* do we have a recovery folder for this page type */
	if(file_exists($NVX_BOOT->FETCH_ENTRY("recovery") ."/".$t)){
		
		/* grab a list of any recovery files */
		$files = glob($NVX_BOOT->FETCH_ENTRY("recovery")."/".$t."/*.zip");
				
		/* do we have an array */
		if(is_array($files)){
			
			/* cycle through the recovery files */
			foreach($files as $f){
				
				/* grab the contents of the archive human-readable script */
				$c=file_get_contents("zip://".$NVX_BOOT->FETCH_ENTRY("recovery")."/".$t."/".pathinfo($f, PATHINFO_BASENAME)."#record/script/human.json");
				
				/*decode the json script */
				$c = $NVX_BOOT->JSON($c,"decode");
				
				/* add the details for this page to the array */
				$pages[$t][]=array(
					"id"=>$c["id"],
					"title"=>$c["title"],
					"modified"=>date("jS M Y - H:i:s",  strtotime($c["modified"])),
					"timestamp"=>strtotime($c["modified"]),
					"by"=>$u[$c["by"]]
				);
			}
			
			$pages[$t] = $NVX_BOOT->SORT_BY_KEYS(array(
				"ARRAY"=>$pages[$t],
				"SORT"=> array(
					array("KEYS"=>array("timestamp"),"DIRECTION"=>"SORT_DESC")
				)
			));			
		}
	}
	
	$tcnt++;
}

?>


<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-recovery.png">
		<h2 class="blank fl">RECOVERY</h2>
		<a class="fr" href="/settings/content/list">UP</a>
	</div>
</div>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-type.png">
		<h2 class="blank fl">TYPES</h2>
	</div>
	
	<div class="blank row">
		<label class="blank fl">Options</label>
		<div class="blank select fr small">
			<?php foreach($type as $tkey=>$tval){ ?>
			<a class='blank mini content-list-item<?php if($tval==$tfilter){echo " selected";}?>' onclick="select(this,'content-list-types');return false;"><?=$tkey;?></a>
			<?php } ?>
		</div>
		<select class="hide" name="content-list-types" id="content-list-types">
			<?php foreach($type as $tval){ ?>
			<option value="<?=$tval;?>"<?php if($tval==$tfilter){echo " selected";}?>></option>
			<?php } ?>
		</select>
	</div>
</div>

<?php $b=0;foreach($type as $t){  ?>

<div class="blank box content-list-type" id="content-list-type-<?=$t;?>" <?php if($t!=$tfilter){ echo "style='display:none'";} ?>>
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-content.png">
		<h2 class="blank fl">DELETED PAGES</h2>
	</div>
	
	<?php
		
	/* do we have a page type reference */
	if(array_key_exists($t,$pages)){
			
		/* do we have at least one page */
		if(count($pages[$t]) > 0){
				
			/* cycle over the deleted pages for this file type */
			foreach($pages[$t] as $c){ ?>
					
				<div class="blank row">
					<label class="blank fl half"><?=ucwords($c["title"]);?><br><span class="tt"><?=$c["modified"];?> - <?=$c["by"];?></span></label>
					<a title="restore" href="<?php echo "/settings/recovery/restore/".$t."/".$c["id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
				</div>

			<?php }
		}
	}
	?>
</div>


<?php $b++;} ?>
