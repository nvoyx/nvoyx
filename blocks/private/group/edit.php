<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns group content
 */

/* rebuild the GROUP array */
$NVX_GROUP->BUILD_ARRAY(false);

/* group id */
$gid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* prepare a list of available types to pass into assoc select */
foreach($NVX_TYPE->FETCH_ARRAY() as $type){
	$o[]=array("INTERNAL"=>$type["id"],"EXTERNAL"=>$type["name"]);
}


/* lookup the group details */
foreach($NVX_GROUP->FETCH_ARRAY() as $group){if($group["id"]==$gid){break;}}

/* have we found the group */
if(isset($group)){ ?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<form method="POST">

	<div class="blank box">
	
		<div class="blank header">
			<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-group.png">
			<h2 class="blank fl">GROUP</h2>
			<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/group/list">UP</a>
		</div>
	
			<div class="blank row">
				<label for="name" class="blank fl">
					Name<br>
					<span class="current-length tt"><?php echo strlen($group["name"]);?></span><span class="tt"> of 50</span>
				</label>
				<input class="blank textbox mini fr" name="name" id="name" maxlength="50" type="text" value="<?php echo $group["name"];?>">
			</div>
	
			<div class="blank row">
				<label class="blank fl">Associations</label>
				<div class="blank mselect fr half">
					<?php
					foreach($o as $option){
						if(in_array($option["INTERNAL"],$group["assoc"])){$flg = " selected";} else {$flg="";} ?>
						<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'assoc');return false;"><?php echo $option["EXTERNAL"];?></a>
					<?php } ?>
				</div>
				<select  class="hide" name="assoc[]" id="assoc" multiple>
					<?php
					foreach($o as $option){
						if(in_array($option["INTERNAL"],$group["assoc"])){$flg = " selected";} else {$flg="";} ?>
						<option value="<?php echo $option["INTERNAL"];?>"<?php echo $flg; ?>></option>
					<?php } ?>
				</select>
			</div>
	
			<div class="blank row">
				<label class="blank fl">Access</label>
				<div class="blank select fr half">
					<?php
					if($group["access"]=="a"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'access');return false;">Admin</a>
					<?php if($group["access"]=="s"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'access');return false;">Superuser</a>
				</div>
				<select class="hide" name="access" id="access">
					<?php if($group["access"]=="a"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="a"></option>
					<?php if($group["access"]=="s"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="s"></option>
				</select>
			</div>
	
			<div class="blank row">
				<label class="blank fl">Variants</label>
				<input class="blank textbox mini fr" name="variants" id="variants" type="number" value="<?php echo $group["variants"];?>">
			</div>
	</div>
	
	<div class="blank box">
		
		<div class="blank header">
			<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-field.png">
			<h2 class="blank fl">FIELDS</h2>
		</div>
		
		<div class="blank row">
			<label class="blank fl">Add A Field</label>
			<div class="blank links fl big">
				<a class="blank mini fl" href="<?php echo "/settings/datebox/add/{$group["id"]}/{$group["nfid"]}"; ?>">DATEBOX</a>
				<a class="blank mini fl" href="<?php echo "/settings/filelist/add/{$group["id"]}/{$group["nfid"]}"; ?>">FILELIST</a>
				<a class="blank mini fl" href="<?php echo "/settings/heirarchy/add/{$group["id"]}/{$group["nfid"]}"; ?>">HEIRARCHY</a>
				<a class="blank mini fl" href="<?php echo "/settings/imagelist/add/{$group["id"]}/{$group["nfid"]}"; ?>">IMAGELIST</a>
				<a class="blank mini fl" href="<?php echo "/settings/mselect/add/{$group["id"]}/{$group["nfid"]}"; ?>">MSELECT</a>
				<a class="blank mini fl" href="<?php echo "/settings/sselect/add/{$group["id"]}/{$group["nfid"]}"; ?>">SSELECT</a>
				<a class="blank mini fl" href="<?php echo "/settings/tagbox/add/{$group["id"]}/{$group["nfid"]}"; ?>">TAGBOX</a>
				<a class="blank mini fl" href="<?php echo "/settings/textarea/add/{$group["id"]}/{$group["nfid"]}"; ?>">TEXTAREA</a>
				<a class="blank mini fl" href="<?php echo "/settings/textbox/add/{$group["id"]}/{$group["nfid"]}"; ?>">TEXTBOX</a>
				<a class="blank mini fl" href="<?php echo "/settings/videolist/add/{$group["id"]}/{$group["nfid"]}"; ?>">VIDEOLIST</a>
			</div>
		</div>
		
		<ul class="sortable">
			<?php /* cycle through the group fields */ foreach($group["outline"] as $field){ ?>
			<li class="blank row">
				<label class="blank fl"><?php echo ucwords($field["name"]);?></label>
				<a title="edit" href="<?php echo "/settings/{$field["type"]}/edit/{$group["id"]}/{$field["fid"]}";?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
				<a title="delete" onclick="deleteFieldOption(this);return false;" href="#"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
				<a class="hand" title="drag and drop"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-grip.png"></a>
				<input type="hidden" name="fields[]" value="<?php echo $field["fid"];?>">
			</li>
			<?php } ?>
		</ul>
		
		<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
	</div>
</form>
<?php }