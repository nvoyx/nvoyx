<?php

$nvType = \nvoy\site\Type::connect($nvDb,$nvBoot,$nvVar->fetch_entry("front")[0]);
$nvGroup = \nvoy\site\Group::connect($nvDb,$nvBoot);
$nvField = \nvoy\site\Field::connect($nvDb,$nvGroup,$nvBoot);
$nvPage = \nvoy\site\Page::connect($nvDb,$nvVar->fetch_entry("front")[0],$nvField,$nvBoot);
$cache = $nvBoot->get_cache("PAGE" . implode("*",$nvBoot->fetch_entry("breadcrumb")));

/* non cms page is not cached */
if(!$cache){		
	$rs = $nvType->fetch_matches($nvUser->fetch_entry("type"),$nvBoot->fetch_entry("breadcrumb"),false);
	if(isset($rs)){		
		$nvPage->find(array("TIDS" => $rs,
					"ALIAS" => $nvBoot->fetch_entry("current"),
					"USER" => $nvUser->fetch_entry("type"),
					"FIELDS" => true,
					"SINGLE" => true
					));

		$rs = $nvPage->fetch_array();
		if(isset($rs)){
			foreach($rs as $r){
				$bcc=count($nvBoot->fetch_entry("breadcrumb"));						
				if(substr_count( $nvType->fetch_by_tid($r["tid"])["prefix"], "/")){
					$pc=substr_count( $nvType->fetch_by_tid($r["tid"])["prefix"], "/")+2;
				} elseif($nvType->fetch_by_tid($r["tid"])["prefix"]!=""){
					$pc=2;
				} elseif($nvType->fetch_by_tid($r["tid"])["prefix"]==""){
					$pc=1;
				}

				if($bcc != $pc){
					unset($rs["nid-{$r['id']}"]);
					$nvPage->clear_entry("nid-{$r['id']}");
					if(empty($rs)){unset($rs);break;}							
				}

				if(array_key_exists("nid-{$r['id']}",$rs)){
					if($nvType->fetch_by_tid($r["tid"])["prefix"]!=""){
						if($nvType->prefixer($r)."/".$r["alias"] != implode("/",$nvBoot->fetch_entry("breadcrumb"))){
							unset($rs["nid-{$r['id']}"]);
							$nvPage->clear_entry("nid-{$r['id']}");
							if(empty($rs)){unset($rs);break;}
						}
					}
				}
			}
		}

		if(isset($rs)){
			$r=array_keys($rs);
			$page = $rs[array_shift($r)];
			$nvBoot->set_cache("PAGE" . implode("*",$nvBoot->fetch_entry("breadcrumb")),$page);
		} else {

			/* having tested the possible page types, nothing has been found, that might mean that the page is using heirarchies */

			$nvPage->clear();
			$nvDb->clear(array("ALL"));
			$nvDb->set_filter("`page`.`id`=`heirarchy`.`nid`");
			$rs = $nvDb->query("SELECT","`heirarchy`.`values`,`heirarchy`.`nid`,`page`.`alias`,`page`.`tid` FROM `heirarchy`,`page`");
			if($rs){
				$requested_url = "/".implode("/",$nvBoot->fetch_entry("breadcrumb"));
				foreach($rs as $r){
					$test["nid"] = $r["heirarchy.nid"];
					$test["alias"] = $r["page.alias"];
					$test["tid"] = $r["page.tid"];
					foreach($nvBoot->json($r["heirarchy.values"],"decode") as $a){
						$x = 0;
						$test["url"]="";
						foreach($a as $n){
							if($n!=-1){
								$nvDb->clear(array("ALL"));
								$nvDb->set_filter("`page`.`id`={$n}");
								$page_alias = $nvDb->query("SELECT","`page`.`id`,`page`.`tid`,`page`.`alias` FROM `page`");
								if($page_alias){
									if($x==0){
										$test["url"] = $nvType->fetch_by_tid($page_alias[0]["page.tid"])["prefix"] . "/" . $page_alias[0]["page.alias"];
									} else {
										$test["url"] .= "/" . $page_alias[0]["page.alias"];
									}
								}
							}
							$x++;
						}

						$test["url"] .= "/" . $test["alias"];
						if($requested_url == $test["url"]){
							$nvPage->find(array("NID"=>$test["nid"],
												"TIDS"=>$test["tid"],
												"USER"=>$nvUser->fetch_entry("type"),
												"FIELDS"=>true
												));
							$rs = $nvPage->fetch_array();
							if(isset($rs)){
								$r=array_keys($rs);
								$page = $rs[array_shift($r)];
								$nvBoot->delete_cache("PAGE" . implode("*",$nvBoot->fetch_entry("breadcrumb")),$page);
							}
						}
					}
				}
			}
		}
	}
} else {

	/* non cms page is cached */		
	$page = $cache;
	if(!$nvUser->granted("a")){
		if($page["published"]==0){
			$nvBoot->header(array("LOCATION"=>"/"));
		}
	}
}

/* if we have a non cms page, either from the cache or freshly gathered */
if(isset($page)){
	$type = $nvType->fetch_by_tid($page["tid"]);
	if(!$nvUser->granted($type["view"])){
		$nvBoot->header(array("LOCATION"=>"/"));
	}
	if($page["id"]==$nvVar->fetch_entry("front") && $nvBoot->fetch_entry("current")!=""){
		$nvBoot->header(array("LOCATION"=>"/"));	
	}

	$nvDept = \nvoy\site\Dept::connect($nvBoot,$nvDb,$nvUser);
	$nvBlock = \nvoy\site\Block::connect($nvDb,$nvBoot,$nvPage,$page);										
	$blocks = $nvBlock->fetch_id($page["tid"],$nvUser->fetch_entry("type"));
	$rs = $nvBoot->test_include("template",$type["template"]);

	if($rs){

		$nvBoot->compress("css",$nvVar->fetch_entry("csspublic"),"public");
		$nvBoot->compress("js",$nvVar->fetch_entry("jspublic"),"public");
		$nvHtml = \nvoy\site\Html5::connect($nvBoot,$nvVar,$page);
		$nvIc = \nvoy\site\ImageCache::connect($nvDb,$nvBoot);

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
} else {

	/* the requested non cms page dosen't exist, check for a redirect */
	$nvRedirects = \nvoy\site\Redirects::connect($nvDb);
	$rs=$nvRedirects->resolve('/'.$nvBoot->fetch_entry('uri'));

	/* no redirect exists, so point at 404 page */
	if($rs=='/'.$nvBoot->fetch_entry('uri')){

		$nvPage->clear();
		$nvPage->find(array("NID"=>$nvVar->fetch_entry('404')[0],"USER"=>$nvUser->fetch_entry("type"),"FIELDS"=>true));
		$rs = $nvPage->fetch_array();
		if(isset($rs)){
			$r=array_keys($rs);
			$page = $rs[array_shift($r)];
			$type = $nvType->fetch_by_tid($page["tid"]);
			$nvDept = \nvoy\site\Dept::connect($nvBoot,$nvDb,$nvUser);
			$nvBlock = \nvoy\site\Block::connect($nvDb,$nvBoot,$nvPage,$page);
			$blocks = $nvBlock->fetch_id($page["tid"],$nvUser->fetch_entry("type"));
			$rs = $nvBoot->test_include("template",$type["template"]);
			if($rs){

				$nvBoot->compress("css",$nvVar->fetch_entry("csspublic"),"public");
				$nvBoot->compress("js",$nvVar->fetch_entry("jspublic"),"public");
				$nvHtml = \nvoy\site\Html5::connect($nvBoot,$nvVar,$page);
				$nvIc = \nvoy\site\ImageCache::connect($nvDb,$nvBoot);

				ob_start();
					include($rs);
				$rs = ob_get_clean();

				$rs = preg_replace("/\s+/", " ", $rs);
				$rs = str_replace(array("[format:newline]","[format:tab]"),array("\n","\t"),$rs);

				ob_start();
					$nvBoot->header(array("404"=>true));
					echo $rs;
				ob_end_flush();
			}
			die();
		}
	} else {
		/* redirect found for non cms page, so head to it */
		$nvBoot->header(array("301"=>$rs));
	}
	$nvBoot->header(array("LOCATION"=>"/"));
}