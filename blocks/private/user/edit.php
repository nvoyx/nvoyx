<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns user content
 */

/* user id */
$uid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);


/* grab all currently registered users */
$NVX_DB->CLEAR(array("ALL"));
$users = $NVX_DB->QUERY("SELECT","* FROM `user`");
$depts = $NVX_DB->QUERY("SELECT","* FROM `dept`");

/* lookup the user details */
foreach($users as $user){if($user["user.id"]==$uid){break;}}

/* have we found the user */
if(isset($user)){ ?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-user.png">
		<h2 class="blank fl">USER</h2>
		<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/user/list">UP</a>
	</div>
	
	<form method="POST">
		<div class="blank row">
			<label for="username" class="blank fl">
				Username<br>
				<span class="current-length tt"><?php echo strlen($NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$user["user.username"])));?></span><span class="tt"> of 50</span>
			</label>
			<input name="filter" id="filter" type="hidden" maxlength="50" value="<?php echo $user["user.filter"];?>">
			<input class="blank textbox mini fr" name="username" id="username" type="text" maxlength="50" value="<?php echo $NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$user["user.username"]));?>">
		</div>
		
		<div class="blank row">
			<label for="password" class="blank fl">
				Password<br>
				<span class="current-length tt"><?php echo strlen($NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$user["user.password"])));?></span><span class="tt"> of 50</span>
			</label>
			<input class="blank textbox mini fr" name="password" id="username" type="text" maxlength="50" value="<?php echo $NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$user["user.password"]));?>">
		</div>
		
		<div class="blank row">
			<label class="blank fl">Type</label>
			<div class="blank select fr half">
				<?php
				if($user["user.type"]=="u"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'type');return false;">User</a>
				<?php if($user["user.type"]=="a"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'type');return false;">Admin</a>
				<?php if($user["user.type"]=="s"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'type');return false;">Superuser</a>
			</div>
			<select class="hide" name="type" id="type">
				<?php if($user["user.type"]=="u"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="u"></option>
				<?php if($user["user.type"]=="a"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="a"></option>
				<?php if($user["user.type"]=="s"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="s"></option>
			</select>
		</div>
		
		<div class="blank row">
			<label class="blank fl">Dept</label>
			<div class="blank select fr half">
				<?php
				foreach($depts as $dept){
				if($dept["dept.id"]==$user['user.dept']){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'dept');return false;"><?=$dept['dept.name'];?></a>
				<?php } ?>
			</div>
			<select class="hide" name="dept" id="dept">
				<?php
				foreach($depts as $dept){
				if($dept["dept.id"]==$user['user.dept']){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="<?=$dept['dept.id'];?>"><?=$dept['dept.name'];?></option>
				<?php } ?>
			</select>
		</div>
		
		<div class="blank row">
			<label for="contact" class="blank fl">
				Contact<br>
				<span class="current-length tt"><?php echo strlen($NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$user["user.username"])));?></span><span class="tt"> of 128</span>
			</label>
			<input class="blank textbox mini fr" name="contact" id="contact" type="text" maxlength="128" value="<?php echo $NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$user["user.contact"]));?>">
		</div>
		
		<div class="blank row">
			<label for="telephone" class="blank fl">
				Telephone<br>
				<span class="current-length tt"><?php echo strlen($NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$user["user.telephone"])));?></span><span class="tt"> of 50</span>
			</label>
			<input class="blank textbox mini fr" name="telephone" id="telephone" type="text" maxlength="50" value="<?php echo $NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$user["user.telephone"]));?>">
		</div>
		
		<div class="blank row">
			<label for="email" class="blank fl">
				Email<br>
				<span class="current-length tt"><?php echo strlen($NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$user["user.email"])));?></span><span class="tt"> of 50</span>
			</label>
			<input class="blank textbox mini fr" name="email" id="email" type="text" maxlength="50" value="<?php echo $NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$user["user.email"]));?>">
		</div>
		
		<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
	</form>
		
</div>
<?php }