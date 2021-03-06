/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

function validEmail(email){
	var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	var valid = emailReg.test(email);
	if(!valid) {
		return false;
	} else {return true;}
}

$(document).ready(function(){
	
	/* set the admin bar to draggable and hide/reveal on backtick key */
	if($('#admin-button').length>0){
		$(document).keypress(function(e){
			if(e.which == 96){
				$('#admin').toggleClass("hide");
			}
		});
		$('#admin-button').click(function(){
			$('#admin').toggleClass('hide');
		});
	}
	
	/* animations which are in view on page load */
	$('.nv-fx:in-viewport').addClass('animated');
	
	/* animations which have come into view as a result of scrolling */
	$(window).scroll(function() {
		$('.nv-fx:in-viewport').run(function(){$(this).addClass('animated');});
	});
	
	/* dropdown filters example */
	if($('.XX-ss').length>0){
		$('.XX-ss').each(function(){
			dropdownUpdate(this);
		});
		multiPagi(pagi_XX['type'],pagi_XX['direction']);
	}
});