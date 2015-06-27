<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * ckeditor browse uploaded images
 */

/* grab the path where ckeditor uploaded images are stored */
$fpath =  $NVX_BOOT->FETCH_ENTRY("ckimages");

/* grab the function number as supplied by ckeditor */
$funcnum =  filter_var($_GET['CKEditorFuncNum'], FILTER_SANITIZE_NUMBER_INT);

/* open the ckeditor image directory */
if ($h = opendir($fpath)) {
	
	/* cycle through the files in this directory */
	while (false !== ($f = readdir($h))) {
		
		/* skip over the directory navigation references */
		if ($f != "." && $f != "..") {
			
			/* hook into mime type library */
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			
			/* grab the mime type of the current file */
			$mt=finfo_file($finfo, "{$fpath}/{$f}");

			/* close the mime type connection */
			finfo_close($finfo);
			
			/* only interested in png and jpg files */
			if($mt=="image/png"){
				
				/* grab the extension at the end of the file */
				$ext = substr(strrchr($f, "."), 1);
				
				/* only interested in files ending with png or jpg */
				if($ext=="png"){
					
					/* grab details of the image */
					$imgs[]="{$fpath}/{$f}";
				}
			}
		}
	}
	/* close the directory connection */
	closedir($h);
}

?>

	
<style>
	.thumbnail {display:block;width:180px;height:120px;vertical-align:middle;text-align:center;float:left;background-color:#eee;margin:0 10px 10px 0; padding:0 0;border:0 none;cursor:pointer;overflow:hidden;}
	.thumbnail:hover {background-color:#ceddf2;}
	img {display:block;padding:0 0;border:0 none;}
</style>
<script>
	function useImg(url){
		window.opener.CKEDITOR.tools.callFunction(<?php echo $funcnum;?>, url);
		self.close();
	}
</script>
	
<?php

/* do we have any images to display */
if(isset($imgs[0])){
	
	/* function used by usort to put the images in order of creation date (newest first) */
	function sortbyChangeTime($file1,$file2){return (filectime($file1) > filectime($file2));}
	
	/* sort the images as per the function above */
	usort($imgs,'sortbyChangeTime');
	
	/* cycle over the images */
	foreach($imgs as $img){
		
		/* grab dims of current image */
		list($iw, $ih, $it, $ia)=getimagesize("{$img}");
		
		/* scale the image to fit (entitrely) in a div 180x120 */
		if($iw>=$ih){$r=$iw;}else{$r=$ih;}
		$iwr=$iw/$r;
		$ihr=$ih/$r;
		$tw=180;$th=120;
		if($iw>=$ih){
			$nw=$tw;
			$nh=floor($nw*$ihr);
			$mt=floor(($th-$nh)/2) . "px";
			$ml="0px";
		
		} else {
			$nh=$th;
			$nw=floor($nh*$iwr);
			$mt="0px";
			$ml=floor(($tw-$nw)/2) . "px";
		}
		?>
		<a class='thumbnail' onclick='useImg("<?php echo str_replace($fpath,"/settings/resources/files/images/ckeditor",$img);?>");return false;'>
			<img src='<?php echo str_replace($fpath,"/settings/resources/files/images/ckeditor",$img);?>' width='<?php echo $nw; ?>' height='<?php echo $nh; ?>' style="display:block;margin:<?php echo $mt;?> 0 0 <?php echo $ml;?>" />
		</a>
	<?php }
}