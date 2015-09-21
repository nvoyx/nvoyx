<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */


/* create a simple captcha instance */
$nvCaptcha = \nvoy\site\SimpleCaptcha::connect(self::$boot);


/* create a captcha image */
$nvCaptcha->CreateImage();
