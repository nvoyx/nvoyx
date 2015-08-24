<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * uploader for imagelist,filelist
 */

$response=array(
	'error'=>'<p><b>Oops</b>: Something went wrong',
	'console'=>0
);

/* is this an imagelist upload */
if(array_key_exists("imagelist",$_FILES)){
		
	$post = $NVX_BOOT->TEXT($_POST['data']);
			
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
				$img = $NVX_MEDIA->CWEBP(array("FILE"=>$image));
				
				/* create an URL to the new image */
				$retimg = "/settings/resources/files/images/cms/".$img;
				
				/* grab the name only of the image */
				$imgname = str_replace('.webp','',$img);
				
				/* sync the file with other servers on the network */
				$NVX_BOOT->SYNC('images/cms/'.$img,'file');
				
				/* set the response error to zero as all is good */
				$response['error']=0;
				
				/* the extra label should be in uppercase */
				$post['elabel']=ucwords($post['elabel']);
				
				/* buld the html to be returned */
				$response['html']='';
				
$response['html'].=<<<HTML
<li class='col all100 pad10 {$post['bc']}'>
	<input type='hidden' name='{$post['nuid']}name' id='{$post['nuid']}name' value='{$imgname}' >

	<!-- IMAGE THUMBNAIL -->
	<div class='col sml100 med33 lge33 pad-r10 sml-pad-r0 pad-b20'>
		<label class='col all100 fs13 c-white pad-b5 grip bw'>&#8597;&nbsp;&nbsp;Thumbnail</label>
		<a target='_blank' href='/settings/resources/files/images/cms/0x0/{$imgname}.png'>
			<img class="col hgt30 brd-blue" src="/settings/resources/files/images/cms/0x30/{$imgname}.png">
		</a>
		<a onclick="deleteListItem(this);" class='fs14 c-white pad-l10' style="line-height:30px;">Delete</a>
	</div>

HTML;
				/* if the image doesn't have an associated link, we can make the description field 66% on lge view */
				if($post["lnk"]==1){
					$lge=33;
					$lgepad='';
				} else {
					$lge=66;
					$lgepad=' lge-pad-r0';
				}
				
$response['html'].=<<<HTML
	<!-- IMAGE DESCRIPTION -->
	<div class='col sml100 med66 lge{$lge} pad-r10 sml-pad-r0 med-pad-r0{$lgepad} pad-b20'>
		<label class='col all100 fs13 c-white pad-b5'>Description</label>
		<input class='col all100 fs14 tb' name='{$post['nuid']}desc' id='{$post['nuid']}desc' type='text' maxlength='1024' value='' placeholder='Description'>
	</div>

HTML;

				/* does this imagelist have the link field enabled */
				if($post["lnk"]==1){
					
$response['html'].=<<<HTML
	<!-- IMAGE LINK -->
	<div class='col sml100 med100 lge33 pad-b20'>
		<label class='col all100 fs13 c-white pad-b5'>Link</label>
		<input class='col all100 fs14 tb' name='{$post['nuid']}link' id='{$post['nuid']}link' type='text' maxlength='255' value='' placeholder='Link'>
	</div>

HTML;
				}

				/* does this imagelist have an additional field associated with it */
				if($post["etype"]!="none"){

					/* is the additional textarea for plain or html text */
					if($post["etype"]=="plain"){$r="plain";$post["eeditor"]="";}else{$r="html";}
									
$response['html'].=<<<HTML
	<!-- IMAGE ADDITIONAL TEXT -->
	<div class='col all100 pad-b20'>
		<label class='col all100 fs13 c-white pad-b5'>{$post['elabel']}</label>
		<div class='col all100'>
			<textarea class='col all100 fs14 ta {$post["eeditor"]} {$r}' data-editor='{$post["eeditor"]}' name='{$post['nuid']}text{$r}' id='{$post['nuid']}text{$r}' maxlength='100000'></textarea>
		</div>
	</div>

HTML;
				}
				
				$response['html'].='</li>';
			}
		}
	}
	
	/* convert the response array to a json string and pass it back */
	echo $NVX_BOOT->JSON($response,'encode');
	die();
}


/* is this a filelist upload */
if(array_key_exists("filelist",$_FILES)){
	
	$post = $NVX_BOOT->TEXT($_POST['data']);
	
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
				
				/* sync the file with other servers on the network */
				$NVX_BOOT->SYNC('documents/'.$now.'/'.$ext,'file');
				
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
				
				/* set the response error to zero as all is good */
				$response['error']=0;
				
				/* buld the html to be returned */
				$response['html']='';
				
$response['html'].=<<<HTML
	<li class='col all100 pad10 {$post['bc']}'>
		<input type='hidden' name='{$post['nuid']}name' id='{$post['nuid']}name' value='{$file}'>
		<input type='hidden' name='{$post['nuid']}size' id='{$post['nuid']}size' value='{$size}'>
		<input type='hidden' name='{$post['nuid']}type' id='{$post['nuid']}type' value='{$ext}'>
		
		<!-- FILE -->
		<div class='col all100 pad-b20'>
			<label class='col all100 fs13 c-white pad-b5 grip bw'>&#8597;&nbsp;&nbsp;Description</label>
			<input class='col all100 fs14 tb mar-b5' name='{$post['nuid']}desc' id='{$post['nuid']}desc' type='text' maxlength='1024' value='' placeholder='Description'>
			<a class='fs14 c-white' target='_blank' href='/settings/resources/files/documents/{$file}'>Download</a>&nbsp;&nbsp;
			<a class='fs14 c-white' onclick="deleteListItem(this);">Delete</a>
		</div>
	</li>
HTML;

			}
		}
	}
	
	/* convert the response array to a json string and pass it back */
	echo $NVX_BOOT->JSON($response,'encode');
	die();
}