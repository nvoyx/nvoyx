<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns debug listings
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

<!-- BLOCK LISTINGS -->
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='row pad-b20'>
			<div class='col all100'>
				<h1 class='pad0 fs20 c-blue'>Debug</h1>
			</div>
		</div>
		<?php if($NVX_DB->IS_CACHED()) { ?>
		<div class='row pad10 c-white b-lblue'>
			<div class='col all70 fs14 pad-r20'>
				<p class='pad0'>Xcache</p>
			</div>
			<div class='col all30 fs14 tar'>
				<a href='/settings/debug/xcache' class='pad-b0 hvr-white'>View</a>
			</div>
		</div>
		<?php } ?>
		<div class='row pad10 c-white b-vlblue'>
			<div class='col all70 fs14 pad-r20'>
				<p class='pad0'>Database Tables</p>
			</div>
			<div class='col all30 fs14 tar'>
				<a href='/settings/debug/database' class='pad-b0 hvr-white'>View</a>
			</div>
		</div>
		<div class='row pad10 c-white b-lblue'>
			<div class='col all70 fs14 pad-r20'>
				<p class='pad0'>PHP Error Log</p>
			</div>
			<div class='col all30 fs14 tar'>
				<a href='/settings/debug/log' class='pad-b0 hvr-white'>View</a>
			</div>
		</div>
		<div class='row pad10 c-white b-vlblue'>
			<div class='col all70 fs14 pad-r20'>
				<p class='pad0'>PHP Info</p>
			</div>
			<div class='col all30 fs14 tar'>
				<a href='/settings/debug/info' class='pad-b0 hvr-white'>View</a>
			</div>
		</div>
		<div class='row pad10 c-white b-lblue'>
			<div class='col all70 fs14 pad-r20'>
				<p class='pad0'>Unit Testing</p>
			</div>
			<div class='col all30 fs14 tar'>
				<a href='/settings/debug/unit' class='pad-b0 hvr-white'>View</a>
			</div>
		</div>
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>
