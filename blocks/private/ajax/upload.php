<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * uploader for imagelist,filelist and videolist
 */

/* is this an imagelist upload */
if(array_key_exists("imagelist",$_FILES)){
	
	/* does the file have a name, is the upload size greater than zero, is the file of an allowed mime type, do we have a temp file name */
	if($_FILES['imagelist'] != "none" && 
	isset($_FILES['imagelist']['name']) && 
	$_FILES['imagelist']["size"] != 0 &&
	stristr($_POST["ftypes"],$_FILES['imagelist']["type"]) &&
	is_uploaded_file($_FILES['imagelist']["tmp_name"])){
		
		/* array of file extensions to exclude */
		$exts = array("..","/",".php",".html",".shtml",".phtml",".php3",".php4",".htaccess");
		
		/* end if the file has an excluded extension */
		$f=0; foreach($exts as $ext){if(stristr($_FILES['imagelist']['name'],$ext)){$f=1;break 2;}}
		
		/* file extension is ok */
		if($f==0){
			
			/* grab the current microstamp */
			$now = $NVX_BOOT->FETCH_ENTRY("microstamp");
			
			/* grab the path to the images folder */
			$fpath =  $NVX_BOOT->FETCH_ENTRY("images");
			
			/* grab the image extension */
			$ext = pathinfo($_FILES['imagelist']['name'], PATHINFO_EXTENSION);
			
			/* create a complete path / name /extension for the image */
			$image = "{$fpath}/{$now}.{$ext}";
			
			/* move the temp file to the new image location */
			$move = @move_uploaded_file($_FILES['imagelist']['tmp_name'], $image);
			
			/* if the move is successful */
			if($move){
				
				/* create new media instance */
				$NVX_MEDIA = \NVOYX\site\Media::CONNECT($NVX_BOOT);
					
				/* convert the image to a webp and compress */
				$img = $NVX_MEDIA->CWEBP(array("MIME"=>'png',"FILE"=>$image));
				
				/* create an URL to the new image */
				$retimg = "/settings/resources/files/images/cms/".$img;
				
				/* pass the details back to the calling javascript */
				echo "*START*{$img}*{$retimg}*{$img}*END*";
			}
		}
	}
}


/* is this a filelist upload */
if(array_key_exists("filelist",$_FILES)){
	
	/* does the file have a name, is the upload size greater than zero, is the file of an allowed mime type, do we have a temp file name */
	if($_FILES['filelist'] != "none" && 
	isset($_FILES['filelist']['name']) && 
	$_FILES['filelist']["size"] != 0 &&
	stristr($_POST["ftypes"],$_FILES['filelist']["type"]) &&
	is_uploaded_file($_FILES['filelist']["tmp_name"])){
		
		/* array of file extensions to exclude */
		$exts = array("..","/",".php",".html",".shtml",".phtml",".php3",".php4",".htaccess");
		
		/* end if the file has an excluded extension */
		$f=0; foreach($exts as $ext){if(stristr($_FILES['filelist']['name'],$ext)){$f=1;break 2;}}
		
		/* file extension is ok */
		if($f==0){
			
			/* grab the current microstamp */
			$now = $NVX_BOOT->FETCH_ENTRY("microstamp");
			
			/* grab the path to the filelist folder */
			$fpath =  $NVX_BOOT->FETCH_ENTRY("documents");
			
			/* grab the file extension */
			$ext = pathinfo($_FILES['filelist']['name'], PATHINFO_EXTENSION);
			
			/* grab the file name */
			$friendly = pathinfo($_FILES['filelist']['name'], PATHINFO_FILENAME);
			
			/* create a complete path / name /extension for the file */
			$file = "{$fpath}/{$now}.{$ext}";
			
			/* move the temp file to the new video location */
			$move = @move_uploaded_file($_FILES['filelist']['tmp_name'], $file);
			
			/* if the move is successful */
			if($move){
				
				/* grab the filesize */
				$fs = filesize($file);
				
				/* create an array of common filesize units of measurement */
				$sz = array("Bytes","Kb","MB","GB","TB","PB");
				
				/* filesize order of magnitude */
				$factor = floor((strlen($fs) -1) / 3);
				
				/* base the number of decimals to be shown by the order of magnitude */
				switch($factor):
					case 0:
					case 1:
						$decimals = 0;
						break;
					case 2:
						$decimals = 1;
						break;
					default:
						$decimals = 2;
						break;
				endswitch;
				
				/* convert the filesize (in bytes) to something more readable */
				$size = sprintf("%.{$decimals}f", $fs / pow(1024, $factor)) . @$sz[$factor];
				
				/* convert the file path to an URL */
				$file = str_replace($fpath."/","",$file);
				
				/* pass the details back to the calling javascript */
				echo "*START*{$file}*.{$ext}*{$friendly}*{$size}*END*";
			}
		}
	}
}