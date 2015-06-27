<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * upload an image via ckeditor
 */

/* image to be returned */
$retimg = "";

/* ckeditor generated function number */
$funcnum =  filter_var($_GET['CKEditorFuncNum'], FILTER_SANITIZE_NUMBER_INT);

/* error message */
$error = "upload failed";

/* are we trying to upload a file */
if(key_exists("upload",$_FILES)){
	
	/* does the file have a name, is the upload size greater than zero, is the file of an allowed mime type, do we have a temp file name */
	if($_FILES['upload'] != "none" && 
	isset($_FILES['upload']['name']) && 
	$_FILES['upload']["size"] != 0 &&
	($_FILES['upload']["type"] == "image/pjpeg" || $_FILES['upload']["type"] == "image/jpeg" || $_FILES['upload']["type"] == "image/png" || $_FILES['upload']["type"] == "image/gif") &&
	is_uploaded_file($_FILES['upload']["tmp_name"])){
		
		/* grab a list of invalid file extensions */
		$exts = array("..","/",".php",".html",".shtml",".phtml",".php3",".php4",".htaccess");
		
		/* set a file validity flag */
		$f=0;
		
		/* cycle through the invalid extensions and if found in the filename, set the flag */
		foreach($exts as $ext){if(stristr($_FILES['upload']['name'],$ext)){$f=1;break 2;}}
		
		/* proceed if no flag has been set */
		if($f==0){
			
			/* grab the page microstamp */
			$now = $NVX_BOOT->FETCH_ENTRY("microstamp");
			
			/* grab a path to the directory where ckeditor uploaded images are to be stored */
			$fpath =  $NVX_BOOT->FETCH_ENTRY("ckimages");
			
			/* hook into a mime-type library */
			$ext = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
			
			/* create the uploaded file name to be used */
			$ckimage = "{$fpath}/{$now}.{$ext}";
			
			/* move the temp file to the desired location */
			$move = @move_uploaded_file($_FILES['upload']['tmp_name'], $ckimage);
			
			/* on successful move */
			if($move){
				
				/* connect to the media class */
				$NVX_MEDIA = Media::CONNECT($NVX_BOOT);
				
				/* convert the image to a png (if necessary) */
				$img = $NVX_MEDIA->IMAGE(array("MIME"=>"png","FILE"=>$ckimage));
				
				/* update the return image details */
				$retimg = "/settings/resources/files/images/ckeditor/".$img;
				
				/* set the error message to blank */
				$error="";
			}
		}
	}
}
echo "<script>window.parent.CKEDITOR.tools.callFunction({$funcnum}, '{$retimg}','{$error}');</script>";