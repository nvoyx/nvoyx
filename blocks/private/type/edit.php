<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns type content
 */

/* type id */
$tid = $nvBoot->fetch_entry("breadcrumb",3);

/* lookup the type details */
foreach($nvType->fetch_array() as $r){if($r["id"]==$tid){break;}}

$opts=array();
foreach($nvType->fetch_array() as $t){
	if($t["id"]!=$tid){
		$opts[$t['id']] = $t["name"];
	}
}

/* have we found the type */
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
					<h1 class='pad0 fs20 c-blue'>Type</h1>
				</div>
				<div class='col all30 tar fs14 lh30'>
					<a href='/settings/type/list' class='pad-r5 c-blue pad-b0'>Up</a>
					<a onclick="$('#submit').click();" class='pad-l5 c-blue pad-b0'>Save</a>
				</div>
			</div>
			<form method="post">
				
				<!-- NAME -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Name</label>
					<input class='col all100 fs14 tb' name='name' id='name' type='text' maxlength='255' value='<?=$r['name'];?>' placeholder='Name' autofocus>
				</div>
				
				<!-- PREFIX -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Prefix</label>
					<input class='col all100 fs14 tb' name='prefix' id='prefix' type='text' maxlength='255' value='<?=$r['prefix'];?>' placeholder='Prefix'>
				</div>
				
				<!-- PARENT -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 lge-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Parent</label>
					<select class='col all100 fs14 ss' name='parent' id='parent' placeholder="Please Select">
						<?php foreach($opts as $k=>$v){
							if($r['parent']==$k){$flg = ' selected';} else {$flg='';} ?>
							<option<?=$flg;?> value='<?=$k;?>'><?=$v;?></option>
						<?php } ?>
					</select>
				</div>
				
				<!-- VIEW -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>View</label>
					<select class='col all100 fs14 ss' name='view' id='view' placeholder="Please Select">
						<option<?php if($r["view"]=="u"){echo " selected";}?> value='u'>User</option>
						<option<?php if($r["view"]=="a"){echo " selected";}?> value='a'>Admin</option>
						<option<?php if($r["view"]=="s"){echo " selected";}?> value='s'>Superuser</option>
					</select>
				</div>
				
				<!-- CREATE DELETE -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Create / Delete</label>
					<select class='col all100 fs14 ss' name='createdelete' id='createdelete' placeholder="Please Select">
						<option<?php if($r["createdelete"]=="u"){echo " selected";}?> value='u'>User</option>
						<option<?php if($r["createdelete"]=="a"){echo " selected";}?> value='a'>Admin</option>
						<option<?php if($r["createdelete"]=="s"){echo " selected";}?> value='s'>Superuser</option>
					</select>
				</div>
				
				<!-- RSS -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 lge-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Rss</label>
					<select class='col all100 fs14 ss' name='rss' id='rss' placeholder="Please Select">
						<option<?php if($r["rss"]==0){echo " selected";}?> value='0'>Disabled</option>
						<option<?php if($r["rss"]==1){echo " selected";}?> value='1'>Enabled</option>
					</select>
				</div>
				
				<!-- BODY -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Body</label>
					<select class='col all100 fs14 ss' name='body' id='body' placeholder="Please Select">
						<option<?php if($r["body"]==0){echo " selected";}?> value='0'>Disabled</option>
						<option<?php if($r["body"]==1){echo " selected";}?> value='1'>Enabled</option>
					</select>
				</div>
				
				<!-- TEMPLATE -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Template</label>
					<input class='col all100 fs14 tb' name='template' id='template' type='number' value='<?=$r['template'];?>' placeholder='Template'>
				</div>
				
				<!-- TAGS -->
				<div class='col all100 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Tags</label>
					<textarea class='col all100 fs14 ta' name='tags' id='tags' maxlength='16777215' placeholder='Tags'><?=implode("[format:newline]",$r["tags"]);?></textarea>
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