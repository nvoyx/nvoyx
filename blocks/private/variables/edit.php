<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns variable content
 */

/* rebuild the VARIABLE array */
$NVX_VAR->BUILD_ARRAY(false);

/* variable id */
$vid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* lookup the variables details */
foreach($NVX_VAR->FETCH_ARRAY() as $variable){if($variable["id"]==$vid){break;}}

/* have we found the variable */
if(isset($variable)){ ?>


<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-variables.png">
		<h2 class="blank fl">VARIABLES</h2>
		<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/variables/list">UP</a>
	</div>
	
	<form method="POST">
		
		<div class="blank row">
			<label for="name" class="blank fl">
				Name<br>
				<span class="current-length tt"><?php echo strlen($variable["name"]);?></span><span class="tt"> of 128</span>
			</label>
			<input class="blank textbox mini fr" name="name" id="name" type="text" maxlength="128" value="<?php echo $variable["name"];?>">
		</div>
		
		<div class="blank row">
			<label for="value" class="blank fl">
				Value<br>
				<span class="current-length tt"><?php echo strlen($NVX_BOOT->JSON($variable["value"],"encode")); ?></span><span class="tt"> of 1677215</span>
			</label>
			<textarea class="blank textarea plain big fl" name="value" id="value" maxlength="1677215"><?php echo $NVX_BOOT->JSON($variable["value"],"encode"); ?></textarea>
		</div>
		
		<div class="blank row">
			<label for="notes" class="blank fl">
				Notes
			</label>
			<div class="blank fl huge"><textarea class="blank textarea ckPublic" name="notes" id="notes" maxlength="1024"><?php echo $variable["notes"]; ?></textarea></div>
		</div>
		
		<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
	</form>
		
</div>
<?php }