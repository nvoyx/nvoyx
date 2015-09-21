<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 * 
 * 
   This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 *
 */

/* load balancer ip fix */
$_SERVER["REMOTE_ADDR"] = 	getenv('HTTP_CLIENT_IP')?:
							getenv('HTTP_X_FORWARDED_FOR')?:
							getenv('HTTP_X_FORWARDED')?:
							getenv('HTTP_FORWARDED_FOR')?:
							getenv('HTTP_FORWARDED')?:
							getenv('REMOTE_ADDR');

/* class loader */
spl_autoload_register(function ($class){
	if( substr($class,0,1) == '\\' ){$class=substr($class,1);}
	$class=$_SERVER['DOCUMENT_ROOT'].'/../classes/'.str_replace('\\','/',$class);
	if(file_exists($class.'.inc')){
		include($class.'.inc');
	} else if(file_exists($class.'.php')){
		include($class.'.php');
	} else if(file_exists($class.'.inc.php')){
		include($class.'.inc.php');
	} else {
		die();
	}
});

/* config */
$nvSetup = \nvoy\site\Setup::connect($_SERVER['DOCUMENT_ROOT']."/../configuration/config.json");
$config = $nvSetup->fetch_options();

/* database */
\nvoy\site\Db::configure($config);
$nvDb = \nvoy\site\Db::connect();

if (mysqli_connect_errno()) {
	echo mysqli_connect_error();
	$nvSetup->page();
	
} else {
	if(is_writable($_SERVER['DOCUMENT_ROOT']."/../configuration/config.json")){		
		chmod($_SERVER['DOCUMENT_ROOT']."/../configuration/config.json", 0400);
	}
}

$nvBoot = \nvoy\site\Boot::connect($nvDb);
if(!isset($nvBoot)){die();}

$nvSetup->tables($nvDb,$nvBoot,$config);
$nvVar = \nvoy\site\Variables::connect($nvDb,$nvBoot);
$nvUser = \nvoy\site\User::connect($nvDb,$nvBoot,$nvVar);

/* holding/maintenance check */
if(!$nvUser->granted("a")){
	if(!in_array("/" . implode("/",array_slice($nvBoot->fetch_entry("breadcrumb"),0,2)),$nvVar->fetch_entry("maintenance paths"))){
		if($nvVar->fetch_entry("live")[0] == 0){
			if($nvVar->fetch_entry("holding")[0]==1){
				include($nvBoot->fetch_entry("resources")."/holding/index.php");die();
			} else {
				include($nvBoot->fetch_entry("resources")."/maintenance/index.php");die();
			}
		}
	}
}

/* resource check */
if("/" . implode("/",array_slice($nvBoot->fetch_entry("breadcrumb"),0,2))=="/settings/resources"){
	$nvIc = \nvoy\site\ImageCache::connect($nvDb,$nvBoot);
	$nvResource = \nvoy\site\Resource::CONNECT($nvDb,$nvBoot,$nvUser,$nvVar,$nvIc);
	$nvResource->fetch();
}

/* set the website timezone */
date_default_timezone_set($nvVar->fetch_entry("timezone")[0]);

/* project honeypot */
if($nvUser->fetch_entry("type")=="!u" && $nvVar->fetch_entry("honeyserver")[0]!=""){
	$nvHoneypot = new \nvoy\site\Honeypot($nvVar->fetch_entry("honeykey")[0],array("root"=>$nvVar->fetch_entry("honeyserver")[0]));
	$rs = $nvHoneypot->check($nvBoot->fetch_entry("remote"));
	if($rs['threat'] > 10 && $rs['age'] < 15){
		header("Location: /settings/resources/honeypot/".$nvVar->fetch_entry('honeyfile')[0]);die();
	}
}

if($nvBoot->fetch_entry("breadcrumb",0) != "settings"){
	
	/* site page request */
	include('site.php');
} else {
	
	/* cms page requested */
	include('cms.php');
}
