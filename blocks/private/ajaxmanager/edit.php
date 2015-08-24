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
$id = $NVX_BOOT->FETCH_ENTRY('breadcrumb',3);

/* rebuild the path array */
$NVX_PATH->BUILD_ARRAY();

/* lookup the path details */
foreach($NVX_PATH->FETCH_ARRAY() as $r){
	if($r['id']==$id){
		$r['url']=str_replace('/settings/ajax/','',$r['url']);
		break;
	}
}

/* have we found the path */
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
	
	<!-- AJAX MANAGER EDIT -->
	<section class='col all100'>
		<div class='col sml5 med10 lge15'></div>
		<div class='col box sml90 med80 lge70'>
			<div class='row pad-b20'>
				<div class='col all70 pad-r20'>
					<h1 class='pad0 fs20 c-blue'>Ajax</h1>
				</div>
				<div class='col all30 tar fs14 lh30'>
					<a href='/settings/ajaxmanager/list' class='pad-r5 c-blue pad-b0'>Up</a>
					<a onclick="$('#submit').click();" class='pad-l5 c-blue pad-b0'>Save</a>
				</div>
			</div>
			<form method="post">
				
				<!-- URL -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Url</label>
					<input class='col all100 fs14 tb' name='url' id='url' type='text' maxlength='255' value='<?=$r['url'];?>' placeholder='Name' autofocus>
				</div>
				
				<!-- ACCESS -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Access</label>
					<select class='col all100 fs14 ss' name='access' id='access' placeholder="Please Select">
						<option<?php if($r["access"]=="u"){echo " selected";}?> value='u'>User</option>
						<option<?php if($r["access"]=="a"){echo " selected";}?> value='a'>Admin</option>
						<option<?php if($r["access"]=="s"){echo " selected";}?> value='s'>Superuser</option>
					</select>
				</div>
				
				<!-- SAVE -->
				<div class='col all100 hide'>
					<input name='orig_url' id='name' type='text' maxlength='255' value='<?=$r['url'];?>'>
					<input type='submit' name='submit' id='submit' value='submit'>
				</div>
			</form>
		</div>
		<div class='col sml5 med10 lge15'></div>
	</section>
<?php }