/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */
 
function groupCompress(obj){
	var group = $(obj).parent();
	$(group).toggleClass("header-only");
}

function select(obj,nme){
	nme = nme.replace("[]","");
	var idx = $(obj).parent().children('a').index(obj);
	if($(obj).parent().hasClass('mselect')){
		if($(obj).hasClass('selected')){
			if($(obj).parent().children("a.selected").length>1){
				$(obj).removeClass('selected');
				$('select[id=' + nme + '] option:eq(' + idx + ')').removeAttr("selected");
			}
		} else {
			if($(obj).html()=="[none]"){
				$(obj).parent().children('a').removeClass('selected');
				$('select[id=' + nme + '] option').removeAttr("selected");				
			} else {
				if($(obj).parent().children('a').first().html()=="[none]"){
					if($(obj).parent().children('a').first().hasClass("selected")){
						$(obj).parent().children('a').first().removeClass('selected');
						$('select[id=' + nme + '] option:eq(0)').removeAttr("selected");
					}
				}
			}
			$(obj).addClass('selected');
			$('select[id=' + nme + '] option:eq(' + idx + ')').attr('selected', 'selected');
		}
	} else {
		if(!$(obj).hasClass('selected')){
			$(obj).parent().children('a').removeClass('selected');
			$('select[id=' + nme + '] option').removeAttr("selected");
			if($(obj).addClass('selected')){
				$('select[id=' + nme + '] option:eq(' + idx + ')').attr('selected', 'selected');
			}
			$('select[id=' + nme + ']').change();
		}
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
		
		$(nobj).append("<span class='tag'><a onclick=\"(deleteTag(this,'" + obj + "','" + tag + "'))\">" + tag + "</a></span>");
	}
}

function heirarchyChange(nid,obj,level,max){
		
	var id = $(obj).attr("id").split("-");
	if($(obj).val()==-1){
		var start = parseInt(id[5]) + 1;
		var finish = 10;
		for (var i=start;i<finish;i++){
			if( $( "#" + id[0] + "-" + id[1] + "-" + id[2] + "-" + id[3] + "-" + id[4] + "-" + i ).length>0 ){
				$( "." + id[0] + "-" + id[1] + "-" + id[2] + "-" + id[3] + "-" + id[4] + "-" + i ).parent().remove();
				$( "#" + id[0] + "-" + id[1] + "-" + id[2] + "-" + id[3] + "-" + id[4] + "-" + i ).remove();
			} 
		}
		
		/* is this happening at the first level */
		if(level==0){
									
			var cnt = 0;
			
			var row = $(obj).parent().parent();
			
			var grab = "";
			
			var obj = $(obj).parent().parent();
			
			$(obj).children(".heirarchy-wrapper").each(function(i,itm){
				if($(itm).children("select").first().val()==-1){
					cnt++;
					if(cnt==1){
						grab = "<label for='" + $(itm).prev('label').attr("for") + "' class='blank fl'>" + $(itm).prev('label').html() + "</label><div class='blank heirarchy-wrapper'>" + $(itm).html() + "</div>\n";
						$(itm).prev('label').remove();
						$(itm).remove();
					}
					if(cnt>1){
						$(itm).prev('label').remove();
						$(itm).remove();
					}
				}
			});
			
			if(grab!=""){
				if($(obj).children(".heirarchy-wrapper").length>0){
					$(obj).children(".heirarchy-wrapper:last").after(grab);
				} else {
					$(grab).appendTo($(obj));
				}
			}
			
			if(cnt>1){

				var nid = "";
				var old_nid = "";
				
				/* we now need to cycle through all the heirarchies for this variation and update the iteration values */
				$(row).children(".heirarchy-wrapper").each(function(i,itm){
					
					nid = $(itm).prev('label').attr("for").split("-");
					nid[4]=i;
					nid=nid.join("-");
					$(itm).prev('label').attr("for",nid);
					$(itm).children(".select").children("a").each(function(x,xtm){
						$(xtm).attr("onclick","select(this,'" + nid + "');return false;");
					});
					$(itm).children("select").attr("name",nid);
					$(itm).children("select").attr("id",nid);
					if(i==0){
						$(itm).prev('label').removeClass("ten-top");
						$(itm).children(".select").removeClass("ten-top");
					} else {
						$(itm).prev('label').addClass("ten-top");
						$(itm).children(".select").addClass("ten-top");
					}
				});
			}
		}
		
	} else {
				
		/* we need a list of any pages that have the same options selected as this */
		var nids = new Array();
		for (var i=0;i<10;i++){
			if(i>id[5]){
				if( $( "#" + id[0] + "-" + id[1] + "-" + id[2] + "-" + id[3] + "-" + id[4] + "-" + i ).length>0 ){
					$( "#" + id[0] + "-" + id[1] + "-" + id[2] + "-" + id[3] + "-" + id[4] + "-" + i ).remove();
				} 
			}
			if( $( "#" + id[0] + "-" + id[1] + "-" + id[2] + "-" + id[3] + "-" + id[4] + "-" + i ).length>0 ){
				if($( "#" + id[0] + "-" + id[1] + "-" + id[2] + "-" + id[3] + "-" + id[4] + "-" + i ).val() != -1){
					nids.push( "\"" + $( "#" + id[0] + "-" + id[1] + "-" + id[2] + "-" + id[3] + "-" + id[4] + "-" + i ).val() + "\"" );
				}
			}
		}
		nids = "[" + nids.toString() + ",\"-1\"]";
		$.ajax({type: "POST",
				url: "/settings/ajax/heirarchy",
				cache: false,
				data: {node:nid,gid:id[1],fid:id[3],parents: nids}
		}).done(function(options){
			var str = "";
			var atr = "";
			id[5] = parseInt(id[5]) + 1;
			id = id.join("-");
			if(options!=""){
				if(options!="empty"){
					options = $.parseJSON(options);
					for (var key in options) {
						if (options.hasOwnProperty(key)) {
							str += "<option value=\"" + options[key]["page.id"] + "\">" + options[key]["page.title"] + "</option>\n";
							atr += "<a class=\"blank huge " + id + "\" onclick=\"select(this,'" + id + "');return false;\">" + options[key]["page.title"] + "</a>\n";
						}
					}
					str = "<select class=\"hide\" name=\"" + id + "\" id=\"" + id + "\" onchange=\"heirarchyChange(" + nid + ",this,"+ parseInt(level+1) +","+ max +");\">\n<option value=\"-1\" selected=\"selected\">[none]</option>\n" + str + "</select>\n";
					atr = "<div class='blank select huge ten-top'><a class=\"blank huge " + id + " selected\" onclick=\"select(this,'" + id + "');return false;\">[none]</a>\n" + atr + "</div>";
					$(obj).after(atr + str);
				} else {
					str = "<select class=\"hide\" name=\"" + id + "\" id=\"" + id + "\" onchange=\"heirarchyChange(" + nid + ",this,"+ parseInt(level+1) +","+ max +");\">\n<option value=\"-1\" selected=\"selected\">[none]</option>\n</select>\n";
					atr = "<div class='blank select huge ten-top'><a class=\"blank huge " + id + " selected\" onclick=\"select(this,'" + id + "');return false;\">[none]</a>\n</div>";
					$(obj).after(atr + str);
				}
			}
		});
		
		
		/* is this happening at the first level, if so we need to create a new heirarchy entry*/
		if(level==0){
			
			var itm_iteration = $(obj).parent().parent().children(".heirarchy-wrapper").length;
			
			if(max ==0 || max>itm_iteration){

				var nstr = "";
				var anstr = "";
			
				var itm_id = id[0] + "-" + id[1] + "-" + id[2] + "-" + id[3] + "-" + itm_iteration + "-" + "0";
			
				itm = $(obj).parent().parent().children(".heirarchy-wrapper:first");
					
				var itm_label = $(itm).parent().children("label").html();
			
				$(itm).children("select").first().children("option:not(:first)").each(function(z,sel){
					nstr += "<option value=\"" + $(sel).val() + "\">" + $(sel).html() + "</option>\n";
					anstr += "<a class=\"blank huge " + itm_id + "\" onclick=\"select(this,'" + itm_id + "');return false;\">" + $(sel).html() + "</a>\n";
				;});
		
		
				anstr = "<label for=\"" + itm_id + "\" class='blank fl ten-top'>" + itm_label + "</label>\n<div class=\"blank heirarchy-wrapper\">\n<div class='blank select huge ten-top'><a class=\"blank huge selected\" onclick=\"select(this,'" + itm_id + "');return false;\">[none]</a>\n" + anstr + "</div>";
				nstr = "<select class=\"hide\" name=\"" + itm_id + "\" id=\"" + itm_id + "\" onchange=\"heirarchyChange(" + nid + ",this,0," + max + ");\">\n<option value=\"-1\" selected=\"selected\">[none]</option>\n" + nstr + "</select>\n</div>\n";

				$(obj).parent().parent().children(".heirarchy-wrapper:last").after(anstr + nstr);
			
			}
			
		}
	}
}

function deleteFieldOption(obj){
	$(obj).parent().remove();
}

function addSelectOption(){
	var t = new Date().getTime();
	$("ul").append("<li class='blank row'>\n" +
								"<label class='blank fl'>External / Internal</label>\n" +
								"<input class='blank textbox mini fl' name='external-" + t + "' id='external-" + t + "' type='text' value=''>\n" +
								"<div class='blank fl ten-space-hori'></div>\n" +
								"<input class='blank textbox mini fl' name='internal-" + t + "' id='external-" + t + "' type='text' value=''>\n" +
								"<div class='blank cb ten-space-vert'></div>\n" +
								"<a title='delete' href='#' onclick='deleteSelectOption(this);return false;'><img class='blank icon fr' src='/settings/resources/files/images/private/group-button-delete.png'></a>\n" +
								"<a class='hand' title='drag and drop'><img class='blank icon fr' src='/settings/resources/files/images/private/group-button-grip.png'></a>\n" +
								"</li>"
								);
}

function deleteSelectOption(obj){
	$(obj).parent().remove();
}

function deleteVariant(obj){
	
	/* number of variations */
	var vcnt = $(obj).parent().parent().parent().children("li").length;
	
	/* you cannot delete the last variation */
	if(vcnt>1){
		
		/* if we currently have two variations */
		if(vcnt==2){
			
			/* remove the option to delete the remaining two variations */
			$(obj).parent().parent().parent().find(".delete-variant").addClass("hide");
		} else {
			
			/* we must have at least 3 variations (though one is about to be deleted). So delete variation should be visible */
			$(obj).parent().parent().parent().parent().find(".add-variation").removeClass("hide");
		}
		
		$(obj).parent().parent().find("textarea.html").each(function(){
			var id=$(this).attr("id");
			CKEDITOR.instances[id].destroy(); 
		});
		
		/* delete the chosen variation */
		$(obj).parent().parent().remove();
	}
}

function addVariant(lnk,gid,mvid){
	
	/* do we already have the max number of variants */
	if($("#group-" + gid).children("li").length != mvid){
				
		var nvid = $("#nvid-" + gid).val();
		
		if(nvid==0){nvid=1;}
						
		var nme = "";
		
		/* grab the number of variations */
		var vcnt = $("#group-" + gid).children("li").length;
		
		if(vcnt==1){$("#group-" + gid).children("li").children(".variation-header").children(".delete-variant").removeClass("hide");}
	
		/* grab a copy of the first variant in the ul */
		var obj = $("#group-" + gid).children("li").first();
			
		/* append the copy to the variant ul */
		$("#group-" + gid).append("<li class='blank variation' data-vid='" + nvid + "'>" + $(obj).html() + "</li>");
	
		/* create a reference to the last li */
		obj = $("#group-" + gid).children("li").last();
	
		/* grab the old vid */
		var ovid = $(obj).children().data("vid");
	
		/* update the vid */
		$(obj).children().data("vid",nvid);
			
		/* remove any lists and update ul id */
		$(obj).children().children("ul").each(function(i,itm){
			$(itm).children("li").remove();
			nme = $(itm).attr("id").split("-");
			nme[2] = nvid;
			nme = nme.join("-");
			$(itm).attr("id",nme);
		});
	
		/* reset the list counters */
		$(obj).children("div").children("label").children(".current-length").html("0");
	
		/* reset the nuid */
		if($(obj).children("div").children("div").children("div").children(".drop").length > 0){
			
			$(obj).children("div").children("div").children("div").children(".drop").each(function(i,itm){
				nme = $(itm).attr("id").split("-");
				nme[2] = nvid;
				nme = nme.join("-");
				$(itm).attr("id",nme);
				$(itm).attr("data-nuid","0");
				$(itm).data("nuid","0");
			});
			/* configure drop zones for files and images */
			$('.drop').unbind();
			$('.drop').each( function(){
				dropZone(this);
			});
		}
		
		if($(obj).children("div").children(".heirarchy-wrapper").length > 0){
			
			/* remove heirarchy labels */
			$(obj).children("div").children(".heirarchy-wrapper:first").parent().children("label:not(:first)").remove();
			
			/* remove any heirarchy rows, but leave the first in place */
			$(obj).children("div").children(".heirarchy-wrapper:not(:first)").remove();
		
			/* remove any heirarchy levels but leave the first in place */
			$(obj).children("div").children(".heirarchy-wrapper").children("select:not(:first)").remove();
		
			/* fix the name */
			nme = $(obj).children("div").children(".heirarchy-wrapper").children("select").attr("id").split("-");
			nme[2] = nvid;
			nme[4] = 0;
			nme = nme.join("-");
			$(obj).children("div").children(".heirarchy-wrapper").children("select").attr("id",nme);
			$(obj).children("div").children(".heirarchy-wrapper").children("select").attr("name",nme);
			$(obj).children("div").children(".heirarchy-wrapper").children(".select").children("a").attr("onclick","select(this,'" + nme + "');return false;");
			$(obj).children("div").children(".heirarchy-wrapper").children(".select").children("a").removeClass("selected");
			$(obj).children("div").children(".heirarchy-wrapper").children(".select").children("a:first").addClass("selected");
			$(obj).children("div").children(".heirarchy-wrapper").children("select option:eq(0)").attr("selected","selected");
		}
		
		/* find the inputs */
		$(obj).children("div").find("input").each(function(i,itm){
			
			/* split the input name by hyphens */
			var nme = $(itm).attr("name").split("-");
			
			/* update the variant id */
			nme[2] = nvid;
			
			/* join the array back together */
			nme = nme.join("-");
			
			/* update the item name and id */
			$(itm).attr("name",nme);
			$(itm).attr("id",nme);
			
			/* reset any stored values */
			$(itm).val("");
		});
		
		/* iterate over each label within the li */
		$(obj).find("label").each(function(i,itm){
			
			/* if the label has a for */
			if($(itm).attr("for")!="" && $(itm).attr("for")!=undefined){
				
				/* check if the for contains any hyphens */
				if($(itm).attr("for").indexOf("-")>0){
					
					/* split the label for by hyphens */
					var nme = $(itm).attr("for").split("-");
			
					/* update the variant id */
					nme[2] = nvid;
			
					/* join the array back together */
					nme = nme.join("-");
			
					/* update the item for */
					$(itm).attr("for",nme);
				
				}
			}
		});
		
		
		/* iterate over each select within the li */
		$(obj).children("div").children("select").each(function(i,itm){
			/* split the input name by hyphens */
			var nme = $(itm).attr("name").split("-");
			
			/* update the variant id */
			nme[2] = nvid;
			
			/* join the array back together */
			nme = nme.join("-");
			
			/* update the item name and id */
			$(itm).attr("name",nme);
			$(itm).attr("id",nme.replace("[]",""));
			
			/* select the first option in the select */
			$(itm).children("option").each(function(oi,opt){
				
				if(oi==0){
					$(opt).attr("selected","selected");
				} else {
					$(opt).removeAttr("selected");
				}
			});
			
			/* sort out the multi select a links */
			$(itm).parent().children(".mselect").children("a").attr("onclick","select(this,'" + nme.replace("[]","") + "');return false;");
			$(itm).parent().children(".mselect").children("a").removeClass("selected");
			$(itm).parent().children(".mselect").children("a:first").addClass("selected");
			$(itm).parent().children(".select").children("a").attr("onclick","select(this,'" + nme.replace("[]","") + "');return false;");
			$(itm).parent().children(".select").children("a").removeClass("selected");
			$(itm).parent().children(".select").children("a:first").addClass("selected");
			
		});
		
		/* sort out drops where the upload bar is hidden */
		if($(obj).children("div").children("div").children("div").children(".drop.hide").length > 0){
			$(obj).children("div").children("div").children("div").children(".drop.hide").removeClass("hide");
		}
		
		/* make the upload bar visible, if an upload exists */
		if($(obj).children("div").children("div").children("div").children(".drop").length > 0){
			$(obj).children("div").children("div").children("div").children(".drop").css("display","block");
		}
		
		/* sort out the tags */
		$(obj).children("div").children(".current-tags").html("");
		$(obj).children("div").children(".current-tags").each(function(i,itm){
			var nme = $(itm).attr("id").split("-");
			nme[2] = nvid;
			nme = nme.join("-");
			$(itm).attr("id",nme);
		});
		$(obj).children("div").children(".available-tags").html("");
		$(obj).children("div").children(".available-tags").each(function(i,itm){
			var nme = $(itm).attr("id").split("-");
			nme[2] = nvid;
			nme = nme.join("-");
			$(itm).attr("id",nme);
		});
		$(obj).children("div").children(".tags").val("[]");
		
		/* cycle through the tag search fields */
		$(obj).children("div").children(".tag-box").each(function(i,itm){
			/* split the input name by hyphens */
			var kup = $(itm).attr("onkeyup").split(",");
			kup = kup[1];
			var nme = $(itm).attr("name").split("-");
			nme[0]  = "#tagbox";
			nme[5] = "addtags";
			nme = nme.join("-");
			$(itm).attr("onkeyup","fetchTags(this," + kup + ",'" + nme + "');");
		});
		
		/* iterate over each textarea within the li */
		$(obj).children("div").find("textarea").each(function(i,itm){
			var nme= $(itm).attr("name");
			if($(itm).parent().children("#cke_" + nme).length>0){
					$(itm).parent().children("#cke_" + nme).remove();
			}
			nme=nme.split("-");
			nme[2]=nvid;
			nme=nme.join("-");
			$(itm).attr("name",nme);
			$(itm).attr("id",nme);
			$(itm).html("");
		});
		
		if($(".ckPrivate").length>0){
			launchCK('.ckPrivate');
		}
		if($(".ckPublic").length>0){
			launchCK('.ckPublic');
		}
		if($(".ckPublic").length>0 || $(".ckPrivate").length>0){
			ckSortable();
		}
		
		/* check again to see if we now have the max number of variants */
		if($("#group-" + gid).children("li").length == mvid){
			/* hide the link button */
			$(lnk).addClass("hide");
		}
		
		/* we must have more than one variant, so ensure the delete variant buttons are visible */
		$("#group-" + gid + " li .variation-header .delete-variant").removeClass("hide");
		
		/* increment the nvid counter by 1 */
		nvid++;
		$("#nvid-" + gid).val(nvid);
   
		/* enable character counting on plain textareas and textboxes */
		countTextbox('.textbox,.textarea.plain');
	}
}

function itemVisibility(obj){
	if($(obj).parent().children(".list-details").css("display") == "block"){
		$(obj).parent().children(".list-details").hide("fade",1000);
	} else {
		$(".list-details").each(function(){
			if($(this).css("display") == "block"){$(this).hide("fade",1000);}
		});
		$(obj).parent().children(".list-details").show("fade",1000);
	}
}

function deleteItem(obj){
	$(obj).parent().hide("fade",500,function(){
		var nuid = parseInt($(obj).parent().parent().parent().children("label").children(".current-length").html()) - 1;
		$(obj).parent().parent().parent().children("label").children(".current-length").html(nuid);
		var id = "#" + $(obj).parent().parent().attr("id");
		id = id.replace("-list","-drop");
		$(obj).parent().remove();
		if($(id).data("maxfiles") > nuid){$(id).show("fade",1000);}
	});
}

function deleteListItem(obj){
	
	$(obj).parent().parent().hide("fade",500,function(){
		
		/* do we have any instances of -texthtml */
		$(obj).parent().parent().children("div.huge").children("textarea.html").each(function(){
			var id =$(this).attr("id");
			CKEDITOR.instances[id].destroy(); 
		});
		var nuid = parseInt($(obj).parent().parent().parent().parent().children("label").children(".current-length").html()) - 1;
		$(obj).parent().parent().parent().parent().children("label").children(".current-length").html(nuid);
		var id = "#" + $(obj).parent().parent().parent().attr("id");
		id = id.replace("-list","-drop");
		$(obj).parent().parent().remove();
		if($(id).data("maxfiles") > nuid){$(id).show("fade",1000);}
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
	if(type=="imagelist"){
		var lnk = $(obj).data('link');
		var etype = $(obj).data('etype');
		var eeditor = $(obj).data('eeditor');
		var elanguage = $(obj).data('elanguage');
		var elabel = $(obj).data('elabel');
	}
	$(obj).filedrop({
		url: '/settings/ajax/upload',
		paramname: type,
		maxFiles: 1, /* maximum parallel uploads, not to be confused with maximum allowed files */
		maxfilesize: maxsize,
		allowedfiletypes: allowed.split(","),
		data: {
			ftypes: allowed
		},
		dragOver: function () {
			//$(this).css('background','blue');
		},
		dragLeave: function () {
			//$(this).css('background','gray');
		},
		drop: function () {
			//$(this).css('background','gray');
		},
		globalProgressUpdated: function (progress) {
			$(did + ' .progressbar').width(progress+"%");
		},
		afterAll: function () {
			$(did + ' .progressbar').hide("puff", 1000, function(){
				$(did + ' .progressbar').width("0%");
				$(did + ' .progressbar').show();
			});
			var nuid = $(did).data("nuid") + 1;
			$(did).data("nuid",nuid);
			$(dul).parent().children("label").children(".current-length").html($(dul + " li").length);
			if($(dul + " li").length == maxfiles){
				$(did).hide("fade",500);
			}
		},
		uploadFinished: function (i, file, response, time) {
			var s = response.indexOf("*START*") + 7;
			var e = response.indexOf("*END*") - s;
			var nfile = response.substr(s,e).split("*");
			var nuid = $(did).data("nuid");
			var r = ref.replace("#","") + nuid + "-";
			if(type=="imagelist"){
				
				var li = "<li>\n";
				li += "<input type='hidden' name='" + r + "name' id='" + r + "name' value='" + nfile[0].replace(".webp","") + "' >\n";
				li += "<label for='" + r + "desc' class='blank whopper ten-bottom'>\n";
				li +="Description <span class='current-length tt'>" + nfile[2].length + "</span><span class='tt'> of 1024</span>";
				li += "<a title='delete' onclick='deleteListItem(this);'><img class='blank icon fr' src='/settings/resources/files/images/private/group-button-delete.png'></a>\n";
				li += "<a class='hand' title='drag and drop'><img class='blank icon fr' src='/settings/resources/files/images/private/group-button-grip.png'></a>\n";
				li += "<a class='download' title='download' target='_blank' href='/settings/resources/files/images/cms/" + nfile[0] +"'>\n";
				li += "<img class='blank fr tiny-thumb' src='/settings/resources/files/images/cms/" + nfile[0] +"'>\n";
				li += "</a>\n";
				li += "</label>\n";
				li += "<input type='text' class='blank textbox large fr' name='" + r + "desc' id='" + r + "desc' maxlength='1024' value='" + nfile[2] + "' >\n";
				li += "<div class='blank cb ten-space-vert'></div>\n";
				if(lnk==1){
					li += "<label for='" + r + "link' class='blank fl'>\n";
					li += "Link <span class='current-length tt'>6</span><span class='tt'> of 255</span>\n";
					li += "</label>\n";
					li += "<input type='text' class='blank textbox mini fr' name='" + r + "link' id='" + r + "link' maxlength='255' value='[none]'>\n";
					li += "<div class='blank cb ten-space-vert'></div>\n";
				}
				if(etype!="none"){
					if(etype=="plain"){
						li += "<label for='" + r + "textplain' class='blank fl'>" + elabel.replace(/\b./g, function(m){ return m.toUpperCase(); }) + " <span class='current-length tt'>0</span><span class='tt'> of 100000 </span></label>\n";
						li += "<div class='blank fl huge'>\n";
						li += "<textarea data-editor='' class='blank textarea huge plain' name='" + r + "textplain' id='" + r + "textplain' maxlength='100000'></textarea>\n";
						li += "</div>\n";
						li += "<div class='blank cb ten-space-vert'></div>\n";
					} else {
						li += "<label for='" + r + "texthtml' class='blank fl'>" + elabel.replace(/\b./g, function(m){ return m.toUpperCase(); }) + ' ';
						li += "<span id='" + r + "texthtml-language' class='tt'>" + elanguage + "</span></label>\n";
						li += "<div class='blank fl huge'>\n";
						li += "<textarea data-editor='" + eeditor + "' class='blank textarea huge html " + eeditor + "' name='" + r + "texthtml' id='" + r + "texthtml' maxlength='100000'></textarea>\n";
						li += "</div>\n";
						li += "<div class='blank cb ten-space-vert'></div>\n";
					}
				}
				li += "</li>\n";
				$(dul).append(li);
				countTextbox('.textbox,.textarea.plain');
				if(etype=="html"){
					launchCK('.' + eeditor);
					ckSortable();
				}
			} else if(type=="filelist") {
				var li = "<li>\n";
				li += "<input type='hidden' name='" + r + "name' id='" + r + "name' value='" + nfile[0] + "' >\n";
				li += "<input type='hidden' name='" + r + "size' id='" + r + "size' value='" + nfile[3] + "' >\n";
				li += "<input type='hidden' name='" + r + "type' id='" + r + "type' value='" + nfile[1] + "' >\n";
				li += "<label for='" + r + "desc' class='blank whopper ten-bottom'>\n";
				li +="Description <span class='current-length tt'>" + nfile[2].length + "</span><span class='tt'> of 1024</span>";
				li += "<a title='delete' onclick='deleteListItem(this);'><img class='blank icon fr' src='/settings/resources/files/images/private/group-button-delete.png'></a>\n";
				li += "<a class='download' title='download' target='_blank' href='/settings/resources/files/documents/" + nfile[0] +"'><img class='blank icon fr' src='/settings/resources/files/images/private/group-button-download.png'></a>\n";
				li += "<a class='hand' title='drag and drop'><img class='blank icon fr' src='/settings/resources/files/images/private/group-button-grip.png'></a>\n";
				li += "</label>\n";
				li += "<input type='text' class='blank textbox large fr' name='" + r + "desc' id='" + r + "desc' maxlength='1024' value='" + nfile[2] + "' >\n";
				li += "<div class='blank cb ten-space-vert'></div>\n";
				li += "</li>\n";
				$(dul).append(li);
				countTextbox('.textbox,.textarea.plain');
			}
		}
	});	
}

function countTextbox(obj){
	$(obj).bind('keyup',function(){
		$("label[for='" + $(this).attr("id") + "']").children('.current-length').html($(this).val().length);
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
		cursor: 'move',
		opacity:0.8,
		cancel: '.cke_resizer',
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

function contentList(who){
	$(".content-list-type").hide();
	$("#content-list-type-" + $("#content-list-types option:selected").val()).show();
	$.ajax({type: "POST",
			url: "/settings/ajax/contentfilter",
			cache: false,
			data: {user: who,filter: $("#content-list-types option:selected").val()}
	});
}

/* we need to cache references to the individual ckeditors */
var ckStore = {};

$(document).ready(function(){
	
	/* content list page-type dropdown */
	if($('#content-list-types').length > 0) {
		$("#content-list-types").change(function(){
			$(".content-list-type").hide();
			$("#content-list-type-" + $("#content-list-types").val()).show();
		});
	}
	
	/* launch ckEditor by class */
	if($('.ckPrivate').length > 0){launchCK('.ckPrivate');}
	if($('.ckPublic').length > 0){launchCK('.ckPublic');}

   
	/* enable character counting on plain textareas and textboxes */
	countTextbox('.textbox,.textarea.plain');
	
	/* make certain ul objects sortable with fixes for ckeditor */
	ckSortable();
	
	/* configure drop zones for files and images */
	$('.drop').each( function(){dropZone(this);});
});


