<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns sselect content
 */

/* rebuild the GROUP array */
$nvGroup->build_array();

/* field gid */
$gid = $nvBoot->fetch_entry("breadcrumb",3);

/* field id */
$fid = $nvBoot->fetch_entry("breadcrumb",4);

/* item insert */
$html=<<<HTML
<li class='row pad20 c-white %%BC%%'>
	<div class='col all80 pad-r20'>
		<div class='col all50 sml100 fs14 pad-r5 sml-pad-r0 sml-pad-b10'>
			<label class='col all100 fs13 c-white pad-b5 grip'>&#8597;&nbsp;&nbsp;External</label>
			<input class='col all100 fs14 tb' name='external-%%T%%' id='external-%%T%%' type='text' value='%%T%%' placeholder='External'>
		</div>
		<div class='col all50 sml100 fs14 pad-l5 sml-pad-l0'>
			<label class='col all100 fs13 c-white pad-b5'>Internal</label>
			<input class='col all100 fs14 tb' name='internal-%%T%%' id='internal-%%T%%' type='text' value='%%T%%' placeholder='Internal'>
		</div>
		</div>
	<div class='col all20 fs14 tar'>
		<a onclick='$(this).parent().parent().remove();' class='pad-b0 hvr-white'>Delete</a>
	</div>
</li>	
HTML;

?>

<!-- INLINE JS -->
<script>
function addSelectOption(){
	if($('ul').length>0){
		if($('li').last().hasClass('b-lblue')){
			var c='b-vlblue';
		} else {
			var c='b-lblue';
		}
	} else {
		var c='b-lblue';
	}
	var h="<?=$html;?>";
	var t = new Date().getTime();
	h=h.replace(/%%T%%/g,t);
	h=h.replace('%%BC%%',c);
	
	$('ul').append(h);
}
</script>

<?php
/* lookup the group details */
foreach($nvGroup->fetch_array() as $group){if($group["id"]==$gid){break;}}

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

			<form method="post">
				
				<!-- SSELECT EDIT -->
				<section class='col all100'>
					<div class='col sml5 med10 lge15'></div>
					<div class='col box sml90 med80 lge70'>
						<div class='row pad-b20'>
							<div class='col all70 pad-r20'>
								<h1 class='pad0 fs20 c-blue'>Sselect</h1>
							</div>
							<div class='col all30 tar fs14 lh30'>
								<a href='/settings/group/edit/<?=$gid;?>' class='pad-r5 c-blue pad-b0'>Up</a>
								<a onclick="$('#submit').click();" class='pad-l5 c-blue pad-b0'>Save</a>
							</div>
						</div>

						<!-- NAME -->
						<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
							<label class='col all100 fs13 c-blue pad-b5'>Name</label>
							<input class='col all100 fs14 tb' name='name' id='name' type='text' maxlength='255' value='<?=$g['name'];?>' placeholder='Name' autofocus>
						</div>
					</div>
					<div class='col sml5 med10 lge15'></div>
				</section>

				<!-- SSELECT OPTIONS EDIT -->
				<section class='col all100'>
					<div class='col sml5 med10 lge15'></div>
					<div class='col box sml90 med80 lge70'>
						<div class='row pad-b20'>
							<div class='col all70 pad-r20'>
								<h1 class='pad0 fs20 c-blue'>Options</h1>
							</div>
							<div class='col all30 tar fs14 lh30'>
								<a onclick='addSelectOption();' class='pad-r5 c-blue pad-b0'>Add</a>
							</div>
						</div>

						<!-- OPTIONS -->
						<ul class='sortable b-lgrey'>
						<?php 
							$x=0;
							$t = $nvBoot->fetch_entry("timestamp");
							foreach($g["content"] as $k=>$v){
							$r['bc']=($x%2==0)?'b-lblue':'b-vlblue';?>
							<li class='row pad20 c-white <?=$r['bc'];?>'>
								<div class='col all80 pad-r20'>
									<div class='col all50 sml100 fs14 pad-r5 sml-pad-r0 sml-pad-b10'>
										<label class='col all100 fs13 c-white pad-b5 grip'>&#8597;&nbsp;&nbsp;External</label>
										<input class='col all100 fs14 tb' name='external-<?=$x;?>' id='external-<?=$x;?>' type='text' value='<?=htmlentities($k,ENT_QUOTES);?>' placeholder='External'>
									</div>
									<div class='col all50 sml100 fs14 pad-l5 sml-pad-l0'>
										<label class='col all100 fs13 c-white pad-b5'>Internal</label>
										<input class='col all100 fs14 tb' name='internal-<?=$x;?>' id='internal-<?=$x;?>' type='text' value='<?=htmlentities($v,ENT_QUOTES);?>' placeholder='Internal'>
									</div>
									</div>
								<div class='col all20 fs14 tar'>
									<a onclick='$(this).parent().parent().remove();' class='pad-b0 hvr-white'>Delete</a>
								</div>
							</li>
						<?php $x++;} ?>
						</ul>
					</div>

					<!-- SAVE -->
					<div class='col all100 hide'>
						<input type='submit' name='submit' id='submit' value="submit">
					</div>
					<div class='col sml5 med10 lge15'></div>
				</section>
			</form>	
			<?php break;			
		}
	}
}