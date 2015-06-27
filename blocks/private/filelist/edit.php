<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns filelist content
 */

/* rebuild the GROUP array */
$NVX_GROUP->BUILD_ARRAY();

/* field gid */
$gid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* field id */
$fid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",4);

/* lookup the group details */
foreach($NVX_GROUP->FETCH_ARRAY() as $group){if($group["id"]==$gid){break;}}

/* have we found the group */
if(isset($group)){
	
	/* loop through the groups */
	foreach($group["outline"] as $g){
		
		/* have we found the correct field */
		if($g["fid"] == $fid){ ?>

			<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
			<div class="blank box" id="header">
				<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
				<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
			</div>

			<form method="POST">

				<div class="blank box">

					<div class="blank header">
						<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-field.png">
						<h2 class="blank fl">FILELIST</h2>
						<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="<?php echo "/settings/group/edit/{$gid}"; ?>">UP</a>
					</div>

					<div class="blank row">
						<label for="name" class="blank fl">
							Name<br>
							<span class="current-length tt"><?php echo strlen($g["name"]);?></span><span class="tt"> of 50</span>
						</label>
						<input class="blank textbox mini fr" name="name" id="name" type="text" maxlength="50" value="<?php echo $g["name"];?>">
					</div>
					
					<div class="blank row">
						<label class="blank fl">Maximum Files</label>
						<input class="blank textbox mini fr" name="total" id="total" type="number" value="<?php echo $g["total"];?>">
					</div>
					
					<div class="blank row">
						<label class="blank fl">Maximum Size (Kb)</label>
						<input class="blank textbox mini fr" name="size" id="size" type="number" step="1024"  value="<?php echo $g["size"];?>">
					</div>
					
					<div class="blank row">
						<label for="filetypes" class="blank fl">
							Filetypes<br>
							<span class="current-length tt"><?php echo strlen($NVX_BOOT->JSON($g["filetypes"],"encode"));?></span><span class="tt"> of 512</span>
						</label>
						<textarea class="blank textarea plain big fl" name="filetypes" id="filetypes" maxlength="512" ><?php echo $NVX_BOOT->JSON($g["filetypes"],"encode"); ?></textarea>
					</div>
					
					<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
				</div>
			</form>
			<?php break;			
		}
	}
}