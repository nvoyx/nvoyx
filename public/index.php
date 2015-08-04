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

/* deal with load balancers not returning correct visitor ip address */
$_SERVER["REMOTE_ADDR"] = 	getenv('HTTP_CLIENT_IP')?:
							getenv('HTTP_X_FORWARDED_FOR')?:
							getenv('HTTP_X_FORWARDED')?:
							getenv('HTTP_FORWARDED_FOR')?:
							getenv('HTTP_FORWARDED')?:
							getenv('REMOTE_ADDR');

/**
 * Load classes only when required (reduces application overhead)
 */
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

/**
 * @instance
 * Site configuration
 */
//$NVX_SETUP = Setup::CONNECT($_SERVER['DOCUMENT_ROOT']."/../configuration/config.json");
$NVX_SETUP = \NVOYX\site\Setup::CONNECT($_SERVER['DOCUMENT_ROOT']."/../configuration/config.json");

/* fetch the site configuration options */
$config = $NVX_SETUP->FETCH_OPTIONS();

/* configure the website */
\NVOYX\site\Db::CONFIGURE($config);

/*
 * @instance
 * connect to the Db
 */
$NVX_DB = \NVOYX\site\Db::CONNECT();

/* if db connection has failed */
if (mysqli_connect_errno()) {
	
	/* the system has failed to connect to the database, so return the website config page */
	$NVX_SETUP->PAGE();
	
} else {
		
	/* the config file is good, is the file still writable */
	if(is_writable($_SERVER['DOCUMENT_ROOT']."/../configuration/config.json")){
		
		/* protect the file from being written to again */
		chmod($_SERVER['DOCUMENT_ROOT']."/../configuration/config.json", 0400);
	}
}

/**
 * @instance
 * System Variables
 */
$NVX_BOOT = \NVOYX\site\Boot::CONNECT($NVX_DB);

/* stop on database failure */
if(!isset($NVX_BOOT)){die();}

/* check the database has all the tables */
$NVX_SETUP->TABLES($NVX_DB,$NVX_BOOT,$config);

/**
 * @instance
 * CMS Variables
 */
$NVX_VAR = \NVOYX\site\Variables::CONNECT($NVX_DB,$NVX_BOOT);

/**
 * @instance
 * User information
 */
$NVX_USER = \NVOYX\site\User::CONNECT($NVX_DB,$NVX_BOOT,$NVX_VAR);


/* is the current user not an admin or above */
if(!$NVX_USER->GRANTED("a")){
	
	/* is the currently requested path not one of the allowed maintenance paths eg. login, logout and resource pages */
	if(!in_array("/" . implode("/",array_slice($NVX_BOOT->FETCH_ENTRY("breadcrumb"),0,2)),$NVX_VAR->FETCH_ENTRY("maintenance paths"))){
	
		/* is the site currently not live */
		if($NVX_VAR->FETCH_ENTRY("live")[0] == 0){
	
			/* has the holding page been swiched on */
			if($NVX_VAR->FETCH_ENTRY("holding")[0]==1){
		
				/* call the holding page and die */
				include($NVX_BOOT->FETCH_ENTRY("resources")."/holding/index.php");die();
			} else {
		
				/* call the maintenance page and die */
				include($NVX_BOOT->FETCH_ENTRY("resources")."/maintenance/index.php");die();
			}
		}
	}
}

/* is a resource being requested */
if("/" . implode("/",array_slice($NVX_BOOT->FETCH_ENTRY("breadcrumb"),0,2))=="/settings/resources"){
	
	/**
	 * @instance
	 * ImageCache
	 */
	$NVX_IC = \NVOYX\site\ImageCache::CONNECT($NVX_DB,$NVX_BOOT);
	
	/**
	 * @instance
	 * Resource information
	 */
	$NVX_RESOURCE = \NVOYX\site\Resource::CONNECT($NVX_DB,$NVX_BOOT,$NVX_USER,$NVX_VAR,$NVX_IC);
	
	/* attempt to retrieve the requested resource */
	$NVX_RESOURCE->FETCH();
}

/* set the website timezone */
date_default_timezone_set($NVX_VAR->FETCH_ENTRY("timezone")[0]);

/* only check against project honeypot if they are general public  and this site is plugged in to the service*/
if($NVX_USER->FETCH_ENTRY("type")=="!u" && $NVX_VAR->FETCH_ENTRY("honeyserver")[0]!=""){

	/**
	 * @instance
	 * Project Honeypot
	 */
	$HONEYPOT = new \NVOYX\site\Honeypot($NVX_VAR->FETCH_ENTRY("honeykey")[0],array("root"=>$NVX_VAR->FETCH_ENTRY("honeyserver")[0]));
	
	/* check IP address against Honeypot records */
	$rs = $HONEYPOT->check($NVX_BOOT->FETCH_ENTRY("remote"));
	
	/* if IP address has been classed as a threat in the last 14 days, redirect the user to the Honeypot */
	if($rs['threat'] > 10 && $rs['age'] < 15){
		header("Location: /settings/resources/honeypot/".$NVX_VAR->FETCH_ENTRY('honeyfile')[0]);die();
	}
}

/* we have a request for a non cms page */
if($NVX_BOOT->FETCH_ENTRY("breadcrumb",0) != "settings"){
	
	/**
	 * @instance
	 * Type information
	 */ 
	$NVX_TYPE = \NVOYX\site\Type::CONNECT($NVX_DB,
				$NVX_BOOT,
				$NVX_VAR->FETCH_ENTRY("front")[0]);

	/**
	 * @instance
	 * Group information
	 */ 
	$NVX_GROUP = \NVOYX\site\Group::CONNECT($NVX_DB,$NVX_BOOT);

	/**
	 * @instance
	 * Field information
	 */ 
	$NVX_FIELD = \NVOYX\site\Field::CONNECT($NVX_DB,$NVX_GROUP,$NVX_BOOT);

	/**
	 * @instance
	 * Page information
	 */
	$NVX_PAGE = \NVOYX\site\Page::CONNECT($NVX_DB,
					$NVX_VAR->FETCH_ENTRY("front")[0],
					$NVX_FIELD,
					$NVX_BOOT);
	
	/* try to fetch the cache for this page */
	$cache = $NVX_BOOT->GET_CACHE("PAGE" . implode("*",$NVX_BOOT->FETCH_ENTRY("breadcrumb")));
	
	/* if this page has not been previously cached */
	if(!$cache){
				
		/* fetch possible TIDs based on user level */
		$rs = $NVX_TYPE->FETCH_MATCHES(array("URL"=>$NVX_BOOT->FETCH_ENTRY("breadcrumb"),
											"USER"=>$NVX_USER->FETCH_ENTRY("type")
											));
	
		/* do we have a possible page type */
		if(isset($rs)){
						
			/* populate PAGE based on possible TIDs (array or integer as string) and lowest alias */
			$NVX_PAGE->FIND(array("TIDS" => $rs,
						"ALIAS" => $NVX_BOOT->FETCH_ENTRY("current"),
						"USER" => $NVX_USER->FETCH_ENTRY("type"),
						"FIELDS" => true,
						"SINGLE" => true
						));

			/* grab current PAGE variable */
			$rs = $NVX_PAGE->FETCH_ARRAY();
		
			/* do we have at least one possible page */
			if(isset($rs)){
				
				/* do we have more than one possible page */
				if(count($rs)>1){
					
					
					/* cycle through the possible matches */
					foreach($rs as $r){
						
						$bcc=count($NVX_BOOT->FETCH_ENTRY("breadcrumb"));
						
						if(substr_count( $NVX_TYPE->FETCH_BY_TID($r["tid"])["prefix"], "/")){
							$pc=substr_count( $NVX_TYPE->FETCH_BY_TID($r["tid"])["prefix"], "/")+2;
						} elseif($NVX_TYPE->FETCH_BY_TID($r["tid"])["prefix"]!=""){
							$pc=2;
						} elseif($NVX_TYPE->FETCH_BY_TID($r["tid"])["prefix"]==""){
							$pc=1;
						}
												
						/* does the prefix levels plus one not equal the breadcrumbs levels  */
						if($bcc != $pc){
														
							/* this cannot be the correct url, so remove it from the array */
							unset($rs["nid-{$r['id']}"]);
							$NVX_PAGE->CLEAR_ENTRY("nid-{$r['id']}");
							
							/* if the array is now empty, delete it completely and exit the loop */
							if(empty($rs)){unset($rs);break;}							
						}
						
						/* do we still have a possible match */
						if(array_key_exists("nid-{$r['id']}",$rs)){
							
							/* if the prefix isn't blank */
							if($NVX_TYPE->FETCH_BY_TID($r["tid"])["prefix"]!=""){
							
								if($NVX_TYPE->PREFIXER($r)."/".$r["alias"] != implode("/",$NVX_BOOT->FETCH_ENTRY("breadcrumb"))){
								
									/* this cannot be the correct url, so remove it from the array */
									unset($rs["nid-{$r['id']}"]);
									$NVX_PAGE->CLEAR_ENTRY("nid-{$r['id']}");
							
									/* if the array is now empty, delete it completely and exit the loop */
									if(empty($rs)){unset($rs);break;}
								}
							}
						}
					}
				}
			}
			
			/* do we still have a page */
			if(isset($rs)){
			
				/* set $PAGE */
				$r=array_keys($rs);
				$PAGE = $rs[array_shift($r)];
				$NVX_BOOT->SET_CACHE("PAGE" . implode("*",$NVX_BOOT->FETCH_ENTRY("breadcrumb")),$PAGE);
		
			} else {
				
				/* having tested the possible page types, nothing has been found, that might mean that the page is using heirarchies */
				
				/* clear the current page array */
				$NVX_PAGE->CLEAR();
				
				/*grab any heirarchy data from the heirarchy table */
				$NVX_DB->CLEAR(array("ALL"));
				$NVX_DB->SET_FILTER("`page`.`id`=`heirarchy`.`nid`");
				$rs = $NVX_DB->QUERY("SELECT","`heirarchy`.`values`,`heirarchy`.`nid`,`page`.`alias`,`page`.`tid` FROM `heirarchy`,`page`");
				
				/* do we have any heirarchy data */
				if($rs){
					
					/*grab the current url, but ignore any forward slashes */
					$requested_url = "/".implode("/",$NVX_BOOT->FETCH_ENTRY("breadcrumb"));
										
					/* cycle through the heirarchy data */
					foreach($rs as $r){
						
						/* grab the node id of the page whose heirarchies are about to be examined */
						$test["nid"] = $r["heirarchy.nid"];
						
						/* grab the alias of the page whose heirarchies are about to be examined (to be added to the concated heirarchy) */
						$test["alias"] = $r["page.alias"];
						
						/* grab the node type of the page whose heirarchies are about to be examined */
						$test["tid"] = $r["page.tid"];
						
						/* cycle through the test pages heirarchies */
						foreach($NVX_BOOT->JSON($r["heirarchy.values"],"decode") as $a){
							
							/* reset the alias level counter */
							$x = 0;
							
							/* reset the heirarchy url */
							$test["url"]="";
							
							/* cycle through the nids and add to the found nids array */
							foreach($a as $n){
																
								/* if the nid is real */
								if($n!=-1){
									
									
									/* grab details for this heirarchy level page */
									$NVX_DB->CLEAR(array("ALL"));
									$NVX_DB->SET_FILTER("`page`.`id`={$n}");
									$page_alias = $NVX_DB->QUERY("SELECT","`page`.`id`,`page`.`tid`,`page`.`alias` FROM `page`");
									
									/* if we have an alias */
									if($page_alias){
										
										/* if this is the first level for the new heirarchy alias */
										if($x==0){
											
											/* grab this pages prefix as well as the page alias */
											$test["url"] = $NVX_TYPE->FETCH_BY_TID($page_alias[0]["page.tid"])["prefix"] . "/" . $page_alias[0]["page.alias"];
										} else {
											
											/* grab the page alias and concat */
											$test["url"] .= "/" . $page_alias[0]["page.alias"];
										}
									}
								}
								
								/* increment the alias level counter */
								$x++;
							}
							
							/* add on the alias of the page providing the heirarchy details */
							$test["url"] .= "/" . $test["alias"];
														
							/* compare the generated alias to the requested alias */
							if($requested_url == $test["url"]){
								
								/* populate PAGE based on possible TIDs (array or integer as string) and lowest alias */
								$NVX_PAGE->FIND(array("NID"=>$test["nid"],
													"TIDS"=>$test["tid"],
													"USER"=>$NVX_USER->FETCH_ENTRY("type"),
													"FIELDS"=>true
													));
				
								/* grab current PAGE variable */
								$rs = $NVX_PAGE->FETCH_ARRAY();
								
								if(isset($rs)){
			
									/* set $PAGE */
									$r=array_keys($rs);
									$PAGE = $rs[array_shift($r)];
									$NVX_BOOT->SET_CACHE("PAGE" . implode("*",$NVX_BOOT->FETCH_ENTRY("breadcrumb")),$PAGE);
		
								}
							}
						}
					}
				}
			}
		}
	} else {
		
		/* grab the page from cache */
		$PAGE = $cache;
		
		/* if the user is not an admin or superuser */
		if(!$NVX_USER->GRANTED("a")){
		
			/* is this page unpublished */
			if($PAGE["published"]==0){
				
				/* redirect the visitor to the homepage, as they lack permission to view this page */
				$NVX_BOOT->HEADER(array("LOCATION"=>"/"));
			}
		}
	}
	
	if(isset($PAGE)){
		
		/* set $TYPE */
		$TYPE = $NVX_TYPE->FETCH_BY_TID($PAGE["tid"]);
		
		/* if the current user is not allowed to view published pages of this type */
		if(!$NVX_USER->GRANTED($TYPE["view"])){
			
			/* redirect the visitor to the homepage */
			$NVX_BOOT->HEADER(array("LOCATION"=>"/"));
		}
	
		/* is the front page being requested and if yes, is it being requested via / or it's alias. Redirect if not / */
		if($PAGE["id"]==$NVX_VAR->FETCH_ENTRY("front") && $NVX_BOOT->FETCH_ENTRY("current")!=""){
			$NVX_BOOT->HEADER(array("LOCATION"=>"/"));	
		}
		
		/* does this page-type and actual page have comments enabled */
		if($TYPE["comments"]==1 && $PAGE["comments"]==1){
						
			/* set $COMMENTS */
			$NVX_COMMENTS = \NVOYX\site\Comments::CONNECT($NVX_DB,$NVX_BOOT);
			
			
		}
		
		/**
		 * @instance
		 * Dept information
		 */ 
		$NVX_DEPT = \NVOYX\site\Dept::CONNECT($NVX_BOOT,$NVX_DB,$NVX_USER);
	
		/**
		 * @instance
		 * Block information
		 */
		$NVX_BLOCK = \NVOYX\site\Block::CONNECT($NVX_DB,
									$NVX_BOOT,
									$NVX_PAGE,
									$PAGE);
										
		/* grab ids of blocks associated with the current page tid / user type */
		$BLOCKS = $NVX_BLOCK->FETCH_ID(array("TID"=> $PAGE["tid"],
											"USER"=> $NVX_USER->FETCH_ENTRY("type")
											));
				
		/* using the TYPE template, test include file */
		$rs = $NVX_BOOT->TEST_INCLUDE(array("TYPE"=>"template","VALUE"=>$TYPE["template"]));
			
		if($rs){
						
			/* check and (if necessary) compress the css files */
			$NVX_BOOT->COMPRESS(array("TYPE"=>"css","FILES"=>$NVX_VAR->FETCH_ENTRY("css")));
			
			/* check and (if necessary) compress the javascript files */
			$NVX_BOOT->COMPRESS(array("TYPE"=>"js","FILES"=>$NVX_VAR->FETCH_ENTRY("js")));
		
			/**
			 * @instance
			 * HTML5 helpers
			 */
			$NVX_HTML = \NVOYX\site\Html5::CONNECT($NVX_BOOT,$NVX_VAR,$PAGE);
			
			/**
			 * @instance
			 * ImageCache
			 */
			$NVX_IC = \NVOYX\site\ImageCache::CONNECT($NVX_DB,$NVX_BOOT);
			
			/* start the output buffer */
			ob_start();
								
				/* load the page template */
				include($rs);
								
			/* get current buffer contents and then delete */
			$rs = ob_get_clean();
			
			/* compress the string */
			$rs = preg_replace("/\s+/", " ", $rs);
			
			/* replace any format tags */
			$rs = str_replace(array("[format:newline]","[format:tab]"),array("\n","\t"),$rs);
								
			/* start an gzip output buffer */
			ob_start("ob_gzhandler");
			
				/* we've found what we were looking for */
				$NVX_BOOT->HEADER(array("OK"=>true));
								
				/* output the final html to the buffer */
				echo $rs;
								
			/* flush the buffer */
			ob_end_flush();
		}
	} else {
		
		$NVX_REDIRECTS = \NVOYX\site\Redirects::CONNECT($NVX_DB);
		
		$rs=$NVX_REDIRECTS->RESOLVE('/'.$NVX_BOOT->FETCH_ENTRY('uri'));
		
		/* no redirect exists, so point at 404 page */
		if($rs=='/'.$NVX_BOOT->FETCH_ENTRY('uri')){
												
			/* populate PAGE based on 404 variable (refers to 404 node id) */
			$NVX_PAGE->CLEAR();
			$NVX_PAGE->FIND(array("NID"=>$NVX_VAR->FETCH_ENTRY('404')[0],
								"USER"=>$NVX_USER->FETCH_ENTRY("type"),
								"FIELDS"=>true
								));

			/* grab current PAGE variable */
			$rs = $NVX_PAGE->FETCH_ARRAY();
			
			if(isset($rs)){
			
				/* set $PAGE */
				$r=array_keys($rs);
				$PAGE = $rs[array_shift($r)];
				
				/* set $TYPE */
				$TYPE = $NVX_TYPE->FETCH_BY_TID($PAGE["tid"]);
				
				/**
				 * @instance
				 * Dept information
				 */ 
				$NVX_DEPT = \NVOYX\site\Dept::CONNECT($NVX_BOOT,$NVX_DB,$NVX_USER);
								
				/**
				 * @instance
				 * Block information
				 */
				$NVX_BLOCK = \NVOYX\site\Block::CONNECT($NVX_DB,
											$NVX_BOOT,
											$NVX_PAGE,
											$PAGE);

				/* grab ids of blocks associated with the current page tid / user type */
				$BLOCKS = $NVX_BLOCK->FETCH_ID(array("TID"=> $PAGE["tid"],
													"USER"=> $NVX_USER->FETCH_ENTRY("type")
													));

				/* using the TYPE template, test include file */
				$rs = $NVX_BOOT->TEST_INCLUDE(array("TYPE"=>"template","VALUE"=>$TYPE["template"]));

				if($rs){

					/* check and (if necessary) compress the css files */
					$NVX_BOOT->COMPRESS(array("TYPE"=>"css","FILES"=>$NVX_VAR->FETCH_ENTRY("css")));

					/* check and (if necessary) compress the javascript files */
					$NVX_BOOT->COMPRESS(array("TYPE"=>"js","FILES"=>$NVX_VAR->FETCH_ENTRY("js")));

					/**
					 * @instance
					 * HTML5 helpers
					 */
					$NVX_HTML = \NVOYX\site\Html5::CONNECT($NVX_BOOT,$NVX_VAR,$PAGE);

					/**
					 * @instance
					 * ImageCache
					 */
					$NVX_IC = \NVOYX\site\ImageCache::CONNECT($NVX_DB,$NVX_BOOT);

					/* start the output buffer */
					ob_start();

						/* load the page template */
						include($rs);

					/* get current buffer contents and then delete */
					$rs = ob_get_clean();

					/* compress the string */
					$rs = preg_replace("/\s+/", " ", $rs);

					/* replace any format tags */
					$rs = str_replace(array("[format:newline]","[format:tab]"),array("\n","\t"),$rs);

					/* start an gzip output buffer */
					ob_start("ob_gzhandler");

						/* send the 404 headers */
						$NVX_BOOT->HEADER(array("404"=>true));

						/* output the final html to the buffer */
						echo $rs;

					/* flush the buffer */
					ob_end_flush();
				}
				
				die();
			}
			
		} else {
			
			/* redirect found, so head to it */
			$NVX_BOOT->HEADER(array("301"=>$rs));
		}
		
		$NVX_BOOT->HEADER(array("LOCATION"=>"/"));
		
	}
	
} else {
	
	/* no need to cache pages from here on in. Save memory for serving public facing pages */
	
	/**
	 * @instance
	 * Path information
	 */
	$NVX_PATH = \NVOYX\site\Path::CONNECT($NVX_DB);
	
	/* are we looking at one of the standard cms pages */
	$rs = $NVX_PATH->FETCH_ENTRY("/" . implode("/",array_slice($NVX_BOOT->FETCH_ENTRY("breadcrumb"),0,3)));
	
	if($rs){
		
		/* now we need to grab the $PATH entry for this page and check user permissions before proceeding */
		if( stristr($NVX_USER->FETCH_ENTRY("type"),$rs["access"])){
			
			/* skip the next bit if we are serving an ajax page (causes cross domain issues if we flip form http to https) */
			if($NVX_BOOT->FETCH_ENTRY("breadcrumb",1)!="ajax"){
			
				/* now we need to check that we are serving as https and redirect if not */
				$NVX_BOOT->SET_PROTOCOL("https");
			}
			
			/* breadcrumb one should specify the CMS class (so push the cms namespace onto it) */
			$rs =  '\\NVOYX\\cms\\' . ucwords($NVX_BOOT->FETCH_ENTRY("breadcrumb",1));
			
			/**
			 * @instance
			 * Dept information
			 */ 
			$NVX_DEPT = \NVOYX\site\Dept::CONNECT($NVX_BOOT,$NVX_DB,$NVX_USER);
			
			/**
			 * @instance
			 * Group information
			 */ 
			$NVX_GROUP = \NVOYX\site\Group::CONNECT($NVX_DB,$NVX_BOOT);
			
			/**
			 * @instance
			 * CMS request information
			*/
			$NVX_CMS = $rs::CONNECT($NVX_BOOT,$NVX_DB,$NVX_USER,$NVX_GROUP,$NVX_DEPT);
			
			/* is this an ajax call  or a debug xcache / info call */
			if($NVX_BOOT->FETCH_ENTRY("breadcrumb",1)=="ajax" || ($NVX_BOOT->FETCH_ENTRY("breadcrumb",1)=="debug" && (
					$NVX_BOOT->FETCH_ENTRY("breadcrumb",2)=="xcache" || 
					$NVX_BOOT->FETCH_ENTRY("breadcrumb",2)=="info" ||
					$NVX_BOOT->FETCH_ENTRY("breadcrumb",2)=="database"
					))){
				
				/* test the include file using the template variable */
				$rs = $NVX_BOOT->TEST_INCLUDE(array("TYPE"=>"template","VALUE"=>$NVX_VAR->FETCH_ENTRY("ajax")[0]));
				
			} else {
			
				/* test the include file using the template variable */
				$rs = $NVX_BOOT->TEST_INCLUDE(array("TYPE"=>"template","VALUE"=>$NVX_VAR->FETCH_ENTRY("template")[0]));
			}
			
			if($rs){
				
				/* is this is not an ajax call  and not a debug call*/
				if($NVX_BOOT->FETCH_ENTRY("breadcrumb",1)!="ajax" && $NVX_BOOT->FETCH_ENTRY("breadcrumb",1)!="debug"){
				
					/* we have a valid page to serve, but before we do, delete any xcache cached user data for this website */
					$NVX_BOOT->DELETE_CACHE();
				
					/* with the cache cleared, disable it from running */
					$NVX_BOOT->SET_ENTRY("cached",false);
				}
				
				/**
				 * @instance
				 * Type information
				 */ 
				$NVX_TYPE = \NVOYX\site\Type::CONNECT($NVX_DB,
							$NVX_BOOT,
							$NVX_VAR->FETCH_ENTRY("front")[0]);
				
				/**
				 * @instance
				 * Comment information
				 */ 
				$NVX_COMMENTS = \NVOYX\site\Comments::CONNECT($NVX_DB,$NVX_BOOT);

				/**
				 * @instance
				 * Field information
				 */ 
				$NVX_FIELD = \NVOYX\site\Field::CONNECT($NVX_DB,$NVX_GROUP,$NVX_BOOT);
				
				/**
				 * @instance
				 * Page information
				 */
				$NVX_PAGE = \NVOYX\site\Page::CONNECT($NVX_DB,
										$NVX_VAR->FETCH_ENTRY("front")[0],
										$NVX_FIELD,
										$NVX_BOOT);
				
				/**
				 * @instance
				 * Block information
				 */
				$NVX_BLOCK = \NVOYX\site\Block::CONNECT($NVX_DB,
											$NVX_BOOT,
											$NVX_PAGE,
											false);
				
				/**
				 * @instance
				 * HTML5 helpers (WE COULD EXTEND NVX_HTML TO INCLUDE ALL OBJECTS IF NEEDED - MIGHT HELP WITH BLOCKS)
				 */
				$NVX_HTML = \NVOYX\site\Html5::CONNECT($NVX_BOOT,$NVX_VAR,false);
								
				/* start the output buffer */
				ob_start();
								
					/* load the page template */
					include($rs);
								
				/* get current buffer contents and then delete */
				$rs = ob_get_clean();
								
				/* compress the string */
				$rs = preg_replace("/\s+/", " ", $rs);
				
				/* replace any format tags */
				$rs = str_replace(array("[format:newline]","[format:tab]"),array("\n","\t"),$rs);
								
				/* start an gzip output buffer */
				ob_start("ob_gzhandler");
					
					/* we've found what we were looking for */
					$NVX_BOOT->HEADER(array("OK"=>true));
				
					/* output the final html to the buffer */
					echo $rs;
								
				/* flush the buffer */
				ob_end_flush();
			}

			die();
		}
	}
}
