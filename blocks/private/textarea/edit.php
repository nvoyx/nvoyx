<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns textarea content
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
						<h2 class="blank fl">TEXTAREA</h2>
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
						<label class="blank fl">Format</label>
						<div class="blank select fr half">
							<?php
							if($g["plain"]==1){$flg = " selected";} else {$flg="";} ?>
							<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'plain');return false;">Plain</a>
							<?php if($g["plain"]==0){$flg = " selected";} else {$flg="";} ?>
							<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'plain');return false;">Html</a>
						</div>
						<select class="hide" name="plain" id="plain">
							<?php if($g["plain"]==1){$flg = " selected";} else {$flg="";} ?>
							<option<?php echo $flg; ?> value="1"></option>
							<?php if($g["plain"]==0){$flg = " selected";} else {$flg="";} ?>
							<option<?php echo $flg; ?> value="0"></option>
						</select>
					</div>
					
					<div class="blank row">
						<label class="blank fl">HTML Editor</label>
						<div class="blank select fr half">
							<?php
							$editors = $NVX_VAR->FETCH_ENTRY("editors");
							foreach($editors as $e){
								if($g["editor"]==$e){$flg = " selected";} else {$flg="";} ?>
								<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'editor');return false;"><?php echo ucwords($e);?></a>
							<?php } ?>
						</div>
						<select class="hide" name="editor" id="editor">
							<?php foreach($editors as $e){
								if($g["editor"]==$e){$flg = " selected";} else {$flg="";} ?>
								<option<?php echo $flg; ?> value="<?php echo $e;?>"></option>
							<?php } ?>
						</select>
					</div>
					
					<div class="blank row">
						<label for="maxlength" class="blank fl">Maximum Length</label>
						<input class="blank textbox mini fr" name="maxlength" id="maxlength" type="number" value="<?php echo $g["maxlength"];?>">
					</div>
				</div>
				
				<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
			</form>
			<?php break;			
		}
	}
}