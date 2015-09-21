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
}
