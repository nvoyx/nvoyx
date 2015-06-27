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
class Media{
	
	protected static $instance;
	protected static $BOOT;
	
	
	/* --- PUBLIC METHODS --- */
	
		
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($boot) {
		
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* create a new instance */
			self::$instance = new self();
		}
		
		/* return instance */
		return self::$instance;
	}

	/*
	 * DWEBP()
	 * take a passed webp and produce a png version
	 */
	public function DWEBP($a){
	
		/* file extension */
		$ext = pathinfo($a["FILE"], PATHINFO_EXTENSION);
		
		/* filename */
		$name = pathinfo($a["FILE"], PATHINFO_FILENAME);
		
		/* path */
		$path = pathinfo($a["FILE"], PATHINFO_DIRNAME);
		
		/* convert to png */
		system(self::$BOOT->FETCH_ENTRY("dwebp")." {$a["FILE"]} -o {$path}/{$name}.png");
		
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

			/* return the low pixel density image name */
			return $name.".jpg";
		} else {
		
			/* return the low pixel density image name */
			return $name.".png";
		}
	}
	
	/*
	 * CWEBP()
	 * take a passed image and produce webp version
	 */
	public function CWEBP($a){
	
		/* file extension */
		$ext = pathinfo($a["FILE"], PATHINFO_EXTENSION);
		
		/* filename */
		$name = pathinfo($a["FILE"], PATHINFO_FILENAME);
		
		/* path */
		$path = pathinfo($a["FILE"], PATHINFO_DIRNAME);
		
		/* convert to webp */
		system(self::$BOOT->FETCH_ENTRY("cwebp")." -q 80 -metadata none {$a["FILE"]} -o {$path}/{$name}.webp");
		
		/* remove the uploaded file */
		unlink($a["FILE"]);
		
		/* return the low pixel density image name */
		return $name.".webp";
		
	}
	
	/* IMAGE_ORIENTATION()
	 * take a passed image and test for which is the longest axis
	 * returns x,y or s
	 */
	public function IMAGE_ORIENTATION($i){
		
		/* create a new instance of imagemagick */
		$im = new Imagick();
		
		/* read in the image to adjusted */
		$im->readImage($i);
		
		/* fetch the dims of this image */
		$dim = $im->getImageGeometry();
		
		/* compare the two dims and return a reference to the longest */
		if($dim["width"]>$dim["height"]){
			return "x";
		} elseif($dim["width"]<$dim["height"]){
			return "y";
		} else {return "s";}
	}
	
	/*
	 * IMAGE()
	 * take a passed image and produce a scaled/cropped version desired effects applied
	 */
	public function IMAGE($a) {
				
		/* file extension */
		$ext = pathinfo($a["FILE"], PATHINFO_EXTENSION);
		
		/* filename */
		$name = pathinfo($a["FILE"], PATHINFO_FILENAME);
		
		/* path */
		$path = pathinfo($a["FILE"], PATHINFO_DIRNAME);
		
		/* create a new instance of imagemagick */
		$im = new Imagick();
		
		/* read in the image to adjusted */
		$im->readImage($a["FILE"]);

		/* do we have x and y dimensions for this image (are we resizing) */
		if(array_key_exists("X",$a) && array_key_exists("Y",$a)){

			if($a['X']==0 && $a['Y']==0){

			} else {
			
				/* the image cache is to hold double sized images, for serving crisp graphics to hi ppi devices */
				$a["X"] = $a["X"]*2;
				$a["Y"] = $a["Y"]*2;
				
				/* is one of the dimensions to be resized proportionately to the other (zero loss of image) */
				if($a['X']==0 || $a['Y']==0){
					
					/* scale the image by the requested dimension */
					/* $im->scaleImage($a['X'],$a['Y']); */
					$im->resizeImage($a['X'],$a['Y'],Imagick::FILTER_LANCZOS,1);
				} else {
					
					/* scale and crop the image by the requested dimensions */
					$im->cropThumbnailImage($a["X"],$a["Y"]);
				}
				
				/* reset the image page */
				$im->setImagePage(0, 0, 0, 0);
			}
		}
		

		/* set the image type */
		if($im->getImageFormat()!=$a['MIME']){$im->setImageFormat($a['MIME']);}
		
		/* do we have an effects entry */
		if(array_key_exists("EFFECTS",$a)){
			
			/* is the effects entry an array */
			if(is_array($a["EFFECTS"])){
				
				/* cycle through the effects entries in order, this allows effects to be chained  via the CMS*/
				foreach($a["EFFECTS"] as $efkey=>$efval){
					
					if(!is_numeric($efkey)){
				
						/* do we have a rounded effect */
						if($efkey=="ROUNDED"){

							/* set the rounded effect */
							$im->roundCorners($efval,$efval);
						}

						/* do we have an opacity effect */
						if($efkey=="OPACITY"){

							/* set the opacity */
							$im->setImageOpacity($efval);
						}

						/* do we have a saturation effect */
						if($efkey=="SATURATION"){

							/* set the saturation */
							$im->modulateImage(100,$efval,100);
						}

						/* do we have an overlay effect */
						if($efkey=="OVERLAY"){

							/* set the overlay */
							$gradient = new Imagick();
							$gradient->newPseudoImage(1, 100, "gradient:{$efval}");
							$im->modulateImage(100,0,100);
							$im->clutImage($gradient);
							$gradient->destroy();
						}

						/* do we have an instagram effect */
						if($efkey=="INSTA"){

							/* apply a filter which turns blacks blue and whites yellow */
							$cima = clone $im;
							$cimb = clone $im;
							$cima->colorizeImage("#222b6d",1);
							$im->compositeImage( $cima, Imagick::COMPOSITE_COPYBLUE, 0, 0,Imagick::CHANNEL_BLACK);
							$cimb->colorizeImage("#f7daae",1);
							$im->compositeImage( $cimb, Imagick::COMPOSITE_COPYYELLOW, 0, 0 ,Imagick::CHANNEL_ALL);
							$im->contrastImage(1);
							$im->modulateImage(110,100,100);
							$cima->destroy();
							$cimb->destroy();
						}

						/* do we have a tilt shift effect */
						if($efkey=="TILTSHIFT"){

							/* generates a passable short field of vision effect */
							$qr = $im->getQuantumRange();
							$qr = $qr['quantumRangeLong'];
							$im->sigmoidalContrastImage(true, 7, $qr / 3, Imagick::CHANNEL_ALL);
							$blurmap = new Imagick();
							$blurmap->setOption('compose:args', '5');
							$blurmap->newPseudoImage($im->getImageWidth(), $im->getImageHeight(), 'gradient:black-white');
							$blurmap->functionImage(Imagick::FUNCTION_POLYNOMIAL, array(4.5, -4.5, 1));
							$blurmap->levelImage(0, 1, $qr / 3);
							$im->compositeImage($blurmap, 57, 0, 0);
							$blurmap->destroy();
						}

						/* do we have a vignette effect */
						if($efkey=="VIGNETTE"){

							/* this could be extended to allow the user to set the following options via IC */
							$crop = 1.5;
							$color="rgb(184,184,184)";
							$comp = Imagick::COMPOSITE_COLORBURN;
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

							$overlay = new Imagick();
							$overlay->newPseudoImage($crop_x, $crop_y, "radial-gradient:rgba(0,0,0,0)-{$color}");
							$im->compositeImage($overlay, $comp, ($w - $crop_x) / 2, ($h - $crop_y)/2);
						}
					}
				}
			}
		}
		
		/* set the image depth to 8bit */
		$im->setImageDepth(8);
		
		/* strip meta data from the image */
		$im->stripImage();
		
		
		if($a['MIME']=='jpg'){
			
			/* enable image compression */
			$im->setImageCompression(Imagick::COMPRESSION_JPEG); 
			
		} else {
		
			/* enable image compression */
			$im->setImageCompression(Imagick::COMPRESSION_UNDEFINED);

			/* set the image quality */
			$im->setImageCompressionQuality(0);
		}
		
		/* do we have x and y dimensions for this image (are we resizing) */
		if(array_key_exists("X",$a) && array_key_exists("Y",$a)){
		

			if($a['X']==0 && $a['Y']==0){

			} else {

				if($a['MIME']=='jpg'){
					
					/* set the image quality (we can squeeze hi res images harder as they are dithered down by the browser) */
					$im->setImageCompressionQuality(90);

					/* write the new image */
					$im->writeImage($path."/@2x.".$name.".jpg");

					/* having sorted the 2x image, scale it down by a half */
					$im->resizeImage(ceil($a['X']/2),ceil($a['Y']/2),Imagick::FILTER_LANCZOS,1);
			
					/* reset the image page */
					$im->setImagePage(0, 0, 0, 0);
					
				} else {
					
					/* write the new image */
					$im->writeImage($path."/@2x.".$name.".png");

					/* having sorted the 2x image, scale it down by a half */
					$im->scaleImage(ceil($a['X']/2),ceil($a['Y']/2));

					/* reset the image page */
					$im->setImagePage(0, 0, 0, 0);
				
				}

			}
		}
		
		if($a['MIME']=='jpg'){
			
			/* set the image quality (normal resolution images need to be crisper) */
			$im->setImageCompressionQuality(95);
			
			/* write the new 1x image */
			$im->writeImage($path."/".$name.".jpg");
		
			/* if the old image was not a jpg, delete it */
			if($ext!="jpg"){unlink($a["FILE"]);}
		} else {
		
			/* write the new 1x image */
			$im->writeImage($path."/".$name.".png");
		
			/* if the old image was not a png, delete it */
			if($ext!="png"){unlink($a["FILE"]);}
		}

		/* do we have a hi pixel density key in the array */
		if(array_key_exists("HIPD",$a)){
			
			/* is the request to return a high pixel density image name */
			if($a["HIPD"]){
				
				/* return the high pixel density image name */
				if($a['MIME']=='jpg'){
					
					return "@2x".$name.".jpg";
				} else {
					
					return "@2x".$name.".png";
				}
			}
		}
		
		/* return the low pixel density image name */
		if($a['MIME']=='jpg'){
			
			return $name.".jpg";
		} else {
		
			return $name.".png";
		}
	}
		
}