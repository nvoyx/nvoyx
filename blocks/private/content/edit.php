<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * edit / update the current page content
 */

/* ------------------------------ PAGE,FIELD,LANGUAGE ARRAYS --------------------------------- */

/* fetch possible TIDs based on nid */
$rs = $NVX_TYPE->FETCH_MATCHES(array("NID"=>$NVX_BOOT->FETCH_ENTRY("breadcrumb",3),
									"USER"=>$NVX_USER->FETCH_ENTRY("type")
									));

/* populate PAGE based on possible TIDs (array or integer as string) and lowest alias */
$NVX_PAGE->FIND(array("NID"=>$NVX_BOOT->FETCH_ENTRY("breadcrumb",3),
						"TIDS"=>$rs,
						"USER"=>$NVX_USER->FETCH_ENTRY("type"),
						"FIELDS"=>true
						));
				
/* grab current PAGE variable */
$rs = $NVX_PAGE->FETCH_ARRAY();

if(isset($rs)){	
	
	/* set $PAGE */
	$r=array_keys($rs);
	$PAGE = $rs[array_shift($r)];
}

/* confirm valid page found */
if(!isset($PAGE["id"])){
	/* END */
	die();
}

/* fetch a list of all content types */
$TYPES = $NVX_TYPE->FETCH_ARRAY();

/* store information relating to the current page type */
$TYPE = $NVX_TYPE->FETCH_BY_TID($PAGE["tid"]);

/* does the current user have sufficient privileges to create and delete pages of this type */
if(stristr($NVX_USER->FETCH_ENTRY("type"),$TYPE["createdelete"])){
	$create = "";
} else {$create = " hide";}

/* grab infortmation concerning all available groups */
$GROUPS = $NVX_GROUP->FETCH_ARRAY();

/* grab information concerning any variants currently included for this page */
$NVIDS = $PAGE["nvids"];

/* view url */

/* create a default sselected variable */
$sselected = "";

/* create a default mselected variable */
$mselected = "";

/* does the current prefix contain an sselect tag or an mselect tag */
if(stristr($TYPE["prefix"],"[ss:") || stristr($TYPE["prefix"],"[ms:")){
	
	/* grab the tag type */
	if(stristr($TYPE["prefix"],"[ss:")){$tag="ss";}else {$tag="ms";}
	
	/* grab everything after the start of the tag definition */
	$r = substr($TYPE["prefix"],strpos($TYPE["prefix"],"[{$tag}:")+4);
				
	/* grab everything until the closing of the tag */
	$r = substr($r,0,strpos($r,"]"));
					
	/* convert the gid-vid-fid to an array */
	$x = explode("-",$r);
					
	/* go grab the selected listing */
	$selected = $PAGE["gid-{$x[0]}"]["vid-{$x[1]}"]["fid-{$x[2]}"][0]["selected"];
	
	/* grab an array group containing the sselect */
	$gs = $NVX_GROUP->FETCH_ARRAY()["id-{$x[0]}"]["outline"];
	
	/* cycle through the group */
	foreach ($gs as $g){
		
		/* have we found the right group */
		if($g["fid"]==$x[2]){
			
			/* cycle through the options */
			foreach($g["content"] as $option){
				
				/* if this option holds the same internal value as the current page */
				if($selected == $option){
					
					/* grab the external reference */
					if($tag=="ss"){
						$sselected = $option;break;
					} else {
						$mselected = $option;break;
					}
				}
								
			}
		}
	}
					
}

$r = $NVX_HTML->URL(array("NID"=>$PAGE["id"],
							"PREFIX"=>$TYPE["prefix"],
							"ALIAS"=>$PAGE["alias"],
							"TITLE"=>$PAGE["title"],
							"HEADING"=>$PAGE["heading"],
							"TAGS"=>array("CREATED"=>$PAGE["date"],"NODE"=>$PAGE["id"],"SSELECT"=>$sselected,"MSELECT"=>$mselected)
							));

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

<form method="post">

	<!-- CONTENT -->
	<section class='col all100'>
		<div class='col sml5 med10 lge15'></div>
		<div class='col box sml90 med80 lge70'>
			<div class='row pad-b20'>
				<div class='col all20 sml100 pad-r20 sml-pad-b10'>
					<h1 class='pad0 fs20 c-blue'>Content</h1>
				</div>
				<div class='col all80 sml100 tar sml-tal fs14 lh30'>
					<a href='/settings/content/add/<?=$PAGE['tid'];?>' class='pad-r5 c-blue pad-b0'>New</a>
					<a href='/settings/content/list' class='pad-lr5 c-blue pad-b0'>Up</a>
					<a onclick="deleteCheck('/settings/content/delete/<?=$PAGE['id'];?>');" class='pad-lr5 c-blue pad-b0'>Delete</a>
					<a href='/settings/rollback/list/<?=$PAGE['id'];?>' class='pad-lr5 c-blue pad-b0'>Rollback</a>
					<a onclick="$('#submit').click();" class='pad-lr5 c-blue pad-b0'>Save</a>
					<a href='//<?=$r['URL'];?>' class='pad-l5 c-blue pad-b0'>View</a>
				</div>
			</div>
			
			<?php if(stristr($NVX_USER->FETCH_ENTRY("type"),'s')){$visibility="";}else{$visibility=" hide";} ?>

			<!-- NODE ID -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20<?=$visibility;?>'>
				<label class='col all100 fs13 c-blue pad-b5'>Node Id</label>
				<input class='col all100 fs14 tb' name='page-id' id='page-id' type='text' maxlength='255' value='<?=$PAGE['id'];?>' placeholder='Node Id' readonly tabindex='-1'>
			</div>

			<!-- TYPE ID -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20<?=$visibility;?>'>
				<label class='col all100 fs13 c-blue pad-b5'>Type Id</label>
				<input class='col all100 fs14 tb' name='page-tid' id='page-tid' type='text' maxlength='255' value='<?=$TYPE['id'];?>' placeholder='Type Id' readonly tabindex='-1'>
			</div>

			<!-- TYPE REF -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 lge-pad-r0 pad-b20<?=$visibility;?>'>
				<label class='col all100 fs13 c-blue pad-b5'>Type Reference</label>
				<input class='col all100 fs14 tb' name='page-tref' id='page-tref' type='text' maxlength='255' value='<?=$TYPE['name'];?>' placeholder='Type Name' readonly tabindex='-1'>
			</div>
		</div>
		<div class='col sml5 med10 lge15'></div>
	</section>

	<!-- NODE CONTENT -->
	<section class='col all100'>
		<div class='col sml5 med10 lge15'></div>
		<div class='col box sml90 med80 lge70'>
			<div class='row pad-b20'>
				<div class='col all100'>
					<h1 class='pad0 fs20 c-blue'>Node</h1>
				</div>
			</div>

			<!-- PAGE TITLE -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Node Id</label>
				<input name="page-oldtitle" id="page-oldtitle" type="hidden" maxlength="255" value="<?=$PAGE["title"];?>">
				<input name="page-prefix" id="page-oldprefix" type="hidden" maxlength="2048" value="<?=substr($r['URL'],strpos($r['URL'],'/'),strrpos($r['URL'],'/')-strpos($r['URL'],'/'));?>">
				<input class='col all100 fs14 tb' name='page-title' id='page-title' type='text' maxlength='255' value='<?=$PAGE['title'];?>' placeholder='Page Title' autofocus>
			</div>

			<!-- PAGE HEADING -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Heading</label>
				<input class='col all100 fs14 tb' name='page-heading' id='page-heading' type='text' maxlength='2048' value='<?=$PAGE['heading'];?>' placeholder='Page Heading'>
			</div>

			<!-- PAGE TEASER -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 lge-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Teaser</label>
				<input class='col all100 fs14 tb' name='page-teaser' id='page-teaser' type='text' maxlength='2048' value='<?=$PAGE['teaser'];?>' placeholder='Page Teaser'>
			</div>

			<!-- PAGE BODY -->
			<div class='col all100 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Body</label>
				<div class='col all100'>
					<textarea class='col all100 fs14 ta ckPrivate' name='page-body' id='page-body' maxlength='16777215'><?=$PAGE["body"];?></textarea>
				</div>
			</div>

		</div>
		<div class='col sml5 med10 lge15'></div>
	</section>

	<?php
	
	/* some of the field types require maximum screen width */
	$full_width_types=array(
		'datebox',
		'heirarchy',
		'imagelist',
		'filelist',
		'tagbox'
	);

	/* cycle through all the groups */
	foreach($GROUPS as $GROUP){

		/* reset a count of variations found for this group */
		$VARICNT=0;

		/* is this group associated with this page */
		if(in_array($PAGE["tid"],$GROUP["assoc"])){

			/* does this page currently NOT have this group within its $PAGE nvids reference */
			if(!key_exists($GROUP["id"],$NVIDS)){

				/* create an initial reference */
				$NVIDS[$GROUP["id"]] = "0";

			}

			/* do field entries NOT exist for this page */
			if(!key_exists("gid-".$GROUP["id"],$PAGE)){

				/* update the next variant reference */
				$NVIDS[$GROUP["id"]] = $NVIDS[$GROUP["id"]];

				/* cycle through the group fields */
				foreach($GROUP["outline"] as $FIELD){

						/* add empty field references for the group variation */
						$PAGE["gid-".$GROUP["id"]]["vid-".$NVIDS[$GROUP["id"]]]["fid-".$FIELD["fid"]][0]=array();
				}
			}	


			/* clear the variation html holder */
			$vhtml = "";

			/* one variation should always be available, so set a variable to hide the variation delete option */
			if(count($PAGE["gid-".$GROUP["id"]])==1){$vdel=" hide";}else{$vdel="";}


			/* does the current user have sufficient privileges to view / edit this group */
			if(stristr($NVX_USER->FETCH_ENTRY("type"),$GROUP["access"])){
				$access = "";
			} else {$access = " hide";}

			/* START BUILDING THE GROUP HTML HERE */
			?>

			<!-- <?=strtoupper($GROUP['name']);?> CONTENT -->
			<section class='col all100<?=$access;?>'>
				<div class='col sml5 med10 lge15'></div>
				<div class='col box sml90 med80 lge70'>
					<div class='row pad-b20'>
						<div class='col all100'>
							<a onclick='groupCompress(this);' class='pad0 fs20 c-blue'><?=$GROUP['name'];?></a>
						</div>
					</div>

					<ul id='group-<?=$GROUP['id'];?>' class='sortable col all100 compressed'>
						
						<?php
						
						$lc=0;
						
						/* cycle through each of the group variations (already in position order) */
						foreach($PAGE["gid-".$GROUP["id"]] as $VARI=>$FIELD){
							
							/* switch the background color */
							$bc=($lc%2==0)?'b-lblue':'b-vlblue';

							/* grab the numeric variation reference */
							$VARI = str_replace("vid-","",$VARI);

							/* increment the variations found for this group */
							$VARICNT++;

							/* START THE VARIATION DEFINITIONS HERE */
							?>
							<li class="col all100 variation pad20 <?=$bc;?>" data-vid="<?php echo $VARI; ?>">
								<div class='col all100 pad-tb10 mar-b25'>
									<div class='col all70 fs14 pad-r20'>
										<p class='pad0 grip bw c-white'>&#8597;&nbsp;&nbsp;Drag To Arrange</p>
									</div>
									<div class='col all30 fs14 tar'>
										<a onclick='deleteVariant(this);' class='pad-b0 delete-variant<?=$vdel;?> c-white'>Delete</a>
									</div>
								</div>
								<div class='col all100'>
									<?php

									/* cycle through the group outlines */
									foreach($GROUP["outline"] as $OUTLINE){
										
										if(in_array($OUTLINE['type'],$full_width_types)){ ?>
										<div class='col all100'>
										<?php }

										/* if we don"t have field information for this field within the page/group/variation array */
										if(!key_exists("fid-".$OUTLINE["fid"],$PAGE["gid-".$GROUP["id"]]["vid-".$VARI])){

											/* add an empty array */
											$FIELD["fid-".$OUTLINE["fid"]][0]=array();
										}

										/* include the field type */
										include($OUTLINE["type"].'.php');	
										
										if(in_array($OUTLINE['type'],$full_width_types)){ ?>
										</div>
										<?php }
									}

									?>
								</div>
							</li>	
							<?php
							$lc++;
						}
						?>

					</ul>
					<input type="hidden" class="hide" name="nvid-<?php echo $GROUP["id"];?>" id="nvid-<?php echo $GROUP["id"];?>" value="<?php echo $NVIDS[$GROUP["id"]] ;?>">

					<?php /* check how many variations are allowed for this group */
					if($GROUP["variants"] == $VARICNT){$r = " hide";} else {$r = "";} ?>

					<a class="add-variation<?=$r;?> compressed" onclick="addVariant(<?=$PAGE['id'];?>,<?=$PAGE['tid'];?>,this,<?php echo $GROUP["id"];?>,<?php echo $GROUP["variants"];?>);">New Variation</a>
				</div>
				<div class='col sml5 med10 lge15'></div>
			</section>
			<?php
		}
	}

	/* -------------------------- SEO --------------------------- */

	?>

	<!-- SEO CONTENT -->
	<section class='col all100'>
		<div class='col sml5 med10 lge15'></div>
		<div class='col box sml90 med80 lge70'>
			<div class='row pad-b20'>
				<div class='col all100'>
					<h1 class='pad0 fs20 c-blue'>SEO</h1>
				</div>
			</div>

			<!-- PAGE IMPORTANCE -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Page Importance</label>
				<select class='col all100 fs14 ss' name='page-importance' id='page-importance' placeholder="Please Select">
					<option<?php if($PAGE['importance']==0.0){echo ' selected';}?> value='0.0'>0.0 ( Not Important )</option>
					<option<?php if($PAGE['importance']==0.1){echo ' selected';}?> value='0.1'>0.1</option>
					<option<?php if($PAGE['importance']==0.2){echo ' selected';}?> value='0.2'>0.2</option>
					<option<?php if($PAGE['importance']==0.3){echo ' selected';}?> value='0.3'>0.3</option>
					<option<?php if($PAGE['importance']==0.4){echo ' selected';}?> value='0.4'>0.4</option>
					<option<?php if($PAGE['importance']==0.5){echo ' selected';}?> value='0.5'>0.5</option>
					<option<?php if($PAGE['importance']==0.6){echo ' selected';}?> value='0.6'>0.6</option>
					<option<?php if($PAGE['importance']==0.7){echo ' selected';}?> value='0.7'>0.7</option>
					<option<?php if($PAGE['importance']==0.8){echo ' selected';}?> value='0.8'>0.8</option>
					<option<?php if($PAGE['importance']==0.9){echo ' selected';}?> value='0.9'>0.9</option>
					<option<?php if($PAGE['importance']==1.0){echo ' selected';}?> value='1.0'>1.0 ( Very Important )</option>
				</select>
			</div>

			<!-- META DESCRIPTION -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Meta Description</label>
				<input class='col all100 fs14 tb' name="page-description" id="page-description" type="text" maxlength="255" value="<?=$PAGE["description"];?>" placeholder='Short Description'>
			</div>
		</div>
		<div class='col sml5 med10 lge15'></div>
	</section>

	<?php

	/* -------------------------- PUBLISHING --------------------------- */

	?>

	<!-- PUBLISHING -->
	<section class='col all100'>
		<div class='col sml5 med10 lge15'></div>
		<div class='col box sml90 med80 lge70'>
			<div class='row pad-b20'>
				<div class='col all100'>
					<h1 class='pad0 fs20 c-blue pad-b10'>Publishing</h1>
						<!-- INFORMATION -->
						<?php
						/* grab user who last modified this page */
						$NVX_DB->CLEAR(array("ALL"));
						$NVX_DB->SET_FILTER("`user`.`id`={$PAGE['by']}");
						$NVX_DB->SET_LIMIT(1);
						$by = $NVX_BOOT->CYPHER(array("STRING"=>$NVX_DB->QUERY("SELECT","`user`.`contact` FROM `user`")[0]["user.contact"],"TYPE"=>'decrypt'));
						?>
						<div class='col all100'>
							<label class='col all100 fs13 c-blue pad-b5'>Last modified <?=$PAGE["modified"];?> by <?=$by;?></label>
						</div>
				</div>
			</div>

			<!-- AUTO PUBLISH -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20<?=$create;?>'>
				<label class='col all100 fs13 c-blue pad-b5'>Auto Publish</label>
				<select class='col all100 fs14 ss' name='page-sttp' id='page-sttp' placeholder="Please Select" onchange="$('#page-ttp-wrapper').toggleClass('hide');">
					<option<?php if($PAGE['sttp']==0){echo ' selected';}?> value='0'>No</option>
					<option<?php if($PAGE['sttp']==1){echo ' selected';}?> value='1'>Yes</option>
				</select>
			</div>

			<!-- PUBLISH DATE -->
			<?php if($PAGE["sttp"]==0){$visibility=" hide";}else{$visibility="";} ?>
			<div id='page-ttp-wrapper' class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20<?=$create;?><?=$visibility;?>'>
				<label class='col all100 fs13 c-blue pad-b5'>Publish Date</label>
				<input class='col all100 fs14 tb date datetimepicker' name="page-ttp" id="page-ttp" type="text" value="<?=$PAGE["ttp"];?>">
			</div>

			<div class='col all100'></div>

			<!-- AUTO CLOSE -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20<?=$create;?>'>
				<label class='col all100 fs13 c-blue pad-b5'>Auto Close</label>
				<select class='col all100 fs14 ss' name='page-sttc' id='page-sttc' placeholder="Please Select" onchange="$('#page-ttc-wrapper').toggleClass('hide');">
					<option<?php if($PAGE['sttc']==0){echo ' selected';}?> value='0'>No</option>
					<option<?php if($PAGE['sttc']==1){echo ' selected';}?> value='1'>Yes</option>
				</select>
			</div>

			<!-- CLOSE DATE -->
			<?php if($PAGE["sttc"]==0){$visibility=" hide";}else{$visibility="";} ?>
			<div id='page-ttc-wrapper' class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20<?=$create;?><?=$visibility;?>'>
				<label class='col all100 fs13 c-blue pad-b5'>Close Date</label>
				<input class='col all100 fs14 tb date datetimepicker' name="page-ttc" id="page-ttc" type="text" value="<?=$PAGE["ttc"];?>">
			</div>

			<div class='col all100'></div>
			
			<!-- CREATED DATE -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Created</label>
				<input class='col all100 fs14 tb date datetimepicker' name="page-date" id="page-date" type="text" value="<?=$PAGE["date"];?>">
			</div>


			<!-- PUBLISH -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20<?=$create;?>'>
				<label class='col all100 fs13 c-blue pad-b5'>Publish</label>
				<select class='col all100 fs14 ss' name='page-published' id='page-published' placeholder="Please Select">
					<option<?php if($PAGE['published']==0){echo ' selected';}?> value='0'>No</option>
					<option<?php if($PAGE['published']==1){echo ' selected';}?> value='1'>Yes</option>
				</select>
			</div>

			<!-- SAVE -->
			<div class='col all100 hide'>
				<input type='submit' name='submit' id='submit' value="submit">
			</div>

		</div>
		<div class='col sml5 med10 lge15'></div>
	</section>
</form>