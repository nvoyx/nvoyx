<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns imagelist fields to edit
 */

/* rebuild the GROUP array */
$NVX_GROUP->BUILD_ARRAY();

/* field gid */
$gid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* field id */
$fid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",4);

/* lookup the group details */
foreach($NVX_GROUP->FETCH_ARRAY() as $group){if($group["id"]==$gid){break;}}

/* have we found the group */
if(isset($group)){
	
	/* loop through the groups */
	foreach($group["outline"] as $g){
		
		/* have we found the correct field */
		if($g["fid"] == $fid){ ?>

			<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
			<div class="blank box" id="header">
				<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
				<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
			</div>

			<form method="POST">

				<div class="blank box">

					<div class="blank header">
						<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-field.png">
						<h2 class="blank fl">IMAGELIST</h2>
						<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="<?php echo "/settings/group/edit/{$gid}"; ?>">UP</a>
					</div>

					<div class="blank row">
						<label for="name" class="blank fl">
							Name<br>
							<span class="current-length tt"><?php echo strlen($g["name"]);?></span><span class="tt"> of 50</span>
						</label>
						<input class="blank textbox mini fr" name="name" id="name" type="text" maxlength="50" value="<?php echo $g["name"];?>">
					</div>
					
					<div class="blank row">
						<label for="total" class="blank fl">Maximum Images</label>
						<input class="blank textbox mini fr" name="total" id="total" type="number" value="<?php echo $g["total"];?>">
					</div>
					
					<div class="blank row">
						<label for="size" class="blank fl">Size</label>
						<input class="blank textbox mini fr" name="size" id="size" type="number" step="1024" value="<?php echo $g["size"];?>">
					</div>
					
					<div class="blank row">
						<label class="blank fl">Link</label>
						<div class="blank select fr half">
							<?php
							if($g["link"]==1){$flg = " selected";} else {$flg="";} ?>
							<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'link');return false;">Enabled</a>
							<?php if($g["link"]==0){$flg = " selected";} else {$flg="";} ?>
							<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'link');return false;">Disabled</a>
						</div>
						<select class="hide" name="link" id="link">
							<?php if($g["link"]==1){$flg = " selected";} else {$flg="";} ?>
							<option<?php echo $flg; ?> value="1"></option>
							<?php if($g["link"]==0){$flg = " selected";} else {$flg="";} ?>
							<option<?php echo $flg; ?> value="0"></option>
						</select>
					</div>
					
					<div class="blank row">
						<label class="blank fl">Extra Type</label>
						<div class="blank select fr half">
							<?php
							if($g["extra-type"]=="none"){$flg = " selected";} else {$flg="";} ?>
							<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'extra-type');return false;">None</a>
							<?php if($g["extra-type"]=="plain"){$flg = " selected";} else {$flg="";} ?>
							<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'extra-type');return false;">Plain</a>
							<?php if($g["extra-type"]=="html"){$flg = " selected";} else {$flg="";} ?>
							<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'extra-type');return false;">Html</a>
						</div>
						<select class="hide" name="extra-type" id="extra-type">
							<?php if($g["extra-type"]=="none"){$flg = " selected";} else {$flg="";} ?>
							<option<?php echo $flg; ?> value="none"></option>
							<?php if($g["extra-type"]=="plain"){$flg = " selected";} else {$flg="";} ?>
							<option<?php echo $flg; ?> value="plain"></option>
							<?php if($g["extra-type"]=="html"){$flg = " selected";} else {$flg="";} ?>
							<option<?php echo $flg; ?> value="html"></option>
						</select>
					</div>
					
					<div class="blank row">
						<label class="blank fl">Extra Editor</label>
						<div class="blank select fr half">
							<?php
							$editors = $NVX_VAR->FETCH_ENTRY("editors");
							foreach($editors as $e){
								if($g["extra-editor"]==$e){$flg = " selected";} else {$flg="";} ?>
								<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'extra-editor');return false;"><?php echo ucwords($e);?></a>
							<?php } ?>
						</div>
						<select class="hide" name="extra-editor" id="extra-editor">
							<?php foreach($editors as $e){
								if($g["extra-editor"]==$e){$flg = " selected";} else {$flg="";} ?>
								<option<?php echo $flg; ?> value="<?php echo $e;?>"></option>
							<?php } ?>
						</select>
					</div>
					
					<div class="blank row">
						<label class="blank fl">Extra Spellchecker</label>
						<div class="blank select fr half">
							<?php
							$languages = $NVX_VAR->FETCH_ENTRY("languages");
							foreach($languages as $l){
								if($g["extra-spellchecker"]==$l){$flg = " selected";} else {$flg="";} ?>
								<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'extra-spellchecker');return false;"><?php echo ucwords($l);?></a>
							<?php } ?>
						</div>
						<select class="hide" name="extra-spellchecker" id="extra-spellchecker">
							<?php foreach($languages as $l){
								if($g["extra-spellchecker"]==$l){$flg = " selected";} else {$flg="";} ?>
								<option<?php echo $flg; ?> value="<?php echo $l;?>"></option>
							<?php } ?>
						</select>
					</div>
					
					<div class="blank row">
						<label for="extra-name" class="blank fl">
							Extra Name<br>
							<span class="current-length tt"><?php echo strlen($g["extra-name"]);?></span><span class="tt"> of 50</span>
						</label>
						<input class="blank textbox mini fr" name="extra-name" id="extra-name" type="text" value="<?php echo $g["extra-name"];?>">
					</div>
					
					<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
				</div>
			</form>
			
			<?php break;			
		}
	}
}