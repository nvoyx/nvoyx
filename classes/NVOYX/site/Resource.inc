<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/** 
 * @class
 * Collection of resource bumf
 */

namespace NVOYX\site;

class Resource{
	
	protected static $instance;
	protected static $DB;
	protected static $BOOT;
	protected static $USER;
	protected static $VAR;
	protected static $IC;
	
	
	/* --- PUBLIC METHODS --- */

	
	/*
	 * CONNECT()
	 * connect to this class
	 */
    public static function CONNECT($db,$boot,$user,$var,$ic) {
        
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/db */
			self::$DB = $db;
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* link with public/user */
			self::$USER = $user;
			
			/* link with public/variables */
			self::$VAR = $var;
			
			/* link with public/imagecache */
			self::$IC = $ic;
			
			/* create a new instance */
            self::$instance = new self();
        }
		
		/* return instance */
        return self::$instance;
    }

	
	/*
	 * FETCH()
	 * check then return the requested files / image / resource (all requests pass through here)
	 */
	public function FETCH() {
		
		/* set the cache control max age flag to zero (off) */
		$maxage=0;
		
		/* check if the file exists */
		$rs=self::$BOOT->FETCH_ENTRY("resources") . "/" . implode("/",array_slice(self::$BOOT->FETCH_ENTRY("breadcrumb"),2));
		
		/* if the file exists */
		if(file_exists($rs)){
						
			/* files from certain directories can always be cached with 1 year expiries */
			$a = array("/resources/files/images/ckeditor/",
						"/resources/files/images/cms/",
						"/resources/files/images/jquery-ui/",
						"/resources/files/images/mimes/",
						"/resources/files/compress/");
			
			/* are we looking for a file in one of those directories */
			foreach($a as $p){if(stristr($rs,$p)){$maxage=31536000;break;}}
			
			/* grab the mime type of the requested file */
			$finfo = new \finfo(FILEINFO_MIME_TYPE);
			$ftype = $finfo->file($rs);
						
			/* do we have a valid mime */
			if($ftype!=""){
				
				/* has a text file been requested */
				if(stristr($ftype,"text/")){
					
					/* grab the file extension */
					$ext = strrchr($rs,".");
										
					/* php is rubbish at guessing javascript,css and html files, so manually set them */
					$a = array(".js"=>"text/javascript",
								".css"=>"text/css",
								".html"=>"text/html",
								".htm"=>"text/html",
								".php"=>"application/php",
								".htc"=>"text/x-component");
					
					/* are we working with a file of this type */
					if(isset($a["$ext"])){
						
						/* manually set the mime type */
						$ftype=$a[$ext];
						
						/* is a php file being requested */
						if($ftype=="application/php"){
							
							/* external php files are spellchecker,captcha and the four cron files */
							$r = "/" . implode("/",self::$BOOT->FETCH_ENTRY("breadcrumb"));
							
							/* array of valid php paths */
							$a = self::$VAR->FETCH_ENTRY("php resources");
							$a[]="/settings/resources/honeypot/" . self::$VAR->FETCH_ENTRY("honeyfile")[0];
							
							/* do we have a valid php file */
							if(in_array($r,$a)){
								
								/* include the php file */
								include($rs);
							}
							
							/* stop */
							die();
						}
					}
					
					/* if we have a text mime, tag the utf-8 chrset onto the request */
					if(stristr($ftype,"text/")){$ftype.=";charset=UTF-8";}
				}
				
				/* nginx defaults to this so run some checks */
				if($ftype=="application/octet-stream"){
					
					/* do we have a web font */
					$a = array(".svg"=>"image/svg+xml",
								".woff"=>"application/font-woff",
								".eot"=>"application/vnd.ms-fontobject",
								".ttf"=>"application/x-font-ttf",
								".oft"=>"application/x-font-opentype");
					$ext=strrchr($rs,".");
					
					/* if we do have a webfont */
					if(isset($a[$ext])){
						
						/* update the mime type */
						$ftype=$a[$ext];
					}
				}
				
				/* grab the output */
				ob_start();
				
				/* set the file headers */
				if($maxage==0){
					
					/* associate headers with this file */
					$r = self::$BOOT->HEADER(array("CTYPE"=>$ftype,"LASTMOD"=>filemtime($rs)));
				} else {
					
					/* associate headers with this file */
					$r = self::$BOOT->HEADER(array("CTYPE"=>$ftype,"LASTMOD"=>filemtime($rs),"MAXAGE"=>$maxage));
				}
								
				/* read the file if the header check requires content to be sent/refreshed */
				if($r==1){
							
					/* build a string to the protected files */
					$rs =  str_replace(self::$BOOT->FETCH_ENTRY("resources"),"/nginx_protected_files",$rs);
					header('X-Accel-Redirect: '.$rs);
				}
				
				/* send it down the line */
				ob_end_flush();
			}
		} else {
			
			/* are we trying to call an image */
			if(implode("/",array_slice(self::$BOOT->FETCH_ENTRY("breadcrumb"),0,5))=="settings/resources/files/images/cms"){
								
				/* do we have an imagecache folder */
				if(is_dir(self::$BOOT->FETCH_ENTRY("core") . "/" . implode("/",array_slice(self::$BOOT->FETCH_ENTRY("breadcrumb"),1,5)))){
					
					/* grab an array of the required dimensions */
					$dims = explode("x",self::$BOOT->FETCH_ENTRY("breadcrumb",5));
					
					/* do we have an original image to work with */
					$source = self::$BOOT->FETCH_ENTRY("core") . "/" . implode("/",array_slice(self::$BOOT->FETCH_ENTRY("breadcrumb"),1,4)) . "/" .str_replace(array("@2x.",".png",".jpg"),array("",".webp",".webp"),self::$BOOT->FETCH_ENTRY("breadcrumb",6));
					
					if(file_exists($source)){

						$cache_found=false;

						/* cycle through the imagecache array and grab the effects array where x and y keys equal the folder dims */
						foreach(self::$IC->FETCH_ARRAY() as $ic){
							
							if($ic["x"]==$dims[0] && $ic["y"]==$dims[1]){

								/* is the requested image of the correct mime for the requested image cache ie. png or jpg */
								if(stristr(self::$BOOT->FETCH_ENTRY("breadcrumb",6),$ic['mime'])){
									$cache_found=true;
								}
								break;
							}
						}

						/* if the cache was not found or the mime was incorrect - die */
						if($cache_found==false){die();}
						
						/* make a copy of the requested image and place it in the imagecache folder */
						copy($source,str_replace(array("@2x.",".png",".jpg"),array("",".webp",".webp"),$rs));
						
						/* has the hi pixel density version of the image been requested */
						if(stristr(self::$BOOT->FETCH_ENTRY("breadcrumb",6),"@2x.")===false){$hipd = false;
						} else {$hipd=true;}
												
						/**
						 * @instance
						 * System Variables
						 */
						$NVX_MEDIA = \NVOYX\site\Media::CONNECT(self::$BOOT);
						
						$NVX_MEDIA->DWEBP(array("MIME"=>$ic["mime"],"FILE"=>str_replace(array("@2x.",".png",".jpg"),array("",".webp",".webp"),$rs)));
							
						unlink(str_replace(array("@2x.",".png",".jpg"),array("",".webp",".webp"),$rs));
						
						/* scale the image */
						$NVX_MEDIA->IMAGE(array("FILE"=>str_replace("@2x.","",$rs),"X"=>$dims[0],"Y"=>$dims[1],"MIME"=>$ic["mime"],"EFFECTS"=>$ic["effects"],"HIPD"=>$hipd));
						
						/* grab the mime type of the requested file */
						$finfo = new \finfo(FILEINFO_MIME_TYPE);
						$ftype = $finfo->file($rs);
						
						/* set the cache control max-age to one year */
						$maxage=31536000;
						
						/* grab the output */
						ob_start();
										
						/* set the file headers */
						$r = self::$BOOT->HEADER(array("CTYPE"=>$ftype,"LASTMOD"=>filemtime($rs),"MAXAGE"=>$maxage));
				
						/* read the file if the header check requires content to be sent/refreshed */
						if($r==1){
							
							/* build a string to the protected files */
							$rs =  str_replace(self::$BOOT->FETCH_ENTRY("resources"),"/nginx_protected_files",$rs);
							header('X-Accel-Redirect: '.$rs);
						}
						
						/* send it down the line */
						ob_end_flush();	
					}					
				}
			}
		}
		
		/* stop */
		die();
	}
}