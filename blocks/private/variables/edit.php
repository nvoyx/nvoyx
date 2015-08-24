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
foreach($NVX_VAR->FETCH_ARRAY() as $r){if($r["id"]==$vid){break;}}

/* have we found the variable */
if(isset($r)){ ?>

	<!-- MAIN MENU -->
	<section class='col all100'>
		<div class='col sml5 med10 lge15'></div>
		<div class='col box sml90 med80 lge70'>
			<div class='col all40'>
				<img height='24' src="/settings/resources/files/images/private/nvoy.svg">
			</div>
			<div class='col all60 tar fs14 pad-t5'>
				<a href='/settings/content/list' class='pad-r5 c-blue pad-b0'>Admin</a>
				<a href='/' class='pad-lr5 c-blue pad-b0'>Front</a>
				<a href='/settings/user/logout' class='pad-l5 c-blue pad-b0'>Logout</a>
			</div>
		</div>
		<div class='col sml5 med10 lge15'></div>
	</section>
	
	<!-- VARIABLE EDIT -->
	<section class='col all100'>
		<div class='col sml5 med10 lge15'></div>
		<div class='col box sml90 med80 lge70'>
			<div class='row pad-b20'>
				<div class='col all70 pad-r20'>
					<h1 class='pad0 fs20 c-blue'>Variable</h1>
				</div>
				<div class='col all30 tar fs14 lh30'>
					<a href='/settings/variables/list' class='pad-r5 c-blue pad-b0'>Up</a>
					<a onclick="$('#submit').click();" class='pad-l5 c-blue pad-b0'>Save</a>
				</div>
			</div>
			<form method="post">
				
				<!-- NAME -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Name</label>
					<input class='col all100 fs14 tb' name='name' id='name' type='text' maxlength='128' value='<?=$r['name'];?>' placeholder='Name' autofocus>
				</div>
				
				<!-- VALUE -->
				<div class='col all100 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Value</label>
					<textarea class='col all100 fs14 ta' name='value' id='value' maxlength='16777215' placeholder='Value'><?=$NVX_BOOT->JSON($r["value"],"encode"); ?></textarea>
				</div>
				
				<!-- NOTES -->
				<div class='col all100 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Notes</label>
					<div class='col all100'>
						<textarea class='col all100 fs14 ta ckPublic' name='notes' id='notes' maxlength='1024'><?=$r["notes"];?></textarea>
					</div>
				</div>
				
				<!-- SAVE -->
				<div class='col all100 hide'>
					<input type='submit' name='submit' id='submit' value="submit">
				</div>
			</form>
		</div>
		<div class='col sml5 med10 lge15'></div>
	</section>
<?php }