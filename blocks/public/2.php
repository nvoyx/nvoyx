<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * @block 2 (helper)
 * standard variable definitions used throughout the website live here 
 */

/* current block id */
$bid = pathinfo(__FILE__, PATHINFO_FILENAME);

/* grab the params */
$p = $NVX_BLOCK->FETCH_PARAMS($bid);

/* notify code */
if(array_key_exists('notify',$_SESSION)){ ?>
<script>
	notif({
		msg: "<?=$_SESSION['notify']['message'];?>",
		type: "<?=$_SESSION['notify']['type'];?>",
		position: "center"
	});
</script>
<?php unset($_SESSION['notify']);}