<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns department content
 */

/* user id */
$did = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);


/* grab all departments */
$NVX_DB->CLEAR(array("ALL"));
$departments = $NVX_DB->QUERY("SELECT","* FROM `dept`");

/* lookup the department details */
foreach($departments as $department){if($department["dept.id"]==$did){break;}}

/* have we found the dept */
if(isset($department)){ 

	/* grab a list of all types */
	$types=array();
	foreach($NVX_TYPE->FETCH_ARRAY() as $rs){
		$types[$rs['name']]=$rs['id'];
	}

	/* explode the access tids for this dept. */
	$access=$NVX_BOOT->JSON($department['dept.access'],'decode');

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
					<h1 class='pad0 fs20 c-blue'>Department</h1>
				</div>
				<div class='col all30 tar fs14 lh30'>
					<a href='/settings/dept/list' class='pad-r5 c-blue pad-b0'>Up</a>
					<a onclick="$('#submit').click();" class='pad-l5 c-blue pad-b0'>Save</a>
				</div>
			</div>
			<form method="post">
				
				<!-- NAME -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Name</label>
					<input class='col all100 fs14 tb' name='name' id='name' type='text' maxlength='255' value='<?=$department['dept.name'];?>' placeholder='Name' autofocus>
				</div>
				
				<!-- ACCESS -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Access</label>
					<select class='col all100 fs14 ms' name='access[]' id='access' multiple placeholder="Please Select">
						<?php foreach($types as $k=>$v){ 
						if(in_array($v,$access)){$flg = " selected";} else {$flg="";} ?>
						<option<?php echo $flg; ?> value="<?=$v;?>"><?=$k;?></option>
						<?php } ?>
					</select>
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