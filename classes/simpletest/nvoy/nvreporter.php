<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */
require_once($st.'/reporter.php');
    
class nvReporter extends HtmlReporter {
	
	var $o;
	
	function setOptions($opts) {
		$this->o=$opts;
	}
	
    function paintFail($message) {
		ob_start();parent::paintFail($message);ob_get_clean();
		print "<div class='box' style='margin:auto;width:95%;margin-bottom:20px;'>\n";
		print "<span class=\"fail\">Fail</span>: ";
		$breadcrumb = $this->getTestList();
		array_shift($breadcrumb);
		$breadcrumb[count($breadcrumb)-1] = str_replace(array('test_','_'),array('',' '),$breadcrumb[count($breadcrumb)-1]);
		print implode("->", $breadcrumb).'<br>';
		print $this->htmlEntities($message) . "\n";
		print "</div>\n";
	}
	
	function paintError($message) {
		ob_start();parent::paintError($message);ob_get_clean();
		print "<div class='box' style='margin:auto;width:95%;margin-bottom:20px;'>\n";
		print "<span class=\"fail\">Exception</span>: ";
		$breadcrumb = $this->getTestList();
		array_shift($breadcrumb);
		$breadcrumb[count($breadcrumb)-1] = str_replace(array('test_','_'),array('',' '),$breadcrumb[count($breadcrumb)-1]);
		print implode("->", $breadcrumb).'<br>';
		print "<strong>" . $this->htmlEntities($message) . "</strong>\n";
		print "</div>\n";
	}
	
	function paintPass($message) {
		parent::paintPass($message);
		if($this->o['passes']==1){
			print "<div class='box' style='margin:auto;width:95%;margin-bottom:20px;'>\n";
			print "<span class=\"pass\">Pass</span>: ";
			$breadcrumb = $this->getTestList();
			array_shift($breadcrumb);
			$breadcrumb[count($breadcrumb)-1] = str_replace(array('test_','_'),array('',' '),$breadcrumb[count($breadcrumb)-1]);
			print '<strong>'.implode("->", $breadcrumb).'</strong><br>';
			print "$message\n";
			print "</div>\n";
		}
	}
	
	protected function getCss() {
		$r=file_get_contents(dirname(__FILE__).'/../../../resources/css/private.css');
		return parent::getCss() . " .pass { color: green; }\n".$r;
	}
	
}
