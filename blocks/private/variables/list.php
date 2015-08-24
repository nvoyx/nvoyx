<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns variable listings
 */
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

<!-- VARIABLE LISTINGS -->
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='row pad-b20'>
			<div class='col all70 pad-r20'>
				<h1 class='pad0 fs20 c-blue'>Variables</h1>
			</div>
			<div class='col all30 tar fs14 lh30'>
				<a href='/settings/variables/add' class='c-blue pad-b0'>Add</a>
			</div>
		</div>
		<?php $x=0;foreach($NVX_VAR->FETCH_ARRAY() as $r){
			$r['bc']=($x%2==0)?'b-lblue':'b-vlblue';?>
			<div class='row pad10 c-white <?=$r['bc'];?>'>
				<div class='col all70 fs14 pad-r20'>
					<p class='pad0 bw'><?=$r['name'];?></p>
				</div>
				<div class='col all30 fs14 tar'>
					<a href='/settings/variables/edit/<?=$r['id'];?>' class='pad-r5 pad-b0 hvr-white'>Edit</a>
					<a onclick='deleteCheck("/settings/variables/delete/<?=$r['id'];?>");' class='pad-l5 pad-b0 hvr-white'>Delete</a>
				</div>
			</div>
		<?php $x++;} ?>
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>