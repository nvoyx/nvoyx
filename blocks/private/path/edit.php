<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns path content
 */

/* rebuild the PATH array */
$NVX_PATH->BUILD_ARRAY();

/* path id */
$pid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* lookup the path details */
foreach($NVX_PATH->FETCH_ARRAY() as $path){if($path["id"]==$pid){break;}}

/* have we found the path */
if(isset($path)){ ?>

	<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
	<div class="blank box" id="header">
		<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
		<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
	</div>

	<form method="POST">

		<div class="blank box">

			<div class="blank header">
				<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-field.png">
				<h2 class="blank fl">PATH</h2>
				<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/path/list">UP</a>
			</div>

			<div class="blank row">
				<label label="url" class="blank fl">
					Url<br>
					<span class="current-length tt"><?php echo strlen($path["url"]);?></span><span class="tt"> of 255</span>
				</label>
				<input class="blank textbox mini fr" name="url" id="url" type="text" maxlength="255" value="<?php echo $path["url"];?>">
			</div>

			<div class="blank row">
				<label class="blank fl">Access</label>
				<div class="blank select fr half">
					<?php
					if($path["access"]=="u"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'access');return false;">User</a>
					<?php if($path["access"]=="a"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'access');return false;">Admin</a>
					<?php if($path["access"]=="s"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'access');return false;">Superuser</a>
				</div>
				<select class="hide" name="access" id="access">
					<?php if($path["access"]=="u"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="u"></option>
					<?php if($path["access"]=="a"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="a"></option>
					<?php if($path["access"]=="s"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="s"></option>
				</select>
			</div>
			
			<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
		</div>
	</form>
<?php }