<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns department content
 */

/* user id */
$did = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);


/* grab all departments */
$NVX_DB->DB_CLEAR(array("ALL"));
$departments = $NVX_DB->DB_QUERY("SELECT","* FROM `dept`");

/* lookup the department details */
foreach($departments as $department){if($department["dept.id"]==$did){break;}}

/* have we found the dept */
if(isset($department)){ ?>

<?php 

/* grab a list of all types */
$types=array();
foreach($NVX_TYPE->FETCH_ARRAY() as $rs){
	$types[$rs['name']]=$rs['id'];
}

/* explode the access tids for this dept. */
$access=$NVX_BOOT->JSON($department['dept.access'],'decode');

?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-user.png">
		<h2 class="blank fl">DEPARTMENT</h2>
		<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/dept/list">UP</a>
	</div>
	
	<form method="POST">
		<div class="blank row">
			<label for="name" class="blank fl">
				Name<br>
				<span class="current-length tt"><?php echo strlen($department["dept.name"]);?></span><span class="tt"> of 255</span>
			</label>
			<input class="blank textbox mini fr" name="name" id="name" type="text" maxlength="255" value="<?php echo $department["dept.name"];?>">
		</div>
		
		<div class="blank row">
			<label class="blank fl">Access</label>
			<div class="blank mselect fr half">
				<?php
				foreach($types as $tkey=>$tval) { 
				if(in_array($tval,$access)){$flg = " selected";} else {$flg="";} ?>
				<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'access');return false;"><?=$tkey;?></a>
				<?php
				} ?>
			</div>
			<select class="hide" name="access[]" id="access" multiple>
				<?php
				foreach($types as $tkey=>$tval) { 
				if(in_array($tval,$access)){$flg = " selected";} else {$flg="";} ?>
				<option<?php echo $flg; ?> value="<?=$tval;?>"><?=$tkey;?></option>
				<?php
				} ?>
			</select>
		</div>
		
		<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
	</form>
		
</div>
<?php }