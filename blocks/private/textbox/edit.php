<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns textbox content
 */

/* rebuild the GROUP array */
$nvGroup->build_array();

/* field gid */
$gid = $nvBoot->fetch_entry("breadcrumb",3);

/* field id */
$fid = $nvBoot->fetch_entry("breadcrumb",4);

/* lookup the group details */
foreach($nvGroup->fetch_array() as $r){if($r["id"]==$gid){break;}}

/* have we found the group */
if(isset($r)){
	
	/* loop through the groups */
	foreach($r["outline"] as $g){
		
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


			<!-- TEXTBOX EDIT -->
			<section class='col all100'>
				<div class='col sml5 med10 lge15'></div>
				<div class='col box sml90 med80 lge70'>
					<div class='row pad-b20'>
						<div class='col all70 pad-r20'>
							<h1 class='pad0 fs20 c-blue'>Textbox</h1>
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

						<!-- CONTENT -->
						<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
							<label class='col all100 fs13 c-blue pad-b5'>Content</label>
							<select class='col all100 fs14 ss' name='content' id='content' placeholder="Please Select">
								<option<?php if($g["content"]=="plain"){echo " selected";}?> value='plain'>Plain</option>
								<option<?php if($g["content"]=="url"){echo " selected";}?> value='url'>Url</option>
							</select>
						</div>

						<!-- MAXLENGTH -->
						<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 lge-pad-r0 pad-b20'>
							<label class='col all100 fs13 c-blue pad-b5'>Maximum Length</label>
							<input class='col all100 fs14 tb' name='maxlength' id='maxlength' type='number' maxlength='255' value='<?=$g['maxlength'];?>' placeholder='Length'>
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