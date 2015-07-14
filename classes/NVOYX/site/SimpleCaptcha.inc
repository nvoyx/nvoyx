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
 * Collection of simplecaptcha bumf
 * the functions below are a stripped subset of the complete simplecaptcha code (see /COPYRIGHT.txt)
 */

namespace NVOYX\site;

class SimpleCaptcha {
	
	protected static $instance;
	
	protected static $BOOT;
	
	public function __construct() {
		
	}
	
	public static function CONNECT($boot) {
		if( !self::$instance ) {
			self::$BOOT = $boot;
			self::$instance = new self();
		}
		return self::$instance;
	}

    public $width  = 484;

    public $height = 170;

    public $minWordLength = 5;

    public $maxWordLength = 8;

    public $session_var = 'captcha';

    public $backgroundColor = array(255, 255, 255);

    public $colors = array(array(66,87,112));

    public $Yperiod    = 12;
    public $Yamplitude = 14;
    public $Xperiod    = 11;
    public $Xamplitude = 5;

    public $maxRotation = 8;

    public $scale = 3;
    
    public $im;

	public function CreateImage() {
		$ini = microtime(true);
		$this->ImageAllocate();
		$text = $this->GetRandomCaptchaText();
		$this->WriteText($text);
		$_SESSION[$this->session_var] = $text;
		$this->WaveImage();
		if (function_exists('imagefilter')) {
			imagefilter($this->im, IMG_FILTER_GAUSSIAN_BLUR);
		}
		$this->ReduceImage();
		$this->WriteImage();
		$this->Cleanup();
	}

	protected function ImageAllocate() {
		if (!empty($this->im)) {imagedestroy($this->im);}
		$this->im = imagecreatetruecolor($this->width*$this->scale, $this->height*$this->scale);
		$this->GdBgColor = imagecolorallocate($this->im,
												$this->backgroundColor[0],
												$this->backgroundColor[1],
												$this->backgroundColor[2]
												);
		imagefilledrectangle($this->im, 0, 0, $this->width*$this->scale, $this->height*$this->scale, $this->GdBgColor);
		$color = $this->colors[mt_rand(0, sizeof($this->colors)-1)];
		$this->GdFgColor = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);
	}

	protected function GetRandomCaptchaText() {
		$length = rand($this->minWordLength, $this->maxWordLength);
		$words  = "abcdefghijlmnopqrstvwyz";
		$vocals = "aeiou";
		$text  = "";
		$vocal = rand(0, 1);
		for ($i=0; $i<$length; $i++) {
			if ($vocal) {
				$text .= substr($vocals, mt_rand(0, 4), 1);
			} else {
				$text .= substr($words, mt_rand(0, 22), 1);
			}
			$vocal = !$vocal;
		}
		return $text;
	}

	protected function WriteText($text) {
		$fontcfg  = array('spacing' => -2, 'minSize' => 60, 'maxSize' => 78, 'font' => 'Duality.ttf');
		$fontfile = self::$BOOT->FETCH_ENTRY("captchafonts") . "/" . $fontcfg['font'];
		$lettersMissing = $this->maxWordLength-strlen($text);
		$fontSizefactor = 1+($lettersMissing*0.09);
		$x = 20*$this->scale;
		$y = round(($this->height*27/40)*$this->scale);
		$length = strlen($text);
		for ($i=0; $i<$length; $i++) {
			$degree = rand($this->maxRotation*-1, $this->maxRotation);
			$fontsize = rand($fontcfg['minSize'], $fontcfg['maxSize'])*$this->scale*$fontSizefactor;
			$letter = substr($text, $i, 1);
			$coords = imagettftext($this->im, $fontsize, $degree,
									$x, $y,
									$this->GdFgColor, $fontfile, $letter);
			$x += ($coords[2]-$x) + ($fontcfg['spacing']*$this->scale);
		}
		$this->textFinalX = $x;
	}

	protected function WaveImage() {
		$xp = $this->scale*$this->Xperiod*rand(1,3);
		$k = rand(0, 100);
		for ($i = 0; $i < ($this->width*$this->scale); $i++) {
			imagecopy($this->im, $this->im,
			$i-1, sin($k+$i/$xp) * ($this->scale*$this->Xamplitude),
			$i, 0, 1, $this->height*$this->scale);
		}
		$k = rand(0, 100);
		$yp = $this->scale*$this->Yperiod*rand(1,2);
		for ($i = 0; $i < ($this->height*$this->scale); $i++) {
			imagecopy($this->im, $this->im,
			sin($k+$i/$yp) * ($this->scale*$this->Yamplitude), $i-1,
			0, $i, $this->width*$this->scale, 1);
		}
	}

	protected function ReduceImage() {
		$imResampled = imagecreatetruecolor($this->width, $this->height);
		imagecopyresampled($imResampled, $this->im,
							0, 0, 0, 0,
							$this->width, $this->height,
							$this->width*$this->scale, $this->height*$this->scale
							);
		imagedestroy($this->im);
		$this->im = $imResampled;
	}

	protected function WriteImage() {
		if (function_exists('imagepng')) {
			header("Content-type: image/png");
			imagepng($this->im);
		} else {
			header("Content-type: image/jpeg");
			imagejpeg($this->im, null, 80);
		}
	}

	protected function Cleanup() {imagedestroy($this->im);}
}