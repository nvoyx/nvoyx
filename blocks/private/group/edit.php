<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns group content
 */

/* rebuild the GROUP array */
$NVX_GROUP->BUILD_ARRAY(false);

/* group id */
$gid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

$fs=array(
	'datebox',
	'filelist',
	'heirarchy',
	'imagelist',
	'mselect',
	'sselect',
	'tagbox',
	'textarea',
	'textbox'
);

/* prepare a list of available types to pass into assoc select */
$opts=array();
foreach($NVX_TYPE->FETCH_ARRAY() as $type){
	$opts[$type["id"]]=$type["name"];
}


/* lookup the group details */
foreach($NVX_GROUP->FETCH_ARRAY() as $r){if($r["id"]==$gid){break;}}

/* have we found the group */
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
	
	<form method="post">
		
		<!-- GROUP EDIT -->
		<section class='col all100'>
			<div class='col sml5 med10 lge15'></div>
			<div class='col box sml90 med80 lge70'>
				<div class='row pad-b20'>
					<div class='col all70 pad-r20'>
						<h1 class='pad0 fs20 c-blue'>Group</h1>
					</div>
					<div class='col all30 tar fs14 lh30'>
						<a href='/settings/group/list' class='pad-r5 c-blue pad-b0'>Up</a>
						<a onclick="$('#submit').click();" class='pad-l5 c-blue pad-b0'>Save</a>
					</div>
				</div>

				<!-- NAME -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Name</label>
					<input class='col all100 fs14 tb' name='name' id='name' type='text' maxlength='255' value='<?=$r['name'];?>' placeholder='Name' autofocus>
				</div>

				<!-- ASSOCIATIONS -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Associations</label>
					<select class='col all100 fs14 ms' name='assoc[]' id='assoc' multiple placeholder="Please Select">
						<?php foreach($opts as $k=>$v){
							if(in_array($k,$r['assoc'])){$flg = ' selected';} else {$flg='';} ?>
							<option<?=$flg;?> value='<?=$k;?>'><?=$v;?></option>
						<?php } ?>
					</select>
				</div>

				<!-- ACCESS -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 lge-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Access</label>
					<select class='col all100 fs14 ss' name='access' id='access' placeholder="Please Select">
						<option<?php if($r["access"]=="a"){echo " selected";}?> value='a'>Admin</option>
						<option<?php if($r["access"]=="s"){echo " selected";}?> value='s'>Superuser</option>
					</select>
				</div>

				<!-- VARIANTS -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Variants</label>
					<input class='col all100 fs14 tb' name='variants' id='variants' type='number' value='<?=$r['variants'];?>' placeholder='Variants'>
				</div>

				<!-- SAVE -->
				<div class='col all100 hide'>
					<input type='submit' name='submit' id='submit' value="submit">
				</div>
			</div>
			<div class='col sml5 med10 lge15'></div>
		</section>
		
		<!-- ADD FIELDS -->
		<section class='col all100'>
			<div class='col sml5 med10 lge15'></div>
			<div class='col box sml90 med80 lge70'>
				<div class='row pad-b20'>
					<div class='col all100'>
						<h1 class='pad0 fs20 c-blue'>Add Fields</h1>
					</div>
				</div>

				<!-- NEW FIELD -->
				<?php $x=0;foreach($fs as $f){ ?>
				<div class='row pad10 c-white <?=($x%2==0)?'b-lblue':'b-vlblue';?>'>
					<div class='col all70 fs14 pad-r20'>
						<p class='pad0'><?=$f;?></p>
					</div>
					<div class='col all30 fs14 tar'>
						<a href='/settings/<?=$f;?>/add/<?=$r['id'];?>/<?=$r['nfid'];?>' class='pad-b0 hvr-white'>Add</a>
					</div>
				</div>
				<?php $x++;} ?>

				<!-- SAVE -->
				<div class='col all100 hide'>
					<input type='submit' name='submit' id='submit' value="submit">
				</div>
			</div>
			<div class='col sml5 med10 lge15'></div>
		</section>
		
		<!-- FIELDS -->
		<section class='col all100'>
			<div class='col sml5 med10 lge15'></div>
			<div class='col box sml90 med80 lge70'>
				<div class='row pad-b20'>
					<div class='col all100'>
						<h1 class='pad0 fs20 c-blue'>Fields</h1>
					</div>
				</div>
				
				<!-- FIELD -->
				<ul class='sortable b-lgrey'>
				<?php $x=0;foreach($r['outline'] as $field){
					$r['bc']=($x%2==0)?'b-lblue':'b-vlblue';?>
					<li class='row pad10 c-white <?=$r['bc'];?>'>
						<div class='col all70 fs14 pad-r20'>
							<p class='pad0 grip bw'>&#8597;&nbsp;&nbsp;<?=$field['name'];?>&nbsp;&nbsp;( <?=$field['fid'];?> )</p>
						</div>
						<div class='col all30 fs14 tar'>
							<a href='/settings/<?=$field['type'];?>/edit/<?=$r['id'];?>/<?=$field['fid'];?>' class='pad-r5 pad-b0 hvr-white'>Edit</a>
							<a onclick='$(this).parent().parent().remove();' class='pad-l5 pad-b0 hvr-white'>Delete</a>
							<input type="hidden" name="fields[]" value="<?=$field['fid'];?>">
						</div>
					</li>
				<?php $x++;} ?>
				</ul>

				<!-- SAVE -->
				<div class='col all100 hide'>
					<input type='submit' name='submit' id='submit' value="submit">
				</div>
			</div>
			<div class='col sml5 med10 lge15'></div>
		</section>
	</form>
<?php }