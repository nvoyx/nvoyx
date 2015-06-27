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
$nid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* check that the rollback folder exists */
if(!file_exists($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid)){
	
	/* make the rollback folder */
	mkdir($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid);
}

/* cycle through the relevant rollback node folder and return a list of files */
$files = glob($NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid."/*.zip");

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
		$files[$a] = $NVX_BOOT->FETCH_ENTRY("rollback")."/".$nid."/".$files[$a].".zip";
	}

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

	/* reset the response array */
	$rs = array();

	/* cycle through any archives that have been found */
	foreach($files as $f){ 

		/* grab the contents of the archive human-readable script */
		$c=file_get_contents("zip://".$f."#record/script/human.json");

		/* grab the file timestamp */
		$f=str_replace(".zip","", pathinfo($f, PATHINFO_FILENAME) );

		/* decode the contents of the json array (database script) */
		$r = $NVX_BOOT->JSON($c,"decode");

		/* create an empty group string */
		$g = "<br>";

		/* do we have any groups */
		foreach($r as $key=>$value){

			if(stristr($key,"gid-")){

				$gid = str_replace("gid-","",$key);
				$g .= "<strong>Group Name: </strong>"
						.$NVX_GROUP->FETCH_ARRAY()["id-{$gid}"]["name"]."<br>";

				foreach($value as $vari){

					foreach($vari as $fkey=>$fvalue){
						$fid = str_replace("fid-","",$fkey);

						foreach($NVX_GROUP->FETCH_ARRAY()["id-{$gid}"]["outline"] as $outline){

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


<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-rollback.png">
		<h2 class="blank fl">ROLLBACK</h2>
		<a class="fr" href="/settings/content/edit/<?=$nid;?>">UP</a>
	</div>
	
	<?php if(is_array($files)){$i=0;foreach($rs as $r){ ?>
	<div class="blank row" style="margin-bottom:10px;">
		<?php if($i!=0){?>
		<label class="blank fl half">Saved: <?php echo date("jS M Y - H:i:s",$r["timestamp"]);?><br>By: <?=$r["by"];?></label>
		<a title="roll" href="<?php echo "/settings/rollback/roll/".$nid."/".$r["timestamp"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
		<?php } else { ?>
		<label class="blank fl half">Current Version<br>By: <?=$r["by"];?></label>
		<?php } ?>
		<div class="cb" style="padding-top:10px;max-height:300px;overflow-y:scroll;">
			<pre style="background-color:#fff;padding:10px;">
				<strong>** SCROLL TO VIEW **</strong><br><br>
				<h2>[NODE]</h2><br><br>
				<strong>Heading:</strong><br>
				<?= $r["heading"];?><br><br>
				<strong>Teaser:</strong><br>
				<?= $r["teaser"];?><br><br>
				<strong>Body:</strong>
				<div  style="padding:10px;padding-top:0;">
					<?= $r["body"];?>
				</div>
				<h2>[GROUPS]</h2><br>
				<?= $r["groups"];?><br><br>
			</pre>
		</div>
	</div>
	<?php $i++;}} ?>
	
</div>