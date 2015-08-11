<?php
/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */
 ?>

<?php if($NVX_BOOT->FETCH_ENTRY("current")!='unit'){ ?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>NVOYX - <?=$NVX_BOOT->FETCH_ENTRY("current");?></title>
		<meta name="Generator" content="NVOYX Open Source CMS">
		<link rel="icon" type="image/png" href="<?= $NVX_BOOT->FETCH_ENTRY("favicon"); ?>" />
		<link rel="stylesheet" href="/settings/resources/files/compress/private/<?=$NVX_BOOT->FETCH_ENTRY("modcssprivate");?>.css" type="text/css" />
		<script src="/settings/resources/files/compress/private/<?=$NVX_BOOT->FETCH_ENTRY("modjsprivate");?>.js"></script>
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
		<?php unset($_SESSION['notify']);} ?>
				
		<div style='width:714px;margin:auto;'>
			<?php }
			include($NVX_CMS->FETCH_HTML());
			if($NVX_BOOT->FETCH_ENTRY("current")!='unit'){ ?>
		</div>
		
		<div style="display: none"><a href="/settings/resources/honeypot/<?= $NVX_VAR->FETCH_ENTRY("honeyfile")[0]; ?>">tendentious-parliamentary</a></div>
	</body>
</html>
<?php } ?>