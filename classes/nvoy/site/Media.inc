<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/** 
 * Media()
 * Collection of media bumf
 */

namespace nvoy\site;

class Media{
	
	protected static $instance;
	protected static $boot;
	
	
	/* --- PUBLIC METHODS --- */
	
		
	public static function connect($nvBoot) {
		if( !self::$instance ) {
			self::$boot = $nvBoot;
			self::$instance = new self();
		}
		return self::$instance;
	}

	/* take a passed webp and produce a png version */
	public function dwebp($a){
		$ext = pathinfo($a["FILE"], PATHINFO_EXTENSION);
		$name = pathinfo($a["FILE"], PATHINFO_FILENAME);
		$path = pathinfo($a["FILE"], PATHINFO_DIRNAME);
		system(self::$boot->fetch_entry("dwebp")." {$a["FILE"]} -o {$path}/{$name}.png");
		if($a["MIME"]=='jpg'){		
			$image = imagecreatefrompng("{$path}/{$name}.png");
			$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
			imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
			imagealphablending($bg, TRUE);
			imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
			imagedestroy($image);
			$quality = 100; // 0 = worst / smaller file, 100 = better / bigger file 
			imagejpeg($bg, "{$path}/{$name}.jpg", $quality);
			imagedestroy($bg);
			unlink("{$path}/{$name}.png");
			return $name.".jpg";
		} else {
			return $name.".png";
		}
	}
	
	/* take a passed image and produce webp version */
	public function cwebp($a){
		$ext = pathinfo($a["FILE"], PATHINFO_EXTENSION);
		$name = pathinfo($a["FILE"], PATHINFO_FILENAME);
		$path = pathinfo($a["FILE"], PATHINFO_DIRNAME);
		system(self::$boot->fetch_entry("cwebp")." -q 80 -metadata none {$a["FILE"]} -o {$path}/{$name}.webp");
		unlink($a["FILE"]);
		return $name.".webp";
	}
	
	 /* take a passed image and test for which is the longest axis returns x,y or s */
	public function image_orientation($i){
		$im = new \Imagick();
		$im->readImage($i);
		$dim = $im->getImageGeometry();
		if($dim["width"]>$dim["height"]){
			return "x";
		} elseif($dim["width"]<$dim["height"]){
			return "y";
		} else {return "s";}
	}
	
	/* take a passed image and produce a scaled/cropped version desired effects applied */
	public function image($a) {
		$ext = pathinfo($a["FILE"], PATHINFO_EXTENSION);
		$name = pathinfo($a["FILE"], PATHINFO_FILENAME);
		$path = pathinfo($a["FILE"], PATHINFO_DIRNAME);
		$im = new \Imagick();
		$im->readImage($a["FILE"]);
		if(array_key_exists("X",$a) && array_key_exists("Y",$a)){
			if($a['X']==0 && $a['Y']==0){
			} else {
				$a["X"] = $a["X"]*2;
				$a["Y"] = $a["Y"]*2;
				if($a['X']==0 || $a['Y']==0){
					$im->resizeImage($a['X'],$a['Y'],\Imagick::FILTER_LANCZOS,1);
				} else {
					$im->cropThumbnailImage($a["X"],$a["Y"]);
				}
				$im->setImagePage(0, 0, 0, 0);
			}
		}
		
		if($im->getImageFormat()!=$a['MIME']){$im->setImageFormat($a['MIME']);}
		if(array_key_exists("EFFECTS",$a)){
			if(is_array($a["EFFECTS"])){
				foreach($a["EFFECTS"] as $efkey=>$efval){
					if(!is_numeric($efkey)){
						if($efkey=="ROUNDED"){
							$im->roundCorners($efval,$efval);
						}

						if($efkey=="OPACITY"){
							$im->setImageOpacity($efval);
						}

						if($efkey=="SATURATION"){
							$im->modulateImage(100,$efval,100);
						}

						if($efkey=="OVERLAY"){
							$gradient = new \Imagick();
							$gradient->newPseudoImage(1, 100, "gradient:{$efval}");
							$im->modulateImage(100,0,100);
							$im->clutImage($gradient);
							$gradient->destroy();
						}

						if($efkey=="INSTA"){
							$cima = clone $im;
							$cimb = clone $im;
							$cima->colorizeImage("#222b6d",1);
							$im->compositeImage( $cima, \Imagick::COMPOSITE_COPYBLUE, 0, 0,\Imagick::CHANNEL_BLACK);
							$cimb->colorizeImage("#f7daae",1);
							$im->compositeImage( $cimb, \Imagick::COMPOSITE_COPYYELLOW, 0, 0 ,\Imagick::CHANNEL_ALL);
							$im->contrastImage(1);
							$im->modulateImage(110,100,100);
							$cima->destroy();
							$cimb->destroy();
						}

						if($efkey=="TILTSHIFT"){
							$qr = $im->getQuantumRange();
							$qr = $qr['quantumRangeLong'];
							$im->sigmoidalContrastImage(true, 7, $qr / 3, \Imagick::CHANNEL_ALL);
							$blurmap = new \Imagick();
							$blurmap->setOption('compose:args', '5');
							$blurmap->newPseudoImage($im->getImageWidth(), $im->getImageHeight(), 'gradient:black-white');
							$blurmap->functionImage(\Imagick::FUNCTION_POLYNOMIAL, array(4.5, -4.5, 1));
							$blurmap->levelImage(0, 1, $qr / 3);
							$im->compositeImage($blurmap, 57, 0, 0);
							$blurmap->destroy();
						}

						if($efkey=="VIGNETTE"){
							$crop = 1.5;
							$color="rgb(184,184,184)";
							$comp = \Imagick::COMPOSITE_COLORBURN;
							/* comp could also be */
							/*
							 *  Imagick::COMPOSITE_COLORBURN
							 *  Imagick::COMPOSITE_MULTIPLY
							 * etc...
							 */
							$h = $im->getImageHeight();
							$w = $im->getImageWIdth();
							$crop_x = floor($h * $crop);
							$crop_y = floor($w * $crop);
							$overlay = new \Imagick();
							$overlay->newPseudoImage($crop_x, $crop_y, "radial-gradient:rgba(0,0,0,0)-{$color}");
							$im->compositeImage($overlay, $comp, ($w - $crop_x) / 2, ($h - $crop_y)/2);
						}
					}
				}
			}
		}
		$im->setImageDepth(8);
		$im->stripImage();
		if($a['MIME']=='jpg'){
			$im->setImageCompression(\Imagick::COMPRESSION_JPEG); 			
		} else {
			$im->setImageCompression(\Imagick::COMPRESSION_UNDEFINED);
			$im->setImageCompressionQuality(0);
		}
		
		if(array_key_exists("X",$a) && array_key_exists("Y",$a)){
			if($a['X']==0 && $a['Y']==0){
			} else {
				if($a['MIME']=='jpg'){
					$im->setImageCompressionQuality(90);
					$im->writeImage($path."/@2x.".$name.".jpg");
					$im->resizeImage(ceil($a['X']/2),ceil($a['Y']/2),\Imagick::FILTER_LANCZOS,1);
					$im->setImagePage(0, 0, 0, 0);
				} else {
					$im->writeImage($path."/@2x.".$name.".png");
					$im->scaleImage(ceil($a['X']/2),ceil($a['Y']/2));
					$im->setImagePage(0, 0, 0, 0);
				}
			}
		}
		
		if($a['MIME']=='jpg'){
			$im->setImageCompressionQuality(95);
			$im->writeImage($path."/".$name.".jpg");
			if($ext!="jpg"){unlink($a["FILE"]);}
		} else {
			$im->writeImage($path."/".$name.".png");
			if($ext!="png"){unlink($a["FILE"]);}
		}

		if(array_key_exists("HIPD",$a)){
			if($a["HIPD"]){
				if($a['MIME']=='jpg'){
					return "@2x".$name.".jpg";
				} else {
					return "@2x".$name.".png";
				}
			}
		}
		
		if($a['MIME']=='jpg'){
			return $name.".jpg";
		} else {
			return $name.".png";
		}
	}
}