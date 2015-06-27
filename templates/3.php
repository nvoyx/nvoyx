<?php
/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */
 ?>

<!DOCTYPE HTML>
<html>
	<head>
		<!--[if lt IE 9]><meta http-equiv="REFRESH" content="0;url=/settings/resources/browser/upgrade.php" /><![endif]-->
		<meta charset="utf-8">
		<meta name="Generator" content="NVOYX Open Source CMS">
		<meta name="format-detection" content="telephone=no">
		<meta name="description" content="<?=$PAGE["description"];?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<title><?php echo $PAGE["title"]; ?> | <?php echo $NVX_VAR->FETCH_ENTRY("company")[0];?></title>
		<link rel="icon" type="image/png" href="<?php echo $NVX_BOOT->FETCH_ENTRY("favicon"); ?>">
		<link rel="alternate" type="application/rss+xml" title="<?php echo $NVX_VAR->FETCH_ENTRY("company")[0];?> RSS" href="/settings/resources/rss/rss.php">
		<script>
			/* is the screen of high pixel density */
			window.devicePixelRatio = window.devicePixelRatio || window.screen.deviceXDPI / window.screen.logicalXDPI;
			if(window.devicePixelRatio>1){document.documentElement.className += " hi-pd";}
			
			/* is the device touch enabled */
			function isTouchDevice(){return true == ("ontouchstart" in window || (window.DocumentTouch && document instanceof DocumentTouch) || navigator.msMaxTouchPoints);}
			if(isTouchDevice()){document.documentElement.className += " touch";} else {document.documentElement.className += " no-touch";}
		</script>
		<?php if($NVX_USER->GRANTED("s")){ 
			foreach($NVX_VAR->FETCH_ENTRY('js') as $r){ ?>
				<script src="/settings/resources/js/<?=$r;?>?uid=<?php echo $NVX_BOOT->FETCH_ENTRY('timestamp');?>" defer></script>
		<?php }} else { ?>
 			<script src="/settings/resources/files/compress/public.js?v=<?php echo $NVX_BOOT->FETCH_ENTRY("modjs"); ?>" defer></script>
 		<?php } ?>
		<link rel="stylesheet" href="/settings/resources/files/compress/public.css?v=<?php echo $NVX_BOOT->FETCH_ENTRY("modcss"); ?>">
	</head>
	<body>

		<?php /* ADMIN BAR */ $rs = $NVX_BLOCK->LOADER($BLOCKS,1);if($rs){include($rs);} ?>

		<?php /* HELPER */ $rs = $NVX_BLOCK->LOADER($BLOCKS,2);if($rs){include($rs);} ?>

		<div class="row">
			<div class="col all100">
				<h1><?=$PAGE["heading"];?></h1>
			</div>
		</div>
		
		<?php /* 404 ERROR */ $rs = $NVX_BLOCK->LOADER($BLOCKS,7);if($rs){include($rs);} ?>
				
		<?php /* COMMENTS */ $rs = $NVX_BLOCK->LOADER($BLOCKS,6);if($rs){include($rs);} ?>
				
		<?php if($NVX_VAR->FETCH_ENTRY("honeyfile")[0]!=""){ ?>
			<div style="display: none"><a href="/settings/resources/honeypot/<?php echo $NVX_VAR->FETCH_ENTRY("honeyfile")[0]; ?>">tendentious-parliamentary</a></div>
		<?php } ?>
		
		<?php /* GOOGLE ANALYTICS */ $rs = $NVX_BLOCK->LOADER($BLOCKS,3);if($rs){include($rs);} ?>
	</body>
</html>
