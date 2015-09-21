<?php

$nvPath = \nvoy\site\Path::CONNECT($nvDb);
$rs = $nvPath->fetch_entry("/" . implode("/",array_slice($nvBoot->fetch_entry("breadcrumb"),0,3)));
if($rs){
	if( stristr($nvUser->fetch_entry("type"),$rs["access"])){
		if($nvBoot->fetch_entry("breadcrumb",1)!="ajax"){
			$nvBoot->set_protocol("https");
		}

		$rs =  '\\nvoy\\cms\\' . ucwords($nvBoot->fetch_entry("breadcrumb",1));
		$nvDept = \nvoy\site\Dept::connect($nvBoot,$nvDb,$nvUser);
		$nvGroup = \nvoy\site\Group::connect($nvDb,$nvBoot);
		$nvCms = $rs::connect($nvBoot,$nvDb,$nvUser,$nvGroup,$nvDept);

		if($nvBoot->fetch_entry("breadcrumb",1)=="ajax" || ($nvBoot->fetch_entry("breadcrumb",1)=="debug" && (
				$nvBoot->fetch_entry("breadcrumb",2)=="xcache" || 
				$nvBoot->fetch_entry("breadcrumb",2)=="info" ||
				$nvBoot->fetch_entry("breadcrumb",2)=="database"
				))){
			$rs = $nvBoot->test_include("template",$nvVar->fetch_entry("ajax")[0]);				
		} else {
			$nvBoot->compress("css",$nvVar->fetch_entry("cssprivate"),"private");
			$nvBoot->compress("js",$nvVar->fetch_entry("jsprivate"),"private");
			$rs = $nvBoot->test_include("template",$nvVar->fetch_entry("template")[0]);
		}

		if($rs){
			if($nvBoot->fetch_entry("breadcrumb",1)!="ajax" && $nvBoot->fetch_entry("breadcrumb",1)!="debug"){
				$nvBoot->delete_cache();
				$nvBoot->set_entry("cached",false);
			}
			$nvType = \nvoy\site\Type::connect($nvDb,$nvBoot,$nvVar->fetch_entry("front")[0]);
			$nvField = \nvoy\site\Field::connect($nvDb,$nvGroup,$nvBoot);
			$nvPage = \nvoy\site\Page::connect($nvDb,$nvVar->fetch_entry("front")[0],$nvField,$nvBoot);
			$nvBlock = \nvoy\site\Block::connect($nvDb,$nvBoot,$nvPage,false);
			$nvHtml = \nvoy\site\Html5::connect($nvBoot,$nvVar,false);

			ob_start();
				include($rs);
			$rs = ob_get_clean();

			$rs = preg_replace("/\s+/", " ", $rs);
			$rs = str_replace(array("[format:newline]","[format:tab]"),array("\n","\t"),$rs);

			ob_start();
				$nvBoot->header(array("OK"=>true));
				echo $rs;
			ob_end_flush();
		}
		die();
	}
}