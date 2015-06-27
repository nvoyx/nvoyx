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

/* available spellchecker language array */
foreach($NVX_VAR->FETCH_ENTRY("languages") as $r){
	$LANG[]=array("INTERNAL"=>$r,"EXTERNAL"=>$r);
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

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<form method="POST">

<div class="blank box">
	
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-content.png">
		<h2 class="blank fl">CONTENT</h2>
		<a class="fr" href="https://<?php echo $r['URL'];?>">VIEW</a>
		<span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
		<a class="fr" onclick="$('#submit').click();">SAVE</a>
		<span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
		<a class="fr" href="/settings/rollback/list/<?php echo $PAGE["id"];?>">ROLLBACK</a>
		<?php if($create==""){?>
		<span class="fr">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<a class="fr" href="/settings/content/delete/<?php echo $PAGE["id"];?>">DELETE</a>
		<?php } ?>
		<span class="fr">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<a class="fr" href="/settings/content/list">UP</a>
		<?php if($create==""){?>
		<span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
		<a class="fr" href="/settings/content/add/<?php echo $PAGE["tid"] ;?>">NEW</a>
		<?php } ?>
	</div>
	
	<div class="blank row">
		<label for="page-id" class="blank fl">Node Id</label>
		<input class="blank textbox mini fr" name="page-id" id="page-id" type="text" readonly value="<?php echo $PAGE["id"];?>">
	</div>
	
	<div class="blank row">
		<label for="page-tid" class="blank fl">Type Id</label>
		<input class="blank textbox mini fr" name="page-tid" id="page-tid" type="text" readonly value="<?php echo $TYPE["id"];?>">
	</div>
	
	<div class="blank row">
		<label for="page-tref" class="blank fl">Type Reference</label>
		<input class="blank textbox mini fr" name="page-tref" id="page-tref" type="text" readonly value="<?php echo $TYPE["name"];?>">
	</div>
	
</div>

<?php
/* -------------------------- BASIC --------------------------- */
?>
	
<div class="blank box">
	
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-content.png">
		<h2 class="blank fl">NODE</h2>
		<input name="page-oldtitle" id="page-oldtitle" type="hidden" maxlength="255" value="<?php echo $PAGE["title"];?>">
		<input name="page-prefix" id="page-oldprefix" type="hidden" maxlength="2048" value="<?php echo substr($r['URL'],strpos($r['URL'],'/'),strrpos($r['URL'],'/')-strpos($r['URL'],'/'));?>">
	</div>
	
	<div class="blank row<?=$create;?>">
		<label for="page-title" class="blank fl">
			Title &amp; Url<br>
			<span class="current-length tt"><?php echo strlen($PAGE["title"]);?></span><span class="tt"> of 255</span>
		</label>
		<input class="blank textbox mini fr" name="page-title" id="page-title" type="text" maxlength="255" value="<?php echo $PAGE["title"];?>">
	</div>
	
	<div class="blank row">
		<label for="page-heading" class="blank fl">
			Heading<br>
			<span class="current-length tt"><?php echo strlen($PAGE["heading"]);?></span><span class="tt"> of 2048</span>
		</label>
		<input class="blank textbox mini fr" name="page-heading" id="page-heading" type="text" maxlength="2048" value="<?php echo $PAGE["heading"];?>">
	</div>
	
	<div class="blank row">
		<label for="page-teaser" class="blank fl">
			Teaser<br>
			<span class="current-length tt"><?php echo strlen($PAGE["teaser"]);?></span><span class="tt"> of 2048</span>
		</label>
		<input class="blank textbox mini fr" name="page-teaser" id="page-teaser" type="text" maxlength="2048" value="<?php echo $PAGE["teaser"];?>">
	</div>
	
	<div class="<?php if($TYPE['body']!=1) {echo 'hide';} ?>">
		<div class="blank row">
			<label for="page-body" class="blank fl">
				Body<br>				
				<span class="current-length tt"><?php echo strlen($PAGE["body"]); ?></span><span class="tt"> of 16777215</span><br>
				<span id="page-body-language" class="tt"><?php echo $NVX_VAR->FETCH_ENTRY("spellchecker")[0];?></span>
				
			</label>
			<div class="blank fl huge"><textarea class="blank textarea ckPrivate" name="page-body" id="page-body" maxlength="16777215" ><?php echo $PAGE["body"]; ?></textarea></div>
		</div>
	</div>
	
</div>

<?php


/* -------------------------- DYNAMIC DATA CHECK --------------------------- */

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
	
		<div class="blank header-only box<?=$access;?>">
	
			<a onclick="groupCompress(this);" class="blank header">
				<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-group.png">
				<h2 class="blank fl"><?php echo strtoupper($GROUP["name"]); ?></h2>
			</a>
	
			<ul class='sortable' id="group-<?php echo $GROUP["id"];?>">
		
		<?php
		
		
		/* cycle through each of the group variations (already in position order) */
		foreach($PAGE["gid-".$GROUP["id"]] as $VARI=>$FIELD){
			
			/* grab the numeric variation reference */
			$VARI = str_replace("vid-","",$VARI);
			
			/* increment the variations found for this group */
			$VARICNT++;
										
			/* START THE VARIATION DEFINITIONS HERE */
										
			?>				
			<li class="blank variation" data-vid="<?php echo $VARI; ?>">
				<div class="blank variation-header row">
					<label class="blank fl">Variant</label>
					<a title="delete" class="delete-variant<?php echo $vdel;?>" onclick='deleteVariant(this);'><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
					<a class="hand" title="drag and drop"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-grip.png"></a>
				</div>
			<?php							
			/* cycle through the group outlines */
			foreach($GROUP["outline"] as $OUTLINE){
								
				/* if we don"t have field information for this field within the page/group/variation array */
				if(!key_exists("fid-".$OUTLINE["fid"],$PAGE["gid-".$GROUP["id"]]["vid-".$VARI])){
					
					/* add an empty array */
					$FIELD["fid-".$OUTLINE["fid"]][0]=array();
				}
				
				/* which type of field comes next in this group */
				switch ($OUTLINE["type"]):
					
					
					case "datebox":
						
						/* start the field html */
						?><div class="blank row"><?php
						
						/* cycle through the values stored for this field */
						foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){
							
							/* do we have a start date */
							if(key_exists("start",$VALUES)){$v=$VALUES["start"];}else{$v="";}
							
							?>
							<label class='blank fl'><?php echo ucwords($OUTLINE["name"]);?></label>
							<input class="blank textbox mini fr" name="<?php echo "datebox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-start";?>" id="<?php echo "datebox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-start";?>" type="date" value="<?php echo $v;?>">
							<?php
							
							/* does the field outline contain a finish date */
							if($OUTLINE["finish"]==1){
								
								/* does the page have a finish date stored */
								if(key_exists("finish",$VALUES)){$v = $VALUES["finish"];} else {$v = "";}

								?>
								<div class='blank cb ten-space-vert'></div>
								<input class="blank textbox mini fr" name="<?php echo "datebox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-finish";?>" id="<?php echo "datebox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-finish";?>" type="date" value="<?php echo $v;?>">
								<?php
							}
						}
						
						/* end the field html */
						?></div><?php
						break;
						
						
					case "tagbox":
						
						/* start the field html */
						?>
						<div class="blank row">
						<label class='blank fl'><?php echo ucwords($OUTLINE["name"]);?></label>	
						<?php
						
						/* create two arrays, one to hold the html tags and one to hold the json tags */
						$v=array();
						$jsonv = array();
						
						/* cycle through the values stored for this field */
						foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){
														
							/* do we have a tag */
							if(key_exists("tag",$VALUES)){
									
									/* wrap each of the tags in a-tags */ 
									$v[] = "<span class='tag'><a onclick=\"deleteTag(this,'#tagbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-tags','{$VALUES["tag"]}')\">{$VALUES["tag"]}</a></span>";
									$jsonv[] = $VALUES["tag"];
							}
						}
						
						/* we have found no tags */
						if(count($v)==0){
							
							/* set the two arrays to empty */
							$v=array("");
							$jsonv = "[\"\"]";
						} else {
							
							/* encode the json array */
							$jsonv = $NVX_BOOT->JSON($jsonv,"encode");
						}
							
						?>
						<input class="blank textbox tags mini fr" name="<?php echo "tagbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-tags";?>" id="<?php echo "tagbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-tags";?>" type="hidden" value='<?php echo $jsonv;?>'>
						<input onkeyup='fetchTags(this,<?php echo $TYPE['id'];?>,<?php echo "\"#tagbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-addtags\""; ?>);' class="blank textbox mini fr tag-box" name="<?php echo "ignore-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-tags";?>" id="<?php echo "ignore-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-tags";?>" type="text" value="">
						<div class='blank cb ten-space-vert'></div>
						<label class='blank fl'>Current Tags</label>
						<div id="<?php echo "tagbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-deletetags";?>" class='links fl big current-tags'><?php echo implode("",$v); ?></div>
						<div class='blank cb ten-space-vert'></div>
						<label class='blank fl'>Available Tags</label>
						<div id="<?php echo "tagbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-addtags";?>" class='links fl big available-tags'></div>
						</div><?php
						break;
						
						
					case "textbox":
						
						/* start the field html */
						?><div class="blank row"><?php
						
						/* cycle through the values stored for this field */
						foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){
							
							/* do we have some text */
							if(key_exists("text",$VALUES)){$v=$VALUES["text"];}else{$v="";}
							
							?>
							<label for='<?php echo "textbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text";?>' class='blank fl'>
								<?php echo ucwords($OUTLINE["name"]);?><br>
								<span class="current-length tt"><?php echo strlen($v);?></span><span class="tt"> of <?php echo $OUTLINE["maxlength"];?></span>
							</label>
							<input class="blank textbox mini fr" name="<?php echo "textbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text";?>" id="<?php echo "textbox-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text";?>" type="text" maxlength="<?php echo $OUTLINE["maxlength"] ?>" value="<?php echo $v;?>">
							<?php
						}
						
						/* end the field html */
						?></div><?php
						
						break;
						
						
					case "textarea":
						
						/* start the field html */
						?><div class="blank row"><?php
						
						/* cycle through the values stored for this field */
						foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){
							
							/* should this textarea contain plain or html text (disable ckeditor if plain) */
							if($OUTLINE["plain"]==1){$r="plain";$OUTLINE["editor"]="";}else{$r="html";}
							
							/* if a maxlength of zero has been specified then return max size allowable by mysql field */
							if($OUTLINE["maxlength"]==0){$OUTLINE["maxlength"]="16777215";}
							
							/* do we have some text */
							if(key_exists("text",$VALUES)){$v=$VALUES["text"];}else{$v="";}
															
							?>
							<label for="<?php echo "textarea-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>" class="blank fl">
								<?php echo ucwords($OUTLINE['name']);?><br>				
								<span class="current-length tt"><?php echo strlen($v); ?></span><span class="tt"> of <?php echo $OUTLINE["maxlength"];?></span><br>
								<span id="<?php echo "textarea-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>-language" class="tt"><?php echo $OUTLINE["spellchecker"];?></span>
							</label>
							<div class="blank fl huge"><textarea data-editor="<?php echo $OUTLINE["editor"];?>" class="blank textarea huge <?php echo $OUTLINE["editor"] . " " .$r; ?>" name="<?php echo "textarea-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>" id="<?php echo "textarea-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>" maxlength="<?php echo $OUTLINE["maxlength"];?>" ><?php echo $v; ?></textarea></div>
							<?php
						}
						
						/* end the field html */
						?></div><?php
						
						break;
						
						
					case "sselect":
						
						/* start the field html */
						?>
						<div class="blank row">
							<label for="<?php echo "sselect-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-option";?>" class="blank fl">
								<?php echo ucwords($OUTLINE['name']);?>
							</label>
						<?php
						
						/* cycle through the values stored for this field */
						foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){
							
							/* do we have a selected value */
							if(key_exists("selected",$VALUES)){$v=$VALUES["selected"];}else{$v="[none]";}
							
							/* reset the results variable */
							$rs="";
							
							/* add a "no response" option to the select options */
							$rs[] = array("INTERNAL"=>"[none]","EXTERNAL"=>"[none]");
								
							/* cycle through any select options associated with this field */
							foreach($OUTLINE["content"] as $key => $value){
								
								/* add the two values to the results array */
								$rs[] = array("INTERNAL"=>$value,"EXTERNAL"=>$key);
							}
							
							?>
							<div class="blank select fr half">
								<?php
								foreach ($rs as $r){
								if($v==$r["INTERNAL"]){$flg = " selected";} else {$flg="";} ?>
								<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'<?php echo "sselect-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-option";?>');return false;"><?php echo $r["EXTERNAL"]; ?></a>
								<?php } ?>
							</div>
							<select class="hide" name="<?php echo "sselect-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-option";?>" id="<?php echo "sselect-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-option";?>">
								<?php 
								foreach ($rs as $r){
								if($v==$r["INTERNAL"]){$flg = " selected";} else {$flg="";} ?>
								<option<?php echo $flg; ?> value="<?php echo $r["INTERNAL"];?>"></option>
								<?php } ?>
							</select>
							<?php
						}
						
						/* end the field html */
						?></div><?php
						
						break;
						
						
					case "mselect":
						
						/* start the field html */
						?>
						<div class="blank row">
							<label for="<?php echo "mselect-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-options";?>" class="blank fl">
								<?php echo ucwords($OUTLINE['name']);?>
							</label>
						<?php
						
						/* holder for any select options to be selected for this page */
						$v="";
						
						/* cycle through the available select options (one in each array entry) */
						foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){
							
							/* if we have a selected entry */
							if(key_exists("selected",$VALUES)){
								
								/* add the selected item to an array */
								$v[]=$VALUES["selected"];
							}
						}
						
						/* if we have no selected items, set to the default response */
						if($v==""){$v[]="[none]";}	
						
						/* reset the results variable */
						$rs="";
						
						/* add a "no response" option to the select options */
						$rs[] = array("INTERNAL"=>"[none]","EXTERNAL"=>"[none]");
						
						foreach($OUTLINE["content"] as $key => $value){
							
							/* cycle through any select options associated with this field */
							$rs[] = array("INTERNAL"=>$value,"EXTERNAL"=>$key);
						}
						
						?>
						<div class="blank mselect fr half">
							<?php
							foreach ($rs as $r){
							if(in_array($r["INTERNAL"],$v)){$flg = " selected";} else {$flg="";} ?>
							<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'<?php echo "mselect-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-options";?>');return false;"><?php echo $r["EXTERNAL"]; ?></a>
							<?php } ?>
						</div>
						<select multiple class="hide" name="<?php echo "mselect-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-options[]";?>" id="<?php echo "mselect-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-options";?>">
							<?php 
							foreach ($rs as $r){
							if(in_array($r["INTERNAL"],$v)){$flg = " selected";} else {$flg="";} ?>
							<option<?php echo $flg; ?> value="<?php echo $r["INTERNAL"];?>"><?php echo $r["EXTERNAL"]; ?></option>
							<?php } ?>
						</select>
						<?php
						
						/* end the field html */
						?></div><?php
						
						break;
						
						
					case "heirarchy":
						
						/* start the field html */
						?>
						<div class="blank row">
						<?php
						
						/* if we have a zero max value (unlimited), set this to 999 */
						if($OUTLINE["max"]==0){$OUTLINE["max"]=999;}
						
						/* create a counter to make sure we don't return more than the maximum number of heirarchies */
						$x=0;
						
						/* cycle through the available heirarchy options */
						foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){
							
							?>
							<label for="<?php echo "heirarchy-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-0-0";?>" class="blank fl">
								<?php echo ucwords($OUTLINE['name']);?>
							</label>
							<div class='blank heirarchy-wrapper'>
							<?php
							
							/* iterate the heirarchy counter */
							$x++;
							
							/* reset the selecteds array */
							$selecteds = false;
							
							/* have we examined no more than the max permissible heirarchies */
							if($x<=$OUTLINE["max"]){
								
								/* if we do not have any values */
								if(!array_key_exists("parent",$VALUES)){
									
									/* make a single blank entry */
									$VALUES["parent"][0] = -1;
								}
																							
								/* cycle through the levels */
								foreach($VALUES["parent"] as $parent){
																											
									/* which content type does the current parent nid belong to */
									$ptid = $NVX_TYPE->FETCH_MATCHES(array("NID"=>$parent,
																			"USER"=>$NVX_USER->FETCH_ENTRY("type")));
									
									if($ptid){
									
										/* update the selecteds array */
										$selecteds[] = array("NID"=>$parent,"TID"=>$ptid[0]);
										
									} else {
										
										/* terminate the selecteds array */
										$selecteds[] = array("NID"=>-1,"TID"=>-1);
									}
								}
								
								/* grab the parent from this page type array and create the first single select */
								$NVX_DB->DB_CLEAR(array("ALL"));
								$NVX_DB->DB_SET_FILTER("`tid`={$TYPE["parent"]}");
								$options = $NVX_DB->DB_QUERY("SELECT","`page`.`title`,`page`.`id` FROM `page`");
								
								/* do we have options */
								if($options){
																		
									/* add a default option to the results array */
									$rs = false;
									
									$rs[] = array("INTERNAL"=>"-1","EXTERNAL"=>"[none]");
									
									/* cycle through the options for this first level */
									foreach($options as $option){
										
										/* add the option to the results array */
										$rs[] = array("INTERNAL"=>$option["page.id"],"EXTERNAL"=>$option["page.title"]);
									}
									?>
									<div class="blank select huge">
										<?php
										
										/* cycle through the options */
										foreach($rs as $r){
																						
											$flg="";
											if($selecteds[0]["NID"] == $r["INTERNAL"]){$flg=" selected";}
											?>
											<a class='blank huge<?php echo $flg; ?><?php echo " heirarchy-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-0";?>' onclick="select(this,'<?php echo "heirarchy-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-0";?>');return false;"><?php echo $r["EXTERNAL"]; ?></a>
											<?php
										}
									?>
									</div>
									
								
									<select class="hide" onchange='<?php echo "heirarchyChange({$PAGE["id"]},this,0,{$OUTLINE["max"]});"; ?>' name="<?php echo "heirarchy-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-0";?>" id="<?php echo "heirarchy-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-0";?>">
										<?php 
										
										/* cycle through the options */
										foreach($rs as $r){
											
											$flg="";
											if($selecteds[0]["NID"] == $r["INTERNAL"]){$flg=" selected";}
											?>	
											<option<?php echo $flg; ?> value="<?php echo $r["INTERNAL"];?>"><?php echo $r["EXTERNAL"]; ?></option>
											<?php
										}
										?>
									</select>
									<?php
									
									/* only cycle through if the the first select doesn't equal -1 */
									if($selecteds[0]["NID"]!=-1){

																										
										/* cycle through 9 times to give a maximum heirarchy depth of 10 */
										for($a=1;$a<9;$a++){
										
											/* reset the results array */
											$rs = false;
											
											if(array_key_exists($a,$selecteds)){
																						
												/* based upon the first answer, what options are available for the next level */
										
												/* grab the nid from the heirarchy responses where the previous nid is in the heirarchy array */
												/* first run will be a bit rough ie, nid might be in the wrong heirarchy iteration, 10 might be returned when requesting 1 etc. */
												/* shorten the list, then let php find valid entries */
												$NVX_DB->DB_CLEAR(array("ALL"));
												$NVX_DB->DB_SET_FILTER("`heirarchy`.`values` LIKE '%\"{$selecteds[$a-1]["NID"]}\"%' AND `heirarchy`.`nid`!={$PAGE["id"]}");
												$possibles = $NVX_DB->DB_QUERY("SELECT","`heirarchy`.`values`,`heirarchy`.`nid` FROM `heirarchy`");
												if($possibles==false){$possibles=true;}
											} else {$possibles = false;}
																				
											/* do we have any possibles */
											if($possibles){
																		
												if(!is_array($possibles)){$possibles = array();}
												
												/* add the default option to the results array */
												$rs = false;
												$rs[] = array("INTERNAL"=>"-1","EXTERNAL"=>"[none]");
									
												/* cycle through the possibles */
												foreach($possibles as $possible){
													
													/* NEW SHIT */
													$heirs=false;
													$heirs = $NVX_BOOT->JSON($possible["heirarchy.values"],"decode");
													foreach($heirs as $heir){
														
														if(in_array($selecteds[$a-1]["NID"],$heir)){
															
															if($heir[$a]!=-1){
														
																$NVX_DB->DB_CLEAR(array("ALL"));
																$NVX_DB->DB_SET_FILTER("`page`.`id`={$heir[$a]} AND `page`.`id`!={$PAGE["id"]}");
																$title = $NVX_DB->DB_QUERY("SELECT","`page`.`title` FROM `page`");
																if($title){
																	$rs[$heir[$a]] = array("INTERNAL"=>$heir[$a],"EXTERNAL"=>$title[0]["page.title"]);
																}
															} else {
														
																$NVX_DB->DB_CLEAR(array("ALL"));
																$NVX_DB->DB_SET_FILTER("`page`.`id`={$possible["heirarchy.nid"]} AND `page`.`id`!={$PAGE["id"]}");
																$title = $NVX_DB->DB_QUERY("SELECT","`page`.`title` FROM `page`");
																if($title){
																	$rs[$possible["heirarchy.nid"]] = array("INTERNAL"=>$possible["heirarchy.nid"],"EXTERNAL"=>$title[0]["page.title"]);
																}
															}
														}
													}
												}
																							
												/* set a flag to check whether the chosen nid actually exists within the heirarchy level */
												$flag=0;
												
												/* run through the heirarchy levels */
												foreach($rs as $chk){
												
													/* does the chosen nid exist in this option */
													if(array_key_exists($a,$selecteds)){
																												
														if($chk["INTERNAL"]==$selecteds[$a]["NID"]){
															
															/* switch the flag and exit the loop */
															$flag = 1;break;
														}
													}
												}
																							
												/* if the flag has not been switched, then the nested nid isn't available at this level of the heirarchy, so switch to default */
												if($flag==0){$selecteds[$a]["NID"]=-1;}
												
												if(count($rs)>0){ ?>
												<div class="blank select huge ten-top">
													<?php
													foreach ($rs as $r){
													if($selecteds[$a]["NID"]==$r["INTERNAL"]){$flg = " selected";} else {$flg="";} ?>
													<a class='blank huge<?php echo $flg; ?><?php echo " heirarchy-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-{$a}";?>' onclick="select(this,'<?php echo "heirarchy-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-{$a}";?>');return false;"><?php echo $r["EXTERNAL"]; ?></a>
													<?php } ?>
												</div>
												<select class="hide" onchange='<?php echo "heirarchyChange({$PAGE["id"]},this,{$a},{$OUTLINE["max"]});"; ?>' name="<?php echo "heirarchy-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-{$a}";?>" id="<?php echo "heirarchy-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-{$a}";?>">
													<?php 
													foreach ($rs as $r){
													if($selecteds[$a]["NID"]==$r["INTERNAL"]){$flg = " selected";} else {$flg="";} ?>
													<option<?php echo $flg; ?> value="<?php echo $r["INTERNAL"];?>"></option>
													<?php } ?>
												</select>
												<!--<div class='blank cb ten-space-vert'></div>-->
												<?php } else {
													?><!--<div class='blank cb ten-space-vert'></div>--><?php
												}
											
											}
										
											/* if the current node reference is -1, then we are done */
											if($selecteds[$a]["NID"]==-1){break;}
										}
									}									
								}
							}
							
							?></div><?php
						}
						
						
						
						/* end the field html */
						?></div><?php

						break;
					
					
					case "filelist":
						
						/* start the field html */
						?>
						<div class="blank row">
						<?php
						
						/* clear file entries */
						$f="";
						
						/* reset the file count to zero */
						$fcount = 0;
						
						?>
						<ul class='blank sortable huge fr' id='<?php echo "filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-list"; ?>'>
						<?php
						
						/* if a maximum number of files wasn't stipulated, fix this at 100 */
						if($OUTLINE["total"]==0){$OUTLINE["total"]=100;}
						
						/* cycle through the values stored */
						foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){
							
							/* reset the results variable */
							$rs="";
							
							/* do this iteration have a "name" entry in its array */
							if(key_exists("name",$VALUES)){
								
								/* increment the file count by one */
								$fcount++;
								
								/* have we added fewer than the maximum allowed files */
								if($fcount<=$OUTLINE["total"]){
								
								?><li><?php
								
								/* grab the file name */
								$v = $VALUES["name"];
								
								?>
								<input type='hidden' name='<?php echo "filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-name";?>' id='<?php echo "filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-name";?>' value='<?php echo $v;?>' >
								<?php
								
								/* grab the file size */
								if(key_exists("size",$VALUES)){$v=$VALUES["size"];}else{$v="";}
															
								?>
								<input type='hidden' name='<?php echo "filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-size";?>' id='<?php echo "filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-size";?>' value='<?php echo $v;?>' >
								<?php
								
								/* grab the file type */
								if(key_exists("type",$VALUES)){$v=$VALUES["type"];}else{$v="";}
								
								?>
								<input type='hidden' name='<?php echo "filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-type";?>' id='<?php echo "filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-type";?>' value='<?php echo $v;?>' >
								<?php
								
								/* grab the file description */
								if(key_exists("desc",$VALUES)){$v=$VALUES["desc"];}else{$v="";}

								?>
								<label for='<?php echo "filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>' class='blank whopper ten-bottom'>
									Description <span class="current-length tt"><?php echo strlen($v);?></span><span class="tt"> of 1024</span>
									<a title="delete" onclick="deleteListItem(this);"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
									<a class="download" title="download" target='_blank' href='<?php echo "/settings/resources/files/documents/".$VALUES['name'];?>'><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-download.png"></a>
									<a class="hand" title="drag and drop"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-grip.png"></a>
								</label>
								<input class="blank textbox large fr" name="<?php echo "filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>" id="<?php echo "filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>" type="text" maxlength="1024" value="<?php echo $v;?>">
								<div class='blank cb ten-space-vert'></div>
								</li>
								<?php
								}
							}
						}
						
						?>
						</ul>	
							
						<label for="" class="blank fl">
						<?php echo ucwords($OUTLINE['name']);?><br>
						<span class="current-length tt"><?php echo $fcount;?></span><span class="tt"> of <?php echo $OUTLINE["total"];?></span>
						</label>
						<?php
						
						/* based on maximum number files allowed, hide or show the drop zone */
						if($fcount>=$OUTLINE["total"]){$r="drop hide half";}else{$r="drop half";}
						
						/* create a list of mime types to be used by the label title */
						$tooltip = "";
						foreach($OUTLINE["filetypes"] as $ft){$tooltip .= ".".substr($ft,strpos($ft,"/")+1)." ";}
						?>
						<div class='cb'>
							<label title='<?php echo round($OUTLINE["size"]/1024);?> Mb ( <?php echo $tooltip;?>)'>Files <span class='tt'>(drag and drop)</span></label>
							<div class='blank half fr'>
								<div class="<?php echo $r;?>" id="<?php echo "filelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-drop";?>" data-type="filelist" data-allowed="<?php echo implode(",",$OUTLINE["filetypes"]);?>" data-maxsize="<?php echo round($OUTLINE["size"]/1024);?>" data-maxfiles="<?php echo $OUTLINE["total"];?>" data-nuid="<?php echo $fcount;?>">
									<div class="blank progressbar-container">
										<div class="blank progressbar"></div>
									</div>
								</div>
							</div>
						</div>
						<?php
												
						/* end the field html */
						?></div><?php
						
						break;
						
						
					case "imagelist":
						
						/* start the field html */
						?>
						<div class="blank row">
						<?php
						
						/* clear the image entries */
						$i = "";
						
						/* reset the image count */
						$icount = 0;
						
						?>
						<ul class='blank sortable huge fr' id='<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-list"; ?>'>
						<?php
						
						/* if a maximum number of images wasn't stipulated, fix this at 100 */
						if($OUTLINE["total"]==0){$OUTLINE["total"]=100;}
						
						/* cycle through the values stored */
						foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){
							
							/* reset the results variable */
							$rs="";
							
							/* do this iteration have a "name" entry in its array */
							if(key_exists("name",$VALUES)){
								
								/* increment the image counter by one */
								$icount++;
								
								/* have we added fewer than the maximum allowed files */
								if($icount<=$OUTLINE["total"]){
									
									?><li><?php

									/* grab the image name (convert .png to .webp) */
									$v = str_replace(".png",".webp",$VALUES["name"]);
									
									?>
									<input type='hidden' name='<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-name";?>' id='<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-name";?>' value='<?php echo $v;?>' >
									<?php

									/* grab the image description */
									if(key_exists("desc",$VALUES)){$v=$VALUES["desc"];}else{$v="";}
									
									?>
									<label for='<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>' class='blank whopper ten-bottom'>
										Description <span class="current-length tt"><?php echo strlen($v);?></span><span class="tt"> of 1024</span>
										<a title="delete" onclick="deleteListItem(this);"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
										<a class="hand" title="drag and drop"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-grip.png"></a>
										<a class="download" title="download" target='_blank' href='<?php echo "/settings/resources/files/images/cms/".$VALUES['name'].".webp";?>'>
											<img class="blank fr tiny-thumb" src="<?= "/settings/resources/files/images/cms/".$VALUES['name'].".webp";?>">
										</a>
									</label>
									<input class="blank textbox large fr" name="<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>" id="<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>" type="text" maxlength="1024" value="<?php echo $v;?>">
									<div class='blank cb ten-space-vert'></div>
									<?php

									/* does this imagelist have the link field enabled */
									if($OUTLINE["link"]==1){

										/* grab the image link */
										if(key_exists("link",$VALUES)){$v=$VALUES["link"];}else{$v="";}
										
										?>
										<label for='<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-link";?>' class='blank fl'>
											Link <span class="current-length tt"><?php echo strlen($v);?></span><span class="tt"> of 255</span>
										</label>
										<input class="blank textbox mini fr" name="<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-link";?>" id="<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-link";?>" type="text" maxlength="255" value="<?php echo $v;?>">
										<div class='blank cb ten-space-vert'></div>
										<?php
									}

									/* does this imagelist have an additional field associated with it */
									if($OUTLINE["extra-type"]!="none"){

										/* is the additional textarea for plain or html text */
										if($OUTLINE["extra-type"]=="plain"){$r="plain";$OUTLINE["extra-editor"]="";}else{$r="html";}

										/* grab the extra text */
										if(key_exists("text",$VALUES)){$v=$VALUES["text"];}else{$v="";}

										?>
										<label for="<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>" class="blank fl">
											<?php echo ucwords($OUTLINE['extra-name']);?> <span class="current-length tt"><?php echo strlen($v); ?></span><span class="tt"> of 100000</span> <span id="<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>-language" class="tt"><?php echo $OUTLINE["extra-spellchecker"];?></span>
										</label>
										<div class="blank fl huge"><textarea data-editor="<?php echo $OUTLINE["extra-editor"];?>" class="blank textarea huge <?php echo $OUTLINE["extra-editor"] . " " .$r; ?>" name="<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>" id="<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>" maxlength="100000" ><?php echo $v; ?></textarea></div>
										<div class='blank cb ten-space-vert'></div>
										<?php
										
									}
									?></li><?php
								}
							}
						}
						
						?>
						</ul>	
							
						<label for="" class="blank fl">
						<?php echo ucwords($OUTLINE['name']);?><br>
						<span class="current-length tt"><?php echo $icount;?></span><span class="tt"> of <?php echo $OUTLINE["total"];?></span>
						</label>
						<?php
						
						/* based on maximum number files allowed, hide or show the drop zone */
						if($icount>=$OUTLINE["total"]){$r="drop hide half";}else{$r="drop half";}
						
						/* create a list of mime types to be used by the label title */
						$tooltip = ".jpeg .png .gif ";
						?>
						<div class='cb'>
							<label title='<?php echo round($OUTLINE["size"]/1024);?> Mb ( <?php echo $tooltip;?>)'>Images <span class='tt'>(drag and drop)</span></label>
							<div class='blank half fr'>
								<div class="<?php echo $r;?>" id="<?php echo "imagelist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-drop";?>" data-eeditor="<?php echo $OUTLINE["extra-editor"];?>" data-link="<?php echo $OUTLINE["link"];?>" data-type="imagelist" data-elabel="<?php echo $OUTLINE["extra-name"];?>" data-elanguage="<?php echo $OUTLINE["extra-spellchecker"];?>" data-etype="<?php echo $OUTLINE["extra-type"];?>" data-allowed="image/jpeg,image/png,image/gif" data-maxsize="<?php echo round($OUTLINE["size"]/1024);?>" data-maxfiles="<?php echo $OUTLINE["total"];?>" data-nuid="<?php echo $icount;?>">
									<div class="blank progressbar-container">
										<div class="blank progressbar"></div>
									</div>
								</div>
							</div>
						</div>
						<?php
												
						/* end the field html */
						?></div><?php
						
						break;
						
						
					case "videolist":
						
						/* start the field html */
						?>
						<div class="blank row">
						<?php
						
						/* clear the movie entries */
						$m = "";
						
						/* reset the movie count */
						$mcount = 0;
						
						?>
						<ul class='blank sortable huge fr' id='<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-list"; ?>'>
						<?php
						
						/* if a maximum number of videos wasn't stipulated, fix this at 100 */
						if($OUTLINE["total"]==0){$OUTLINE["total"]=100;}
						
						/* cycle through the values stored */
						foreach($FIELD["fid-{$OUTLINE["fid"]}"] as $ITERATION=>$VALUES){
							
							/* reset the results variable */
							$rs="";
							
							/* do this iteration have a "name" entry in its array */
							if(key_exists("name",$VALUES)){
								
								/* increment the movie counter */
								$mcount++;
								
								/* have we added fewer than the maximum allowed files */
								if($mcount<=$OUTLINE["total"]){
									
									?><li><?php

									/* grab the movie name */
									$v = $VALUES["name"];
									
									?>
									<input type='hidden' name='<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-name";?>' id='<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-name";?>' value='<?php echo $v;?>' >
									<?php

									/* grab the movie duration */
									if(key_exists("duration",$VALUES)){$v=$VALUES["duration"];}else{$v="";}
									
									?>
									<input type='hidden' name='<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-duration";?>' id='<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-duration";?>' value='<?php echo $v;?>' >
									<?php

									/* grab a json encoded string of the available movie thumbs */
									$v = $NVX_BOOT->JSON($VALUES["thumbs"],"encode");
									
									?>
									<input type='hidden' name='<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-thumbs";?>' id='<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-thumbs";?>' value='<?php echo $v;?>' >
									<?php

									/* grab the movie thumb */
									$v = $VALUES["thumb"];
									
									?>
									<input type='hidden' name='<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-thumb";?>' id='<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-thumb";?>' value='<?php echo $v;?>' >
									<?php
									
									
									/* grab the movie description */
									if(key_exists("desc",$VALUES)){$v=$VALUES["desc"];}else{$v="";}
									
									?>
									<label for='<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>' class='blank whopper ten-bottom'>
										Description <span class="current-length tt"><?php echo strlen($v);?></span><span class="tt"> of 1024</span>
										<a title="delete" onclick="deleteListItem(this);"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
										<a class="download" title="download" target='_blank' href='<?php echo "/settings/resources/files/videos/".$VALUES['name'];?>'><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-download.png"></a>
										<a class="hand" title="drag and drop"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-grip.png"></a>
									</label>
									<input class="blank textbox large fr" name="<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>" id="<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-desc";?>" type="text" maxlength="1024" value="<?php echo $v;?>">
									<div class='blank cb ten-space-vert'></div>
									<?php

									/* reset the thumbs html */
									$t="";
									
									?><div class='blank'><?php
									
									/* setup a counter to reset on 3 */
									$cnt = 0;
									
									/* setup a count of the total images processed */
									$tcnt = 0;
									
									/* cycle through the thumbs */
									foreach($VALUES["thumbs"] as $r){
										$tcnt++;
										if($tcnt<=$OUTLINE['thumbs']){
										$cnt++;
										if($cnt==3){$cnt=0;$last=" last";}else{$last="";}
										?>
										<img onclick='videoThumb(this,"<?php echo "#videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-thumb";?>","<?php echo $r;?>");' class='blank fl video-thumb<?php echo ($r == $VALUES['thumb'] ? '' : ' opaque'); echo $last;?>' width='124' alt='<?php echo $VALUES["desc"];?>' src='/settings/resources/files/videos/<?php echo pathinfo($VALUES["name"],PATHINFO_FILENAME);?>/<?php echo $r;?>'>
										<?php
										}
									}

									?>
									</div>
									<div class='blank cb ten-space-vert'></div>
									<?php
									
									/* does this videolist have the link field enabled */
									if($OUTLINE["link"]==1){

										/* grab the video link */
										if(key_exists("link",$VALUES)){$v=$VALUES["link"];}else{$v="";}
										
										?>
										<label for='<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-link";?>' class='blank fl'>
											Link <span class="current-length tt"><?php echo strlen($v);?></span><span class="tt"> of 255</span>
										</label>
										<input class="blank textbox mini fr" name="<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-link";?>" id="<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-link";?>" type="text" maxlength="255" value="<?php echo $v;?>">
										<div class='blank cb ten-space-vert'></div>
										<?php
									}

									/* does this movie have an additional textarea */
									if($OUTLINE["extra-type"]!="none"){

										/* is the additional textarea for plain or html text */
										if($OUTLINE["extra-type"]=="plain"){$r="plain";$OUTLINE["extra-editor"]="";}else{$r="html";}

										/* grab the extra text */
										if(key_exists("text",$VALUES)){$v=$VALUES["text"];}else{$v="";}

										?>
										<label for="<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>" class="blank fl">
											<?php echo ucwords($OUTLINE['extra-name']);?> <span class="current-length tt"><?php echo strlen($v); ?></span><span class="tt"> of 100000</span> <span id="<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>-language" class="tt"><?php echo $OUTLINE["extra-spellchecker"];?></span>
										</label>
										<div class="blank fl huge"><textarea data-editor="<?php echo $OUTLINE["extra-editor"];?>" class="blank textarea huge <?php echo $OUTLINE["extra-editor"] . " " .$r; ?>" name="<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>" id="<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-{$ITERATION}-text{$r}";?>" maxlength="100000" ><?php echo $v; ?></textarea></div>
										<div class='blank cb ten-space-vert'></div>
										<?php

									}
									?></li><?php
								}
							}
						}
						
						?>
						</ul>	
						<label for="" class="blank fl">
						<?php echo ucwords($OUTLINE['name']);?><br>
						<span class="current-length tt"><?php echo $mcount;?></span><span class="tt"> of <?php echo $OUTLINE["total"];?></span>
						</label>
						<?php
						
						/* based on maximum number files allowed, hide or show the drop zone */
						if($mcount>=$OUTLINE["total"]){$r="drop hide half";}else{$r="drop half";}
						
						/* create a list of mime types to be used by the label title */
						$tooltip = ".mpeg .mov .flv .mp4 .wmv .avi ";
						?>
						<div class='cb'>
							<label title='<?php echo round($OUTLINE["size"]/1024);?> Mb ( <?php echo $tooltip;?>)'>Videos <span class='tt'>(drag and drop)</span></label>
							<div class='blank half fr'>
								<div class="<?php echo $r;?>" id="<?php echo "videolist-{$GROUP["id"]}-{$VARI}-{$OUTLINE["fid"]}-drop";?>" data-veffects="<?php echo $NVX_BOOT->JSON($OUTLINE['effects'],"encode");?>" data-vthumbs="<?php echo $OUTLINE['thumbs'];?>" data-vheight="<?php echo $OUTLINE['height'];?>" data-vwidth="<?php echo $OUTLINE['width'];?>" data-eeditor="<?php echo $OUTLINE["extra-editor"];?>" data-link="<?php echo $OUTLINE["link"];?>" data-type="videolist" data-elabel="<?php echo $OUTLINE["extra-name"];?>" data-elanguage="<?php echo $OUTLINE["extra-spellchecker"];?>" data-etype="<?php echo $OUTLINE["extra-type"];?>" data-allowed="video/mpeg,video/quicktime,video/x-flv,video/mp4,video/x-ms-wmv,video/x-msvideo,video/H264" data-maxsize="<?php echo round($OUTLINE["size"]/1024);?>" data-maxfiles="<?php echo $OUTLINE["total"];?>" data-nuid="<?php echo $mcount;?>">
									<div class="blank progressbar-container">
										<div class="blank progressbar"></div>
									</div>
								</div>
							</div>
						</div>
						<?php
						
						/* end the field html */
						?></div><?php
						
						break;
				endswitch;
			}
			
			
			/* END VARIATION HTML */
			
			?></li><?php
		}
		?>
		
			</ul>
			<input type="hidden" class="hide" name="nvid-<?php echo $GROUP["id"];?>" id="nvid-<?php echo $GROUP["id"];?>" value="<?php echo $NVIDS[$GROUP["id"]] ;?>">
			
			<?php
				/* check how many variations are allowed for this group */
				if($GROUP["variants"] == $VARICNT){$r = " hide";} else {$r = "";}
			?>
			
			<a class="add-variation<?=$r;?>" onclick="addVariant(this,<?php echo $GROUP["id"];?>,<?php echo $GROUP["variants"];?>);">NEW VARIATION</a>
			
		</div>
		
		<?php
	}
}



/* ----------------------- COMMENTS ------------------------- */

/* are comments enabled at a group level */
if($TYPE["comments"]==1 OR $TYPE["comments"]==2){$hidden = "";}else{$hidden=" hide";}

?>
<div class="blank<?php echo $hidden;?>">
	<div class="blank box">

		<div class="blank header">
			<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-content.png">
			<h2 class="blank fl">COMMENTS</h2>
		</div>

		<div class="blank row">
			<label class="blank fl">Configure</label>
			<div class="blank select fr half">
				<?php
				if($PAGE["comments"]==1){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-comments');return false;">Enabled</a>
				<?php if($PAGE["comments"]==0){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-comments');return false;">Disabled</a>
			</div>
			<select class="hide" name="page-comments" id="page-comments">
				<?php if($PAGE["comments"]==1){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="1"></option>
				<?php if($PAGE["comments"]==0){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="0"></option>
			</select>
		</div>
<?php

/* grab any comments and replies*/
$comments = $NVX_COMMENTS->FETCH_BY_NID($PAGE['id']);

/* do we have any comments */
if($comments){
	
	/* set an comment iteration variable  */
	$ccnt = 0;
	
	/* cycle through the comments */
	foreach($comments as $c){
				
		/* increment the comment count */
		$ccnt++;
		
		/* should this textarea contain plain or html text (disable ckeditor if plain) */
		if($TYPE["comments"]==0 || $TYPE["comments"]==1){$r="plain";$TYPE["comeditor"]="";}else{$r="html";}
									
		?>
		<div class="blank row">
			<label for="comments-<?php echo $c['comment']['id']; ?>-text-<?php echo $r; ?>" class="blank fl">
				<?php echo "Comment {$ccnt}<br>by {$c['comment']['username']}";?><br><span class="current-length tt"><?php echo strlen($c['comment']['values']); ?></span><span class="tt"> of 16777215</span> <span id="comments-<?php echo $c['comment']['id']; ?>-text-<?php echo $r; ?>-language" class="tt"><?php echo $NVX_VAR->FETCH_ENTRY("spellchecker")[0];?></span>
			</label>
			<div class="blank fl huge">
				<textarea class="blank textarea huge <?php echo $TYPE["comeditor"]; ?>" data-editor="<?php echo $TYPE["comeditor"]; ?>" name="comments-<?php echo $c['comment']['id']; ?>-text-<?php echo $r; ?>" id="comments-<?php echo $c['comment']['id']; ?>-text-<?php echo $r; ?>" maxlength="16777215" ><?php echo $c['comment']['values']; ?></textarea>
				<div class='blank cb ten-space-vert'></div>
			</div>	
		<?php
		
		/* set a reply iteration variable  */
		$rcnt = 0;
		
		/* cycle through any replies attached to this comment */
		foreach($c["replies"] as $reply){
			
			/* increment the reply count */
			$rcnt++;
		
		?>
			<label for="comments-<?php echo $reply['id']; ?>-text-<?php echo $r; ?>" class="blank fl">
				<?php echo "Reply {$rcnt}<br>by {$reply['username']}";?><br><span class="current-length tt"><?php echo strlen($reply['values']); ?></span><span class="tt"> of 16777215</span> <span id="comments-<?php echo $reply['id']; ?>-text-<?php echo $r; ?>-language" class="tt"><?php echo $NVX_VAR->FETCH_ENTRY("spellchecker")[0];?></span>
			</label>
			<div class="blank fl huge">
				<textarea class="blank textarea huge <?php echo $TYPE["comeditor"]; ?>" data-editor="<?php echo $TYPE["comeditor"]; ?>" name="comments-<?php echo $reply['id']; ?>-text-<?php echo $r; ?>" id="comments-<?php echo $reply['id']; ?>-text-<?php echo $r; ?>" maxlength="16777215" ><?php echo $reply['values']; ?></textarea>
				<div class='blank cb ten-space-vert'></div>
			</div>	
		<?php
		}
	?></div><?php
	}
}
?></div></div><?php
/* -------------------------- SEO --------------------------- */

?>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-content.png">
		<h2 class="blank fl">SEO</h2>
	</div>
	
	<div class="blank row">
		<label class="blank fl">Search Engine Importance</label>
		<div class="blank select fr half">
		<?php
			if($PAGE["importance"]==0.0){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-importance');return false;">0.0 (Not Important)</a>
			<?php if($PAGE["importance"]==0.1){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-importance');return false;">0.1</a>
			<?php if($PAGE["importance"]==0.2){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-importance');return false;">0.2</a>
			<?php if($PAGE["importance"]==0.3){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-importance');return false;">0.3</a>
			<?php if($PAGE["importance"]==0.4){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-importance');return false;">0.4</a>
			<?php if($PAGE["importance"]==0.5){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-importance');return false;">0.5</a>
			<?php if($PAGE["importance"]==0.6){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-importance');return false;">0.6</a>
			<?php if($PAGE["importance"]==0.7){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-importance');return false;">0.7</a>
			<?php if($PAGE["importance"]==0.8){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-importance');return false;">0.8</a>
			<?php if($PAGE["importance"]==0.9){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-importance');return false;">0.9</a>
			<?php if($PAGE["importance"]==1.0){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-importance');return false;">1.0 (Very Important)</a>
		</div>
		<select class="hide" name="page-importance" id="page-importance">
			<?php if($PAGE["importance"]==0.0){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0.0"></option>
			<?php if($PAGE["importance"]==0.1){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0.1"></option>
			<?php if($PAGE["importance"]==0.2){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0.2"></option>
			<?php if($PAGE["importance"]==0.3){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0.3"></option>
			<?php if($PAGE["importance"]==0.4){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0.4"></option>
			<?php if($PAGE["importance"]==0.5){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0.5"></option>
			<?php if($PAGE["importance"]==0.6){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0.6"></option>
			<?php if($PAGE["importance"]==0.7){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0.7"></option>
			<?php if($PAGE["importance"]==0.8){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0.8"></option>
			<?php if($PAGE["importance"]==0.9){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0.9"></option>
			<?php if($PAGE["importance"]==1.0){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="1.0"></option>
		</select>
	</div>
	
	<div class="blank row">
		<label for="page-description" class="blank fl">
			Search Engine Description<br>
			<span class="current-length tt"><?php echo strlen($PAGE["description"]);?></span><span class="tt"> of 255</span>
		</label>
		<input class="blank textbox mini fr" name="page-description" id="page-description" type="text" maxlength="255" value="<?php echo $PAGE["description"];?>">
	</div>
	
</div>

<?php

/* -------------------------- PUBLISHING --------------------------- */

?>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-content.png">
		<h2 class="blank fl">PUBLISHING</h2>
	</div>
	
	<div class='blank row'>
		<label class='blank fl'>Created</label>
		<input class="blank textbox mini fr" name="page-date" id="page-date" type="datetime-local" value="<?php echo str_replace(" ","T",$PAGE["date"]) . ".00";?>">
	</div>
	
	<div class='blank row<?=$create;?>'>
		<label class="blank fl">Auto Publish</label>
		<div class="blank select fr half">
		<?php
			if($PAGE["sttp"]==0){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-sttp');return false;">No</a>
			<?php if($PAGE["sttp"]==1){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-sttp');return false;">Yes</a>
		</div>
		<select class="hide" name="page-sttp" id="page-sttp" onchange="$('#page-ttp').toggle();">
			<?php if($PAGE["sttp"]==0){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0"></option>
			<?php if($PAGE["sttp"]==1){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="1"></option>
		</select>
	
		<?php if($PAGE["sttp"]==0){$visibility=" hide";}else{$visibility="";} ?>
		<div class='blank cb ten-space-vert'></div>
		<input class="blank textbox mini fr<?php echo $visibility;?>" name="page-ttp" id="page-ttp" type="datetime-local" value="<?php echo str_replace(" ","T",$PAGE["ttp"]) . ".00";?>">
	</div>
	
	<div class='blank row<?=$create;?>'>
		<label class="blank fl">Auto Close</label>
		<div class="blank select fr half">
		<?php
			if($PAGE["sttc"]==0){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-sttc');return false;">No</a>
			<?php if($PAGE["sttc"]==1){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-sttc');return false;">Yes</a>
		</div>
		<select class="hide" name="page-sttc" id="page-sttc" onchange="$('#page-ttc').toggle();">
			<?php if($PAGE["sttc"]==0){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0"></option>
			<?php if($PAGE["sttc"]==1){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="1"></option>
		</select>
	
		<?php if($PAGE["sttc"]==0){$visibility=" hide";}else{$visibility="";} ?>
		<div class='blank cb ten-space-vert'></div>
		<input class="blank textbox mini fr<?php echo $visibility;?>" name="page-ttc" id="page-ttc" type="datetime-local" value="<?php echo str_replace(" ","T",$PAGE["ttc"]) . ".00";?>">
	</div>

	<div class='blank row<?=$create;?>'>
		<label class="blank fl">Publish</label>
		<div class="blank select fr half">
		<?php
			if($PAGE["published"]==0){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-published');return false;">No</a>
			<?php if($PAGE["published"]==1){$flg = " selected";} else {$flg="";} ?>
			<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'page-published');return false;">Yes</a>
		</div>
		<select class="hide" name="page-published" id="page-published">
			<?php if($PAGE["published"]==0){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="0"></option>
			<?php if($PAGE["published"]==1){$flg = " selected";} else {$flg="";} ?>
			<option<?php echo $flg; ?> value="1"></option>
		</select>
	</div>
	
	<div class="blank row">
		<?php
		/* grab user who last modified this page */
		$NVX_DB->DB_CLEAR(array("ALL"));
		$NVX_DB->DB_SET_FILTER("`user`.`id`={$PAGE['by']}");
		$NVX_DB->DB_SET_LIMIT(1);
		$by = $NVX_BOOT->CYPHER(array("STRING"=>$NVX_DB->DB_QUERY("SELECT","`user`.`contact` FROM `user`")[0]["user.contact"],"TYPE"=>'decrypt'));
		?>
		<label class="blank fl huge">Last modified <span class="tt"><?php echo date('d-m-Y H:i',strtotime($PAGE["modified"]));?></span> By <span class="tt"><?php echo $by;?></span></label>
		<input type="hidden" name="page-by" id="page-by" value="<?php echo $PAGE["by"];?>">
	</div>
	<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
</div>
</form>