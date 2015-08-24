<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns imagecache content
 */

/* imagecache iid */
$iid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* grab the imagecache */
$NVX_DB->CLEAR(array("ALL"));
$NVX_DB->SET_FILTER("`imagecache`.`id`={$iid}");
$r = $NVX_DB->QUERY("SELECT","* FROM `imagecache`");
?>

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

<!-- BLOCK EDIT -->
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='row pad-b20'>
			<div class='col all70 pad-r20'>
				<h1 class='pad0 fs20 c-blue'>Image Cache</h1>
			</div>
			<div class='col all30 tar fs14 lh30'>
				<a href='/settings/imagecache/list' class='pad-r5 c-blue pad-b0'>Up</a>
				<a onclick="$('#submit').click();" class='pad-l5 c-blue pad-b0'>Save</a>
			</div>
		</div>
		<form method="post">

			<!-- NAME -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Name</label>
				<input class='col all100 fs14 tb' name='name' id='name' type='text' maxlength='255' value='<?=$r[0]['imagecache.name'];?>' placeholder='Name' autofocus>
			</div>
			
			<!-- X -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Horizontal (pixels)</label>
				<input class='col all100 fs14 tb' name='x' id='x' type='number' value='<?=$r[0]['imagecache.x'];?>' placeholder='Horizontal'>
			</div>
			
			<!-- Y -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 lge-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Vertical (pixels)</label>
				<input class='col all100 fs14 tb' name='y' id='y' type='number' value='<?=$r[0]['imagecache.y'];?>' placeholder='Vertical'>
			</div>

			<!-- MIME -->
			<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Mime</label>
				<select class='col all100 fs14 ss' name='mime' id='mime' placeholder="Please Select">
					<option<?php if($r[0]["imagecache.mime"]=="jpg"){echo " selected";}?> value='jpg'>Jpg</option>
					<option<?php if($r[0]["imagecache.mime"]=="png"){echo " selected";}?> value='png'>Png</option>
				</select>
			</div>

			<!-- EFFECTS -->
			<div class='col all100 pad-b20'>
				<label class='col all100 fs13 c-blue pad-b5'>Effects</label>
				<textarea class='col all100 fs14 ta' name='effects' id='effects' placeholder='Effects'><?=$r[0]['imagecache.effects'];?></textarea>
			</div>

			<!-- SAVE -->
			<div class='col all100 hide'>
				<input type='submit' name='submit' id='submit' value="submit">
			</div>
		</form>
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>