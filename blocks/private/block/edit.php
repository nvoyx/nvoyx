<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * return / edit block content
 */

/* block id */
$bid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* prepare a list of available page types to pass into tid select */
foreach($NVX_TYPE->FETCH_ARRAY() as $type){
	$o[]=array("INTERNAL"=>$type["id"],"EXTERNAL"=>$type["name"]);
}

/* lookup the block details */
foreach($NVX_BLOCK->FETCH_ARRAY() as $block){if($block["id"]==$bid){break;}}

/* have we found the block */
if(isset($block)){ ?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-block.png">
		<h2 class="blank fl">BLOCK</h2>
		<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/block/list">UP</a>
	</div>
	
	<form method="POST">
		<div class="blank row">
			<label for="name" class="blank fl">
				Name<br>
				<span class="current-length tt"><?php echo strlen($block["name"]);?></span><span class="tt"> of 255</span>
			</label>
			<input class="blank textbox mini fr" name="name" id="name" type="text" maxlength="255" value="<?php echo $block["name"];?>">
		</div>
		
		<div class="blank row">
			<label class="blank fl">Associations</label>
			<div class="blank mselect fr small">
				<?php
				$rs = $block["tid"];
				foreach($o as $option){
					if(in_array($option["INTERNAL"],$rs)){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'tid');return false;"><?php echo $option["EXTERNAL"];?></a>
				<?php } ?>
			</div>
			<select  class="hide" name="tid[]" id="tid" multiple>
				<?php
				foreach($o as $option){
					if(in_array($option["INTERNAL"],$rs)){$flg = " selected";} else {$flg="";} ?>
					<option value="<?php echo $option["INTERNAL"];?>"<?php echo $flg; ?> ></option>
				<?php } ?>
			</select>
		</div>
	
		<div class="blank row">
			<label class="blank fl">Access</label>
			<div class="blank select fr small">
				<?php
				if($block["access"]=="u"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'access');return false;">User</a>
				<?php if($block["access"]=="a"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'access');return false;">Admin</a>
				<?php if($block["access"]=="s"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'access');return false;">Superuser</a>
			</div>
			<select class="hide" name="access" id="access">
				<?php
				if($block["access"]=="u"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="u"></option>
				<?php if($block["access"]=="a"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="a"></option>
				<?php if($block["access"]=="s"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="s"></option>
			</select>
		</div>
	
		<div class="blank row">
			<label for="params" class="blank fl">
				Params<br>
				<span class="current-length tt"><?php echo strlen($NVX_BOOT->JSON($block["params"],"encode")); ?></span><span class="tt"> of 16777215</span>
			</label>
			<textarea class="blank textarea plain big fl" name="params" id="params" maxlength="16777215" ><?php echo $NVX_BOOT->JSON($block["params"],"encode"); ?></textarea>
		</div>
	
		<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
	</form>
		
</div>

<?php }