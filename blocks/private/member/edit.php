<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns member content
 */

/* member id */
$mid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* lookup the members details */
$NVX_DB->CLEAR(array("ALL"));
$NVX_DB->SET_FILTER("`member`.`id`={$mid}");
$NVX_DB->SET_LIMIT(1);
$rs = $NVX_DB->QUERY("SELECT","* FROM `member`");

/* create an empty array to hold the decyphered member details */
$member=array();

/* cycle over the member array */
foreach($rs[0] as $key=>$value){
	
	switch($key):
		case "member.title":
		case "member.firstname":
		case "member.lastname":
		case "member.position":
		case "member.company":
		case "member.email":
		case "member.username":
		case "member.password":
			
			/* add the decyphered member details */
			$member[str_replace("member.","",$key)] = $NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$value));
			break;
		
		default:
			
			/* add the member details */
			$member[str_replace("member.","",$key)] = $value;
			break;
		endswitch;	
}
	
?>


<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>


<form method="POST">
	<div class="blank box">
		<div class="blank header">
			<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-user.png">
			<h2 class="blank fl">MEMBER</h2>
			<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/member/list">UP</a>
		</div>

		<div class="blank row">
			<label for="username" class="blank fl">
				Username<br>
				<span class="current-length tt"><?php echo strlen($member["username"]);?></span><span class="tt"> of 50</span>
			</label>
			<input class="blank textbox mini fr" name="username" id="username" type="text" maxlength="50" value="<?php echo $member["username"];?>">
		</div>

		<div class="blank row">
			<label for="password" class="blank fl">
				Password<br>
				<span class="current-length tt"><?php echo strlen($member["password"]);?></span><span class="tt"> of 50</span>
			</label>
			<input class="blank textbox mini fr" name="password" id="password" type="text" maxlength="50" value="<?php echo $member["password"];?>">
		</div>
	</div>
	
	<div class="blank box">
		<div class="blank header">
			<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-user.png">
			<h2 class="blank fl">Details</h2>
		</div>

		<div class="blank row">
			<label class="blank fl">Sex</label>
			<div class="blank select fr half">
				<?php
				if($member["sex"]=="m"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'sex');return false;">Male</a>
				<?php if($member["sex"]=="f"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'sex');return false;">Female</a>
			</div>
			<select class="hide" name="sex" id="sex">
				<?php if($member["sex"]=="m"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="m"></option>
				<?php if($member["sex"]=="f"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="f"></option>
			</select>
		</div>
		
		<div class="blank row">
			<label class="blank fl">Title</label>
			<div class="blank select fr half">
				<?php
				if($member["title"]=="Mr"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'title');return false;">Mr</a>
				<?php if($member["title"]=="Ms"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'title');return false;">Ms</a>
				<?php if($member["title"]=="Miss"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'title');return false;">Miss</a>
				<?php if($member["title"]=="Mrs"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'title');return false;">Mrs</a>
			</div>
			<select class="hide" name="title" id="title">
				<?php if($member["title"]=="Mr"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="Mr"></option>
				<?php if($member["title"]=="Ms"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="Ms"></option>
				<?php if($member["title"]=="Miss"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="Miss"></option>
				<?php if($member["title"]=="Mrs"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="Mrs"></option>
			</select>
		</div>
		
		<div class="blank row">
			<label for="firstname" class="blank fl">
				Firstname<br>
				<span class="current-length tt"><?php echo strlen($member["firstname"]);?></span><span class="tt"> of 50</span>
			</label>
			<input class="blank textbox mini fr" name="firstname" id="firstname" type="text" maxlength="50" value="<?php echo $member["firstname"];?>">
		</div>
		
		<div class="blank row">
			<label for="lastname" class="blank fl">
				Lastname<br>
				<span class="current-length tt"><?php echo strlen($member["lastname"]);?></span><span class="tt"> of 50</span>
			</label>
			<input class="blank textbox mini fr" name="lastname" id="lastname" type="text" maxlength="50" value="<?php echo $member["lastname"];?>">
		</div>
		
		<div class="blank row">
			<label for="position" class="blank fl">
				Position<br>
				<span class="current-length tt"><?php echo strlen($member["position"]);?></span><span class="tt"> of 50</span>
			</label>
			<input class="blank textbox mini fr" name="position" id="position" type="text" maxlength="50" value="<?php echo $member["position"];?>">
		</div>

		<div class="blank row">
			<label for="company" class="blank fl">
				Company<br>
				<span class="current-length tt"><?php echo strlen($member["company"]);?></span><span class="tt"> of 50</span>
			</label>
			<input class="blank textbox mini fr" name="company" id="company" type="text" maxlength="50" value="<?php echo $member["company"];?>">
		</div>
		
		<div class="blank row">
			<label for="dob" class="blank fl">Date Of Birth</label>
			<input class="blank textbox mini fr" name="dob" id="dob" type="date" value="<?php echo $member["dob"];?>">
		</div>
		
		<div class="blank row">
			<label for="email" class="blank fl">
				Email<br>
				<span class="current-length tt"><?php echo strlen($member["email"]);?></span><span class="tt"> of 128</span>
			</label>
			<input class="blank textbox mini fr" name="email" id="email" type="text" maxlength="128" value="<?php echo $member["email"];?>">
		</div>
		
		<div class="blank row">
			<label for="web" class="blank fl">
				Website<br>
				<span class="current-length tt"><?php echo strlen($member["web"]);?></span><span class="tt"> of 128</span>
			</label>
			<input class="blank textbox mini fr" name="web" id="web" type="text" maxlength="128" value="<?php echo $member["web"];?>">
		</div>
	</div>
	
	<div class="blank box">
		<div class="blank header">
			<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-user.png">
			<h2 class="blank fl">Settings</h2>
		</div>
		
		<div class="blank row">
			<label for="joined" class="blank fl">Date Joined</label>
			<?php $member["joined"] = str_replace(" ","T",$member["joined"]) . ".00";?>
			<input class="blank textbox mini fr" name="joined" id="joined" type="datetime-local" value="<?php echo $member["joined"];?>">
		</div>
		
		<div class="blank row">
			<label class="blank fl">State</label>
			<div class="blank select fr half">
				<?php
				if($member["state"]=="0"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'state');return false;">Disabled</a>
				<?php if($member["state"]=="1"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'state');return false;">Enabled</a>
			</div>
			<select class="hide" name="state" id="state">
				<?php if($member["state"]=="0"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="0"></option>
				<?php if($member["state"]=="1"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="1"></option>
			</select>
		</div>
		
		<div class="blank row">
			<label class="blank fl">Comments</label>
			<div class="blank select fr half">
				<?php
				if($member["comments"]=="0"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'comments');return false;">Disabled</a>
				<?php if($member["comments"]=="1"){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'comments');return false;">Enabled</a>
			</div>
			<select class="hide" name="comments" id="comments">
				<?php if($member["comments"]=="0"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="0"></option>
				<?php if($member["comments"]=="1"){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="1"></option>
			</select>
		</div>
		
		<div class="blank row">
			<label for="avatar" class="blank fl">
				Avatar<br>
				<span class="current-length tt"><?php echo strlen($member["avatar"]);?></span><span class="tt"> of 128</span>
			</label>
			<input class="blank textbox mini fr" name="avatar" id="avatar" type="text" maxlength="128" value="<?php echo $member["avatar"];?>">
		</div>
		
	</div>

	<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
</form>