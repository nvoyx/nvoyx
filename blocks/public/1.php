<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * @block 1 (admin bar)
 * param none
 * returns admin navigation
 */

?>

<div id="admin" class="hide">
	<div>
		<img class='fl'src='/settings/resources/files/images/public/header-client.png' height='24' >
		<a href="/settings/content/list">Admin</a> | 
		<a href="/settings/content/edit/<?php echo $PAGE["id"]; ?>">Edit</a> | 
		<a href="/settings/user/logout">Logout</a>
	</div>
</div>