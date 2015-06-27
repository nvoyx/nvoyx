<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns mselect content
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
						<h2 class="blank fl">MSELECT</h2>
						<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="<?php echo "/settings/group/edit/{$gid}"; ?>">UP</a>
					</div>

					<div class="blank row">
						<label for="name" class="blank fl">
							Name<br>
							<span class="current-length tt"><?php echo strlen($g["name"]);?></span><span class="tt"> of 50</span>
						</label>
						<input class="blank textbox mini fr" name="name" id="name" type="text" maxlength="50" value="<?php echo $g["name"];?>">
					</div>
				</div>
				
				<div class="blank box">

					<div class="blank header">
						<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-field.png">
						<h2 class="blank fl">OPTIONS</h2>
						<a class="fr" href="#" onclick="addSelectOption(this);return false;">ADD</a>
					</div>

					<ul class="sortable">
						<?php 
							$t = $NVX_BOOT->FETCH_ENTRY("timestamp");
							foreach($g["content"] as $external=>$internal){
						?>
						<li class="blank row">
							<label class="blank fl">External / Internal</label>
							<input class="blank textbox mini fl" name="external-<?php echo $t;?>" id="external-<?php echo $t;?>" type="text" value="<?php echo $external;?>">
							<div class="blank fl ten-space-hori"></div>
							<input class="blank textbox mini fl" name="internal-<?php echo $t;?>" id="external-<?php echo $t;?>" type="text" value="<?php echo $internal;?>">
							<div class="blank cb  ten-space-vert"></div>
							<a title="delete" href="#" onclick="deleteSelectOption(this);return false;"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
							<a class="hand" title="drag and drop"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-grip.png"></a>
						</li>
						<?php $t++; } ?>
					</ul>
				</div>
				
				<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
			</form>		
			<?php break;			
		}
	}
}