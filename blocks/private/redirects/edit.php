<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * returns redirects content
 */

/* redirects id */
$rid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);


/* grab all current redirects */
$NVX_DB->CLEAR(array("ALL"));
$redirects = $NVX_DB->QUERY("SELECT","* FROM `redirects`");

/* lookup the redirects details */
foreach($redirects as $redirect){if($redirect["redirects.id"]==$rid){break;}}

/* have we found the redirect */
if(isset($redirect)){ ?>

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
	
	<!-- REDIRECTS EDIT -->
	<section class='col all100'>
		<div class='col sml5 med10 lge15'></div>
		<div class='col box sml90 med80 lge70'>
			<div class='row pad-b20'>
				<div class='col all70 pad-r20'>
					<h1 class='pad0 fs20 c-blue'>301 Redirect</h1>
				</div>
				<div class='col all30 tar fs14 lh30'>
					<a href='/settings/redirects/list' class='pad-r5 c-blue pad-b0'>Up</a>
					<a onclick="$('#submit').click();" class='pad-l5 c-blue pad-b0'>Save</a>
				</div>
			</div>
			<form method="post">
				
				<!-- OLD URL -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Old Url</label>
					<input class='col all100 fs14 tb' name='old' id='old' type='text' maxlength='2048' value='<?=$redirect["redirects.old"];?>' placeholder='Old Url' autofocus>
				</div>
				
				<!-- NEW URL -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>New Url</label>
					<input class='col all100 fs14 tb' name='new' id='new' type='text' maxlength='2048' value='<?=$redirect["redirects.new"];?>' placeholder='New Url'>
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