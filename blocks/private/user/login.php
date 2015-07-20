<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns site login form
 */

?>


<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
</div>

<div class="blank box">
	
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-login.png">
		<h2 class="blank fl">LOGIN</h2>
		<a id="login-button" class="fr" onclick="$('#login-submit').click();">ENTER</a>
	</div>

	<form id="login" method="post">
	
		<div class="blank row">
			<label class="blank fl " for="username">Username</label>
			<input class="blank textbox big fl" name="username" type="text" value="" tabindex="1" autocomplete="off">
		</div>
		
		<div class="blank row">
			<label class="blank fl " for="password">Password</label>
			<input class="blank textbox big fl" name="password" type="password" value="" tabindex="2" autocomplete="off">
		</div>

		<div class="blank row">
			<label class="blank fl" for="captcha">Captcha</label>
			<img class="blank fl half" src="/settings/resources/captcha/captcha.php" id="captcha">
			<div class="blank half fr">
				<input class="blank textbox mini" name="captcha" id="captcha" type="text" value="" tabindex="3" autocomplete="off" onkeydown="if(event.keyCode==13){$('#login-button').click();return false;}">
				<a  class="blank half" href="#" onclick="document.getElementById('captcha').src='/settings/resources/captcha/captcha.php?'+Math.random();return false;" id="change-image">REFRESH</a>
				<p style="margin:20px 0 0 0;padding:0 0;color:#fff;">NVOYX version: <?php echo $NVX_VAR->FETCH_ENTRY("version")[0];  ?></p>
				<input id="login-submit" style="display:none;" type="submit" value="submit">
			</div>
		</div>
	</form>
	
</div>