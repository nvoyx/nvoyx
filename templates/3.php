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
<html lang="<?=$n['opts'][$n['this']]['language'];?>" dir="<?=$n['opts'][$n['this']]['direction'];?>">
	<head>
		<!--[if lt IE 9]><meta http-equiv="REFRESH" content="0;url=/settings/resources/browser/upgrade.php" /><![endif]-->
		<meta charset="utf-8">
		<meta name="Generator" content="NVOY Open Source CMS">
		<meta name="format-detection" content="telephone=no">
		<meta name="description" content="<?=$page["description"][$n['this']];?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<title><?=$page["title"][$n['this']];?> | <?=$nvVar->fetch_entry("company")[0];?></title>
		<?php if($n['canon']){ ?>
		<link rel="canonical" href="<?=$nvBoot->fetch_entry('protocol');?>://<?=$nvBoot->fetch_entry('domain');?>/<?=implode('/',$nvBoot->fetch_entry('breadcrumb'));?>">
		<?php } ?>
		<link rel="icon" type="image/png" href="<?=$nvBoot->fetch_entry("favicon"); ?>">
		<link rel="alternate" type="application/rss+xml" title="<?=$nvVar->fetch_entry("company")[0];?> RSS" href="/settings/resources/rss/rss.php">
		<link href='//fonts.googleapis.com/css?family=Lato:300normal,400normal&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		<script>
			/* is the screen of high pixel density */
			window.devicePixelRatio = window.devicePixelRatio || window.screen.deviceXDPI / window.screen.logicalXDPI;
			if(window.devicePixelRatio>1){document.documentElement.className += " hi-pd";}
			
			/* is the device touch enabled */
			function isTouchDevice(){return true == ("ontouchstart" in window || (window.DocumentTouch && document instanceof DocumentTouch) || navigator.msMaxTouchPoints);}
			if(isTouchDevice()){document.documentElement.className += " touch";} else {document.documentElement.className += " no-touch";}
		</script>
		<link rel="stylesheet" href="/settings/resources/files/compress/public/<?=$nvBoot->fetch_entry("modcsspublic");?>.css">
		<?php if($nvUser->granted("s")){ 
			foreach($nvVar->fetch_entry('jspublic') as $r){ ?>
				<script src="/settings/resources/js/<?=$r;?>?uid=<?=$nvBoot->fetch_entry('timestamp');?>" defer></script>
		<?php }} else { ?>
 			<script src="/settings/resources/files/compress/public/<?=$nvBoot->fetch_entry("modjspublic");?>.js" defer></script>
 		<?php } ?>
	</head>
	<body>

		<?php /* helper */ $rs = $nvBlock->loader($blocks,2);if($rs){include($rs);} ?>
		
		<section class="row">
			<div class="col all100">
				<h1><?=$page["heading"][$n['this']];?></h1>
				<h2><?=$page["teaser"][$n['this']];?></h2>
				<?=$page["body"][$n['this']];?>
			</div>
		</section>
				
		<?php /* 404 error */ $rs = $nvBlock->loader($blocks,4);if($rs){include($rs);} ?>
								
		<?php if($nvVar->fetch_entry("honeyfile")[0]!=""){ ?>
			<div class='hide'><a href="/settings/resources/honeypot/<?=$nvVar->fetch_entry("honeyfile")[0];?>">tendentious-parliamentary</a></div>
		<?php } ?>
		
		<?php /* google analytics */ $rs = $nvBlock->loader($blocks,3);if($rs){include($rs);} ?>
			
		<?php /* admin bar */ $rs = $nvBlock->loader($blocks,1);if($rs){include($rs);} ?>
	</body>
</html>
