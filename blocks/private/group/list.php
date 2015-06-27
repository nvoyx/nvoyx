<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns group listings
 */

/* rebuild the GROUP array */
$NVX_GROUP->BUILD_ARRAY(false);
?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	
	<form method="POST">
		<div class="blank header">
			<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-group.png">
			<h2 class="blank fl">GROUPS</h2>
			<a class="fr" onclick="$('#submit').click();">SAVE</a>
			<span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
			<a class="fr" href="/settings/group/add">ADD</a>
			<span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
			<a class="fr" href="/settings/content/list">UP</a>
		</div>
	
		<ul class="sortable">
			<?php /* cycle through the existing groups */ foreach($NVX_GROUP->FETCH_ARRAY() as $group){ ?>
			<li class="blank row">
				<label class="blank fl"><?php echo ucwords($group["name"]);?></label>
				<a title="edit" href="<?php echo "/settings/group/edit/".$group["id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
				<a title="delete" href="<?php echo "/settings/group/delete/".$group["id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
				<a class="hand" title="drag and drop"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-grip.png"></a>
				<input type="hidden" name="group-<?php echo $group["id"];?>" value="<?php echo $group["position"];?>">
			</li>
			<?php } ?>
		</ul>
		
		<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
	</form>
	
</div>