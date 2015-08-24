<?php
/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

if($NVX_BOOT->FETCH_ENTRY("current")!='unit'){ ?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>NVOY - <?=$NVX_BOOT->FETCH_ENTRY("current");?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="Generator" content="NVOYX Open Source CMS">
		<link rel="icon" type="image/png" href="<?= $NVX_BOOT->FETCH_ENTRY("favicon"); ?>" />
		<link rel="stylesheet" href="/settings/resources/files/compress/private/<?=$NVX_BOOT->FETCH_ENTRY("modcssprivate");?>.css" type="text/css" />
		<script src="/settings/resources/files/compress/private/<?=$NVX_BOOT->FETCH_ENTRY("modjsprivate");?>.js"></script>
		<link href='//fonts.googleapis.com/css?family=Lato:300normal,400normal&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		
		<!--[if IE 9]>
			<script>
				window.location = "//settings/resources/browser/upgrade.php";
			</script>
		<![endif]-->
	</head>
	<body>
		
		<?php if(array_key_exists('notify',$_SESSION)){ ?>
		<script>
		notif({
			msg: "<?=$_SESSION['notify']['message'];?>",
			type: "<?=$_SESSION['notify']['type'];?>",
			position: "center"
		});
		</script>
		<?php unset($_SESSION['notify']);}
				
}
		include($NVX_CMS->FETCH_HTML());
if($NVX_BOOT->FETCH_ENTRY("current")!='unit'){ ?>
		
		<div style="display: none"><a href="/settings/resources/honeypot/<?= $NVX_VAR->FETCH_ENTRY("honeyfile")[0]; ?>">tendentious-parliamentary</a></div>
	</body>
</html>
<?php } ?>