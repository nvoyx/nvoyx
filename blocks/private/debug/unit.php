<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

$st=$nvBoot->fetch_entry('core').'/classes/simpletest/';
$nv=$st.'nvoy/';

/* list of test files to include */
require_once($nv.'frontend.php');
require_once($nv.'backend.php');
require_once($nv.'nvreporter.php');

/* reporter options */
$ro=array('passes'=>0);

/* test options */
$to=array(
	'http'=>'http://'.$_SERVER['SERVER_NAME'],
	'https'=>'https://'.$_SERVER['SERVER_NAME'],
	'session_id'=>session_id(),
	'captcha'=>$_SESSION['captcha'],
	'core'=>$nvBoot->fetch_entry('core')
);

/* grab a reporter and pass in some options */
$reporter = new nvReporter();
$reporter->setOptions($ro);

/* grab the frontend tests and pass in some options */
$frontend = new Frontend('Front End');
$frontend->setOptions($to);

/* grab the backend tests and pass in some options */
$backend = new Backend('Back End');
$backend->setOptions($to);

/* load all the tests inside the testsuite and apply our own styling */
$test= new TestSuite('nvoy Tests');
$test->add($frontend);
$test->add($backend);
$test->run($reporter);
