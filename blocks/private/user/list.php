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

/* grab all currently registered users */
$NVX_DB->DB_CLEAR(array("ALL"));
$users = $NVX_DB->DB_QUERY("SELECT","* FROM `user`");

?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-user.png">
		<h2 class="blank fl">USERS</h2>
		<a class="fr" href="/settings/user/add">ADD</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">UP</a>
	</div>
	
	<?php /* cycle through the users*/ foreach($users as $user){ ?>
	<div class="blank row">
		<label class="blank fl half"><?php echo ucwords($NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$user["user.username"])));?></label>
		<?php if($user['user.type']!='s' || $NVX_USER->GRANTED('s')){ ?>
		<a title="edit" href="<?php echo "/settings/user/edit/".$user["user.id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
		<a title="delete" href="<?php echo "/settings/user/delete/".$user["user.id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
		<?php } ?>
	</div>
	<?php } ?>
	
</div>