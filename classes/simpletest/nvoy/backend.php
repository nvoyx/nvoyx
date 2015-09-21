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
		$this->assertText('System');
	}
	
	/* 301 REDIRECT TESTS */
	
	function test_301_Redirects_List_Response() {
		$this->get($this->o['https'].'/settings/redirects/list');
		$this->assertResponse(200);
		$this->assertText('301 Redirects');
	}
	
	function test_Add_Redirect() {		
		$this->get($this->o['https'].'/settings/redirects/add');
		$this->assertText('Old Url');
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
			$this->assertText('301 Redirects');
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
		$this->assertText('Ajax');
	}
	
	function test_Ajax_System_Files_Available() {
		$this->get($this->o['https'].'/settings/ajaxmanager/list');
		$this->assertText('addresslookup.php');
		$this->assertText('ckbrowse.php');
		$this->assertText('ckupload.php');
		$this->assertText('contentfilter.php');
		$this->assertText('heirarchy.php');
		$this->assertText('tagbox.php');
		$this->assertText('upload.php');
		$this->assertText('variation.php');
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
			$this->assertText('Ajax');
		}
	}
	
	/* BLOCK TESTS */
	
	function test_Blocks_List_Response() {
		$this->get($this->o['https'].'/settings/block/list');
		$this->assertResponse(200);
		$this->assertText('Blocks');
	}
	
	function test_Blocks_System_Blocks_Exist() {
		$this->get($this->o['https'].'/settings/block/list');
		$this->assertText('404 Error');
		$this->assertText('Admin Bar');
		$this->assertText('Analytics');
		$this->assertText('Helper');
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
			$this->assertText('Blocks');
		}
	}
	
	/* DEBUG TESTS */

	function test_Debug_List_Response() {
		$this->get($this->o['https'].'/settings/debug/list');
		$this->assertResponse(200);
		$this->assertText('Debug');
	}
	
	function test_Debug_Database_Response() {
		$this->get($this->o['https'].'/settings/debug/database');
		$this->assertResponse(200);
		$this->assertText('Select a table to view');
	}
	
	function test_Departments_List_Response() {
		$this->get($this->o['https'].'/settings/dept/list');
		$this->assertResponse(200);
		$this->assertText('Departments');
	}
	
	function test_Groups_List_Response() {
		$this->get($this->o['https'].'/settings/group/list');
		$this->assertResponse(200);
		$this->assertText('Groups');
	}
	
	function test_Image_Cache_List_Response() {
		$this->get($this->o['https'].'/settings/imagecache/list');
		$this->assertResponse(200);
		$this->assertText('Image Cache');
	}
	
	function test_Paths_List_Response() {
		$this->get($this->o['https'].'/settings/path/list');
		$this->assertResponse(200);
		$this->assertText('Paths');
	}
	
	function test_Recovery_List_Response() {
		$this->get($this->o['https'].'/settings/recovery/list');
		$this->assertResponse(200);
		$this->assertText('Recovery');
	}
	
	function test_Types_List_Response() {
		$this->get($this->o['https'].'/settings/type/list');
		$this->assertResponse(200);
		$this->assertText('Types');
	}
	
	function test_Users_List_Response() {
		$this->get($this->o['https'].'/settings/user/list');
		$this->assertResponse(200);
		$this->assertText('Users');
	}
	
	function test_Variables_List_Response() {
		$this->get($this->o['https'].'/settings/variables/list');
		$this->assertResponse(200);
		$this->assertText('Variables');
	}
	
	function test_Redirect_From_Login_To_Content_List() {
		$this->get($this->o['https'].'/settings/user/login');
		$this->assertText('System');
	}
	
	function test_Content_System_Link_301_Redirects() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('301-redirects');
		$this->clickLinkById('301-redirects');
		$this->assertText('301 Redirects');
	}

	function test_Content_System_Link_Ajax() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('ajax');
		$this->clickLinkById('ajax');
		$this->assertText('Ajax');
	}
	
	function test_Content_System_Link_Blocks() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('blocks');
		$this->clickLinkById('blocks');
		$this->assertText('Blocks');
	}	

	function test_Content_System_Link_Debug() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('debug');
		$this->clickLinkById('debug');
		$this->assertText('Debug');
	}	

	function test_Content_System_Link_Departments() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('departments');
		$this->clickLinkById('departments');
		$this->assertText('Departments');
	}	

	function test_Content_System_Link_Groups() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('groups');
		$this->clickLinkById('groups');
		$this->assertText('Groups');
	}	

	function test_Content_System_Link_Image_Cache() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('image-cache');
		$this->clickLinkById('image-cache');
		$this->assertText('Image Cache');
	}	

	function test_Content_System_Link_Paths() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('paths');
		$this->clickLinkById('paths');
		$this->assertText('Paths');
	}	

	function test_Content_System_Link_Recovery() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('recovery');
		$this->clickLinkById('recovery');
		$this->assertText('Recovery');
	}	

	function test_Content_System_Link_Types() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('types');
		$this->clickLinkById('types');
		$this->assertText('Types');
	}	

	function test_Content_System_Link_Users() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('users');
		$this->clickLinkById('users');
		$this->assertText('Users');
	}
	
	/* VARIABLES TESTS */

	function test_Content_System_Link_Variables() {
		$this->get($this->o['https'].'/settings/content/list');
		$this->assertLinkById('variables');
		$this->clickLinkById('variables');
		$this->assertText('Variables');
	}
	
	function test_Default_Variables_Exist() {
		$this->get($this->o['https'].'/settings/variables/list');
		$this->assertText('404');
		$this->assertText('ajax');
		$this->assertText('company');
		$this->assertText('cssprivate');
		$this->assertText('csspublic');
		$this->assertText('editors');
		$this->assertText('front');
		$this->assertText('holding');
		$this->assertText('honeyfile');
		$this->assertText('honeykey');
		$this->assertText('honeyserver');
		$this->assertText('jsprivate');
		$this->assertText('jspublic');
		$this->assertText('live');
		$this->assertText('mailer');
		$this->assertText('maintenance paths');
		$this->assertText('php resources');
		$this->assertText('template');
		$this->assertText('timezone');
		$this->assertText('version');
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
			$this->assertText('Variables');
		}
	}

}
