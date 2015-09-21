<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns user content
 */

/* user id */
$uid = $nvBoot->fetch_entry("breadcrumb",3);


/* grab all currently registered users */
$nvDb->clear(array("ALL"));
$users = $nvDb->query("SELECT","* FROM `user`");
$depts = $nvDb->query("SELECT","* FROM `dept`");

/* lookup the user details */
foreach($users as $user){if($user["user.id"]==$uid){break;}}

/* have we found the user */
if(isset($user)){ 
	
	$r=array(
		'username'=>$nvBoot->cypher('decrypt',$user["user.username"]),
		'password'=>$nvBoot->cypher('decrypt',$user["user.password"]),
		'type'=>$user['user.type'],
		'dept'=>$user['user.dept'],
		'filter'=>$user['user.filter'],
		'contact'=>$nvBoot->cypher('decrypt',$user["user.contact"]),
		'telephone'=>$nvBoot->cypher('decrypt',$user["user.telephone"]),
		'email'=>$nvBoot->cypher('decrypt',$user["user.email"])
	);
	
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
					<h1 class='pad0 fs20 c-blue'>User</h1>
				</div>
				<div class='col all30 tar fs14 lh30'>
					<a href='/settings/user/list' class='pad-r5 c-blue pad-b0'>Up</a>
					<a onclick="$('#submit').click();" class='pad-l5 c-blue pad-b0'>Save</a>
				</div>
			</div>
			<form method="post">
				
				<!-- USERNAME -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Username</label>
					<input class='col all100 fs14 tb' name='username' id='username' type='text' maxlength='50' value='<?=$r['username'];?>' placeholder='Username' autofocus>
					<input name='filter' id='filter' type='hidden' value='<?=$r['filter'];?>'>
				</div>
				
				<!-- PASSWORD -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Password</label>
					<input class='col all100 fs14 tb' name='password' id='password' type='text' maxlength='50' value='<?=$r['password'];?>' placeholder='Password'>
				</div>
				
				<!-- Type -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 lge-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Type</label>
					<select class='col all100 fs14 ss' name='type' id='type' placeholder="Please Select">
						<option<?php if($r["type"]=="a"){echo " selected";}?> value='a'>Admin</option>
						<option<?php if($r["type"]=="s"){echo " selected";}?> value='s'>Superuser</option>
					</select>
				</div>
				
				<!-- DEPT -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Department</label>
					<select class='col all100 fs14 ss' name='dept' id='dept' placeholder="Please Select">
						<?php foreach($depts as $dept){
							if($dept["dept.id"]==$r['dept']){$flg = " selected";} else {$flg="";}?>
							<option<?=$flg;?> value='<?=$dept['dept.id'];?>'><?=$dept['dept.name'];?></option>
						<?php } ?>
					</select>
				</div>
				
				<!-- CONTACT -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Contact</label>
					<input class='col all100 fs14 tb' name='contact' id='contact' type='text' maxlength='128' value='<?=$r['contact'];?>' placeholder='Contact Name'>
				</div>
				
				<!-- TELEPHONE -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 lge-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Telephone</label>
					<input class='col all100 fs14 tb' name='telephone' id='telephone' type='text' maxlength='50' value='<?=$r['telephone'];?>' placeholder='Telephone'>
				</div>
				
				<!-- EMAIL -->
				<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 pad-b20'>
					<label class='col all100 fs13 c-blue pad-b5'>Email</label>
					<input class='col all100 fs14 tb' name='email' id='email' type='text' maxlength='50' value='<?=$r['email'];?>' placeholder='Email'>
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