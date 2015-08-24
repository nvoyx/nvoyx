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

if($NVX_USER->GRANTED("s")){
	if(isset($DEBUG)){
		if(is_array($DEBUG)){
			$DEBUG=$NVX_HTML->UL($DEBUG);
		}
	} else {
		$DEBUG = 'pass an array or variable to $DEBUG to view here.';
	}
}

?>

<div id="admin" class="hide">
	<div id='admin-nav'>
		<a href="/settings/content/list">Admin</a> | 
		<?php if($NVX_DEPT->GRANTED($NVX_USER->FETCH_ARRAY()['dept'],$PAGE['tid'])){ ?>
		<a href="/settings/content/edit/<?php echo $PAGE["id"]; ?>">Edit</a> | 
		<?php } ?>
		<a href="/settings/user/logout">Logout</a>
	</div>
	<?php if($NVX_USER->GRANTED("s")){ ?>
	<div class="row pad-t20">
		<div class='col all100'>
			<p class='fs12 pad-b15'>
				Memory usage: <?=implode(' ',$NVX_BOOT->HUMAN_FILESIZE(memory_get_peak_usage()));?><br>
				Db usage: <?=$NVX_DB->CALLS();?> calls<br>
				Execution time: <?=number_format(microtime(true)-$NVX_BOOT->FETCH_ENTRY('microstamp'),5);?> seconds
			</p>
			<p class='fs12 pad-b0'>
				Page:
			</p>
			<div class='col all100 fs12 pad-lr20 pad-tb10 mar-b15' style='background-color:#f8f8f8;max-height:200px;overflow:scroll;'>
				<?=$NVX_HTML->UL($PAGE);?>
			</div>
			<p class='fs12 pad-b0'>
				Debug:
			</p>
			<div class='col all100 fs12 pad-lr20 pad-tb10 mar-b15' style='background-color:#f8f8f8;max-height:200px;overflow:scroll;'>
				<?=$DEBUG;?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>