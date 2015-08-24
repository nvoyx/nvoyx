<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * return / edit block content
 */

/* block id */
$bid = $NVX_BOOT->FETCH_ENTRY('breadcrumb',3);

/* prepare a list of available page types to pass into tid select */
$opts=array();
foreach($NVX_TYPE->FETCH_ARRAY() as $type){
	$opts[$type['id']]=$type['name'];
}

/* lookup the block details */
foreach($NVX_BLOCK->FETCH_ARRAY() as $r){
	if($r['id']==$bid){
		break;
	}
}

/* have we found the block */
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
	
	<!-- BLOCK EDIT -->
	<section class='col all100'>
		<div class='col sml5 med10 lge15'></div>
		<div class='col box sml90 med80 lge70'>
			<div class='row pad-b20'>
				<div class='col all70 pad-r20'>
					<h1 class='pad0 fs20 c-blue'>Block</h1>
				</div>
				<div class='col all30 tar fs14 lh30'>
					<a href='/settings/block/list' class='pad-r5 c-blue pad-b0'>Up</a>
					<a onclick="$('#submit').click();" class='pad-l5 c-blue pad-b0'>Save</a>
				</div>
			</div>
			<form method="post">
				
				<!-- NAME -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Name</label>
					<input class='col all100 fs14 tb' name='name' id='name' type='text' maxlength='255' value='<?=$r['name'];?>' placeholder='Name' autofocus>
				</div>
				
				<!-- ASSOCIATIONS -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Associations</label>
					<select class='col all100 fs14 ms' name='tid[]' id='tid' multiple placeholder="Please Select">
						<?php foreach($opts as $k=>$v){
							if(in_array($k,$r['tid'])){$flg = ' selected';} else {$flg='';} ?>
							<option<?=$flg;?> value='<?=$k;?>'><?=$v;?></option>
						<?php } ?>
					</select>
				</div>
				
				<!-- ACCESS -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 lge-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Access</label>
					<select class='col all100 fs14 ss' name='access' id='access' placeholder="Please Select">
						<option<?php if($r["access"]=="u"){echo " selected";}?> value='u'>User</option>
						<option<?php if($r["access"]=="a"){echo " selected";}?> value='a'>Admin</option>
						<option<?php if($r["access"]=="s"){echo " selected";}?> value='s'>Superuser</option>
					</select>
				</div>
				
				<!-- PARAMETERS -->
				<div class='col all100 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Params</label>
					<textarea class='col all100 fs14 ta' name='params' id='params' maxlength='16777215' placeholder='Parameters'><?=$NVX_BOOT->JSON($r["params"],"encode");?></textarea>
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