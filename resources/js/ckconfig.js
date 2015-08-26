/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

CKEDITOR.on( 'dialogDefinition', function(e) {
	e.data.definition.resizable = CKEDITOR.DIALOG_RESIZE_NONE;
});

var rules = {indent:false,
			breakBeforeOpen:false,
			breakAfterOpen:false,
			breakBeforeClose:false,
			breakAfterClose:false
			};

CKEDITOR.on('instanceReady', function (e) {
	e.editor.dataProcessor.writer.setRules('p',rules);
	e.editor.dataProcessor.writer.setRules('h1',rules);
	e.editor.dataProcessor.writer.setRules('h2',rules);
	e.editor.dataProcessor.writer.setRules('h3',rules);
	e.editor.dataProcessor.writer.setRules('h4',rules);
	e.editor.dataProcessor.writer.setRules('h5',rules);
	e.editor.dataProcessor.writer.setRules('h6',rules);
	e.editor.dataProcessor.writer.setRules('address',rules);
	e.editor.dataProcessor.writer.setRules('table',rules);
	e.editor.dataProcessor.writer.setRules('thead',rules);
	e.editor.dataProcessor.writer.setRules('tr',rules);
	e.editor.dataProcessor.writer.setRules('th',rules);
	e.editor.dataProcessor.writer.setRules('tbody',rules);
	e.editor.dataProcessor.writer.setRules('tr',rules);
	e.editor.dataProcessor.writer.setRules('td',rules);
	e.editor.dataProcessor.writer.setRules('span',rules);
	e.editor.dataProcessor.writer.setRules('ul',rules);
	e.editor.dataProcessor.writer.setRules('ol',rules);
	e.editor.dataProcessor.writer.setRules('li',rules);
	e.editor.dataProcessor.writer.setRules('div',rules);
	e.editor.dataProcessor.writer.setRules('pre',rules);
	e.editor.dataProcessor.writer.setRules('script',rules);
	e.editor.dataProcessor.writer.selfClosingEnd = '>';
	e.editor.dataProcessor.htmlFilter.addRules({
	elements : {
			a: function (element) {element.attributes.rel = 'nofollow';}
		}
	});
});

CKEDITOR.editorConfig = function( config ) {
	config.skin = 'moono';
	config.resize_enabled = true;
	config.wsc_lang = 'en_GB';
	config.resize_dir = 'vertical';
	config.toolbarCanCollapse = false;
	config.filebrowserBrowseUrl='/settings/ajax/ckbrowse';
	config.filebrowserUploadUrl='/settings/ajax/ckupload';
	config.stylesSet = 'nvx:/settings/resources/js/ckstyles.js';
	config.filebrowserWindowWidth = '620';
	config.filebrowserWindowHeight = '470';
	config.entities = false;
	config.entities_latin = false;
	config.contentsCss = '/settings/resources/css/ckeditor.css';
	
	config.toolbar = 'Private';
	config.toolbar_Private = [
		['Styles'],
		['Source'],
		['Scayt'],
		['PasteText','PasteFromWord'],
		['Bold','Italic','Underline','Subscript','Superscript'],
		['BulletedList','NumberedList'],
		['Link','Unlink','Table','SpecialChar'],
		['Image']
	];
	
	config.toolbar = 'Public';
	config.toolbar_Public = [
		['Source'],
		['Scayt']
	];
};
