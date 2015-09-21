<?php
/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

if($nvBoot->fetch_entry("current")!='unit'){ ?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>NVOY - <?=$nvBoot->fetch_entry("current");?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="Generator" content="NVOYX Open Source CMS">
		<link rel="icon" type="image/png" href="<?= $nvBoot->fetch_entry("favicon"); ?>" />
		<link rel="stylesheet" href="/settings/resources/files/compress/private/<?=$nvBoot->fetch_entry("modcssprivate");?>.css" type="text/css" />
		<script src="/settings/resources/files/compress/private/<?=$nvBoot->fetch_entry("modjsprivate");?>.js"></script>
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
		include($nvCms->fetch_html());
if($nvBoot->fetch_entry("current")!='unit'){ ?>
		
		<div class='hide'><a href="/settings/resources/honeypot/<?= $nvVar->fetch_entry("honeyfile")[0]; ?>">tendentious-parliamentary</a></div>
	</body>
</html>
<?php } ?>