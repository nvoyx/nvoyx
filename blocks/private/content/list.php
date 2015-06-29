<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns site content listing
 */

/* set type array counter */
$a=-1;

/* cycle through the content types */
foreach($NVX_TYPE->FETCH_ARRAY() as $type){
	
	/* check permissions to see the current page type */
	if(stristr($NVX_USER->FETCH_ENTRY("type"),$type["view"])){
		
		/* increment the type counter */
		$a++;
		
		/* add to the page-type dropdown */
		$options[$a]=array("INTERNAL"=>$a,"EXTERNAL"=>$type["name"]);
		
		/* if current user type is allowed to create delete this page */
		if(stristr($NVX_USER->FETCH_ENTRY("type"),$type["createdelete"])){
			
			/* flag true in create array */
			$create[$a]=1;
		} else {
			
			/* flag false in create array */
			$create[$a]=0;
		}
		
		/* create a list of all pages for that content type */
		$NVX_DB->DB_CLEAR(array("ALL"));
		$NVX_DB->DB_SET_FILTER("`page`.`tid`={$type['id']}");
		$NVX_DB->DB_SET_ORDER(array("`page`.`title`"=>"ASC"));
		$pages[$a] = $NVX_DB->DB_QUERY("SELECT","* FROM `page`");
		
		/* make a note of the type prefix */
		$prefix[$a] = $type["prefix"];
		
		/* make a note of the tid */
		$tid[$a] = $type['id'];
	}
}

/* grab the "type filter variable" */
$NVX_DB->DB_CLEAR(array("ALL"));
$NVX_DB->DB_SET_FILTER("`user`.`id`={$_SESSION['id']}");
$type_filter = $NVX_DB->DB_QUERY("SELECT","* FROM `user`")[0]["user.filter"];

?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">	
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-system.png">
		<h2 class="blank fl">SYSTEM</h2>
	</div>
	
	<div class="blank row">
		<label class="blank fl">Options</label>
		<?php
			$rs = array(
				array("link"=>"/settings/redirects/list","txt"=>"301 REDIRECTS"),
				array("link"=>"/settings/ajaxmanager/list","txt"=>"AJAX"),
				array("link"=>"/settings/block/list","txt"=>"BLOCKS"),
				array("link"=>"/settings/debug/list","txt"=>"DEBUG"),
				array("link"=>"/settings/dept/list","txt"=>"DEPARTMENTS"),
				array("link"=>"/settings/group/list","txt"=>"GROUPS"),
				array("link"=>"/settings/imagecache/list","txt"=>"IMAGE CACHE"),
				array("link"=>"/settings/member/list","txt"=>"MEMBERS"),
				array("link"=>"/settings/path/list","txt"=>"PATHS"),
				array("link"=>"/settings/recovery/list","txt"=>"RECOVERY"),
				array("link"=>"/settings/type/list","txt"=>"TYPES"),
				array("link"=>"/settings/user/list","txt"=>"USERS"),
				array("link"=>"/settings/variables/list","txt"=>"VARIABLES")
				);
		?>
		<div class="blank links fl big">
			<?php foreach($rs as $r){
				
				if($NVX_USER->GRANTED($NVX_PATH->FETCH_ENTRY($r["link"])["access"])){ ?>
					<a class="blank mini fl" href="<?php echo $r["link"]; ?>"><?php echo $r["txt"];?></a>
				<?php }
			} ?>
		</div>
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
			<?php 
			$a=0;
			foreach($options as $o){ 
			?>
			<a class='blank mini content-list-item<?php if($type_filter==$a){echo " selected";}?>' onclick="select(this,'content-list-types');contentList('<?= $_SESSION["id"];?>');return false;"><?php echo $o["EXTERNAL"];?></a>
			<?php $a++;} ?>
		</div>
		<select class="hide" name="content-list-types" id="content-list-types">
			<?php
			$a=0;
			foreach($options as $o){
			?>
			<option value="<?php echo $o["INTERNAL"];?>"<?php if($type_filter==$a){echo " selected";}?>></option>
			<?php $a++;} ?>
		</select>
	</div>
</div>

<?php

/* output variable */
$html = "";

/* do we have some page types */
if(is_array($options)){	
	
	for($b=0;$b<=$a-1;$b++){
		
		?>
		<div class="blank box content-list-type" id="content-list-type-<?php echo $b;?>" <?php if($b!=$type_filter){echo " style='display:none'";}?>>
			<div class="blank header">
				<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-content.png">
				<h2 class="blank fl">CONTENT</h2>
		<?php
		
		/* is this user allowed to create/delete pages of this type */
		if($create[$b]==1){
			
			?><a class="fr" href="/settings/content/add/<?php echo $tid[$b];?>">ADD</a><?php
			
		} ?></div><?php
		
		if($pages[$b]!=false){
			foreach($pages[$b] as $page){
				
				/* create a default sselected variable */
				$sselected = "";
				
				/* does the current prefix contain an sselect tag */
				if(stristr($prefix[$b],"[ss:")){
										
					/* grab everything after the start of the sselect tag definition */
					$r = substr($prefix[$b],strpos($prefix[$b],"[ss:")+4);
				
					/* grab everything until the closing of the tag */
					$r = substr($r,0,strpos($r,"]"));
					
					/* convert the gid-vid-fid to an array */
					$x = explode("-",$r);
					
					/* go grab the selected listing */
					$NVX_DB->DB_CLEAR(array("ALL"));
					$NVX_DB->DB_SET_FILTER("`sselect`.`nid`={$page["page.id"]} AND `sselect`.`gid`={$x[0]} AND `sselect`.`vid`={$x[1]} AND `sselect`.`fid`={$x[2]}");
					$NVX_DB->DB_SET_LIMIT(1);
					$sselected = $NVX_DB->DB_QUERY("SELECT","`sselect`.`values` FROM `sselect`")[0]['sselect.values'];
					
					/* grab an array group containing the sselect */
					$gs = $NVX_GROUP->FETCH_ARRAY()["id-{$x[0]}"]["outline"];
					
					/* cycle through the group */
					foreach ($gs as $g){
						
						/* have we found the right group */
						if($g["fid"]==$x[2]){
							
							/* cycle through the options */
							foreach($g["content"] as $option){
								
								/* if this option holds the same internal value as the current page */
								if($sselected == $option){

									/* grab the external reference */
									$sselected = $option;break;
								}
								
							}
						}
					}
					
				}
				
				/* create a default mselected variable */
				$mselected = "";
				
				/* does the current prefix contain an mselect tag */
				if(stristr($prefix[$b],"[ms:")){
										
					/* grab everything after the start of the mselect tag definition */
					$r = substr($prefix[$b],strpos($prefix[$b],"[ms:")+4);
				
					/* grab everything until the closing of the tag */
					$r = substr($r,0,strpos($r,"]"));
					
					/* convert the gid-vid-fid to an array */
					$x = explode("-",$r);
										
					/* go grab the selected listing */
					$NVX_DB->DB_CLEAR(array("ALL"));
					$NVX_DB->DB_SET_FILTER("`mselect`.`nid`={$page["page.id"]} AND `mselect`.`gid`={$x[0]} AND `mselect`.`vid`={$x[1]} AND `mselect`.`fid`={$x[2]}");
					$NVX_DB->DB_SET_LIMIT(1);
					$mselected = $NVX_DB->DB_QUERY("SELECT","`mselect`.`values` FROM `mselect`")[0]['mselect.values'];
										
					/* grab an array group containing the mselect */
					$gs = $NVX_GROUP->FETCH_ARRAY()["id-{$x[0]}"]["outline"];
					
					/* cycle through the group */
					foreach ($gs as $g){
						
						/* have we found the right group */
						if($g["fid"]==$x[2]){
							
							/* cycle through the options */
							foreach($g["content"] as $option){
								
								/* cycle through the selected mselect options */
								foreach($NVX_BOOT->JSON($mselected,"decode") as $mselect){
									
									/* if this option holds the same internal value as the current page */
									if($mselect == $option){

										/* grab the external reference (this will always return the first selected mselect option) */
										$mselected = $option;break;										
									}
								}								
							}
						}
					}
				}
								
				/* build the URL including any TYPE prefix and TAG substitution and converting front page to empty alias */
				$r = $NVX_HTML->URL_BY_NID(array("NID"=>$page["page.id"],
												"PREFIX"=>$prefix[$b],
												"ALIAS"=>$page["page.alias"],
												"TITLE"=>$page["page.title"],
												"HEADING"=>$page["page.heading"],
												"TAGS"=>array("CREATED"=>$page["page.date"],
																"NODE"=>$page["page.id"],
																"SSELECT"=>$sselected,
																"MSELECT"=>$mselected)
												));
				?>
			
				<div class="blank row">
					<label class="blank fl half"><a href="https://<?php echo $r["URL"];?>"><?php echo ucwords($r["TITLE"]);?></a></label>
					<a title="edit" href="<?php echo "/settings/content/edit/".$page["page.id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
					<?php if($create[$b]==1){ ?>
					<a title="delete" href="<?php echo "/settings/content/delete/".$page["page.id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
					<?php } ?>
				</div>
			
				<?php
			}
		}?></div><?php					
	}
}