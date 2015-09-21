<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns list all archives currently associated with the passed node
 */

/*grab the page id we are interested in */
$nid = $nvBoot->fetch_entry("breadcrumb",3);

/* check that the rollback folder exists */
if(!file_exists($nvBoot->fetch_entry("rollback")."/".$nid)){
	
	/* make the rollback folder */
	mkdir($nvBoot->fetch_entry("rollback")."/".$nid);
}

/* cycle through the relevant rollback node folder and return a list of files */
$files = glob($nvBoot->fetch_entry("rollback")."/".$nid."/*.zip");

/* if we have an array*/
if(is_array($files)){

	/* cycle over the files */
	for($a=0;$a<count($files);$a++){

		/* strip the path and filename leaving only the timestamp*/
		$files[$a] = pathinfo($files[$a], PATHINFO_FILENAME);
	}

	/* sort the timestamps highest to lowest numerically */
	rsort($files,SORT_NUMERIC);

	/* cycle over the files */
	for($a=0;$a<count($files);$a++){

		/* add the timestamp and filetype back in */
		$files[$a] = $nvBoot->fetch_entry("rollback")."/".$nid."/".$files[$a].".zip";
	}

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

	/* reset the response array */
	$rs = array();

	/* cycle through any archives that have been found */
	foreach($files as $f){ 

		/* grab the contents of the archive human-readable script */
		$c=file_get_contents("zip://".$f."#record/script/human.json");

		/* grab the file timestamp */
		$f=str_replace(".zip","", pathinfo($f, PATHINFO_FILENAME) );

		/* decode the contents of the json array (database script) */
		$r = $nvBoot->json($c,"decode");

		/* create an empty group string */
		$g = "<br>";

		/* do we have any groups */
		foreach($r as $key=>$value){

			if(stristr($key,"gid-")){

				$gid = str_replace("gid-","",$key);
				$g .= "<strong>Group Name: </strong>"
						.$nvGroup->fetch_array()["id-{$gid}"]["name"]."<br>";

				foreach($value as $vari){

					foreach($vari as $fkey=>$fvalue){
						$fid = str_replace("fid-","",$fkey);

						foreach($nvGroup->fetch_array()["id-{$gid}"]["outline"] as $outline){

							if($outline["fid"]==$fid){

								$g .= "<div style='padding:0 10px 0 10px;'><strong>Field: </strong> {$outline["name"]}<br>";

								foreach($fvalue as $iteration){

									foreach($iteration as $datakey => $datavalue){

										if(is_array($datavalue)){
											$datavalue=implode(" | ",$datavalue);
										}
										$g .= "<div style='padding:0 10px 0 10px;'><strong>{$datakey}:</strong> {$datavalue}<br></div>";
									}
								}
								$g .="<br></div>";
							}
						}
					}
				}
			}
		}

		/* build an array for each archive */
		$rs[] =array(
			"heading"=>$r["heading"],
			"teaser"=>$r["teaser"],
			"body"=>$r["body"],
			"saved"=>date("jS M Y - H:i:s",  $f  ),
			"by"=>$u[$r["by"]],
			"timestamp" => $f,
			"groups"=> $g
		);
	}
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

<!-- ROLLBACK LIST -->
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='row pad-b20'>
			<div class='col all70 pad-r20'>
				<h1 class='pad0 fs20 c-blue'>Rollback</h1>
			</div>
			<div class='col all30 tar fs14 lh30'>
				<a href='/settings/content/edit/<?=$nid;?>' class='pad-r5 c-blue pad-b0'>Up</a>
			</div>
		</div>
		
	<?php if(is_array($files)){$i=0;foreach($rs as $r){ ?>
	<div class="col all100 pad-t20">
		<?php if($i!=0){?>
		<label class='col all100 fs13 c-blue pad-b5'>Saved: <?=date("jS M Y - H:i:s",$r["timestamp"]);?><br>By: <?=$r["by"];?></label>
		<a class='col all100 fs13 c-blue pad-b5' href="<?php echo "/settings/rollback/roll/".$nid."/".$r["timestamp"];?>">Click to Rollback</a>
		<?php } else { ?>
		<label class="col all100 fs13 c-blue pad-b5">Current Version<br>By: <?=$r["by"];?></label>
		<?php } ?>
		<pre class="col all100 pad-lr10 pad-tb20 hgt300 b-blue c-white fs13" style="overflow-y:scroll;">
			<strong>** SCROLL TO VIEW **</strong><br><br>
			<h2 class='c-white pad-b10'>[NODE]</h2><br><br>
			<strong>Heading:</strong><br>
			<?= $r["heading"];?><br><br>
			<strong>Teaser:</strong><br>
			<?= $r["teaser"];?><br><br>
			<strong>Body:</strong>
			<div  style="padding:10px;padding-top:0;">
				<?= $r["body"];?>
			</div>
			<h2 class='c-white pad-b10'>[GROUPS]</h2><br>
			<?= $r["groups"];?><br><br>
	</div>
	<?php $i++;}} ?>		
		
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>