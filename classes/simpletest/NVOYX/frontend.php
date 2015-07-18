<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

require_once($st.'web_tester.php');

class Frontend extends WebTestCase {
	
	var $o;
		
	function setOptions($opts) {
		$this->o=$opts;
	}
	
	function getOption($key){
		return $this->o[$key];
	}
    
	function test_Home_Response_HTTP() {
		$this->get($this->o['http']);
        $this->assertResponse(200);
	}
	
	function test_Home_Response_HTTPS() {
        $this->get($this->o['https']);
        $this->assertResponse(200);
    }
	
	function test_Login_Response_HTTPS() {
		$this->get($this->o['http'].'/settings/user/login');
		$this->assertResponse(200);
	}

	function test_Login_Form_Not_Chrome() {
		$this->get($this->o['http'].'/settings/user/login');
		$this->assertText('Website');
	}
	
	function test_Login_Form_Chrome() {
		$this->addHeader('User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36');
		$this->get($this->o['http'].'/settings/user/login');
		$this->assertText('Password');
	}
}
