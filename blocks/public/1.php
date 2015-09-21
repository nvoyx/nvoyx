<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * @block 1 (admin bar)
 * param none
 * returns admin navigation
 */

if($nvUser->granted("s")){
	if(isset($debug)){
		if(is_array($debug)){
			$debu=$nvHtml->ul($debug);
		}
	} else {
		$debug = 'pass an array or variable to $debug to view here.';
	}
}

?>

<a id="admin-button">
	<img width="col all100" src="/settings/resources/files/images/private/admin.svg">
</a>

<div id="admin" class="hide col all100 pad20">
	<div id='admin-nav' class='col all100 pad10 fs16 tar'>
		<a href="/settings/content/list">Admin</a> | 
		<?php if($nvDept->granted($nvUser->fetch_array()['dept'],$page['tid'])){ ?>
		<a href="/settings/content/edit/<?=$page["id"]; ?>">Edit</a> | 
		<?php } ?>
		<a href="/settings/user/logout">Logout</a>
	</div>
	<?php if($nvUser->granted("s")){ ?>
	<div class="row pad-t20">
		<div class='col all100'>
			<p class='fs12 pad-b15'>
				Memory usage: <?=implode(' ',$nvBoot->human_filesize(memory_get_peak_usage()));?><br>
				Db usage: <?=$nvDb->calls();?> calls<br>
				Execution time: <?=number_format(microtime(true)-$nvBoot->fetch_entry('microstamp'),5);?> seconds
			</p>
			<p class='fs12 pad-b0'>
				Page:
			</p>
			<div class='col all100 fs12 pad-lr20 pad-tb10 mar-b15' style='background-color:#f8f8f8;max-height:200px;overflow:scroll;'>
				<?=$nvHtml->ul($page);?>
			</div>
			<p class='fs12 pad-b0'>
				Debug:
			</p>
			<div class='col all100 fs12 pad-lr20 pad-tb10 mar-b15' style='background-color:#f8f8f8;max-height:200px;overflow:scroll;'>
				<?=$debug;?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>