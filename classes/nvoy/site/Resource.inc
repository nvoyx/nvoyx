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

namespace nvoy\site;

class Resource{
	
	protected static $instance;
	protected static $db;
	protected static $boot;
	protected static $user;
	protected static $var;
	protected static $ic;
	
	
	/* --- PUBLIC METHODS --- */

	
    public static function connect($nvDb,$nvBoot,$nvUser,$nvVar,$nvIc) {
		if( !self::$instance ) {
			self::$db = $nvDb;
			self::$boot = $nvBoot;
			self::$user = $nvUser;
			self::$var = $nvVar;
			self::$ic = $nvIc;
            self::$instance = new self();
        }
        return self::$instance;
    }

	
	/* check then return the requested files / image / resource (all requests pass through here) */
	public function fetch() {
		$maxage=0;
		$rs=self::$boot->fetch_entry("resources") . "/" . implode("/",array_slice(self::$boot->fetch_entry("breadcrumb"),2));
		if(file_exists($rs)){
			$a = array("/resources/files/images/ckeditor/",
						"/resources/files/images/cms/",
						"/resources/files/images/jquery-ui/",
						"/resources/files/images/mimes/",
						"/resources/files/compress/");
			foreach($a as $p){if(stristr($rs,$p)){$maxage=31536000;break;}}
			$finfo = new \finfo(FILEINFO_MIME_TYPE);
			$ftype = $finfo->file($rs);
			if($ftype!=""){
				if(stristr($ftype,"text/")){
					$ext = strrchr($rs,".");
					$a = array(".js"=>"text/javascript",
								".css"=>"text/css",
								".html"=>"text/html",
								".htm"=>"text/html",
								".php"=>"application/php",
								".htc"=>"text/x-component");
					if(isset($a["$ext"])){
						$ftype=$a[$ext];
						if($ftype=="application/php"){
							$r = "/" . implode("/",self::$boot->fetch_entry("breadcrumb"));
							$a = self::$var->fetch_entry("php resources");
							$a[]="/settings/resources/honeypot/" . self::$var->fetch_entry("honeyfile")[0];
							if(in_array($r,$a)){
								include($rs);
							}
							die();
						}
					}
					if(stristr($ftype,"text/")){$ftype.=";charset=UTF-8";}
				}
				
				if($ftype=="application/octet-stream"){
					$a = array(".svg"=>"image/svg+xml",
								".woff"=>"application/font-woff",
								".eot"=>"application/vnd.ms-fontobject",
								".ttf"=>"application/x-font-ttf",
								".oft"=>"application/x-font-opentype");
					$ext=strrchr($rs,".");
					if(isset($a[$ext])){
						$ftype=$a[$ext];
					}
				}
				
				ob_start();
				if($maxage==0){
					$r = self::$boot->header(array("CTYPE"=>$ftype,"LASTMOD"=>filemtime($rs)));
				} else {
					$r = self::$boot->header(array("CTYPE"=>$ftype,"LASTMOD"=>filemtime($rs),"MAXAGE"=>$maxage));
				}
								
				if($r==1){
					$rs =  str_replace(self::$boot->fetch_entry("resources"),"/nginx_protected_files",$rs);
					header('X-Accel-Redirect: '.$rs);
				}
				
				ob_end_flush();
			}
		} else {
			if(implode("/",array_slice(self::$boot->fetch_entry("breadcrumb"),0,5))=="settings/resources/files/images/cms"){
				if(is_dir(self::$boot->fetch_entry("core") . "/" . implode("/",array_slice(self::$boot->fetch_entry("breadcrumb"),1,5)))){
					$dims = explode("x",self::$boot->fetch_entry("breadcrumb",5));
					$source = self::$boot->fetch_entry("core") . "/" . implode("/",array_slice(self::$boot->fetch_entry("breadcrumb"),1,4)) . "/" .str_replace(array("@2x.",".png",".jpg"),array("",".webp",".webp"),self::$boot->fetch_entry("breadcrumb",6));
					if(file_exists($source)){
						$cache_found=false;
						foreach(self::$ic->fetch_array() as $ic){
							if($ic["x"]==$dims[0] && $ic["y"]==$dims[1]){
								if(stristr(self::$boot->fetch_entry("breadcrumb",6),$ic['mime'])){
									$cache_found=true;
								}
								break;
							}
						}

						if($cache_found==false){die();}
						copy($source,str_replace(array("@2x.",".png",".jpg"),array("",".webp",".webp"),$rs));
						if(stristr(self::$boot->fetch_entry("breadcrumb",6),"@2x.")===false){$hipd = false;
						} else {$hipd=true;}

						$nvMedia = \nvoy\site\Media::connect(self::$boot);
						$nvMedia->dwebp(array("MIME"=>$ic["mime"],"FILE"=>str_replace(array("@2x.",".png",".jpg"),array("",".webp",".webp"),$rs)));
						unlink(str_replace(array("@2x.",".png",".jpg"),array("",".webp",".webp"),$rs));
						$nvMedia->image(array("FILE"=>str_replace("@2x.","",$rs),"X"=>$dims[0],"Y"=>$dims[1],"MIME"=>$ic["mime"],"EFFECTS"=>$ic["effects"],"HIPD"=>$hipd));

						$finfo = new \finfo(FILEINFO_MIME_TYPE);
						$ftype = $finfo->file($rs);
						$maxage=31536000;
						
						ob_start();
						$r = self::$boot->header(array("CTYPE"=>$ftype,"LASTMOD"=>filemtime($rs),"MAXAGE"=>$maxage));
						if($r==1){
							$rs =  str_replace(self::$boot->fetch_entry("resources"),"/nginx_protected_files",$rs);
							header('X-Accel-Redirect: '.$rs);
						}
						ob_end_flush();	
					}					
				}
			}
		}
		die();
	}
}