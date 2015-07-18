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
		<link rel="stylesheet" href="/settings/resources/css/private.css" type="text/css" />
		<link rel="stylesheet" href="/settings/resources/css/jquery-ui-1.10.2.custom.min.css" type="text/css" />
		<script src="/settings/resources/js/jquery-2.1.1.min.js"></script>
		<script src="/settings/resources/js/ckbase.js"></script>
		<script src="/settings/resources/ckeditor/ckeditor.js"></script>
		<script src="/settings/resources/ckeditor/adapters/jquery.js"></script>
		<script src="/settings/resources/js/jquery.filedrop.js"></script>
		<script src="/settings/resources/js/jquery-ui-1.10.2.custom.min.js"></script>
		<script src="/settings/resources/js/private.js"></script>
	</head>
	<body>
				
		<div style="width:714px;margin:auto;">
<?php } ?>
			<?php include($NVX_CMS->FETCH_HTML()); ?>
<?php if($NVX_BOOT->FETCH_ENTRY("current")!='unit'){ ?>
		</div>
		
		<div style="display: none"><a href="/settings/resources/honeypot/<?= $NVX_VAR->FETCH_ENTRY("honeyfile")[0]; ?>">tendentious-parliamentary</a></div>
	</body>
</html>
<?php } ?>