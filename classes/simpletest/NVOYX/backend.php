<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

require_once($st.'web_tester.php');

class Backend extends WebTestCase {
	
	var $o;
	
	function setUp(){
		$this->restart();
		$this->setCookie('nvx_unit',$this->o['session_id']);
		$this->setCookie('nvx_cc',$this->o['captcha']);
		$this->addHeader('User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36');
	}
		
	function setOptions($opts) {
		$this->o=$opts;
	}
	
	function getOption($key){
		return $this->o[$key];
	}
	
	function test_Content_List_Response() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertResponse(200);
		$this->assertText('SYSTEM');
	}
	
	function test_301_Redirects_List_Response() {
		$this->get($this->o['https'].'/settings/redirects/list');
		$this->assertResponse(200);
		$this->assertText('301 REDIRECTS');
	}	

	function test_Ajax_List_Response() {
		$this->get($this->o['https'].'/settings/ajaxmanager/list');
		$this->assertResponse(200);
		$this->assertText('AJAX');
	}
	
	function test_Blocks_List_Response() {
		$this->get($this->o['https'].'/settings/block/list');
		$this->assertResponse(200);
		$this->assertText('BLOCKS');
	}	

	function test_Debug_List_Response() {
		$this->get($this->o['https'].'/settings/debug/list');
		$this->assertResponse(200);
		$this->assertText('DEBUG');
	}
	
	function test_Departments_List_Response() {
		$this->get($this->o['https'].'/settings/dept/list');
		$this->assertResponse(200);
		$this->assertText('DEPARTMENTS');
	}
	
	function test_Groups_List_Response() {
		$this->get($this->o['https'].'/settings/group/list');
		$this->assertResponse(200);
		$this->assertText('GROUPS');
	}
	
	function test_Image_Cache_List_Response() {
		$this->get($this->o['https'].'/settings/imagecache/list');
		$this->assertResponse(200);
		$this->assertText('IMAGE CACHE');
	}
	
	function test_Paths_List_Response() {
		$this->get($this->o['https'].'/settings/path/list');
		$this->assertResponse(200);
		$this->assertText('PATHS');
	}
	
	function test_Recovery_List_Response() {
		$this->get($this->o['https'].'/settings/recovery/list');
		$this->assertResponse(200);
		$this->assertText('RECOVERY');
	}
	
	function test_Types_List_Response() {
		$this->get($this->o['https'].'/settings/type/list');
		$this->assertResponse(200);
		$this->assertText('TYPES');
	}
	
	function test_Users_List_Response() {
		$this->get($this->o['https'].'/settings/user/list');
		$this->assertResponse(200);
		$this->assertText('USERS');
	}
	
	function test_Variables_List_Response() {
		$this->get($this->o['https'].'/settings/variables/list');
		$this->assertResponse(200);
		$this->assertText('VARIABLES');
	}
	
	function test_Redirect_From_Login_To_Content_List() {
		$this->get($this->o['https'].'/settings/user/login');
		$this->assertText('SYSTEM');
	}
	
	function test_Content_System_Link_301_Redirects() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('301 REDIRECTS');
		$this->clickLink('301 REDIRECTS');
		$this->assertText('301 REDIRECTS');
	}

	function test_Content_System_Link_Ajax() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('AJAX');
		$this->clickLink('AJAX');
		$this->assertText('AJAX');
	}
	
	function test_Content_System_Link_Blocks() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('BLOCKS');
		$this->clickLink('BLOCKS');
		$this->assertText('BLOCKS');
	}	

	function test_Content_System_Link_Debug() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('DEBUG');
		$this->clickLink('DEBUG');
		$this->assertText('DEBUG');
	}	

	function test_Content_System_Link_Departments() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('DEPARTMENTS');
		$this->clickLink('DEPARTMENTS');
		$this->assertText('DEPARTMENTS');
	}	

	function test_Content_System_Link_Groups() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('GROUPS');
		$this->clickLink('GROUPS');
		$this->assertText('GROUPS');
	}	

	function test_Content_System_Link_Image_Cache() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('IMAGE CACHE');
		$this->clickLink('IMAGE CACHE');
		$this->assertText('IMAGE CACHE');
	}	

	function test_Content_System_Link_Paths() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('PATHS');
		$this->clickLink('PATHS');
		$this->assertText('PATHS');
	}	

	function test_Content_System_Link_Recovery() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('RECOVERY');
		$this->clickLink('RECOVERY');
		$this->assertText('RECOVERY');
	}	

	function test_Content_System_Link_Types() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('TYPES');
		$this->clickLink('TYPES');
		$this->assertText('TYPES');
	}	

	function test_Content_System_Link_Users() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('USERS');
		$this->clickLink('USERS');
		$this->assertText('USERS');
	}	

	function test_Content_System_Link_Variables() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('VARIABLES');
		$this->clickLink('VARIABLES');
		$this->assertText('VARIABLES');
	}	

}
