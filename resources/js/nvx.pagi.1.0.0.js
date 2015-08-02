/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2015 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

	
/* sync a hidden dropdown with its associated text field */
function dropdownUpdate(obj){
	$('#' +  $(obj).attr('id') + '-tb').val($(obj).children('option:selected').text());
}

/* multiple filter control (text and dropdown) for OR filtered pages */
function multiPagi(fullname,direction){
	var name = fullname.split("-")[0];
	var type = fullname.split("-")[1];
	var filter='';
	if($('#' + fullname).hasClass(name + '-ss')){
		filter = $('#' + fullname + ' option:selected').val();
	}
	if($('#' + fullname).hasClass(name + '-tb')){
		filter = $('#' + fullname).val();
	}
	$('.' + name + '-ss').each(function(){
		if($(this).attr('id')!==fullname){
			$(this).removeAttr('selected');
			$(this).children("option:first").attr('selected','selected');
			dropdownUpdate($(this));
		}
	});
	$('.' + name + '-tb').each(function(){
		if($(this).attr('id')!==fullname){
			$(this).val('');
		}
	});
	if($('.pagi-results-' + name).length){
		$.ajax({type: 'POST',
				url: '/settings/ajax/pagi_' + name,
				cache: false,
				data: {
					'direction': direction,
					'filter': filter,
					'type': type
				}
		}).done(function(rs){
			if(rs!=='empty'){
				$('.pagi-results-' + name).html(rs);
			}
		});
	}
}

/* Cycle through returned results based on the pagi code */
function multiCycle(page_id,name,direction,type){
	$.ajax({type: 'POST',
			url: '/settings/ajax/cycle_' + name,
			cache: false,
			data: {
				'direction': direction,
				'page_id': page_id,
				'type': type
			}
	}).done(function(rs){
		if(rs!=='empty'){
			window.location.href = rs;
		}
	});
}
