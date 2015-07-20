<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns ajaxmanager content
 */

/* ajax file id */
$id = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* create a list of all the files in the ajax folder */
$files = array_diff(scandir($NVX_BOOT->FETCH_ENTRY("blocks")."/private/ajax"), array('.','..'));

/* rebuild the path array */
$NVX_PATH->BUILD_ARRAY();

/* lookup the path details */
foreach($NVX_PATH->FETCH_ARRAY() as $path){if($path["id"]==$id){break;}}

/* have we found the path */
if(isset($path)){ ?>


	<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
	<div class="blank box" id="header">
		<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
		<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
	</div>

	<form method="POST">

		<div class="blank box">

			<div class="blank header">
				<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-ajaxmanager.png">
				<h2 class="blank fl">AJAX</h2>
				<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/ajaxmanager/list">UP</a>
			</div>

			<div class="blank row">
				<label for="name" class="blank fl">
					Name<br>
					<span class="current-length tt"><?php echo strlen(str_replace("/settings/ajax/","",$path["url"]));?></span><span class="tt"> of 255</span>
				</label>
				<input class="blank textbox mini fr" name="url" id="url" type="text" maxlength="255" value="<?php echo str_replace("/settings/ajax/","",$path["url"]);?>">
			</div>
			
			<div class="blank row">
				<label class="blank fl">Access</label>
				<div class="blank select fr half">
					<?php
					$options = array(
						array("INTERNAL"=>"s","EXTERNAL"=>"Superuser"),
						array("INTERNAL"=>"a","EXTERNAL"=>"Admin"),
						array("INTERNAL"=>"u","EXTERNAL"=>"User")
					);
					
					foreach($options as $o){
						if($path["access"]==$o["INTERNAL"]){$flg = " selected";} else {$flg="";} ?>
						<a class='blank mini<?php echo $flg; ?>' onclick="select(this,'access');return false;"><?php echo ucwords($o['EXTERNAL']);?></a>
					<?php } ?>
				</div>
				<select class="hide" name="access" id="access">
					<?php foreach($options as $o){
						if($path["access"]==$o["INTERNAL"]){$flg = " selected";} else {$flg="";} ?>
						<option<?php echo $flg; ?> value="<?php echo $o["INTERNAL"];?>"></option>
					<?php } ?>
				</select>
			</div>
			
			<div>
				<input name="orig_url" id="name" type="hidden" maxlength="255" value="<?=str_replace("/settings/ajax/","",$path["url"]);?>">
				<input type="submit" class="hide" name="submit" id="submit" value="submit">
			</div>
		</div>
	</form>
	
<?php }