<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns imagecache content
 */

/* imagecache iid */
$iid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* grab the imagecache */
$NVX_DB->DB_CLEAR(array("ALL"));
$NVX_DB->DB_SET_FILTER("`imagecache`.`id`={$iid}");
$imagecache = $NVX_DB->DB_QUERY("SELECT","* FROM `imagecache`");
?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-image.png">
		<h2 class="blank fl">IMAGE CACHE</h2>
		<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/imagecache/list">UP</a>
	</div>
	
	<form method="POST">
		<div class="blank row">
			<label for="name" class="blank fl">
				Name<br>
				<span class="current-length tt"><?php echo strlen($imagecache[0]["imagecache.name"]);?></span><span class="tt"> of 50</span>
			</label>
			<input class="blank textbox mini fr" name="name" id="name" type="text" maxlength="50" value="<?php echo $imagecache[0]["imagecache.name"];?>">
		</div>
		
		<div class="blank row">
			<label for="x" class="blank fl">Horizontal (pixels)</label>
			<input class="blank textbox mini fr" name="x" id="x" type="number" value="<?php echo $imagecache[0]["imagecache.x"];?>">
		</div>
		
		<div class="blank row">
			<label for="y" class="blank fl">Vertical (pixels)</label>
			<input class="blank textbox mini fr" name="y" id="y" type="number" value="<?php echo $imagecache[0]["imagecache.y"];?>">
		</div>
		
		<div class="blank row">
			<label class="blank fl">Mime</label>
			<div class="blank select fr half">
				<?php
				if($imagecache[0]["imagecache.mime"]=="jpg"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'mime');return false;">jpg</a>
				<?php if($imagecache[0]["imagecache.mime"]=="png"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'mime');return false;">png</a>
			</div>
			<select class="hide" name="mime" id="mime">
				<?php if($imagecache[0]["imagecache.mime"]=="jpg"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="jpg"></option>
				<?php if($imagecache[0]["imagecache.mime"]=="png"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="png"></option>
			</select>
		</div>
		
		<div class="blank row">
			<label for="value" class="blank fl">
				Effects<br>
				<span class="current-length tt"><?php echo strlen($imagecache[0]["imagecache.effects"]); ?></span><span class="tt"> of 1677215</span>
			</label>
			<textarea class="blank textarea plain big fl" name="effects" id="effects" maxlength="1677215"><?php echo $imagecache[0]["imagecache.effects"]; ?></textarea>
		</div>
		
		<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
	</form>
</div>