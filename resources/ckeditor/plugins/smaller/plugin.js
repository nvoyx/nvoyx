/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function(){
	//Section 1 : Code to execute when the toolbar button is pressed
	var a= {
		exec:function(editor){
			if(editor.config.height>200){
				editor.config.height = editor.config.height +-100;
				editor.resize( '100%',editor.config.height, true );
			}
		}
	},

	//Section 2 : Create the button and add the functionality to it
	b='smaller';
	CKEDITOR.plugins.add(b,{
		init:function(editor){
			editor.addCommand(b,a);
			editor.ui.addButton("smaller",{
				label:'Smaller Editor', 
				icon:this.path+"icon.png",
				command:b
			});
		}
	});
})();