<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns imagelist fields to edit
 */

/* rebuild the GROUP array */
$NVX_GROUP->BUILD_ARRAY();

/* field gid */
$gid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);

/* field id */
$fid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",4);

/* lookup the group details */
foreach($NVX_GROUP->FETCH_ARRAY() as $group){if($group["id"]==$gid){break;}}

/* have we found the group */
if(isset($group)){
	
	/* loop through the groups */
	foreach($group["outline"] as $g){
		
		/* have we found the correct field */
		if($g["fid"] == $fid){ ?>

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
			
			<!-- IMAGELIST EDIT -->
			<section class='col all100'>
				<div class='col sml5 med10 lge15'></div>
				<div class='col box sml90 med80 lge70'>
					<div class='row pad-b20'>
						<div class='col all70 pad-r20'>
							<h1 class='pad0 fs20 c-blue'>Imagelist</h1>
						</div>
						<div class='col all30 tar fs14 lh30'>
							<a href='/settings/group/edit/<?=$gid;?>' class='pad-r5 c-blue pad-b0'>Up</a>
							<a onclick="$('#submit').click();" class='pad-l5 c-blue pad-b0'>Save</a>
						</div>
					</div>
					<form method="post">

						<!-- NAME -->
						<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
							<label class='col all100 fs13 c-blue pad-b5'>Name</label>
							<input class='col all100 fs14 tb' name='name' id='name' type='text' maxlength='255' value='<?=$g['name'];?>' placeholder='Name' autofocus>
						</div>
						
						<!-- TOTAL -->
						<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
							<label class='col all100 fs13 c-blue pad-b5'>Maximum Images</label>
							<input class='col all100 fs14 tb' name='total' id='total' type='number' value='<?=$g['total'];?>' placeholder='Maximum Images'>
						</div>
						
						<!-- SIZE -->
						<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 lge-pad-r0 pad-b20'>
							<label class='col all100 fs13 c-blue pad-b5'>Maximum Size (Kb)</label>
							<input class='col all100 fs14 tb' name='size' id='size' type='number' step='1024' value='<?=$g['size'];?>' placeholder='Maximum Filesize'>
						</div>
						
						<!-- LINK -->
						<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
							<label class='col all100 fs13 c-blue pad-b5'>Link</label>
							<select class='col all100 fs14 ss' name='link' id='link' placeholder="Please Select">
								<option<?php if($g["link"]==1){echo " selected";}?> value='1'>Enabled</option>
								<option<?php if($g["link"]==0){echo " selected";}?> value='0'>Disabled</option>
							</select>
						</div>
						
						<!-- EXTRA TYPE -->
						<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
							<label class='col all100 fs13 c-blue pad-b5'>Extra Type</label>
							<select class='col all100 fs14 ss' name='extra-type' id='extra-type' placeholder="Please Select">
								<option<?php if($g["extra-type"]=='none'){echo " selected";}?> value='none'>None</option>
								<option<?php if($g["extra-type"]=='plain'){echo " selected";}?> value='plain'>Plain</option>
								<option<?php if($g["extra-type"]=='html'){echo " selected";}?> value='html'>Html</option>
							</select>
						</div>
						
						<!-- EXTRA EDITOR -->
						<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 lge-pad-r0 pad-b20'>
							<label class='col all100 fs13 c-blue pad-b5'>Extra Editor</label>
							<select class='col all100 fs14 ss' name='extra-editor' id='extra-editor' placeholder="Please Select">
								<?php foreach($NVX_VAR->FETCH_ENTRY("editors") as $e){
									if($g["extra-editor"]==$e){$flg = " selected";} else {$flg="";} ?>
									<option<?=$flg;?> value='<?=$e;?>'><?=$e;?></option>
								<?php } ?>
							</select>
						</div>
						
						<!-- EXTRA NAME -->
						<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
							<label class='col all100 fs13 c-blue pad-b5'>Extra Name</label>
							<input class='col all100 fs14 tb' name='extra-name' id='extra-name' type='text' maxlength='255' value='<?=$g['extra-name'];?>' placeholder='Extra Name'>
						</div>

						<!-- SAVE -->
						<div class='col all100 hide'>
							<input type='submit' name='submit' id='submit' value="submit">
						</div>
					</form>
				</div>
				<div class='col sml5 med10 lge15'></div>
			</section>
			<?php break;			
		}
	}
}