<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/* grab all departments */
$NVX_DB->DB_CLEAR(array("ALL"));
$departments = $NVX_DB->DB_QUERY("SELECT","* FROM `dept`");

?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-user.png">
		<h2 class="blank fl">DEPARTMENTS</h2>
		<a class="fr" href="/settings/dept/add">ADD</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">UP</a>
	</div>
	
	<?php /* cycle through the departments */ $x=0;foreach($departments as $department){ ?>
	<div class="blank row">
		<label class="blank fl half"><?php echo ucwords($department["dept.name"]);?></label>
		<?php if($x>0){?>
		<a title="edit" href="<?php echo "/settings/dept/edit/".$department["dept.id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
		<a title="delete" href="<?php echo "/settings/dept/delete/".$department["dept.id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
		<?php } ?>
	</div>
	<?php $x++;} ?>
	
</div>