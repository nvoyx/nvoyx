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


<!-- MAIN MENU -->
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='col all100'>
			<img height='24' src="/settings/resources/files/images/private/nvoy.svg">
		</div>
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>

<!-- BLOCK EDIT -->
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='row pad-b20'>
			<div class='col all70 pad-r20'>
				<h1 class='pad0 fs20 c-blue'>Login</h1>
			</div>
			<div class='col all30 tar fs14 lh30'>
				<a onclick="$('#submit').click();" class='c-blue pad-b0'>Enter</a>
			</div>
		</div>
		<form method="post">

			<!-- NAME -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Username</label>
				<input class='col all100 fs14 tb' name='username' id='username' type='text' placeholder='Username' autofocus autocomplete='off'>
			</div>
			
			<!-- PASSWORD -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Password</label>
				<input class='col all100 fs14 tb' name='password' id='password' type='password' placeholder='Password' autocomplete='off'>
			</div>

			<!-- CAPTCHA -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 lge-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Captcha</label>
				<input class='col all100 fs14 tb' name='captcha' id='captcha' type='text' placeholder='Captcha' autocomplete='off'>
				<img class="col all100" src="/settings/resources/captcha/captcha.php" id="captcha">
			</div>
			
			<!-- SAVE -->
			<div class='col all100 hide'>
				<input type='submit' name='submit' id='submit' value="submit">
			</div>
		</form>
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>