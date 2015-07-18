<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns debug listings
 */
?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-debug.png">
		<h2 class="blank fl">DEBUG</h2>
		<a class="fr" href="/settings/content/list">UP</a>
	</div>
	
	<div class="blank row">
		<label class="blank fl half">XCache</label>
		<a title="edit" target="_blank" href="<?php echo "/settings/debug/xcache";?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
	</div>

	<div class="blank row">
		<label class="blank fl half">Folders</label>
		<a title="edit" target="_blank" href="<?php echo "/settings/debug/folders";?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
	</div>
	
	<div class="blank row">
		<label class="blank fl half">Database Tables</label>
		<a title="edit" target="_blank" href="<?php echo "/settings/debug/database";?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
	</div>
	
	<div class="blank row">
		<label class="blank fl half">PHP Error Log</label>
		<a title="edit" target="_blank" href="<?php echo "/settings/debug/log";?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
	</div>
	
	<div class="blank row">
		<label class="blank fl half">PHP Info</label>
		<a title="edit" target="_blank" href="<?php echo "/settings/debug/info";?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
	</div>
	
	<div class="blank row">
		<label class="blank fl half">Unit Testing</label>
		<a title="edit" target="_blank" href="<?php echo "/settings/debug/unit";?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
	</div>
	
</div>