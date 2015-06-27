<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns variable listings
 */
?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-variables.png">
		<h2 class="blank fl">VARIABLES</h2>
		<a class="fr" href="/settings/variables/add">ADD</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">UP</a>
	</div>
	
	<?php /* cycle through the variables*/ foreach($NVX_VAR->FETCH_ARRAY() as $variable){ ?>
	<div class="blank row">
		<label class="blank fl half"><?php echo ucwords($variable["name"]);?></label>
		<a title="edit" href="<?php echo "/settings/variables/edit/".$variable["id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
		<a title="delete" href="<?php echo "/settings/variables/delete/".$variable["id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
	</div>
	<?php } ?>
	
</div>