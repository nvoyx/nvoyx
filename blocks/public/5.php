<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * @block 5 (contact form)
 * param from (string holding email address that the form should appear from - typically cms@[domain.co.uk] )
 * param to (string holding one or more comma separated email addresses which should receive this form)
 * returns and processes standard contact form
 */

/* current block id */
$bid = pathinfo(__FILE__, PATHINFO_FILENAME);

/* grab the params */
$p = $NVX_BLOCK->FETCH_PARAMS($bid);

/* set a mail-sent flag to false */
$r = false;

/* do we have posted data */
if(array_key_exists("cf-button-submit",$_POST)){
	
	$mail = $NVX_HTML->MAIL('mailer');

	$mail->isHTML();
	$mail->From = 'someone@address.com';
	$mail->FromName = 'Some One';
	$mail->addAddress($_POST['cf-reply-email'], $_POST['cf-text-name']);
	$mail->addReplyTo('no-reply@address.com', 'No Reply');
	$mail->Subject = 'All the fishes in the sea...';
	$mail->Body = "<p>They don't bother me <b>today!</b></p>";
	$mail->AltBody = "They don't bother me today!";

	if(!$mail->send()){		
		$r='<h3><strong>Oops</strong>, something went wrong</h3>';
	} else {echo '<h3><strong>Thank you</strong>, for contacting us. We will be in touch.</h3>';}
}

?>

<?php if($r){ echo $r;} ?>

<div class="blank content">
	<form name="cf" id="cf" method="post">
		<div class='blank'>
			<input type="text" name="cf-text-name" value="">
			<input type="email" name="cf-reply-email" value="">
			<input type="radio" name="cf-radio-keep_updated" value="yes"> yes
			<input type="radio" name="cf-radio-keep_updated" value="no"> no
			<input type="checkbox" name="cf-checkbox-stuff[]" value="dogs"> dogs
			<input type="checkbox" name="cf-checkbox-stuff[]" value="cats"> cats
			<select name="cf-sselect-mind">
				<option value="happy">Happy</option>
				<option value="sad">Sad</option>
			</select>
			<select name="cf-mselect-colours[]" multiple>
				<option value="blue">Blue</option>
				<option value="green">Green</option>
			</select>
			<textarea name="cf-textarea-message"></textarea>
			<input type="submit" name="cf-button-submit" value="submit">
		</div>
	</form>
</div>