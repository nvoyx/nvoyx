/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

function ajaxbox(obj,url){
	
	var results_id='#' + $(obj).attr('id').replace('lookup','results');
	var lookup=$(obj).val();
	$.ajax({type: "POST",
			url: "/settings/ajax/" + url,
			cache: false,
			data: {lookup: lookup}
	}).done(function(results){
		$(results_id).val(results);
	});
}

function deleteGo(url){
	window.location.href = url;
}

function deleteCheck(url){
	notif({
		msg: "Are you sure?&nbsp;&nbsp;<a onclick='deleteGo(\""+url+"\")' class='c-white'>yes</a>&nbsp;&nbsp;<a class='c-white' onclick='return false;'>no</a>",
		type: "info",
		position: "center",
		autohide: 0
	});
}

// NEW dropdown filter
function dropfilter(obj,who){
	var filter = $(obj).val();
	$('.dropfilter').addClass('hide');
	$('.filter-' + filter).removeClass('hide');
	if(who>-1){
		$.ajax({type: "POST",
				url: "/settings/ajax/contentfilter",
				cache: false,
				data: {user: who,filter: filter}
		});
	}
}

// NEW multiple and single select
function sel(obj){
	$(obj).each(function(){
		$(this).multipleSelect({
			width: '100%',
			placeholder: $(this).attr('placeholder'),
			single: $(this).hasClass('ss') ? true : false,
			maxHeight: 100
		});
	});
}

function groupCompress(obj){
	$(obj).closest('.box').children('ul,.add-variation').toggleClass('compressed');
	if($(obj).closest('.box').children('ul').hasClass('compressed')){
		$(obj).closest('.box').find('textarea.html').each(function(){
			CKEDITOR.instances[$(this).attr("id")].destroy();
		});
	} else {
		$(obj).closest('.box').find('textarea.html').each(function(i,itm){
			if($('#' + $(itm).attr('id') + '.ckPublic').length > 0){launchCK('#' + $(itm).attr('id') + '.ckPublic');}
			if($('#' + $(itm).attr('id') + '.ckPrivate').length > 0){launchCK('#' + $(itm).attr('id') + '.ckPrivate');}
			ckSortable();
		});
	}
}

function fetchTags(sbox,tid,rbox){
	var sstring = $.trim($(sbox).val());
	var lnk = rbox.replace("addtags","tags");
	$.ajax({type: "POST",
			url: "/settings/ajax/tagbox",
			cache: false,
			data: {lookup: sstring,typeid: tid,link: lnk}
	}).done(function(options){
		if(options!=""){
			$(rbox).html(options);
		} else {
			$(rbox).html("");
		}
	});
}


function deleteTag(lnk,obj,tag){
	
	var val = $.parseJSON($(obj).val());
	
	$(val).each(function(i,itm){
		if(itm == tag){val.splice(i,1);}
	});
	
	val = JSON.stringify(val);
	$(obj).val(val);
	$(lnk).parent().remove();
}

function addTag(obj,tag){
	var flag=0;
	var val = $.parseJSON($(obj).val());
	$(val).each(function(i,itm){
		if(itm == tag){flag=1;return;}
	});
	if(flag==0){
		val.splice(0,0,tag);
		val = JSON.stringify(val);
		$(obj).val(val);
		
		tobj = obj.replace("tagbox","ignore");
		$(tobj).val("");
		
		aobj = obj.replace("tags","addtags");
		$(aobj).html("");
		
		nobj = obj.replace("tags","deletetags");
		
		$(nobj).append("<span class='tag'><a class='fs14 c-white pad-r10' onclick=\"(deleteTag(this,'" + obj + "','" + tag + "'))\">" + tag + "</a></span>");
	}
}

function heirarchyChange(nid,obj,level,max){

	/* grab the object id */
	var obj_id = $(obj).attr('id');

	/* split the object id */
	var obj_id_split  = obj_id.split('-');

	var field_iteration = obj_id_split[5];
	
	/* are we extending the levels (ie not selecting the first entry) */
	var selected_val = $(obj).val();
	
	/* count the number of heirarchies in total */
	var heirarchy_count = $(obj).parent().parent().children('.heirarchy-wrapper').length;
	
	/* grab the label associated with this heirarchy */
	var label = $(obj).parent().children('label').html();

	/* cycle over the selects and ms-parents removing any beyond the one which has just been set */
	$(obj).siblings('.ms-parent').each(
		function(i,itm){
			if(i>field_iteration){
				$(itm).remove();
			}
		}
	);
	$(obj).siblings('select').each(
		function(i,itm){
			if(i>=field_iteration){
				$(itm).remove();
			}
		}
	);

	/* are we clearing an entry */
	if(selected_val==='-1'){

		/* are we editing the first level (zero indexed) */
		if(level===0){
		
			/* how many heirarchies starting [none] do we now have */
			var nones = 0;
			$(obj).parent().parent().children('.heirarchy-wrapper').each(function(i,itm){
				if($(itm).children('select').first().val()==='-1'){nones++;}
			});
		
			/* if we have two [nones], we need to delete the last of these */
			if(nones===2){
				$(obj).parent().parent().children('.heirarchy-wrapper:nth-child(' + nones + ')').remove();
			}
		}
	} else {
		/* we are not clearing an entry */
		
		/* are we editing the first level */
		if(level===0){
			
			/* are we able to add additional heirarchies if needed */
			if(heirarchy_count < max){
				
				/* how many heirarchies have a first level set to none */
				var nones = 0;
				$(obj).parent().parent().children('.heirarchy-wrapper').each(function(i,itm){
					if($(itm).children('select').first().val()==='-1'){nones++;}
				});

				/* do we currently have no heirarchies where the first level is set to [none] */
				if(nones===0){
					
					/* cycle through the heirarchies, grab the highest iteration then increment by one */
					var next_iteration = 0;
					$(obj).parent().parent().children('.heirarchy-wrapper').each(function(i,itm){
						if($(itm).children('select').first().attr('id').split('-')[4]>next_iteration){
							next_iteration=$(itm).children('select').first().attr('id').split('-')[4];
						}
					});
					next_iteration++;
										
					/* grab the available options */
					var select_options=$(obj).html();
					
					/* build the new entry */
					var select_ref="heirarchy-"+obj_id_split[1]+"-"+obj_id_split[2]+"-"+obj_id_split[3]+'-'+next_iteration+'-0';
					var new_heirarchy = "<div class='col all100 pad-b35 heirarchy-wrapper'>";
					new_heirarchy += "<label class='col all100 fs13 c-white pad-b5'>"+label+"</label>";
					new_heirarchy += "<select class='col all100 fs14 ss pad-b5' onchange='heirarchyChange("+nid+",this,0,"+max+");' name='"+select_ref+"' id='"+select_ref+"'>";
					new_heirarchy += select_options;
					new_heirarchy += "</select>";
					new_heirarchy += "</div>";
										
					/* add the new entry */
					$(obj).parent().parent().append(new_heirarchy);
					
					/* as this is a copy, reset the selected option to -1 [none] */
					$('#'+select_ref + ' option:selected').prop("selected",false);

					/* make the select look nice */
					sel('#'+select_ref);
				}
			}
		}
		
		/* check to see if there are any levels to be added to this heirarchy */
		
		/* grab the currently selected levels for this heirarchy */
		var nids=new Array();
		$(obj).parent().children('select').each(function(i,itm){
			nids.push('"'+$(itm).val()+'"');
		});
		nids='['+nids.toString()+',"-1"]';
				
		/* fetch the options available at this level */
		$.ajax({type: "POST",
			url: "/settings/ajax/heirarchy",
			cache: false,
			data: {
				node:nid,
				gid:obj_id_split[1],
				fid:obj_id_split[3],
				parents: nids
			}
		}).done(function(results){
			
			var options = "<option value=\"-1\" selected=\"selected\">[none]</option>";
			
			if(results!=='empty'){
				results = $.parseJSON(results);
				for (var key in results) {
					if (results.hasOwnProperty(key)) {
						options += "<option value=\"" + results[key]["page.id"] + "\">" + results[key]["page.title"] + "</option>\n";
					}
				}
			}
			
			/* build the new entry */
			var select_ref="heirarchy-"+obj_id_split[1]+"-"+obj_id_split[2]+"-"+obj_id_split[3]+'-'+obj_id_split[4]+'-'+parseInt(obj_id_split[5]+1);
			var new_heirarchy = "<select class='col all100 fs14 ss pad-b5' onchange='heirarchyChange("+nid+",this,"+parseInt(level+1)+","+max+");' name='"+select_ref+"' id='"+select_ref+"'>";
			new_heirarchy += options;
			new_heirarchy += "</select>";
			$(obj).parent().append(new_heirarchy);
			sel('#'+select_ref);
			
		});
	}
	
}

function deleteVariant(obj){
	var vcnt = $(obj).closest('ul').children("li").length;
	if(vcnt===1){return;}
	if(vcnt===2){
		$(obj).closest('ul').children('li').find(".delete-variant").addClass("hide");
	} else {
		$(obj).closest('ul').parent().children('.add-variation').removeClass('hide');
	}
	$(obj).closest('li').find('textarea.html').each(function(){
		var id=$(this).attr("id");
		CKEDITOR.instances[id].destroy(); 
	});
	$(obj).closest('li').remove();
}

function addVariant(nid,tid,lnk,gid,mvid){
	if($("#group-" + gid).children("li").length === mvid){return;}
	var data={
		'nid':nid,
		'tid':tid,
		'gid':gid,
		'vid':parseInt($("#nvid-" + gid).val()),
		'bc':$("#group-" + gid).children("li").length
	};
	if(data['vid']===0){data['vid']=1;}
	
	$.ajax({type: "POST",
			url: "/settings/ajax/variation",
			cache: false,
			data: data
	}).done(function(response){
		response = $.parseJSON(response);
		if(response.console!==0){
			console.log(response.console);
		}
		if(response.error!==0){
			notif({
				msg: response.error,
				type: "warning",
				position: "center"
			});
		} else {
			$(lnk).siblings('ul').append(response.html);
			$('.drop').unbind();
			$('.drop').each( function(){
				dropZone(this);
			});
			if($('#group-' + gid +'.ckPrivate').length > 0){launchCK('#group-' + gid +'.ckPrivate');}
			if($('#group-' + gid +'.ckPublic').length > 0){launchCK('#group-' + gid +'.ckPublic');}
			ckSortable();
			if($("#group-" + gid).children("li").length === mvid){
				$(lnk).addClass("hide");
			}
			$("#group-" + gid + " li div:first div.tar a").removeClass("hide");
			$("#group-" + gid + ' li:last').find('.ms,.ss').each(function(i,itm){
				sel('#'+$(itm).attr('id'));
			});
			data['vid']++;
			$("#nvid-" + gid).val(data['vid']);
		}
	});
}

function deleteListItem(obj){
	
	var list_id = '#' + $(obj).closest('ul').attr('id');
	var drop_id = list_id.replace('-list','-drop');
	
	$(obj).closest('li').hide("fade",500,function(){
		
		$(obj).parent().siblings('.col').children('.col').children('textarea.html').each(function(){
			CKEDITOR.instances[$(this).attr("id")].destroy();
		});

		var licount = $(list_id).parent().children('div').children('p').children('span');
		var maxfiles = $(drop_id).data('maxfiles');
		$(licount).html(parseInt($(licount).html())- 1);
		if(maxfiles > parseInt($(licount).html())){
			$(drop_id).removeClass('hide');
		}
		$(obj).closest('li').remove();
	});
}

function dropZone(obj){
	var did = "#" + $(obj).attr("id");
	var ref = did.replace("drop","");
	var dul = ref + "list";
	var type = $(obj).data('type');
	var allowed = $(obj).data('allowed');
	var maxsize = $(obj).data('maxsize');
	var maxfiles = $(obj).data('maxfiles');
	if(type==="imagelist"){
		var data={
			"lnk":$(obj).data('link'),
			"etype":$(obj).data('etype'),
			"eeditor":$(obj).data('eeditor'),
			"elabel":$(obj).data('elabel'),
			"nuid":ref.replace("#","") + '%%ITERATION%%-',
			"bc":$(dul).closest('.variation').hasClass('b-lblue')?'b-lblue':'b-vlblue'
		};
		var lnk = $(obj).data('link');
		var etype = $(obj).data('etype');
		var eeditor = $(obj).data('eeditor');
		var elabel = $(obj).data('elabel');
	} else {
		var data={
			"nuid":ref.replace("#","") + '%%ITERATION%%-',
			"bc":$(dul).closest('.variation').hasClass('b-lblue')?'b-lblue':'b-vlblue',
			"etype":0
		};
	}
	
	$(obj).filedrop({
		url: '/settings/ajax/upload',
		paramname: type,
		maxFiles: 1, /* maximum parallel uploads, not to be confused with maximum allowed files */
		maxfilesize: maxsize,
		allowedfiletypes: allowed.split(","),
		error: function(err, file, i, status) {
			notif({
				msg: "<b>Oops</b>: Please check the filesize and type.",
				type: "warning",
				position: "center"
			});
		},
		data: {
			ftypes: allowed,
			data: data,
			chk: $(obj).attr('data-nuid')
		},
		globalProgressUpdated: function (progress) {
			$(did + ' .progressbar').width(progress+"%");
		},
		afterAll: function () {
			$(did + ' .progressbar').hide("puff", 1000, function(){
				$(did + ' .progressbar').width("0%");
				$(did + ' .progressbar').show();
			});
			var nuid = parseInt($(did).attr('data-nuid'))+1;
			$(did).attr('data-nuid',nuid);			
			$(did).siblings('p').children('span').html($(dul + ' li').length);
			if($(dul + " li").length === maxfiles){
				$(did).addClass('hide');
			}
		},
		uploadFinished: function (i, file, response, time) {
			if(response.console!==0){
				console.log(response.console);
			}
			if(response.error!==0){
				notif({
					msg: response.error,
					type: "warning",
					position: "center"
				});
			} else {
				response.html=response.html.replace(/%%ITERATION%%/g,$(did).attr('data-nuid'));
				$(dul).append(response.html);
				if(data['etype']==="html"){
					launchCK(dul + ' .' + eeditor);
					ckSortable();
				}
			}
		}
	});	
}

function launchCK(obj){	
	if(obj.indexOf('.ckPrivate')>-1){
		$(obj).each(function(idx,ele){
			$(ele).ckeditor({
				customConfig:'/settings/resources/js/ckconfig.js',
				toolbar:'Private',
				language:'en-gb',
				height:200,
				width:'100%',
				extraPlugins:'wordcount,notification',
				wordcount:{
					showCharCount: true,
					maxCharCount: $(this).prop('maxlength'),
					countSpacesAsChars: true,
					showWordCount: false,
					showParagraphs: false,
					countHTML: true
				},
				format_tags:'p;h1;h2;h3'
			});
		});
	} else if(obj.indexOf('.ckPublic')>-1){
		$(obj).each(function(idx,ele){
			$(ele).ckeditor({
				customConfig:'/settings/resources/js/ckconfig.js?wsc=fr_FR',
				toolbar:'Public',
				language:'en-gb',
				height:200,
				width:'100%',
				extraPlugins:'wordcount,notification',
				wordcount:{
					showCharCount: true,
					maxCharCount: $(this).prop('maxlength'),
					countSpacesAsChars: true,
					showWordCount: false,
					showParagraphs: false,
					countHTML: true
				},
				format_tags:'p;h1;h2;h3'
			});
		});
		
	}
}

function ckSortable(){
	$( ".sortable" ).sortable({
		cursor: 'pointer',
		opacity:0.8,
		cancel: '.cke_resizer,.tb',
		handle: '.grip',
		helper: 'clone',
		start:function (event,ui) {
	
			$($(ui.item).find('textarea')).each(function(){
				var id = $(this).attr('id');
				if(CKEDITOR.instances[id]){
					var ck = CKEDITOR.instances[id];
					ckStore[id] = ck.getData();
					ck.destroy(true);
				}
			});
		},
		stop: function(event, ui) {
			
			$($(ui.item).find('textarea')).each(function(){
				var id = $(this).attr('id');
				var cla = '';
				if($(this).hasClass('ckPrivate')){cla='.ckPrivate';}
				if($(this).hasClass('ckPublic')){cla='.ckPublic';}
				if(cla!==''){
					if(typeof CKEDITOR.instances[id]==="undefined"){
						launchCK('#' + id + cla);
						CKEDITOR.instances[id].setData(ckStore[id]);
					}
				}
			});
		}
	});
}

/* we need to cache references to the individual ckeditors */
var ckStore = {};

$(document).ready(function(){

	/* launch ckEditor by class */
	if($('.ckPrivate').length > 0){
		$('.ckPrivate').each(function(i,itm){
			if($(itm).closest('.compressed').length===0){
				launchCK('#' + $(itm).attr('id') + '.ckPrivate');
			}
		});
	}
	if($('.ckPublic').length > 0){
		$('.ckPublic').each(function(i,itm){
			if($(itm).closest('.compressed').length===0){
				launchCK('#' + $(itm).attr('id') + '.ckPublic');
			}
		});
	}

	/* enable NEW mutliple and single select boxes */
	sel('.ms,.ss');
	
	/* make certain ul objects sortable with fixes for ckeditor */
	ckSortable();
	
	/* configure drop zones for files and images */
	$('.drop').each( function(){dropZone(this);});
});


