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
	
	/* sanitise the form fields */
	$post = $NVX_HTML->POSTED_FIELDS($_POST);
		
	/* check that the submitted email recipient is valid */
	if($post["email"]["value"]!=""){
						
		/* send the email */
		$r = $NVX_HTML->MAIL($p,$post);
	}
}

?>

<?php if($r){ ?><h3><strong>Thank you for contacting <?php echo $NVX_VAR->FETCH_ENTRY("company")[0];?>, we will be touch.</strong></h3><?php } else { ?>

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

<?php }