<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns list of ajax files with the ability to add/delete/edit user defined files
 */

$AJAXS = array();

$files = array_diff(scandir($NVX_BOOT->FETCH_ENTRY("blocks")."/private/ajax"), array('.','..')); 
foreach ($files as $file) {
	
	if(is_link($NVX_BOOT->FETCH_ENTRY("blocks")."/private/ajax/".$file)){
		$AJAXS[] = array(
			"id"=>$NVX_PATH->FETCH_ENTRY("/settings/ajax/".str_replace(".php","",$file))["id"],
			"name"=>$file,
			"path"=>$NVX_BOOT->FETCH_ENTRY("blocks")."/private/ajax/".$file,
			"alias"=>true
		);
	} else {
		$AJAXS[] = array(
			"id"=>$NVX_PATH->FETCH_ENTRY("/settings/ajax/".str_replace(".php","",$file))["id"],
			"name"=>$file,
			"path"=>$NVX_BOOT->FETCH_ENTRY("blocks")."/private/ajax/".$file,
			"alias"=>false
		);
	}
}

?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-ajaxmanager.png">
		<h2 class="blank fl">AJAX</h2>
		<a class="fr" href="/settings/ajaxmanager/add">ADD</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">UP</a>
	</div>
	
	<?php /* cycle through the ajax scripts*/ foreach($AJAXS as $ajax){?>
	<div class="blank row">
		<label class="blank fl half"><?php echo $ajax["name"];?></label>
		<?php if($ajax["alias"]===false){ ?>
		<a title="edit" href="<?php echo "/settings/ajaxmanager/edit/".$ajax["id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
		<a title="delete" href="<?php echo "/settings/ajaxmanager/delete/".$ajax["id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
		<?php } ?>
	</div>
	<?php } ?>
	
</div>