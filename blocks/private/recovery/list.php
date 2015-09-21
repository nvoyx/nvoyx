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
$nvDb->clear(array("ALL"));
$nvDb->set_filter("`user`.`id`={$_SESSION['id']}");
$type_filter = $nvDb->query("SELECT","* FROM `user`")[0]["user.filter"];

/* grab all currently registered users */
$nvDb->clear(array("ALL"));
$users = $nvDb->query("SELECT","* FROM `user`");

/* create an empty user array */
$u = array();

/* cycle over the fetched user array */
foreach($users as $user){

	/* create a new array based on the user id containing the decrypted user contact details */
	$u[$user["user.id"]] = $nvBoot->cypher('decrypt',$user["user.contact"]);
}

/* create an empty array to hold page type details */
$type = array();

/* create an array to hold the pages */
$pages = array();

/* cycle over the page types */
foreach($nvType->fetch_array() as $t){
	
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
	if(file_exists($nvBoot->fetch_entry("recovery") ."/".$t)){
		
		/* grab a list of any recovery files */
		$files = glob($nvBoot->fetch_entry("recovery")."/".$t."/*.zip");
				
		/* do we have an array */
		if(is_array($files)){
			
			/* cycle through the recovery files */
			foreach($files as $f){
				
				/* grab the contents of the archive human-readable script */
				$c=file_get_contents("zip://".$nvBoot->fetch_entry("recovery")."/".$t."/".pathinfo($f, PATHINFO_BASENAME)."#record/script/human.json");
				
				/*decode the json script */
				$c = $nvBoot->json($c,"decode");
				
				/* add the details for this page to the array */
				$pages[$t][]=array(
					"id"=>$c["id"],
					"title"=>$c["title"],
					"modified"=>date("jS M Y - H:i:s",  strtotime($c["modified"])),
					"timestamp"=>strtotime($c["modified"]),
					"by"=>$u[$c["by"]]
				);
			}
			
			$pages[$t] = $nvBoot->sort_by_keys(array(
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

<!-- MAIN MENU -->
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='col all40'>
			<img height='24' src="/settings/resources/files/images/private/nvoy.svg">
		</div>
		<div class='col all60 tar fs14 pad-t5'>
			<a href='/settings/content/list' class='pad-r5 c-blue pad-b0'>Admin</a>
			<a href='/' class='pad-lr5 c-blue pad-b0'>Front</a>
			<a href='/settings/user/logout' class='pad-l5 c-blue pad-b0'>Logout</a>
		</div>
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>

<!-- RECOVERY TYPES -->
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='row pad-b20'>
			<div class='col all100'>
				<h1 class='pad0 fs20 c-blue'>Recovery</h1>
			</div>
		</div>
		
		<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
			<label class='col all100 fs13 c-blue pad-b5'>Page Type</label>
			<select class='col all100 fs14 ss' name='tid' id='tid' placeholder="Please Select" onchange='dropfilter(this,-1);'>
				<?php $x=0;foreach($type as $k=>$v){ ?>
				<option<?php if($v==$tfilter){echo " selected";}?> value='<?=$v;?>'><?=$k;?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>

<!-- DELETED PAGES -->
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='row pad-b20'>
			<div class='col all100'>
				<h1 class='pad0 fs20 c-blue'>Deleted Pages</h1>
			</div>
		</div>
		
		<?php foreach($type as $k=>$v){
			$hide=($v==$tfilter)?'':' hide';
			$x=0;
			if(array_key_exists($v,$pages)){
				if(count($pages[$v]) > 0){
					foreach($pages[$v] as $c){
						$c['bc']=($x%2==0)?'b-lblue':'b-vlblue';
						?>
						
						<div class='dropfilter filter-<?=$v;?> row pad10 c-white <?=$c['bc'];?><?=$hide;?>'>
							<div class='col all70 pad-r20'>
								<p class='pad0 fs14 bw'><?=$c['title'];?></p>
								<p class='pad0 fs12 bw'><?=$c['modified'];?><br><?=$c['by'];?></p>
							</div>
							<div class='col all30 fs14 tar'>
								<a href='/settings/recovery/restore/<?=$v;?>/<?=$c['id'];?>' class='pad-r5 pad-b0 hvr-white'>Restore</a>
							</div>
						</div>
						<?php $x++;
					}
				}
			}
		}
		?>
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>
