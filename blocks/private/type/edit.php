<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns type content
 */

/* type id */
$tid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);


/* lookup the type details */
foreach($NVX_TYPE->FETCH_ARRAY() as $type){if($type["id"]==$tid){break;}}

/* have we found the type */
if(isset($type)){ ?>


	<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
	<div class="blank box" id="header">
		<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
		<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
	</div>

	<form method="POST">

		<div class="blank box">

			<div class="blank header">
				<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-type.png">
				<h2 class="blank fl">TYPE</h2>
				<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/type/list">UP</a>
			</div>

			<div class="blank row">
				<label for="name" class="blank fl">
					Name<br>
					<span class="current-length tt"><?php echo strlen($type["name"]);?></span><span class="tt"> of 255</span>
				</label>
				<input class="blank textbox mini fr" name="name" id="name" type="text" maxlength="255" value="<?php echo $type["name"];?>">
			</div>
			
			<div class="blank row">
				<label for="prefix" class="blank fl">
					Prefix<br>
					<span class="current-length tt"><?php echo strlen($type["prefix"]);?></span><span class="tt"> of 255</span>
				</label>
				<input class="blank textbox mini fr" name="prefix" id="prefix" type="text" maxlength="255" value="<?php echo $type["prefix"];?>">
				<div class="blank cb">
					<span class="blank tt fr" style="color:#fff;">[id:] | [cd:dateformat] | [ss:gid-vid-fid] | [ms:gid-vid-fid]</span>
				</div>
			</div>
			
			<div class="blank row">
				<label class="blank fl">Parent</label>
				<div class="blank select fr half">
					<?php
					$options[] = array("INTERNAL"=>-1,"EXTERNAL"=>"[none]");
					foreach($NVX_TYPE->FETCH_ARRAY() as $t){
						if($t["id"]!=$type["id"]){
							$options[] = array("INTERNAL"=>$t["id"],"EXTERNAL"=>$t["name"]);
						}
					}
					foreach($options as $o){
						if($type["parent"]==$o["INTERNAL"]){$flg = " selected";} else {$flg="";} ?>
						<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'parent');return false;"><?php echo ucwords($o['EXTERNAL']);?></a>
					<?php } ?>
				</div>
				<select class="hide" name="parent" id="parent">
					<?php foreach($options as $o){
						if($type["parent"]==$o["INTERNAL"]){$flg = " selected";} else {$flg="";} ?>
						<option<?php echo $flg; ?> value="<?php echo $o["INTERNAL"];?>"></option>
					<?php } ?>
				</select>
			</div>
			
			<div class="blank row">
				<label class="blank fl">View</label>
				<div class="blank select fr half">
					<?php
					if($type["view"]=="u"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'view');return false;">User</a>
					<?php if($type["view"]=="a"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'view');return false;">Admin</a>
					<?php if($type["view"]=="s"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'view');return false;">Superuser</a>
				</div>
				<select class="hide" name="view" id="view">
					<?php if($type["view"]=="u"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="u"></option>
					<?php if($type["view"]=="a"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="a"></option>
					<?php if($type["view"]=="s"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="s"></option>
				</select>
			</div>
			
			<div class="blank row">
				<label class="blank fl">Create / Delete</label>
				<div class="blank select fr half">
					<?php
					if($type["createdelete"]=="u"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'createdelete');return false;">User</a>
					<?php if($type["createdelete"]=="a"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'createdelete');return false;">Admin</a>
					<?php if($type["createdelete"]=="s"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'createdelete');return false;">Superuser</a>
				</div>
				<select class="hide" name="createdelete" id="createdelete">
					<?php if($type["createdelete"]=="u"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="u"></option>
					<?php if($type["createdelete"]=="a"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="a"></option>
					<?php if($type["createdelete"]=="s"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="s"></option>
				</select>
			</div>
			
			<div class="blank row">
				<label class="blank fl">RSS</label>
				<div class="blank select fr half">
					<?php
					if($type["rss"]=="0"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'rss');return false;">Disabled</a>
					<?php if($type["rss"]=="1"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'rss');return false;">Enabled</a>
				</div>
				<select class="hide" name="rss" id="rss">
					<?php if($type["rss"]=="0"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="0"></option>
					<?php if($type["rss"]=="1"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="1"></option>
				</select>
			</div>
			
			<div class="blank row">
				<label class="blank fl">Body</label>
				<div class="blank select fr half">
					<?php
					if($type["body"]=="0"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'body');return false;">Disabled</a>
					<?php if($type["body"]=="1"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'body');return false;">Enabled</a>
				</div>
				<select class="hide" name="body" id="body">
					<?php if($type["body"]=="0"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="0"></option>
					<?php if($type["body"]=="1"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="1"></option>
				</select>
			</div>
			
			<div class="blank row">
				<label class="blank fl">Comments</label>
				<div class="blank select fr half">
					<?php
					if($type["comments"]=="0"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'comments');return false;">Disabled</a>
					<?php if($type["comments"]=="1"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'comments');return false;">Plain</a>
					<?php if($type["comments"]=="2"){$flg = " selected";} else {$flg="";} ?>
					<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'comments');return false;">Html</a>
				</div>
				<select class="hide" name="comments" id="comments">
					<?php if($type["comments"]=="0"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="0"></option>
					<?php if($type["comments"]=="1"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="1"></option>
					<?php if($type["comments"]=="2"){$flg = " selected";} else {$flg="";} ?>
					<option<?php echo $flg; ?> value="2"></option>
				</select>
			</div>
			
			<div class="blank row">
				<label class="blank fl">Comments HTML Editor</label>
				<div class="blank select fr half">
					<?php
					$editors = $NVX_VAR->FETCH_ENTRY("editors");
					foreach($editors as $e){
						if($type["comeditor"]==$e){$flg = " selected";} else {$flg="";} ?>
						<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'comeditor');return false;"><?php echo ucwords($e);?></a>
					<?php } ?>
				</div>
				<select class="hide" name="comeditor" id="comeditor">
					<?php foreach($editors as $e){
						if($type["comeditor"]==$e){$flg = " selected";} else {$flg="";} ?>
						<option<?php echo $flg; ?> value="<?php echo $e;?>"></option>
					<?php } ?>
				</select>
			</div>
			
			<div class="blank row">
				<label for="template" class="blank fl">Template</label>
				<input class="blank textbox mini fr" name="template" id="template" type="number" value="<?php echo $type["template"];?>">
			</div>
			
			<div class="blank row">
				<label for="tags" class="blank fl">
					Tags<br>
					<span class="current-length tt"><?php echo strlen(implode("[format:newline]",$type["tags"]));?></span><span class="tt"> of 16777215</span>
				</label>
				<textarea class="blank textarea plain big fl" name="tags" id="tags" maxlength="16777215" ><?php echo implode("[format:newline]",$type["tags"]); ?></textarea>
			</div>
			
			<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
		</div>
	</form>
<?php }