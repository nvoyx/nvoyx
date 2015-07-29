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
		$this->setCookie('nvx_unit',$this->o['session_id']);
		$this->setCookie('nvx_cc',$this->o['captcha']);
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
	
	/* 301 REDIRECT TESTS */
	
	function test_301_Redirects_List_Response() {
		$this->get($this->o['https'].'/settings/redirects/list');
		$this->assertResponse(200);
		$this->assertText('301 REDIRECTS');
	}
	
	function test_Add_Redirect() {		
		$this->get($this->o['https'].'/settings/redirects/add');
		$this->assertText('Old URL');
		$r=explode('/',$this->getUrl());
		$this->o['redirect_id']=array(0=>$r[count($r)-1]);
	}
	
	function test_Update_Redirect() {
		if(is_numeric($this->o['redirect_id'][0])){
			$this->get($this->o['https'].'/settings/redirects/edit/'.$this->o['redirect_id'][0]);
			$this->setFieldById('old', '/nvx-redirect-test-a');
			$this->setFieldById('new', '/contact');
			$this->clickSubmitById('submit');
			$this->assertField('old',true,'/nvx-redirect-test-a');
			$this->assertField('new',true,'/contact');
		}
	}
	
	function test_Redirect_Reponds_301() {
		if(is_numeric($this->o['redirect_id'][0])){
			$this->setMaximumRedirects(0);
			$this->get($this->o['https'].'/nvx-redirect-test-a');
			$this->assertResponse(301);
		}
	}
	
	function test_Redirect_Of_Redirect() {
		$this->get($this->o['https'].'/settings/redirects/add');
		$r=explode('/',$this->getUrl());
		$this->o['redirect_id'][1]=$r[count($r)-1];
		$this->setFieldById('old', '/nvx-redirect-test-b');
		$this->setFieldById('new', '/nvx-redirect-test-a');
		$this->clickSubmitById('submit');
		$this->setMaximumRedirects(0);
		$this->get($this->o['https'].'/nvx-redirect-test-b');
		$this->assertResponse(301);
	}
	
	function test_Delete_Redirects() {
		if(is_numeric($this->o['redirect_id'][0])){
			$this->get($this->o['https'].'/settings/redirects/delete/'.$this->o['redirect_id'][0]);
			$this->get($this->o['https'].'/settings/redirects/delete/'.$this->o['redirect_id'][1]);
			$this->assertText('301 REDIRECTS');
		}
	}
	
	function test_404_Redirect_Repsonse() {
		$this->get($this->o['https'].'/nvx-redirect-test-a');
		$this->assertResponse(404);
	}

	/* AJAX TESTS */
	
	function test_Ajax_List_Response() {
		$this->get($this->o['https'].'/settings/ajaxmanager/list');
		$this->assertResponse(200);
		$this->assertText('AJAX');
	}
	
	function test_Ajax_System_Files_Available() {
		$this->get($this->o['https'].'/settings/ajaxmanager/list');
		$this->assertText('ckbrowse.php');
		$this->assertText('ckupload.php');
		$this->assertText('comments.php');
		$this->assertText('contentfilter.php');
		$this->assertText('heirarchy.php');
		$this->assertText('tagbox.php');
		$this->assertText('upload.php');
	}
	
	function test_Ajax_Folder_Writeable_By_Server() {
		$this->assertTrue(is_writable($this->o['core'].'/blocks/private/ajax'));
	}
	
	function test_Add_Ajax() {
		$this->get($this->o['https'].'/settings/ajaxmanager/add');
		$this->assertText('Name');
		$r=explode('/',$this->getUrl());
		$this->o['ajax_id']=$r[count($r)-1];
	}
	
	function test_Update_Ajax() {
		if(is_numeric($this->o['ajax_id'])){
			$this->get($this->o['https'].'/settings/ajaxmanager/edit/'.$this->o['ajax_id']);
			$this->setFieldById('url', 'nvxunit');
			$this->clickSubmitById('submit');
			$this->assertField('url',true,'nvxunit');
			$this->assertTrue(file_exists($this->o['core'].'/blocks/private/ajax/nvxunit.php'));
		}
	}
	
	function test_Delete_Ajax() {
		if(is_numeric($this->o['ajax_id'])){
			$this->get($this->o['https'].'/settings/ajaxmanager/delete/'.$this->o['ajax_id']);
			$this->assertText('AJAX');
		}
	}
	
	/* BLOCK TESTS */
	
	function test_Blocks_List_Response() {
		$this->get($this->o['https'].'/settings/block/list');
		$this->assertResponse(200);
		$this->assertText('BLOCKS');
	}
	
	function test_Blocks_System_Blocks_Exist() {
		$this->get($this->o['https'].'/settings/block/list');
		$this->assertText('404 Error');
		$this->assertText('Admin Bar');
		$this->assertText('Analytics');
		$this->assertText('Comments');
		$this->assertText('Contact Form');
		$this->assertText('Helper');
		$this->assertText('Twitter');
	}
	
	function test_Blocks_Folder_Writeable_By_Server() {
		$this->assertTrue(is_writable($this->o['core'].'/blocks/public'));
	}
	
	function test_Add_Block() {
		$this->get($this->o['https'].'/settings/block/add');
		$this->assertText('Name');
		$r=explode('/',$this->getUrl());
		$this->o['block_id']=$r[count($r)-1];
	}
	
	function test_Update_Block() {
		if(is_numeric($this->o['block_id'])){
			$this->get($this->o['https'].'/settings/block/edit/'.$this->o['block_id']);
			$this->setFieldById('name', 'unit variable test');
			$this->clickSubmitById('submit');
			$this->assertField('name',true,'unit block test');
		}
	}
	
	function test_Delete_Block() {
		if(is_numeric($this->o['block_id'])){
			$this->get($this->o['https'].'/settings/block/delete/'.$this->o['block_id']);
			$this->assertText('BLOCKS');
		}
	}
	
	/* DEBUG TESTS */

	function test_Debug_List_Response() {
		$this->get($this->o['https'].'/settings/debug/list');
		$this->assertResponse(200);
		$this->assertText('DEBUG');
	}
	
	function test_Debug_Database_Response() {
		$this->get($this->o['https'].'/settings/debug/database');
		$this->assertResponse(200);
		$this->assertText('Select a table to view');
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
	
	/* VARIABLES TESTS */

	function test_Content_System_Link_Variables() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLink('VARIABLES');
		$this->clickLink('VARIABLES');
		$this->assertText('VARIABLES');
	}
	
	function test_Default_Variables_Exist() {
		$this->get($this->o['https'].'/settings/variables/list');
		$this->assertText('404');
		$this->assertText('Ajax');
		$this->assertText('Company');
		$this->assertText('Css');
		$this->assertText('Editors');
		$this->assertText('Front');
		$this->assertText('404');
		$this->assertText('Holding');
		$this->assertText('Honeyfile');
		$this->assertText('Honeykey');
		$this->assertText('Honeyserver');
		$this->assertText('Js');
		$this->assertText('Languages');
		$this->assertText('Live');
		$this->assertText('Maintenance Paths');
		$this->assertText('Members');
		$this->assertText('Php Resources');
		$this->assertText('Spellchecker');
		$this->assertText('Template');
		$this->assertText('Timezone');
		$this->assertText('Version');
	}
	
	function test_Add_Variable() {
		$this->get($this->o['https'].'/settings/variables/add');
		$this->assertText('Name');
		$r=explode('/',$this->getUrl());
		$this->o['variable_id']=$r[count($r)-1];
	}
	
	function test_Update_Variable() {
		if(is_numeric($this->o['variable_id'])){
			$this->get($this->o['https'].'/settings/variables/edit/'.$this->o['variable_id']);
			$this->setFieldById('name', 'unit variable test');
			$this->clickSubmitById('submit');
			$this->assertField('name',true,'unit variable test');
		}
	}

	function test_Delete_Variable() {
		if(is_numeric($this->o['variable_id'])){
			$this->get($this->o['https'].'/settings/variables/delete/'.$this->o['variable_id']);
			$this->assertText('VARIABLES');
		}
	}

}
