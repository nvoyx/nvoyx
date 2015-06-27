<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns imagecache listings
 */

/* grab the imagecaches */
$NVX_DB->DB_CLEAR(array("ALL"));
$NVX_DB->DB_SET_ORDER(array("`imagecache`.`name`"=>"ASC"));
$imagecaches = $NVX_DB->DB_QUERY("SELECT","* FROM `imagecache`");
?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-image.png">
		<h2 class="blank fl">IMAGE CACHE</h2>
		<a class="fr" href="/settings/imagecache/add">ADD</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">UP</a>
	</div>
	
	<?php /* cycle through the imagecache */ if($imagecaches){ foreach($imagecaches as $imagecache){ ?>
	<div class="blank row">
		<label class="blank fl"><?php echo ucwords($imagecache["imagecache.name"]);?></label>
		<a title="edit" href="<?php echo "/settings/imagecache/edit/" . $imagecache["imagecache.id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
		<a title="delete" href="<?php echo "/settings/imagecache/delete/" . $imagecache["imagecache.id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
	</div>
	<?php }} ?>
	
</div>