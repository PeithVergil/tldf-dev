function hasXHR2()
{
	var progEv = !!(window.ProgressEvent);
	var fdata = !!(window.FormData);
	var wCreds = window.XMLHttpRequest && "withCredentials" in new XMLHttpRequest;
	//alert(progEv + '|' + fdata + '|' + wCreds);
	return progEv && fdata && wCreds;
}

function hasFlash()
{
	if (typeof swfobject !== 'undefined') {
		//alert(1);
		if (swfobject.getFlashPlayerVersion().major !== 0) {
			//alert(2);
			return true;
		}
	}
	return false;
}

function getFileExtension(filename)
{
	var ext = /^.+\.([^.]+)$/.exec(filename);
	return ext == null ? '' : ext[1].toLowerCase();
}

function flash_popup(url, title, width, height)
{
	window.open(url, title, 'menubar=0,resizable=1,scrollbars=0,status=0,toolbar=0,width='+width+',height='+height);
}

// function to confirm Shout deletion
function DeleteShout(pId)
{
	if (pId > 0)
	{
		var lobjConfirm = confirm("Are you sure to delete the Shout?");
		if (lobjConfirm) {
			redirectUrl = "shoutbox.php?sel=del&id=" + pId;
			//alert(redirectUrl);
			location.href = redirectUrl;
		}
	}
}

var currImgNum = 0;
function ShowImgNext()
{
	lobjCon = document.getElementById('imgContainer');
	arrImg = lobjCon.getElementsByTagName('img');
	//alert (arrImg.length);
	
	if (currImgNum < arrImg.length - 1) {
		arrImg[currImgNum].className = "disp_none";
		//alert(currImgNum);
		currImgNum = parseInt(currImgNum) + 1;
		//alert(currImgNum);
		arrImg[currImgNum].className = "disp_block";
		document.getElementById('spnImgShowing').innerHTML = currImgNum + 1;
	}
}

function ShowImgPrev()
{
	lobjCon = document.getElementById('imgContainer');
	arrImg = lobjCon.getElementsByTagName('img');
	//alert (arrImg.length);
	
	if (currImgNum > 0) {
		arrImg[currImgNum].className = "disp_none";
		//alert(currImgNum);
		currImgNum = parseInt(currImgNum) - 1;
		//alert(currImgNum);
		arrImg[currImgNum].className = "disp_block";
		document.getElementById('spnImgShowing').innerHTML = currImgNum+1;
	}
}

function ExpandCollapseBox(pBlockId)
{
	//alert(pBlockId);
	lObj = document.getElementById(pBlockId);
	if (lObj) {
		if (lObj.className == "disp_block") {
			lObj.className = "disp_none";
		} else {
			lObj.className = "disp_block";
		}
	}
}

var arrLadiesPrivacy = new Array('vis_lady_1', 'vis_lady_2', 'vis_lady_3');
var arrGentsPrivacy = new Array('vis_guy_1', 'vis_guy_2', 'vis_guy_3', 'vis_guy_4');

function SetDisableProperty(pArray, pState)
{
	for (i = 0; i < pArray.length; i++) {
		//alert(pArray[i]);
		lObj = document.getElementById(pArray[i]);
		if (pState == 1) {
			lObj.disabled = true;
		} else {
			lObj.disabled = false;	
		}
	}
}

function SetSelectedProperty(pArray, pState)
{
	for (i = 0; i < pArray.length; i++) {
		//alert(pArray[i]);
		lObj = document.getElementById(pArray[i]);
		if (pState == 1) {
			lObj.checked = true;
		} else {
			lObj.checked = false;	
		}
	}
}

// Ladies Options
function SetLadiesVisibleToNone()
{
	SetDisableProperty(arrLadiesPrivacy, 1);
	SetSelectedProperty(arrLadiesPrivacy, 0);
}

function SetLadiesVisibleToAll()
{
	SetDisableProperty(arrLadiesPrivacy, 1);
	SetSelectedProperty(arrLadiesPrivacy, 1);
}

function SetLadiesVisibleToSelected()
{
	SetDisableProperty(arrLadiesPrivacy, 0);
	SetSelectedProperty(arrLadiesPrivacy, 1);
}

// Gents Options
function SetGentsVisibleToNone()
{
	SetDisableProperty(arrGentsPrivacy, 1);
	SetSelectedProperty(arrGentsPrivacy, 0);
}

function SetGentsVisibleToAll()
{
	SetDisableProperty(arrGentsPrivacy, 1);
	SetSelectedProperty(arrGentsPrivacy, 1);
}

function SetGentsVisibleToSelected()
{
	SetDisableProperty(arrGentsPrivacy, 0);
	SetSelectedProperty(arrGentsPrivacy, 1);
}

function CheckSelectedOptions(pObj,pGender)
{
	lstrBool = false;
	
	if (pGender == 1) {
		larrObj = arrLadiesPrivacy;	
	} else {
		larrObj = arrGentsPrivacy;	
	}
	
	if (!pObj.checked) {
		//alert('checked');
		var len = larrObj.length;
		for (i = 0; i < len; i++) {
			//alert(pArray[i]);
			lObj = document.getElementById(larrObj[i]);
			if (lObj.checked) {
				lstrBool = true;
			}
		}
		//alert(lstrBool);
		if (!lstrBool) {
			alert('Please keep at least one option selected.');
			pObj.checked = true;
		}
	}
}


function CheckPromotionOptions(pObj)
{
	lstrBool = false;
	lObjChk1 = document.getElementById('promotion_1');
	lObjChk2 = document.getElementById('promotion_2');
	lObjChk3 = document.getElementById('promotion_3');
	
	if (pObj.checked) {
		if (pObj.id == 'promotion_1') {
			lObjChk2.checked = false;
			lObjChk3.checked = false;
		}
		if (pObj.id == 'promotion_2' || pObj.id == 'promotion_3') {
			lObjChk1.checked = false;
		}
	}
}

//#A VP checking book mark in Query String
function checkBookMark()
{
	var url = window.location.toString();
	
	if (url.indexOf("#") != -1) {
		//alert("Book Mark is there");
		var QS = url;
		var indexOfHash = QS.indexOf("#") + 1;
		strBookMark = QS.substring(indexOfHash);
		//alert(strBookMark);
		return strBookMark;
	} else {
		//alert("There is No Book Mark");
	}
}

function RunJS(dest, response)
{
	if (response.search(/<script/) == -1){
		dest.innerHTML = response;
		return false;
	}
	
	//Check user browser
	var agent = (window.navigator.appName == 'Netscape') ? 'Firefox' : 'IE';
	
	if (agent == 'IE') {
		///place &nbsp; before <scrript> tag
		dest.innerHTML = response;
		var arr_scripts = dest.getElementsByTagName("script");
		for (i in arr_scripts) {
			var src = arr_scripts[i].src;
			if (null != src) {
				var has_question = (src.indexOf('?', 1) > 0 ? true : false);
				if (0 == src.length) {
					arr_scripts[i].setAttribute('src', '');
				} else {
					arr_scripts[i].setAttribute('src', arr_scripts[i].src + (has_question ? '&' : '?') + '1=1');
				}
			}
		}
	} else {
		//Mozilla
		var d = dest.parentNode;
		var d_new = document.createElement('div');

		//Set same attributes as original
		var attributes = dest.attributes;
		var len = attributes.length;

		d_new.innerHTML = response;
		for (var i = 0; i < len; i++) {
			d_new.setAttribute(attributes[i].name, attributes[i].value);
		}

		d.replaceChild(d_new, dest);
	}
}

opacity = 0;
i1 = 0;
i2 = 0;
t1 = 0;

function noticeCode(code)
{
	//if(code == '') return false;
	addNoticeDiv(code);
	divTag = document.getElementById('notice_div');
	opacity = 0;
	i1 = setInterval('increaseOpacity(divTag);' , 2);
}

function increaseOpacity(divTag)
{
	opacity++;
	if (opacity >= 100) {
		clearInterval(i1);
		setTimeout("i2 = setInterval('decreaseOpacity(divTag)', 5)", 3000);
		return false;
	}
	setOpacity(divTag, opacity);
}

function decreaseOpacity(divTag)
{
	opacity = opacity-10;
	if (opacity <= 1) {
		clearInterval(i2);
		clearTimeout(t1);
		document.body.removeChild(divTag);
		return false;
	}
	setOpacity(divTag, opacity);
}

function setOpacity(divTag, i)
{
	divTag.style.filter = 'alpha(opacity=' + i + ')';
	divTag.style.opacity = i/100;
}

function addNoticeDiv(code)
{
	if (document.getElementById('notice_div')) {
		clearInterval(i1);
		clearInterval(i2);
		clearTimeout(t1);
		divTag = document.getElementById('notice_div');
		if (divTag) {
			document.body.removeChild(divTag);
		}
	}
	
	var divTag = document.createElement("div"); 
  
	divTag.id = 'notice_div'; 
	divTag.setAttribute('align','center'); 
	divTag.style.margin = '0px auto'; 
	divTag.style.border = '1px solid #000000';
	divTag.style.background = '#ffffff';
	divTag.style.padding = '15px'
	
	if (navigator.appName == 'Microsoft Internet Explorer') {
		divTag.style.position = 'absolute';
		divTag.style.top = document.body.scrollTop+10;
	} else {	
		divTag.style.position = 'fixed';
		divTag.style.top = '10px';
	}
	divTag.style.left = '10px';
	
	divTag.style.filter = 'alpha(opacity=1)';
	divTag.style.opacity = '0.01';
	
	divTag.className ="dynamicDiv"; 
	divTag.innerHTML = code;
	document.body.appendChild(divTag);	
}

/*
 * START: location.js
 */

/**
 * comment by ralf
 * would be nice to replace all this ajax stuff with jquery
 * consolidated from /templates/dtl_theme_n/location.js and /templates/admin/location.js
 **/

var req = null;

function InitXMLHttpRequest()
{
	// Make a new XMLHttp object
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		req = new ActiveXObject('Microsoft.XMLHTTP');
	}
//	if (typeof window.ActiveXObject != 'undefined' ) req = new ActiveXObject("Microsoft.XMLHTTP");
//	else req = new XMLHttpRequest();
}

function ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous)
{
	if (str != '') {
		str = str + '&ajax=1';
		InitXMLHttpRequest();
		// Load the result from the response page
		if (req) {
			if (anisochronous){
				req.onreadystatechange = function() {
					if (req.readyState == 4) {
						//destination_odj.innerHTML = req.responseText;
						RunJS(destination_odj,req.responseText);
					} else {
						destination_odj.innerHTML = tmp_text;
					}
				}
			}
			req.open('POST', file_name + '?rnd=' + Math.random(), anisochronous);
			req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8 ');
			req.setRequestHeader('Content-length', str.length);
      		req.setRequestHeader('Connection', 'close');
			req.send(str);
			if (!anisochronous) RunJS(destination_odj,req.responseText);
		} else {
			destination_odj.innerHTML = 'Browser unable to create XMLHttp Object';
		}        
	} else {
		destination_odj.innerHTML = 'no string';
	}
}

//VP
function ajaxRequestPage(file_name, destination)
{
	InitXMLHttpRequest();
	// Load the result from the response page
	if (req) {
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				destination.innerHTML = req.responseText;
			} else {
				destination.innerHTML = '<span>Loading data...</span>';
			}
		}
		req.open('GET', file_name, true);
		req.send(null);
	} else {
		destination.innerHTML = 'Browser unable to create XMLHttp Object';
	}
}

function ajaxRequestAdminProUser(file_name, str, destination_odj, tmp_text, anisochronous)
{
	if (str != '') {
		str = str + '&ajax=1';
		InitXMLHttpRequest();
		// Load the result from the response page
		if (req) {
			if (anisochronous) {
				req.onreadystatechange = function() {
					if (req.readyState == 4) {
						//destination_odj.innerHTML = req.responseText;
						RunJS(destination_odj, req.responseText);
						window.close();
						opener.focus();
						return;
					} else {
						destination_odj.innerHTML = tmp_text;
					}
				}
			}
			req.open('POST', file_name+'?rnd='+Math.random(), anisochronous);
			req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8 ');
			req.setRequestHeader('Content-length', str.length);
      		req.setRequestHeader('Connection', 'close');
			req.send(str);
			if (!anisochronous) {
				RunJS(destination_odj, req.responseText);
			}
		} else {
			destination_odj.innerHTML = 'Browser unable to create XMLHttp Object';
		}        
	} else {
		destination_odj.innerHTML = 'no string';
	}
}

function SelectCountry(section, destination)
{
	InitXMLHttpRequest();
	// Load the result from the response page
	if (req) {
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				destination.innerHTML = req.responseText;
			} else {
				if (section == 'rp') {
					destination.innerHTML = '<select style="width:150px;"><option>Loading data...</option></select>';
				} else {
					destination.innerHTML = '<select style="width:150px;" class="index_select"><option>Loading data...</option></select>';
				}
			}
		}
		req.open('GET', 'location.php?sec=' + section + '&sel=country', true);
		req.send(null);
	} else {
		destination.innerHTML = 'Browser unable to create XMLHttp Object';
	}
}

function SelectRegion(section, id_country, destination, destination2)
{
	if (id_country != '') {
		InitXMLHttpRequest();
		// Load the result from the response page
		if (req) {
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					destination.innerHTML = req.responseText;
				} else {
					if (section == 'rp') {
						destination.innerHTML = '<select style="width:150px"><option>Loading data...</option></select>';
					} else {
						destination.innerHTML = '<select style="width:150px" class="index_select"><option>Loading data...</option></select>';
					}
				}
			}
			req.open('GET', 'location.php?sec=' + section + '&sel=region&id_country=' + id_country, true);
			req.send(null);
		} else {
			destination.innerHTML = 'Browser unable to create XMLHttp Object';
		}
	} else {
		destination.innerHTML = 'Country is not selected';
	}
	
	if (section == 'as') {
		destination2.innerHTML = '<select style="width:150px" class="index_select"><option>All</option></select>';
	} else {
		if (section == 'rp') {
			destination2.innerHTML = '<select style="width:150px"><option>Please select...</option></select>';
		} else {
			destination2.innerHTML = '<select style="width:150px" class="index_select"><option>Please select...</option></select>';
		}

	}
}

function SelectRegionAdmin(id_country, destination, destination2)
{
	if (id_country != '') {
		InitXMLHttpRequest();
		// Load the result from the response page
		if (req) {
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					destination.innerHTML = req.responseText;
				} else {
					destination.innerHTML = 'Loading data...';
				}
			}
			req.open('GET', 'admin_location.php?sel=region&id_country=' + id_country, true);
			req.send(null);
		} else {
			destination.innerHTML = 'Browser unable to create XMLHttp Object';
		}
	} else {
		destination.innerHTML = 'Country is not selected';
	}
	destination2.innerHTML = '';
}

function SelectCity(section, id_region, destination)
{
	if (id_region != '') {
		InitXMLHttpRequest();
		// Load the result from the response page
		if (req) {
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					destination.innerHTML = req.responseText;
				} else {
					if (section == 'rp') {
						destination.innerHTML = '<select style="width:150px"><option>Loading data...</option></select>';
					} else {
						destination.innerHTML = '<select style="width:150px" class="index_select"><option>Loading data...</option></select>';
					}
				}
			}
			req.open('GET', 'location.php?sec=' + section + '&sel=city&id_region=' + id_region, true);
			req.send(null);
		} else {
			destination.innerHTML = 'Browser unable to create XMLHttp Object';
		}
	} else {
		destination.innerHTML = 'Region is not selected';
	}
}

function SelectCityAdmin(id_region, destination)
{
	if (id_region != '') {
		InitXMLHttpRequest();
		// Load the result from the response page
		if (req) {
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					destination.innerHTML = req.responseText;
				} else {
					destination.innerHTML = 'Loading data...';
				}
			}
			req.open('GET', 'admin_location.php?sel=city&id_region=' + id_region, true);
			req.send(null);
		} else {
			destination.innerHTML = 'Browser unable to create XMLHttp Object';
		}
	} else {
		destination.innerHTML = 'Region is not selected';
	}
}

function CheckLogin(section, login, destination)
{
	if (login != '') {
		InitXMLHttpRequest();
		// Load the result from the response page
		if (req) {
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					destination.innerHTML = req.responseText;
				}
			}
			req.open('GET', 'location.php?sec=' + section + '&sel=login&login=' + login, true);
			req.send(null);
		} else {
			destination.innerHTML = 'Browser unable to create XMLHttp Object';
		}
	} else {
		destination.innerHTML = 'Username is empty';
	}
}

/**
 * analysis and documentation of requests in multimedia album part by ralf
 *
 * icon tab				: ShowTab(7, './myprofile.php?sel=4', 1)
 * photos tab			: ShowTab(8, './myprofile.php?sel=4', 1)
 * audio tab			: ShowTab(9, './myprofile.php?sel=4', 1)
 * video tab			: ShowTab(10, './myprofile.php?sel=4', 1)
 * photo album count	: ShowTab(8, './myprofile.php?sel=4', 1)
 * audio album count	: ShowTab(9, './myprofile.php?sel=4', 1)
 * video album count	: ShowTab(10, './myprofile.php?sel=4', 1)
 * add photo album		: ShowTab(8, './myprofile.php?sel=4', 2)
 * add audio album		: ShowTab(9, './myprofile.php?sel=4', 2)
 * add video album		: ShowTab(10, './myprofile.php?sel=4', 2)
 * insert photo album	: submit action="myprofile.php?sel=save_album" hidden: album_type=1
 * insert audio album	: submit action="myprofile.php?sel=save_album" hidden: album_type=2
 * insert video album	: submit action="myprofile.php?sel=save_album" hidden: album_type=3
 * edit photo album		: ShowTab(8, './myprofile.php?sel=4&id_album=302', 3)
 * edit audio album		: ShowTab(9, './myprofile.php?sel=4&id_album=305', 3)
 * edit video album		: ShowTab(10, './myprofile.php?sel=4&id_album=303', 3)
 * update photo album	: submit action="myprofile.php?sel=save_album" hidden: album_type=1, edit_album=1, id_album=302
 * update audio album	: submit action="myprofile.php?sel=save_album" hidden: album_type=2, edit_album=1, id_album=305
 * update video album	: submit action="myprofile.php?sel=save_album" hidden: album_type=3, edit_album=1, id_album=303
 * delete photo album	: myprofile.php?sel=del_album&id_album=302&sub=8
 * delete audio album	: myprofile.php?sel=del_album&id_album=305&sub=9
 * delete video album	: myprofile.php?sel=del_album&id_album=303&sub=10
 * browse photo album	: ShowTab(8, './myprofile.php?sel=4&id_album=302', 4)
 * browse audio album	: ShowTab(9, './myprofile.php?sel=4&id_album=305', 4)
 * browse video album	: ShowTab(10, './myprofile.php?sel=4&id_album=303', 4)
 * update photo			: multiple form, onclick=document_upload_photo_N.submit() with N being the index of the form, min. 0
 *						  action="" hidden: sel=save_9, id_file=1234, id_album=302, upload_type=f
 * update audio			: multiple form, onclick=document_upload_audio_N.submit() with N being the index of the form, min. 0
 *						  action="" hidden: sel=save_9, id_file=1234, id_album=305, upload_type=a
 * update video			: multiple form, onclick=document_upload_video_N.submit() with N being the index of the form, min. 0
 *						  action="" hidden: sel=save_9, id_file=1234, id_album=303, upload_type=v
 * insert photo			: multiple form, onclick=document_upload_photo_N.submit() with N being the index of the form, min. 1
 *						  action="" hidden: sel=save_9, id_album=302, upload_type=f
 * insert audio			: multiple form, onclick=document_upload_audio_N.submit() with N being the index of the form, min. 1
 *						  action="" hidden: sel=save_9, id_album=305, upload_type=a
 * insert video			: multiple form, onclick=document_upload_video_N.submit() with N being the index of the form, min. 1
 *						  action="" hidden: sel=save_9, id_album=303, upload_type=v
 * delete photo			: location.href='./myprofile.php?sel=upload_del&id_file=1234&type_upload=f
 * delete audio			: location.href='./myprofile.php?sel=upload_del&id_file=1234&type_upload=a
 * delete video			: location.href='./myprofile.php?sel=upload_del&id_file=1234&type_upload=v
 *
 * bad code: req.open("GET", mlink + "&sub=" + sel + "&act=ajax&action=" + sub, true);
 *
 * sub		: 1=description, 2=my fact sheet, 3=criteria, 4=multimedia, 5=rating, 6=tags
			  then inside multimedia: 7=icon, 8=photos, 9=audio, 10=video
 *			  (much of the multimedia code is generic so we need the sub for selecting the media type)
 * action	: 1=album list, 2=create album, 3=edit album, 4=browse album
 *			  (used for showing and hiding content on the template which is returned with ajax)
 * sec_par	: 1=edit profile, 2=view profile
 *
 **/

function ShowTab(sub, mlink, sec_par)
{
	if (sub <= 6) {
		// not multimedia sub-page
		document.getElementById('menu1').className = 'tab_first';
		document.getElementById('menu2').className = 'tab';
		document.getElementById('menu3').className = 'tab';
		document.getElementById('menu4').className = 'tab_last';
		//document.getElementById('menu5').className = 'tab';
		//document.getElementById('menu6').className = 'tab_last';
		
		if (sub == 1) {
			document.getElementById('menu1').className = 'tab_active_first';
		} else if (sub == 4) {
			document.getElementById('menu4').className = 'tab_active_last';
		} else {
			document.getElementById('menu'+sub).className = 'tab_active';
		}
	}
	
	$('#tab_div').load(mlink + '&act=ajax', function() {
		// be careful to attach tooltips only to ajax content
		$(this).find('label').tooltip();
		$(this).find('.tooltip').tooltip();
		$(this).find('.video_colorbox').colorbox({iframe:true, innerWidth:700, innerHeight:450});
	});
}

function VoteAction(id_upload, vote, id_category, destination, upload_type)
{
	InitXMLHttpRequest();
    if (req){
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				destination.innerHTML = req.responseText;
			} else {
				destination.innerHTML = 'Loading...';
			}
		}
		req.open('GET', 'gallary.php?sel=vote&id_upload=' + id_upload + '&vote=' + vote + '&id_category=' + id_category+ '&upload_type=' + upload_type, true);
		req.send(null);
    }
    else{
       destination.innerHTML = 'Browser unable to create XMLHttp Object';
    }
    return;
}
/*
 * END: location.js
 */

/*
 * jQuery Tooltip plugin 1.3
 *
 * http://bassistance.de/jquery-plugins/jquery-plugin-tooltip/
 * http://docs.jquery.com/Plugins/Tooltip
 *
 * Copyright (c) 2006 - 2008 Jörn Zaefferer
 *
 * $Id: jquery.tooltip.js,v 1.1 2009/01/26 13:09:29 pavel Exp $
 * 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
 
(function($) {
	
		// the tooltip element
	var helper = {},
		// the current tooltipped element
		current,
		// the title of the current element, used for restoring
		title,
		// timeout id for delayed tooltips
		tID,
		// IE 5.5 or 6
		IE = $.browser.msie && /MSIE\s(5\.5|6\.)/.test(navigator.userAgent),
		// flag for mouse tracking
		track = false;
	
	$.tooltip = {
		blocked: false,
		defaults: {
			delay: 200,
			fade: false,
			showURL: true,
			extraClass: "",
			top: 15,
			left: 15,
			id: "tooltip"
		},
		block: function() {
			$.tooltip.blocked = !$.tooltip.blocked;
		}
	};
	
	$.fn.extend({
		tooltip: function(settings) {
			settings = $.extend({}, $.tooltip.defaults, settings);
			createHelper(settings);
			return this.each(function() {
					$.data(this, "tooltip", settings);
					this.tOpacity = helper.parent.css("opacity");
					// copy tooltip into its own expando and remove the title
					this.tooltipText = this.title;
					$(this).removeAttr("title");
					// also remove alt attribute to prevent default tooltip in IE
					this.alt = "";
				})
				.mouseover(save)
				.mouseout(hide)
				.click(hide);
		},
		fixPNG: IE ? function() {
			return this.each(function () {
				var image = $(this).css('backgroundImage');
				if (image.match(/^url\(["']?(.*\.png)["']?\)$/i)) {
					image = RegExp.$1;
					$(this).css({
						'backgroundImage': 'none',
						'filter': "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=crop, src='" + image + "')"
					}).each(function () {
						var position = $(this).css('position');
						if (position != 'absolute' && position != 'relative')
							$(this).css('position', 'relative');
					});
				}
			});
		} : function() { return this; },
		unfixPNG: IE ? function() {
			return this.each(function () {
				$(this).css({'filter': '', backgroundImage: ''});
			});
		} : function() { return this; },
		hideWhenEmpty: function() {
			return this.each(function() {
				$(this)[ $(this).html() ? "show" : "hide" ]();
			});
		},
		url: function() {
			return this.attr('href') || this.attr('src');
		}
	});
	
	function createHelper(settings) {
		// there can be only one tooltip helper
		if( helper.parent )
			return;
		// create the helper, h3 for title, div for url
		helper.parent = $('<div id="' + settings.id + '"><h3></h3><div class="body"></div><div class="url"></div></div>')
			// add to document
			.appendTo(document.body)
			// hide it at first
			.hide();
			
		// apply bgiframe if available
		if ( $.fn.bgiframe )
			helper.parent.bgiframe();
		
		// save references to title and url elements
		helper.title = $('h3', helper.parent);
		helper.body = $('div.body', helper.parent);
		helper.url = $('div.url', helper.parent);
	}
	
	function settings(element) {
		return $.data(element, "tooltip");
	}
	
	// main event handler to start showing tooltips
	function handle(event) {
		// show helper, either with timeout or on instant
		if( settings(this).delay )
			tID = setTimeout(show, settings(this).delay);
		else
			show();
		
		// if selected, update the helper position when the mouse moves
		track = !!settings(this).track;
		$(document.body).bind('mousemove', update);
			
		// update at least once
		update(event);
	}
	
	// save elements title before the tooltip is displayed
	function save() {
		// if this is the current source, or it has no title (occurs with click event), stop
		if ( $.tooltip.blocked || this == current || (!this.tooltipText && !settings(this).bodyHandler) )
			return;

		// save current
		current = this;
		title = this.tooltipText;
		
		if ( settings(this).bodyHandler ) {
			helper.title.hide();
			var bodyContent = settings(this).bodyHandler.call(this);
			if (bodyContent.nodeType || bodyContent.jquery) {
				helper.body.empty().append(bodyContent)
			} else {
				helper.body.html( bodyContent );
			}
			helper.body.show();
		} else if ( settings(this).showBody ) {
			var parts = title.split(settings(this).showBody);
			helper.title.html(parts.shift()).show();
			helper.body.empty();
			for(var i = 0, part; (part = parts[i]); i++) {
				if(i > 0)
					helper.body.append("<br/>");
				helper.body.append(part);
			}
			helper.body.hideWhenEmpty();
		} else {
			helper.title.html(title).show();
			helper.body.hide();
		}
		
		// if element has href or src, add and show it, otherwise hide it
		if( settings(this).showURL && $(this).url() )
			helper.url.html( $(this).url().replace('http://', '') ).show();
		else 
			helper.url.hide();
		
		// add an optional class for this tip
		helper.parent.addClass(settings(this).extraClass);

		// fix PNG background for IE
		if (settings(this).fixPNG )
			helper.parent.fixPNG();
			
		handle.apply(this, arguments);
	}
	
	// delete timeout and show helper
	function show() {
		tID = null;
		if ((!IE || !$.fn.bgiframe) && settings(current).fade) {
			if (helper.parent.is(":animated"))
				helper.parent.stop().show().fadeTo(settings(current).fade, current.tOpacity);
			else
				helper.parent.is(':visible') ? helper.parent.fadeTo(settings(current).fade, current.tOpacity) : helper.parent.fadeIn(settings(current).fade);
		} else {
			helper.parent.show();
		}
		update();
	}
	
	/**
	 * callback for mousemove
	 * updates the helper position
	 * removes itself when no current element
	 */
	function update(event)	{
		if($.tooltip.blocked)
			return;
		
		if (event && event.target.tagName == "OPTION") {
			return;
		}
		
		// stop updating when tracking is disabled and the tooltip is visible
		if ( !track && helper.parent.is(":visible")) {
			$(document.body).unbind('mousemove', update)
		}
		
		// if no current element is available, remove this listener
		if( current == null ) {
			$(document.body).unbind('mousemove', update);
			return;	
		}
		
		// remove position helper classes
		helper.parent.removeClass("viewport-right").removeClass("viewport-bottom");
		
		var left = helper.parent[0].offsetLeft;
		var top = helper.parent[0].offsetTop;
		if (event) {
			// position the helper 15 pixel to bottom right, starting from mouse position
			left = event.pageX + settings(current).left;
			top = event.pageY + settings(current).top;
			var right='auto';
			if (settings(current).positionLeft) {
				right = $(window).width() - left;
				left = 'auto';
			}
			helper.parent.css({
				left: left,
				right: right,
				top: top
			});
		}
		
		var v = viewport(),
			h = helper.parent[0];
		// check horizontal position
		if (v.x + v.cx < h.offsetLeft + h.offsetWidth) {
			left -= h.offsetWidth + 20 + settings(current).left;
			helper.parent.css({left: left + 'px'}).addClass("viewport-right");
		}
		// check vertical position
		if (v.y + v.cy < h.offsetTop + h.offsetHeight) {
			top -= h.offsetHeight + 20 + settings(current).top;
			helper.parent.css({top: top + 'px'}).addClass("viewport-bottom");
		}
	}
	
	function viewport() {
		return {
			x: $(window).scrollLeft(),
			y: $(window).scrollTop(),
			cx: $(window).width(),
			cy: $(window).height()
		};
	}
	
	// hide helper and restore added classes and the title
	function hide(event) {
		if($.tooltip.blocked)
			return;
		// clear timeout if possible
		if(tID)
			clearTimeout(tID);
		// no more current element
		current = null;
		
		var tsettings = settings(this);
		function complete() {
			helper.parent.removeClass( tsettings.extraClass ).hide().css("opacity", "");
		}
		if ((!IE || !$.fn.bgiframe) && tsettings.fade) {
			if (helper.parent.is(':animated'))
				helper.parent.stop().fadeTo(tsettings.fade, 0, complete);
			else
				helper.parent.stop().fadeOut(tsettings.fade, complete);
		} else
			complete();
		
		if( settings(this).fixPNG )
			helper.parent.unfixPNG();
	}
	
})(jQuery);

/*
 * END: jQuery Tooltip plugin 1.3
 */

/*
 * 	Easy Slider 1.7 - jQuery plugin
 *	written by Alen Grakalic	
 *	http://cssglobe.com/post/4004/easy-slider-15-the-easiest-jquery-plugin-for-sliding
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */
 
/*
 *	markup example for $("#slider").easySlider();
 *	
 * 	<div id="slider">
 *		<ul>
 *			<li><img src="images/01.jpg" alt="" /></li>
 *			<li><img src="images/02.jpg" alt="" /></li>
 *			<li><img src="images/03.jpg" alt="" /></li>
 *			<li><img src="images/04.jpg" alt="" /></li>
 *			<li><img src="images/05.jpg" alt="" /></li>
 *		</ul>
 *	</div>
 *
 */

(function($) {

	$.fn.easySlider = function(options){
	  
		// default configuration properties
		var defaults = {			
			prevId: 		'prevBtn',
			prevText: 		'Previous',
			nextId: 		'nextBtn',	
			nextText: 		'Next',
			controlsShow:	true,
			controlsBefore:	'',
			controlsAfter:	'',	
			controlsFade:	true,
			animateFade:	true,
			firstId: 		'firstBtn',
			firstText: 		'First',
			firstShow:		false,
			lastId: 		'lastBtn',	
			lastText: 		'Last',
			lastShow:		false,				
			vertical:		false,
			speed: 			800,
			auto:			false,
			pause:			2000,
			continuous:		false, 
			numeric: 		false,
			numericId: 		'controls'
		}; 
		
		var options = $.extend(defaults, options);  
				
		this.each(function() {  
			var obj = $(this); 				
			var s = $("li", obj).length;
			var w = $("li", obj).width(); 
			var h = $("li", obj).height(); 
			var clickable = true;
			obj.width(w); 
			obj.height(h); 
			obj.css("overflow","hidden");
			var ts = s-1;
			var t = 0;
			$("ul", obj).css('width',s*w);			
			
			if(options.continuous){
				$("ul", obj).prepend($("ul li:last-child", obj).clone().css("margin-left","-"+ w +"px"));
				$("ul", obj).append($("ul li:nth-child(2)", obj).clone());
				$("ul", obj).css('width',(s+1)*w);
			}			
			
			if(!options.vertical) $("li", obj).css('float','left');
								
			if(options.controlsShow){
				var html = options.controlsBefore;				
				if(options.numeric){
					html += '<ol id="'+ options.numericId +'"></ol>';
				} else {
					if(options.firstShow) html += '<span id="'+ options.firstId +'"><a href=\"javascript:void(0);\">'+ options.firstText +'</a></span>';
					html += ' <span id="'+ options.prevId +'"><a href=\"javascript:void(0);\">'+ options.prevText +'</a></span>';
					html += ' <span id="'+ options.nextId +'"><a href=\"javascript:void(0);\">'+ options.nextText +'</a></span>';
					if(options.lastShow) html += ' <span id="'+ options.lastId +'"><a href=\"javascript:void(0);\">'+ options.lastText +'</a></span>';				
				}
				
				html += options.controlsAfter;						
				$(obj).after(html);										
			}
			
			if(options.numeric){									
				for(var i=0;i<s;i++){						
					$(document.createElement("li"))
						.attr('id',options.numericId + (i+1))
						.html('<a rel='+ i +' href=\"javascript:void(0);\">'+ (i+1) +'</a>')
						.appendTo($("#"+ options.numericId))
						.click(function(){							
							animate($("a",$(this)).attr('rel'),true);
						}); 												
				}
			} else {
				$("a","#"+options.nextId).click(function(){		
					animate("next",true);
				});
				$("a","#"+options.prevId).click(function(){		
					animate("prev",true);				
				});	
				$("a","#"+options.firstId).click(function(){		
					animate("first",true);
				});				
				$("a","#"+options.lastId).click(function(){		
					animate("last",true);				
				});				
			}
			
			function setCurrent(i){
				i = parseInt(i)+1;
				$("li", "#" + options.numericId).removeClass("current");
				$("li#" + options.numericId + i).addClass("current");
			}
			
			function adjust(){
				if(t>ts) t=0;		
				if(t<0) t=ts;	
				if(!options.vertical) {
					$("ul",obj).css("margin-left",(t*w*-1));
				} else {
					$("ul",obj).css("margin-left",(t*h*-1));
				}
				
				clickable = true;
				if(options.numeric) setCurrent(t);
			}
			
			function animate(dir,clicked){
				if (clickable){
					clickable = false;
					var ot = t;				
					switch(dir){
						case "next":
							t = (ot>=ts) ? (options.continuous ? t+1 : ts) : t+1;						
							break; 
						case "prev":
							t = (t<=0) ? (options.continuous ? t-1 : 0) : t-1;
							break; 
						case "first":
							t = 0;
							break; 
						case "last":
							t = ts;
							break; 
						default:
							t = dir;
							break; 
					}
					var diff = Math.abs(ot-t);
					var speed = diff*options.speed;						
					if(!options.vertical) {
						p = (t*w*-1);
						
						if(options.animateFade==true){
							$("ul",obj).animate(
								{ opacity: 0 }, 
								{ queue:false, duration:speed, complete:function(){
									adjust();
									$("ul",obj).css({marginLeft: p}).animate({ opacity: 1 });
								} }
							);
						}else{
							$("ul",obj).animate(
								{ marginLeft: p }, 
								{ queue:false, duration:speed, complete:adjust }
							);
						}				
					} else {
						p = (t*h*-1);
						if(options.animateFade==true){
							$("ul",obj).animate(
								{ opacity: 0 }, 
								{ queue:false, duration:speed, complete:function(){
									adjust();
									$("ul",obj).css({marginTop: p}).animate({ opacity: 1 });
								} }
							);
						}else{
							$("ul",obj).animate(
								{ marginTop: p }, 
								{ queue:false, duration:speed, complete:adjust }
							);
						}
					}
					
					if(!options.continuous && options.controlsFade){					
						if(t==ts){
							$("a","#"+options.nextId).hide();
							$("a","#"+options.lastId).hide();
						} else {
							$("a","#"+options.nextId).show();
							$("a","#"+options.lastId).show();					
						}
						if(t==0){
							$("a","#"+options.prevId).hide();
							$("a","#"+options.firstId).hide();
						} else {
							$("a","#"+options.prevId).show();
							$("a","#"+options.firstId).show();
						}
					}			
					
					if(clicked) clearTimeout(timeout);
					if(options.auto && dir=="next" && !clicked){
						timeout = setTimeout(function(){
							animate("next",false);
						},diff*options.speed+options.pause);
					}
				}
			}
			// init
			var timeout;
			if(options.auto){
				timeout = setTimeout(function(){
					animate("next",false);
				},options.pause);
			}
			
			if(options.numeric) setCurrent(0);
		
			if(!options.continuous && options.controlsFade){					
				$("a","#"+options.prevId).hide();
				$("a","#"+options.firstId).hide();				
			}
		});
	};
})(jQuery);
/*
 * END: Easy Slider 1.7 - jQuery plugin
 */

/*
 * START: jQuery alertr.js
 */
(function($) {
	$.fn.alertr = function(alertDuration) {
		var element = this;
		if ($(element).length) {
			$(element).fadeIn('slow');
			if (alertDuration) {
				var alerttimer = window.setTimeout(function() {
					$(element).trigger('click');
				}, alertDuration);
			}
			$(element).click(function() {
				window.clearTimeout(alerttimer);
				$(element).fadeOut('slow');
			});
		}
	};
})(jQuery);

// Center Function
jQuery.fn.center = function(parent) {
    if (parent) {
        parent = this.parent();
    } else {
        parent = window;
    }
    this.css({
        "position": "absolute",
        "top": ((($(parent).height() - this.outerHeight()) / 2) + $(parent).scrollTop() + "px"),
        "left": ((($(parent).width() - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
    });
	return this;
};

// New Center Function
jQuery.fn.centernew = function() {
    this.css({
        "position": "absolute",
        "left": (((1000 - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
    });
	return this;
};
/*
 * END: jQuery alertr.js
 */

/*
 * START: jquery.alerts.js
 */
// jQuery Alert Dialogs Plugin
//
// Version 1.1
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 14 May 2009
//
// Visit http://abeautifulsite.net/notebook/87 for more information
//
// Usage:
//		jAlert( message, [title, callback] )
//		jConfirm( message, [title, callback] )
//		jPrompt( message, [value, title, callback] )
// 
// History:
//
//		1.00 - Released (29 December 2008)
//
//		1.01 - Fixed bug where unbinding would destroy all resize events
//
// License:
// 
// This plugin is dual-licensed under the GNU General Public License and the MIT License and
// is copyright 2008 A Beautiful Site, LLC. 
//
(function($) {
	
	$.alerts = {
		
		// These properties can be read/written by accessing $.alerts.propertyName from your scripts at any time
		
		verticalOffset: -75,                // vertical offset of the dialog from center screen, in pixels
		horizontalOffset: 0,                // horizontal offset of the dialog from center screen, in pixels/
		repositionOnResize: true,           // re-centers the dialog on window resize
		overlayOpacity: .01,                // transparency level of overlay
		overlayColor: '#FFF',               // base color of overlay
		draggable: true,                    // make the dialogs draggable (requires UI Draggables plugin)
		closeButton: '&nbsp;Close&nbsp;',   // text for the Close button (added by RS)
		okButton: '&nbsp;OK&nbsp;',         // text for the OK button
		cancelButton: '&nbsp;Cancel&nbsp;', // text for the Cancel button
		dialogClass: null,                  // if specified, this class will be applied to all dialogs
		
		// Public methods
		
		alert: function(message, title, callback) {
			if( title == null ) title = 'Alert';
			$.alerts._show(title, message, null, 'alert', function(result) {
				if( callback ) callback(result);
			});
		},
		
		confirm: function(message, title, callback) {
			if( title == null ) title = 'Confirm';
			$.alerts._show(title, message, null, 'confirm', function(result) {
				if( callback ) callback(result);
			});
		},
			
		prompt: function(message, value, title, callback) {
			if( title == null ) title = 'Prompt';
			$.alerts._show(title, message, value, 'prompt', function(result) {
				if( callback ) callback(result);
			});
		},
		
		// Private methods
		
		_show: function(title, msg, value, type, callback) {
			
			$.alerts._hide();
			$.alerts._overlay('show');
			
			$("BODY").append(
			  '<div id="popup_container">' +
			    '<h1 id="popup_title"></h1>' +
			    '<div id="popup_content">' +
			      '<div id="popup_message"></div>' +
				'</div>' +
			  '</div>');
			
			if( $.alerts.dialogClass ) $("#popup_container").addClass($.alerts.dialogClass);
			
			// IE6 Fix
			var pos = ($.browser.msie && parseInt($.browser.version) <= 6 ) ? 'absolute' : 'fixed'; 
			
			$("#popup_container").css({
				position: pos,
				zIndex: 99999,
				padding: 0,
				margin: 0
			});
			
			$("#popup_title").text(title);
			$("#popup_content").addClass(type);
			$("#popup_message").text(msg);
			// RS 2012/09/03 conversion of \n to <br /> disabled so we won't add any extra line breaks
			// $("#popup_message").html( $("#popup_message").text().replace(/\n/g, '<br />') );
			$("#popup_message").html( $("#popup_message").text() );
			
			$("#popup_container").css({
				minWidth: $("#popup_container").outerWidth(),
				maxWidth: $("#popup_container").outerWidth()
			});
			
			$.alerts._reposition();
			$.alerts._maintainPosition(true);
			
			switch( type ) {
				case 'alert':
					$("#popup_message").after('<div id="popup_panel"><input type="button" value="' + $.alerts.closeButton + '" id="popup_ok" /></div>');
					$("#popup_ok").click( function() {
						$.alerts._hide();
						callback(true);
					});
					$("#popup_ok").focus().keypress( function(e) {
						if( e.keyCode == 13 || e.keyCode == 27 ) $("#popup_ok").trigger('click');
					});
				break;
				case 'confirm':
					$("#popup_message").after('<div id="popup_panel"><input type="button" value="' + $.alerts.okButton + '" id="popup_ok" /> <input type="button" value="' + $.alerts.cancelButton + '" id="popup_cancel" /></div>');
					$("#popup_ok").click( function() {
						$.alerts._hide();
						if( callback ) callback(true);
					});
					$("#popup_cancel").click( function() {
						$.alerts._hide();
						if( callback ) callback(false);
					});
					$("#popup_ok").focus();
					$("#popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) $("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) $("#popup_cancel").trigger('click');
					});
				break;
				case 'prompt':
					$("#popup_message").append('<br /><input type="text" size="30" id="popup_prompt" />').after('<div id="popup_panel"><input type="button" value="' + $.alerts.okButton + '" id="popup_ok" /> <input type="button" value="' + $.alerts.cancelButton + '" id="popup_cancel" /></div>');
					$("#popup_prompt").width( $("#popup_message").width() );
					$("#popup_ok").click( function() {
						var val = $("#popup_prompt").val();
						$.alerts._hide();
						if( callback ) callback( val );
					});
					$("#popup_cancel").click( function() {
						$.alerts._hide();
						if( callback ) callback( null );
					});
					$("#popup_prompt, #popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) $("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) $("#popup_cancel").trigger('click');
					});
					if( value ) $("#popup_prompt").val(value);
					$("#popup_prompt").focus().select();
				break;
			}
			
			// Make draggable
			if( $.alerts.draggable ) {
				try {
					$("#popup_container").draggable({ handle: $("#popup_title") });
					$("#popup_title").css({ cursor: 'move' });
				} catch(e) { /* requires jQuery UI draggables */ }
			}
		},
		
		_hide: function() {
			$("#popup_container").remove();
			$.alerts._overlay('hide');
			$.alerts._maintainPosition(false);
		},
		
		_overlay: function(status) {
			switch( status ) {
				case 'show':
					$.alerts._overlay('hide');
					$("BODY").append('<div id="popup_overlay"></div>');
					$("#popup_overlay").css({
						position: 'absolute',
						zIndex: 99998,
						top: '0px',
						left: '0px',
						width: '100%',
						height: $(document).height(),
						background: $.alerts.overlayColor,
						opacity: $.alerts.overlayOpacity
					});
				break;
				case 'hide':
					$("#popup_overlay").remove();
				break;
			}
		},
		
		_reposition: function() {
			var top = (($(window).height() / 2) - ($("#popup_container").outerHeight() / 2)) + $.alerts.verticalOffset;
			var left = (($(window).width() / 2) - ($("#popup_container").outerWidth() / 2)) + $.alerts.horizontalOffset;
			if( top < 0 ) top = 0;
			if( left < 0 ) left = 0;
			
			// IE6 fix
			if( $.browser.msie && parseInt($.browser.version) <= 6 ) top = top + $(window).scrollTop();
			
			$("#popup_container").css({
				top: top + 'px',
				left: left + 'px'
			});
			$("#popup_overlay").height( $(document).height() );
		},
		
		_maintainPosition: function(status) {
			if( $.alerts.repositionOnResize ) {
				switch(status) {
					case true:
						$(window).bind('resize', $.alerts._reposition);
					break;
					case false:
						$(window).unbind('resize', $.alerts._reposition);
					break;
				}
			}
		}
		
	}
	
	// Shortuct functions
	jAlert = function(message, title, callback) {
		$.alerts.alert(message, title, callback);
	}
	
	jConfirm = function(message, title, callback) {
		$.alerts.confirm(message, title, callback);
	};
		
	jPrompt = function(message, value, title, callback) {
		$.alerts.prompt(message, value, title, callback);
	};
	
})(jQuery);
/*
 * END: jquery.alerts.js
 */

/*
 * START: jQuery popup.js
 */
// RS: used in express_interest_table.tpl, platinum_table.tpl
// for showing congratulation popup.
// suggestion is to replace this with jAlert

/***************************/
//@Author: Adrian "yEnS" Mato Gondelle
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

//SETTING UP OUR POPUP
//0 means disabled; 1 means enabled;
var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopup(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popupContact").fadeIn("slow");
		popupStatus = 1;
	}
}

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#popupContact").fadeOut("slow");
		popupStatus = 0;
	}
}

//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#popupContact").height();
	var popupWidth = $("#popupContact").width();
	//centering
	$("#popupContact").css({
		"position": "absolute",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6
	
	$("#backgroundPopup").css({
		"height": windowHeight
	});
	
}

//VP initializing popup
function create_popup()
{
	//centering with css
	centerPopup();
	//load popup
	loadPopup();
}

//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
	//LOADING POPUP
	//Click the button event!
	/*
	$("#button").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup();
	});
	*/
	
	//CLOSING POPUP
	//Click the x event!
	$("#popupContactClose").click(function(){
		disablePopup();
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup();
	});
	//Press Escape event!
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});

});
/*
 * END: jQuery popup.js
 */

/*
 * START: jquery.bxSlider.js
 */
/**
 * jQuery bxSlider v3.0
 * http://bxslider.com
 *
 * Copyright 2011, Steven Wanderski
 * http://bxcreative.com
 *
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 */


(function($){
	
	$.fn.bxSlider = function(options){		
				
		var defaults = {
			mode: 'horizontal',									// 'horizontal', 'vertical', 'fade'
			infiniteLoop: false,									// true, false - display first slide after last
			hideControlOnEnd: false,						// true, false - if true, will hide 'next' control on last slide and 'prev' control on first
			controls: true,											// true, false - previous and next controls
			speed: 500,													// integer - in ms, duration of time slide transitions will occupy
			easing: 'swing',                    // used with jquery.easing.1.3.js - see http://gsgd.co.uk/sandbox/jquery/easing/ for available options
			pager: false,												// true / false - display a pager
			pagerSelector: null,								// jQuery selector - element to contain the pager. ex: '#pager'
			pagerType: 'full',									// 'full', 'short' - if 'full' pager displays 1,2,3... if 'short' pager displays 1 / 4
			pagerLocation: 'bottom',						// 'bottom', 'top' - location of pager
			pagerShortSeparator: '/',						// string - ex: 'of' pager would display 1 of 4
			pagerActiveClass: 'pager-active',		// string - classname attached to the active pager link
			nextText: 'next',										// string - text displayed for 'next' control
			nextImage: '',											// string - filepath of image used for 'next' control. ex: 'images/next.jpg'
			nextSelector: null,									// jQuery selector - element to contain the next control. ex: '#next'
			prevText: 'prev',										// string - text displayed for 'previous' control
			prevImage: '',											// string - filepath of image used for 'previous' control. ex: 'images/prev.jpg'
			prevSelector: null,									// jQuery selector - element to contain the previous control. ex: '#next'
			captions: false,										// true, false - display image captions (reads the image 'title' tag)
			captionsSelector: null,							// jQuery selector - element to contain the captions. ex: '#captions'
			auto: false,												// true, false - make slideshow change automatically
			autoDirection: 'next',							// 'next', 'prev' - direction in which auto show will traverse
			autoControls: false,								// true, false - show 'start' and 'stop' controls for auto show
			autoControlsSelector: null,					// jQuery selector - element to contain the auto controls. ex: '#auto-controls'
			autoStart: true,										// true, false - if false show will wait for 'start' control to activate
			autoHover: false,										// true, false - if true show will pause on mouseover
			autoDelay: 0,                       // integer - in ms, the amount of time before starting the auto show
			pause: 3000,												// integer - in ms, the duration between each slide transition
			startText: 'start',									// string - text displayed for 'start' control
			startImage: '',											// string - filepath of image used for 'start' control. ex: 'images/start.jpg'
			stopText: 'stop',										// string - text displayed for 'stop' control
			stopImage: '',											// string - filepath of image used for 'stop' control. ex: 'images/stop.jpg'
			ticker: false,											// true, false - continuous motion ticker mode (think news ticker)
																					// note: autoControls, autoControlsSelector, and autoHover apply to ticker!
			tickerSpeed: 5000,								  // float - use value between 1 and 5000 to determine ticker speed - the smaller the value the faster the ticker speed
			tickerDirection: 'next',						// 'next', 'prev' - direction in which ticker show will traverse
			tickerHover: false,                 // true, false - if true ticker will pause on mouseover
			wrapperClass: 'bx-wrapper',					// string - classname attached to the slider wraper
			startingSlide: 0, 									// integer - show will start on specified slide. note: slides are zero based!
			displaySlideQty: 5,									// integer - number of slides to display at once
			moveSlideQty: 1,										// integer - number of slides to move at once
			randomStart: false,									// true, false - if true show will start on a random slide
			onBeforeSlide: function(){},				// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			onAfterSlide: function(){},					// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			onLastSlide: function(){},					// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			onFirstSlide: function(){},					// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			onNextSlide: function(){},					// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			onPrevSlide: function(){},					// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			buildPager: null										// function(slideIndex, slideHtmlObject){ return string; } - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
		}
		
		var options = $.extend(defaults, options);
		
		// cache the base element
		var base = this;
		// initialize (and localize) all variables
		var $parent = '';
		var $origElement = '';
		var $children = '';
		var $outerWrapper = '';
		var $firstChild = '';
		var childrenWidth = '';
		var childrenOuterWidth = '';
		var wrapperWidth = '';
		var wrapperHeight = '';
		var $pager = '';	
		var interval = '';
		var $autoControls = '';
		var $stopHtml = '';
		var $startContent = '';
		var $stopContent = '';
		var autoPlaying = true;
		var loaded = false;
		var childrenMaxWidth = 0;
		var childrenMaxHeight = 0;
		var currentSlide = 0;	
		var origLeft = 0;
		var origTop = 0;
		var origShowWidth = 0;
		var origShowHeight = 0;
		var tickerLeft = 0;
		var tickerTop = 0;
		var isWorking = false;
    
		var firstSlide = 0;
		var lastSlide = $children.length - 1;
		
						
		// PUBLIC FUNCTIONS
						
		/**
		 * Go to specified slide
		 */		
		this.goToSlide = function(number, stopAuto){
			if(!isWorking){
				isWorking = true;
				// set current slide to argument
				currentSlide = number;
				options.onBeforeSlide(currentSlide, $children.length, $children.eq(currentSlide));
				// check if stopAuto argument is supplied
				if(typeof(stopAuto) == 'undefined'){
					var stopAuto = true;
				}
				if(stopAuto){
					// if show is auto playing, stop it
					if(options.auto){
						base.stopShow(true);
					}
				}			
				slide = number;
				// check for first slide callback
				if(slide == firstSlide){
					options.onFirstSlide(currentSlide, $children.length, $children.eq(currentSlide));
				}
				// check for last slide callback
				if(slide == lastSlide){
					options.onLastSlide(currentSlide, $children.length, $children.eq(currentSlide));
				}
				// horizontal
				if(options.mode == 'horizontal'){
					$parent.animate({'left': '-'+getSlidePosition(slide, 'left')+'px'}, options.speed, options.easing, function(){
						isWorking = false;
						// perform the callback function
						options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
					});
				// vertical
				}else if(options.mode == 'vertical'){
					$parent.animate({'top': '-'+getSlidePosition(slide, 'top')+'px'}, options.speed, options.easing, function(){
						isWorking = false;
						// perform the callback function
						options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
					});			
				// fade	
				}else if(options.mode == 'fade'){
					setChildrenFade();
				}
				// check to remove controls on last/first slide
				checkEndControls();
				// accomodate multi slides
				if(options.moveSlideQty > 1){
					number = Math.floor(number / options.moveSlideQty);
				}
				// make the current slide active
				makeSlideActive(number);
				// display the caption
				showCaptions();
			}
		}
		
		/**
		 * Go to next slide
		 */		
		this.goToNextSlide = function(stopAuto){
			// check if stopAuto argument is supplied
			if(typeof(stopAuto) == 'undefined'){
				var stopAuto = true;
			}
			if(stopAuto){
				// if show is auto playing, stop it
				if(options.auto){
					base.stopShow(true);
				}
			}			
			// makes slideshow finite
			if(!options.infiniteLoop){
				if(!isWorking){
					var slideLoop = false;
					// make current slide the old value plus moveSlideQty
					currentSlide = (currentSlide + (options.moveSlideQty));
					// if current slide has looped on itself
					if(currentSlide <= lastSlide){
						checkEndControls();
						// next slide callback
						options.onNextSlide(currentSlide, $children.length, $children.eq(currentSlide));
						// move to appropriate slide
						base.goToSlide(currentSlide);						
					}else{
						currentSlide -= options.moveSlideQty;
					}
				} // end if(!isWorking)		
			}else{ 
				if(!isWorking){
					isWorking = true;					
					var slideLoop = false;
					// make current slide the old value plus moveSlideQty
					currentSlide = (currentSlide + options.moveSlideQty);
					// if current slide has looped on itself
					if(currentSlide > lastSlide){
						currentSlide = currentSlide % $children.length;
						slideLoop = true;
					}
					// next slide callback
					options.onNextSlide(currentSlide, $children.length, $children.eq(currentSlide));
					// slide before callback
					options.onBeforeSlide(currentSlide, $children.length, $children.eq(currentSlide));
					if(options.mode == 'horizontal'){						
						// get the new 'left' property for $parent
						var parentLeft = (options.moveSlideQty * childrenOuterWidth);
						// animate to the new 'left'
						$parent.animate({'left': '-='+parentLeft+'px'}, options.speed, options.easing, function(){
							isWorking = false;
							// if its time to loop, reset the $parent
							if(slideLoop){
								$parent.css('left', '-'+getSlidePosition(currentSlide, 'left')+'px');
							}
							// perform the callback function
							options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
						});
					}else if(options.mode == 'vertical'){
						// get the new 'left' property for $parent
						var parentTop = (options.moveSlideQty * childrenMaxHeight);
						// animate to the new 'left'
						$parent.animate({'top': '-='+parentTop+'px'}, options.speed, options.easing, function(){
							isWorking = false;
							// if its time to loop, reset the $parent
							if(slideLoop){
								$parent.css('top', '-'+getSlidePosition(currentSlide, 'top')+'px');
							}
							// perform the callback function
							options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
						});
					}else if(options.mode == 'fade'){
						setChildrenFade();
					}					
					// make the current slide active
					if(options.moveSlideQty > 1){
						makeSlideActive(Math.ceil(currentSlide / options.moveSlideQty));
					}else{
						makeSlideActive(currentSlide);
					}
					// display the caption
					showCaptions();
				} // end if(!isWorking)
				
			}	
		} // end function
		
		/**
		 * Go to previous slide
		 */		
		this.goToPreviousSlide = function(stopAuto){
			// check if stopAuto argument is supplied
			if(typeof(stopAuto) == 'undefined'){
				var stopAuto = true;
			}
			if(stopAuto){
				// if show is auto playing, stop it
				if(options.auto){
					base.stopShow(true);
				}
			}			
			// makes slideshow finite
			if(!options.infiniteLoop){	
				if(!isWorking){
					var slideLoop = false;
					// make current slide the old value plus moveSlideQty
					currentSlide = currentSlide - options.moveSlideQty;
					// if current slide has looped on itself
					if(currentSlide < 0){
						currentSlide = 0;
						// if specified, hide the control on the last slide
						if(options.hideControlOnEnd){
							$('.bx-prev', $outerWrapper).hide();
						}
					}
					checkEndControls();
					// next slide callback
					options.onPrevSlide(currentSlide, $children.length, $children.eq(currentSlide));
					// move to appropriate slide
					base.goToSlide(currentSlide);
				}							
			}else{
				if(!isWorking){
					isWorking = true;			
					var slideLoop = false;
					// make current slide the old value plus moveSlideQty
					currentSlide = (currentSlide - (options.moveSlideQty));
					// if current slide has looped on itself
					if(currentSlide < 0){
						negativeOffset = (currentSlide % $children.length);
						if(negativeOffset == 0){
							currentSlide = 0;
						}else{
							currentSlide = ($children.length) + negativeOffset; 
						}
						slideLoop = true;
					}
					// next slide callback
					options.onPrevSlide(currentSlide, $children.length, $children.eq(currentSlide));
					// slide before callback
					options.onBeforeSlide(currentSlide, $children.length, $children.eq(currentSlide));
					if(options.mode == 'horizontal'){
						// get the new 'left' property for $parent
						var parentLeft = (options.moveSlideQty * childrenOuterWidth);
						// animate to the new 'left'
						$parent.animate({'left': '+='+parentLeft+'px'}, options.speed, options.easing, function(){
							isWorking = false;
							// if its time to loop, reset the $parent
							if(slideLoop){
								$parent.css('left', '-'+getSlidePosition(currentSlide, 'left')+'px');
							}
							// perform the callback function
							options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
						});
					}else if(options.mode == 'vertical'){
						// get the new 'left' property for $parent
						var parentTop = (options.moveSlideQty * childrenMaxHeight);
						// animate to the new 'left'
						$parent.animate({'top': '+='+parentTop+'px'}, options.speed, options.easing, function(){
							isWorking = false;
							// if its time to loop, reset the $parent
							if(slideLoop){
								$parent.css('top', '-'+getSlidePosition(currentSlide, 'top')+'px');
							}
							// perform the callback function
							options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
						});
					}else if(options.mode == 'fade'){
						setChildrenFade();
					}					
					// make the current slide active
					if(options.moveSlideQty > 1){
						makeSlideActive(Math.ceil(currentSlide / options.moveSlideQty));
					}else{
						makeSlideActive(currentSlide);
					}
					// display the caption
					showCaptions();
				} // end if(!isWorking)				
			}
		} // end function
		
		/**
		 * Go to first slide
		 */		
		this.goToFirstSlide = function(stopAuto){
			// check if stopAuto argument is supplied
			if(typeof(stopAuto) == 'undefined'){
				var stopAuto = true;
			}
			base.goToSlide(firstSlide, stopAuto);
		}
		
		/**
		 * Go to last slide
		 */		
		this.goToLastSlide = function(){
			// check if stopAuto argument is supplied
			if(typeof(stopAuto) == 'undefined'){
				var stopAuto = true;
			}
			base.goToSlide(lastSlide, stopAuto);
		}
		
		/**
		 * Get the current slide
		 */		
		this.getCurrentSlide = function(){
			return currentSlide;
		}
		
		/**
		 * Get the total slide count
		 */		
		this.getSlideCount = function(){
			return $children.length;
		}
		
		/**
		 * Stop the slideshow
		 */		
		this.stopShow = function(changeText){
			clearInterval(interval);
			// check if changeText argument is supplied
			if(typeof(changeText) == 'undefined'){
				var changeText = true;
			}
			if(changeText && options.autoControls){
				$autoControls.html($startContent).removeClass('stop').addClass('start');
				autoPlaying = false;
			}
		}
		
		/**
		 * Start the slideshow
		 */		
		this.startShow = function(changeText){
			// check if changeText argument is supplied
			if(typeof(changeText) == 'undefined'){
				var changeText = true;
			}
			setAutoInterval();
			if(changeText && options.autoControls){
				$autoControls.html($stopContent).removeClass('start').addClass('stop');
				autoPlaying = true;
			}
		}
		
		/**
		 * Stops the ticker
		 */		
		this.stopTicker = function(changeText){
			$parent.stop();
			// check if changeText argument is supplied
			if(typeof(changeText) == 'undefined'){
				var changeText = true;
			}
			if(changeText && options.ticker){
				$autoControls.html($startContent).removeClass('stop').addClass('start');
				autoPlaying = false;
			}			
		}
		
		/**
		 * Starts the ticker
		 */		
		this.startTicker = function(changeText){
			if(options.mode == 'horizontal'){
				if(options.tickerDirection == 'next'){
					// get the 'left' property where the ticker stopped
					var stoppedLeft = parseInt($parent.css('left'));
					// calculate the remaining distance the show must travel until the loop
					var remainingDistance = (origShowWidth + stoppedLeft) + $children.eq(0).width();			
				}else if(options.tickerDirection == 'prev'){
					// get the 'left' property where the ticker stopped
					var stoppedLeft = -parseInt($parent.css('left'));
					// calculate the remaining distance the show must travel until the loop
					var remainingDistance = (stoppedLeft) - $children.eq(0).width();
				}
				// calculate the speed ratio to seamlessly finish the loop
				var finishingSpeed = (remainingDistance * options.tickerSpeed) / origShowWidth;
				// call the show
				moveTheShow(tickerLeft, remainingDistance, finishingSpeed);					
			}else if(options.mode == 'vertical'){
				if(options.tickerDirection == 'next'){
					// get the 'top' property where the ticker stopped
					var stoppedTop = parseInt($parent.css('top'));
					// calculate the remaining distance the show must travel until the loop
					var remainingDistance = (origShowHeight + stoppedTop) + $children.eq(0).height();			
				}else if(options.tickerDirection == 'prev'){
					// get the 'left' property where the ticker stopped
					var stoppedTop = -parseInt($parent.css('top'));
					// calculate the remaining distance the show must travel until the loop
					var remainingDistance = (stoppedTop) - $children.eq(0).height();
				}
				// calculate the speed ratio to seamlessly finish the loop
				var finishingSpeed = (remainingDistance * options.tickerSpeed) / origShowHeight;
				// call the show
				moveTheShow(tickerTop, remainingDistance, finishingSpeed);
				// check if changeText argument is supplied
				if(typeof(changeText) == 'undefined'){
					var changeText = true;
				}
				if(changeText && options.ticker){
					$autoControls.html($stopContent).removeClass('start').addClass('stop');
					autoPlaying = true;
				}						
			}
		}
				
		/**
		 * Initialize a new slideshow
		 */		
		this.initShow = function(){
			
			// reinitialize all variables
			// base = this;
			$parent = $(this);
			$origElement = $parent.clone();
			$children = $parent.children();
			$outerWrapper = '';
			$firstChild = $parent.children(':first');
			childrenWidth = $firstChild.width();
			childrenMaxWidth = 0;
			childrenOuterWidth = $firstChild.outerWidth();
			childrenMaxHeight = 0;
			wrapperWidth = getWrapperWidth();
			wrapperHeight = getWrapperHeight();
			isWorking = false;
			$pager = '';	
			currentSlide = 0;	
			origLeft = 0;
			origTop = 0;
			interval = '';
			$autoControls = '';
			$stopHtml = '';
			$startContent = '';
			$stopContent = '';
			autoPlaying = true;
			loaded = false;
			origShowWidth = 0;
			origShowHeight = 0;
			tickerLeft = 0;
			tickerTop = 0;
      
			firstSlide = 0;
			lastSlide = $children.length - 1;
						
			// get the largest child's height and width
			$children.each(function(index) {
			  if($(this).outerHeight() > childrenMaxHeight){
					childrenMaxHeight = $(this).outerHeight();
				}
				if($(this).outerWidth() > childrenMaxWidth){
					childrenMaxWidth = $(this).outerWidth();
				}
			});

			// get random slide number
			if(options.randomStart){
				var randomNumber = Math.floor(Math.random() * $children.length);
				currentSlide = randomNumber;
				origLeft = childrenOuterWidth * (options.moveSlideQty + randomNumber);
				origTop = childrenMaxHeight * (options.moveSlideQty + randomNumber);
			// start show at specific slide
			}else{
				currentSlide = options.startingSlide;
				origLeft = childrenOuterWidth * (options.moveSlideQty + options.startingSlide);
				origTop = childrenMaxHeight * (options.moveSlideQty + options.startingSlide);
			}
						
			// set initial css
			initCss();
			
			// check to show pager
			if(options.pager && !options.ticker){
				if(options.pagerType == 'full'){
					showPager('full');
				}else if(options.pagerType == 'short'){
					showPager('short');
				}
			}
						
			// check to show controls
			if(options.controls && !options.ticker){
				setControlsVars();
			}
						
			// check if auto
			if(options.auto || options.ticker){
				// check if auto controls are displayed
				if(options.autoControls){
					setAutoControlsVars();
				}
				// check if show should auto start
				if(options.autoStart){
					// check if autostart should delay
					setTimeout(function(){
						base.startShow(true);
					}, options.autoDelay);
				}else{
					base.stopShow(true);
				}
				// check if show should pause on hover
				if(options.autoHover && !options.ticker){
					setAutoHover();
				}
			}						
			// make the starting slide active
			if(options.moveSlideQty > 1){
				makeSlideActive(Math.ceil(currentSlide / options.moveSlideQty));
			}else{			
				makeSlideActive(currentSlide);			
			}
			// check for finite show and if controls should be hidden
			checkEndControls();
			// show captions
			if(options.captions){
				showCaptions();
			}
			// perform the callback function
			options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
		}
		
		/**
		 * Destroy the current slideshow
		 */		
		this.destroyShow = function(){			
			// stop the auto show
			clearInterval(interval);
			// remove any controls / pagers that have been appended
			$('.bx-next, .bx-prev, .bx-pager, .bx-auto', $outerWrapper).remove();
			// unwrap all bx-wrappers
			$parent.unwrap().unwrap().removeAttr('style');
			// remove any styles that were appended
			$parent.children().removeAttr('style').not('.pager').remove();
			// remove any childrent that were appended
			$children.removeClass('pager');
			
		}
		
		/**
		 * Reload the current slideshow
		 */		
		this.reloadShow = function(){
			base.destroyShow();
			base.initShow();
		}
		
		// PRIVATE FUNCTIONS
		
		/**
		 * Creates all neccessary styling for the slideshow
		 */		
		function initCss(){
			// layout the children
			setChildrenLayout(options.startingSlide);
			// CSS for horizontal mode
			if(options.mode == 'horizontal'){
				// wrap the <ul> in div that acts as a window and make the <ul> uber wide
				$parent
				.wrap('<div class="'+options.wrapperClass+'" style="width:'+wrapperWidth+'px; position:relative;"></div>')
				.wrap('<div class="bx-window" style="position:relative; overflow:hidden; width:'+wrapperWidth+'px;"></div>')
				.css({
				  width: '999999px',
				  position: 'relative',
					left: '-'+(origLeft)+'px'
				});
				$parent.children().css({
					width: childrenWidth,
				  'float': 'left',
				  listStyle: 'none'
				});					
				$outerWrapper = $parent.parent().parent();
				$children.addClass('pager');
			// CSS for vertical mode
			}else if(options.mode == 'vertical'){
				// wrap the <ul> in div that acts as a window and make the <ul> uber tall
				$parent
				.wrap('<div class="'+options.wrapperClass+'" style="width:'+childrenMaxWidth+'px; position:relative;"></div>')
				.wrap('<div class="bx-window" style="width:'+childrenMaxWidth+'px; height:'+wrapperHeight+'px; position:relative; overflow:hidden;"></div>')
				.css({
				  height: '999999px',
				  position: 'relative',
					top: '-'+(origTop)+'px'
				});
				$parent.children().css({
				  listStyle: 'none',
					height: childrenMaxHeight
				});					
				$outerWrapper = $parent.parent().parent();
				$children.addClass('pager');
			// CSS for fade mode
			}else if(options.mode == 'fade'){
				// wrap the <ul> in div that acts as a window
				$parent
				.wrap('<div class="'+options.wrapperClass+'" style="width:'+childrenMaxWidth+'px; position:relative;"></div>')
				.wrap('<div class="bx-window" style="height:'+childrenMaxHeight+'px; width:'+childrenMaxWidth+'px; position:relative; overflow:hidden;"></div>');
				$parent.children().css({
				  listStyle: 'none',
				  position: 'absolute',
					top: 0,
					left: 0,
					zIndex: 98
				});					
				$outerWrapper = $parent.parent().parent();
				$children.not(':eq('+currentSlide+')').fadeTo(0, 0);
				$children.eq(currentSlide).css('zIndex', 99);
			}
			// if captions = true setup a div placeholder
			if(options.captions && options.captionsSelector == null){
				$outerWrapper.append('<div class="bx-captions"></div>');
			}			
		}
		
		/**
		 * Depending on mode, lays out children in the proper setup
		 */		
		function setChildrenLayout(){			
			// lays out children for horizontal or vertical modes
			if(options.mode == 'horizontal' || options.mode == 'vertical'){
								
				// get the children behind
				var $prependedChildren = getArraySample($children, 0, options.moveSlideQty, 'backward');
				
				// add each prepended child to the back of the original element
				$.each($prependedChildren, function(index) {
					$parent.prepend($(this));
				});			
				
				// total number of slides to be hidden after the window
				var totalNumberAfterWindow = ($children.length + options.moveSlideQty) - 1;
				// number of original slides hidden after the window
				var pagerExcess = $children.length - options.displaySlideQty;
				// number of slides to append to the original hidden slides
				var numberToAppend = totalNumberAfterWindow - pagerExcess;
				// get the sample of extra slides to append
				var $appendedChildren = getArraySample($children, 0, numberToAppend, 'forward');
				
				if(options.infiniteLoop){
					// add each appended child to the front of the original element
					$.each($appendedChildren, function(index) {
						$parent.append($(this));
					});
				}
			}
		}
		
		/**
		 * Sets all variables associated with the controls
		 */		
		function setControlsVars(){
			// check if text or images should be used for controls
			// check "next"
			if(options.nextImage != ''){
				nextContent = options.nextImage;
				nextType = 'image';
			}else{
				nextContent = options.nextText;
				nextType = 'text';
			}
			// check "prev"
			if(options.prevImage != ''){
				prevContent = options.prevImage;
				prevType = 'image';
			}else{
				prevContent = options.prevText;
				prevType = 'text';
			}
			// show the controls
			showControls(nextType, nextContent, prevType, prevContent);
		}			
		
		/**
		 * Puts slideshow into auto mode
		 *
		 * @param int pause number of ms the slideshow will wait between slides 
		 * @param string direction 'forward', 'backward' sets the direction of the slideshow (forward/backward)
		 * @param bool controls determines if start/stop controls will be displayed
		 */		
		function setAutoInterval(){
			if(options.auto){
				// finite loop
				if(!options.infiniteLoop){
					if(options.autoDirection == 'next'){
						interval = setInterval(function(){
							currentSlide += options.moveSlideQty;
							// if currentSlide has exceeded total number
							if(currentSlide > lastSlide){
								currentSlide = currentSlide % $children.length;
							}
							base.goToSlide(currentSlide, false);
						}, options.pause);
					}else if(options.autoDirection == 'prev'){
						interval = setInterval(function(){
							currentSlide -= options.moveSlideQty;
							// if currentSlide is smaller than zero
							if(currentSlide < 0){
								negativeOffset = (currentSlide % $children.length);
								if(negativeOffset == 0){
									currentSlide = 0;
								}else{
									currentSlide = ($children.length) + negativeOffset; 
								}
							}
							base.goToSlide(currentSlide, false);
						}, options.pause);
					}
				// infinite loop
				}else{
					if(options.autoDirection == 'next'){
						interval = setInterval(function(){
							base.goToNextSlide(false);
						}, options.pause);
					}else if(options.autoDirection == 'prev'){
						interval = setInterval(function(){
							base.goToPreviousSlide(false);
						}, options.pause);
					}
				}
			
			}else if(options.ticker){
				
				options.tickerSpeed *= 10;
												
				// get the total width of the original show
				$('.pager', $outerWrapper).each(function(index) {
				  origShowWidth += $(this).width();
					origShowHeight += $(this).height();
				});
				
				// if prev start the show from the last slide
				if(options.tickerDirection == 'prev' && options.mode == 'horizontal'){
					$parent.css('left', '-'+(origShowWidth+origLeft)+'px');
				}else if(options.tickerDirection == 'prev' && options.mode == 'vertical'){
					$parent.css('top', '-'+(origShowHeight+origTop)+'px');
				}
				
				if(options.mode == 'horizontal'){
					// get the starting left position
					tickerLeft = parseInt($parent.css('left'));
					// start the ticker
					moveTheShow(tickerLeft, origShowWidth, options.tickerSpeed);
				}else if(options.mode == 'vertical'){
					// get the starting top position
					tickerTop = parseInt($parent.css('top'));
					// start the ticker
					moveTheShow(tickerTop, origShowHeight, options.tickerSpeed);
				}												
				
				// check it tickerHover applies
				if(options.tickerHover){
					setTickerHover();
				}					
			}			
		}
		
		function moveTheShow(leftCss, distance, speed){
			// if horizontal
			if(options.mode == 'horizontal'){
				// if next
				if(options.tickerDirection == 'next'){
					$parent.animate({'left': '-='+distance+'px'}, speed, 'linear', function(){
						$parent.css('left', leftCss);
						moveTheShow(leftCss, origShowWidth, options.tickerSpeed);
					});
				// if prev
				}else if(options.tickerDirection == 'prev'){
					$parent.animate({'left': '+='+distance+'px'}, speed, 'linear', function(){
						$parent.css('left', leftCss);
						moveTheShow(leftCss, origShowWidth, options.tickerSpeed);
					});
				}
			// if vertical		
			}else if(options.mode == 'vertical'){
				// if next
				if(options.tickerDirection == 'next'){
					$parent.animate({'top': '-='+distance+'px'}, speed, 'linear', function(){
						$parent.css('top', leftCss);
						moveTheShow(leftCss, origShowHeight, options.tickerSpeed);
					});
				// if prev
				}else if(options.tickerDirection == 'prev'){
					$parent.animate({'top': '+='+distance+'px'}, speed, 'linear', function(){
						$parent.css('top', leftCss);
						moveTheShow(leftCss, origShowHeight, options.tickerSpeed);
					});
				}
			}
		}		
		
		/**
		 * Sets all variables associated with the controls
		 */		
		function setAutoControlsVars(){
			// check if text or images should be used for controls
			// check "start"
			if(options.startImage != ''){
				startContent = options.startImage;
				startType = 'image';
			}else{
				startContent = options.startText;
				startType = 'text';
			}
			// check "stop"
			if(options.stopImage != ''){
				stopContent = options.stopImage;
				stopType = 'image';
			}else{
				stopContent = options.stopText;
				stopType = 'text';
			}
			// show the controls
			showAutoControls(startType, startContent, stopType, stopContent);
		}
		
		/**
		 * Handles hover events for auto shows
		 */		
		function setAutoHover(){
			// hover over the slider window
			$outerWrapper.find('.bx-window').hover(function() {
				if(autoPlaying){
					base.stopShow(false);
				}
			}, function() {
				if(autoPlaying){
					base.startShow(false);
				}
			});
		}
		
		/**
		 * Handles hover events for ticker mode
		 */		
		function setTickerHover(){
			// on hover stop the animation
			$parent.hover(function() {
				if(autoPlaying){
					base.stopTicker(false);
				}
			}, function() {
				if(autoPlaying){
					base.startTicker(false);
				}
			});
		}		
		
		/**
		 * Handles fade animation
		 */		
		function setChildrenFade(){
			// fade out any other child besides the current
			$children.not(':eq('+currentSlide+')').fadeTo(options.speed, 0).css('zIndex', 98);
			// fade in the current slide
			$children.eq(currentSlide).css('zIndex', 99).fadeTo(options.speed, 1, function(){
				isWorking = false;
				// ie fade fix
				if(jQuery.browser.msie){
					$children.eq(currentSlide).get(0).style.removeAttribute('filter');
				}
				// perform the callback function
				options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
			});
		};
				
		/**
		 * Makes slide active
		 */		
		function makeSlideActive(number){
			if(options.pagerType == 'full' && options.pager){
				// remove all active classes
				$('a', $pager).removeClass(options.pagerActiveClass);
				// assign active class to appropriate slide
				$('a', $pager).eq(number).addClass(options.pagerActiveClass);
			}else if(options.pagerType == 'short' && options.pager){
				$('.bx-pager-current', $pager).html(currentSlide+1);
			}
		}
				
		/**
		 * Displays next/prev controls
		 *
		 * @param string nextType 'image', 'text'
		 * @param string nextContent if type='image', specify a filepath to the image. if type='text', specify text.
		 * @param string prevType 'image', 'text'
		 * @param string prevContent if type='image', specify a filepath to the image. if type='text', specify text.
		 */		
		function showControls(nextType, nextContent, prevType, prevContent){
			// create pager html elements
			var $nextHtml = $('<a href="" class="bx-next"></a>');
			var $prevHtml = $('<a href="" class="bx-prev"></a>');
			// check if next is 'text' or 'image'
			if(nextType == 'text'){
				$nextHtml.html(nextContent);
			}else{
				$nextHtml.html('<img src="'+nextContent+'" />');
			}
			// check if prev is 'text' or 'image'
			if(prevType == 'text'){
				$prevHtml.html(prevContent);
			}else{
				$prevHtml.html('<img src="'+prevContent+'" />');
			}
			// check if user supplied a selector to populate next control
			if(options.prevSelector){
				$(options.prevSelector).append($prevHtml);
			}else{
				$outerWrapper.append($prevHtml);
			}
			// check if user supplied a selector to populate next control
			if(options.nextSelector){
				$(options.nextSelector).append($nextHtml);
			}else{
				$outerWrapper.append($nextHtml);
			}
			// click next control
			$nextHtml.click(function() {
				base.goToNextSlide();
				return false;
			});
			// click prev control
			$prevHtml.click(function() {
				base.goToPreviousSlide();
				return false;
			});
		}
		
		/**
		 * Displays the pager
		 *
		 * @param string type 'full', 'short'
		 */		
		function showPager(type){
			// sets up logic for finite multi slide shows
			var pagerQty = $children.length;
			// if we are moving more than one at a time and we have a finite loop
			if(options.moveSlideQty > 1){
				// if slides create an odd number of pages
				if($children.length % options.moveSlideQty != 0){
					// pagerQty = $children.length / options.moveSlideQty + 1;
					pagerQty = Math.ceil($children.length / options.moveSlideQty);
				// if slides create an even number of pages
				}else{
					pagerQty = $children.length / options.moveSlideQty;
				}
			}
			var pagerString = '';
			// check if custom build function was supplied
			if(options.buildPager){
				for(var i=0; i<pagerQty; i++){
					pagerString += options.buildPager(i, $children.eq(i * options.moveSlideQty));
				}
				
			// if not, use default pager
			}else if(type == 'full'){
				// build the full pager
				for(var i=1; i<=pagerQty; i++){
					pagerString += '<a href="" class="pager-link pager-'+i+'">'+i+'</a>';
				}
			}else if(type == 'short') {
				// build the short pager
				pagerString = '<span class="bx-pager-current">'+(options.startingSlide+1)+'</span> '+options.pagerShortSeparator+' <span class="bx-pager-total">'+$children.length+'</span>';
			}	
			// check if user supplied a pager selector
			if(options.pagerSelector){
				$(options.pagerSelector).append(pagerString);
				$pager = $(options.pagerSelector);
			}else{
				var $pagerContainer = $('<div class="bx-pager"></div>');
				$pagerContainer.append(pagerString);
				// attach the pager to the DOM
				if(options.pagerLocation == 'top'){
					$outerWrapper.prepend($pagerContainer);
				}else if(options.pagerLocation == 'bottom'){
					$outerWrapper.append($pagerContainer);
				}
				// cache the pager element
				$pager = $('.bx-pager', $outerWrapper);
			}
			$pager.children().click(function() {
				// only if pager is full mode
				if(options.pagerType == 'full'){
					// get the index from the link
					var slideIndex = $pager.children().index(this);
					// accomodate moving more than one slide
					if(options.moveSlideQty > 1){
						slideIndex *= options.moveSlideQty;
					}
					base.goToSlide(slideIndex);
				}
				return false;
			});
		}
				
		/**
		 * Displays captions
		 */		
		function showCaptions(){
			// get the title from each image
		  var caption = $('img', $children.eq(currentSlide)).attr('title');
			// if the caption exists
			if(caption != ''){
				// if user supplied a selector
				if(options.captionsSelector){
					$(options.captionsSelector).html(caption);
				}else{
					$('.bx-captions', $outerWrapper).html(caption);
				}
			}else{
				// if user supplied a selector
				if(options.captionsSelector){
					$(options.captionsSelector).html('&nbsp;');
				}else{
					$('.bx-captions', $outerWrapper).html('&nbsp;');
				}				
			}
		}
		
		/**
		 * Displays start/stop controls for auto and ticker mode
		 *
		 * @param string type 'image', 'text'
		 * @param string next [optional] if type='image', specify a filepath to the image. if type='text', specify text.
		 * @param string prev [optional] if type='image', specify a filepath to the image. if type='text', specify text.
		 */
		function showAutoControls(startType, startContent, stopType, stopContent){
			// create pager html elements
			$autoControls = $('<a href="" class="bx-start"></a>');
			// check if start is 'text' or 'image'
			if(startType == 'text'){
				$startContent = startContent;
			}else{
				$startContent = '<img src="'+startContent+'" />';
			}
			// check if stop is 'text' or 'image'
			if(stopType == 'text'){
				$stopContent = stopContent;
			}else{
				$stopContent = '<img src="'+stopContent+'" />';
			}
			// check if user supplied a selector to populate next control
			if(options.autoControlsSelector){
				$(options.autoControlsSelector).append($autoControls);
			}else{
				$outerWrapper.append('<div class="bx-auto"></div>');
				$('.bx-auto', $outerWrapper).html($autoControls);
			}
						
			// click start control
			$autoControls.click(function() {
				if(options.ticker){
					if($(this).hasClass('stop')){
						base.stopTicker();
					}else if($(this).hasClass('start')){
						base.startTicker();
					}
				}else{
					if($(this).hasClass('stop')){
						base.stopShow(true);
					}else if($(this).hasClass('start')){
						base.startShow(true);
					}
				}
				return false;
			});
			
		}
		
		/**
		 * Checks if show is in finite mode, and if slide is either first or last, then hides the respective control
		 */		
		function checkEndControls(){
			if(!options.infiniteLoop && options.hideControlOnEnd){
				// check previous
				if(currentSlide == firstSlide){
					$('.bx-prev', $outerWrapper).hide();				
				}else{
					$('.bx-prev', $outerWrapper).show();
				}
				// check next
				if(currentSlide == lastSlide){
					$('.bx-next', $outerWrapper).hide();
				}else{
					$('.bx-next', $outerWrapper).show();
				}
			}
		}
		
		/**
		 * Returns the left offset of the slide from the parent container
		 */		
		function getSlidePosition(number, side){			
			if(side == 'left'){
				var position = $('.pager', $outerWrapper).eq(number).position().left;
			}else if(side == 'top'){
				var position = $('.pager', $outerWrapper).eq(number).position().top;
			}
			return position;
		}
		
		/**
		 * Returns the width of the wrapper
		 */		
		function getWrapperWidth(){
			var wrapperWidth = $firstChild.outerWidth() * options.displaySlideQty;
			return wrapperWidth;
		}
		
		/**
		 * Returns the height of the wrapper
		 */		
		function getWrapperHeight(){
			// if displaying multiple slides, multiple wrapper width by number of slides to display
			var wrapperHeight = $firstChild.outerHeight() * options.displaySlideQty;
			return wrapperHeight;
		}
		
		/**
		 * Returns a sample of an arry and loops back on itself if the end of the array is reached
		 *
		 * @param array array original array the sample is derived from
		 * @param int start array index sample will start
		 * @param int length number of items in the sample
		 * @param string direction 'forward', 'backward' direction the loop should travel in the array
		 */		
		function getArraySample(array, start, length, direction){
			// initialize empty array
			var sample = [];
			// clone the length argument
			var loopLength = length;
			// determines when the empty array should start being populated
			var startPopulatingArray = false;
			// reverse the array if direction = 'backward'
			if(direction == 'backward'){
				array = $.makeArray(array);
				array.reverse();
			}
			// loop through original array until the length argument is met
			while(loopLength > 0){				
				// loop through original array
				$.each(array, function(index, val) {
					// check if length has been met
					if(loopLength > 0){
						// don't do anything unless first index has been reached
					  if(!startPopulatingArray){
							// start populating empty array
							if(index == start){
								startPopulatingArray = true;
								// add element to array
								sample.push($(this).clone());
								// decrease the length clone variable
								loopLength--;
							}
						}else{
							// add element to array
							sample.push($(this).clone());
							// decrease the length clone variable
							loopLength--;
						}
					// if length has been met, break loose
					}else{
						return false;
					}			
				});				
			}
			return sample;
		}
												
		this.each(function(){
			// make sure the element has children
			if($(this).children().length > 0){
				base.initShow();
			}
		});
				
		return this;						
	}
	
	jQuery.fx.prototype.cur = function(){
		if ( this.elem[this.prop] != null && (!this.elem.style || this.elem.style[this.prop] == null) ) {
			return this.elem[ this.prop ];
		}

		var r = parseFloat( jQuery.css( this.elem, this.prop ) );
		// return r && r > -10000 ? r : 0;
		return r;
	}
})(jQuery);
/*
 * END: jquery.bxSlider.js
 */

/*
 * START: jquery.msAccordion.js
 */
//menu Accordion
//author: Marghoob Suleman
//Date: 05th Aug, 2009
//Version: 1.0
//web: www.giftlelo.com | www.marghoobsuleman.com
;(function($){
	$.fn.msAccordion = function(options) {
		options = $.extend({
					currentDiv:'1',
					previousDiv:'',
					vertical: false,
					defaultid:0,
					currentcounter:0,
					intervalid:0,
					autodelay:0,
					event:"click",
					alldivs_array:new Array()
			}, options);
		$(this).addClass("accordionWrapper");
		$(this).css({overflow:"hidden"});
		//alert(this);
		var elementid = $(this).attr("id");
		var allDivs = this.children();
		if(options.autodelay>0)  {
			$("#"+ elementid +" > div").bind("mouseenter", function(){
														   pause();
														   });
			$("#"+ elementid +" > div").bind("mouseleave", function(){
																  startPlay();
																  });
		}
		//set ids
		allDivs.each(function(current) {
								 var iCurrent = current;
								 var sTitleID = elementid+"_msTitle_"+(iCurrent);
								 var sContentID = sTitleID+"_msContent_"+(iCurrent);
								 var currentDiv = allDivs[iCurrent];
								 var totalChild = currentDiv.childNodes.length;
								 var titleDiv = $(currentDiv).find("div.title");
								 titleDiv.attr("id", sTitleID);
								 var contentDiv = $(currentDiv).find("div.content");
								 contentDiv.attr("id", sContentID);
								 options.alldivs_array.push(sTitleID);
								 //$("#"+sTitleID).click(function(){openMe(sTitleID);});
								 $("#"+sTitleID).bind(options.event, function(){pause();openMe(sTitleID);});
								 });
		
		//make vertical
		if(options.vertical) {makeVertical();};
		//open default
		openMe(elementid+"_msTitle_"+options.defaultid);
		if(options.autodelay>0) {startPlay();};
		//alert(allDivs.length);
		function openMe(id) {
			var sTitleID = id;
			var iCurrent = sTitleID.split("_")[sTitleID.split("_").length-1];
			options.currentcounter = iCurrent;
			var sContentID = id+"_msContent_"+iCurrent;
			if($("#"+sContentID).css("display")=="none") {
				if(options.previousDiv!="") {
					closeMe(options.previousDiv);
				};
				if(options.vertical) {
					$("#"+sContentID).slideDown("slow");
				} else {
					$("#"+sContentID).show("slow");
				}
				options.currentDiv = sContentID;
				options.previousDiv = options.currentDiv;
			};
		};
		function closeMe(div) {
			if(options.vertical) {
				$("#"+div).slideUp("slow");
			} else {
				$("#"+div).hide("slow");
			};
		};	
		function makeVertical() {
			$("#"+elementid +" > div").css({display:"block", float:"none", clear:"both"});
			$("#"+elementid +" > div > div.title").css({display:"block", float:"none", clear:"both"});
			$("#"+elementid +" > div > div.content").css({clear:"both"});
		};
		function startPlay() {
			options.intervalid = window.setInterval(play, options.autodelay*1000);
		};
		function play() {
			var sTitleId = options.alldivs_array[options.currentcounter];
			openMe(sTitleId);
			options.currentcounter++;
			if(options.currentcounter==options.alldivs_array.length) options.currentcounter = 0;
		};
		function pause() {
			window.clearInterval(options.intervalid);
		};
		}
})(jQuery);
/*
 * END: jquery.msAccordion.js
 */

/*
 * START: jquery.nailthumb.1.1.js
 */
/*	jQuery NailThumb Plugin - any image to any thumbnail
 *  Examples and documentation at: http://www.garralab.com/nailthumb.php
 *  Copyright (C) 2012  garralab@gmail.com
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
(function($) {
    var DEBUG = false;
    var version = '1.1';
    $.fn.nailthumb = function(options) {
        var opts = $.extend({}, $.fn.nailthumb.defaults, options);
        return this.each(function() {
            var $this = $(this);
            var o = $.metadata ? $.extend({}, opts, $this.metadata()) : opts;
            thumbize($this,o)
        });
    };
    function thumbize(element,options) {
        var image = setImage(element,options);
        var container = setContainer(element,options);
        if (options.serverSideParams) {
            $.fn.nailthumb.setServerSideParams(image,container,options);
        }
        debugObject('image',image);
        debugObject('container',container);
        if (options.onStart) options.onStart(container,options);
        if (options.loadingClass) container.addClass(options.loadingClass);

        if (options.preload || image.data('nailthumb.replaceto')) {
            debug('wait on load');
            image.one('load',function() {
                debugObject('before check',image);
                if(!image.data('nailthumb.working') && !image.data('nailthumb.replacing')) {
                    image.data('nailthumb.working',true);
                    debugObject('inside check',image);
                    doThumb(image,container,options);
                }
            });
            var src = image.attr('src');
            image.attr('src',null).attr('src',src);
        } else {
            debug('nail thumb directly');
            image.data('nailthumb.working',true);
            doThumb(image,container,options);
        }
    };
    function doThumb(image,container,options) {
        resetImage(image,options);
        resetContainer(container,options);
        var imageDims = getImageDims(image,options);
        debugObject('image',image);
        debugObject('imageDims',imageDims);
        if (imageDims.width==0 || imageDims.height==0) {
            imageDims = getHiddenCloneDims(image);
            debugObject('imageCloneDims',imageDims);
        }
        var containerDims = getContainerDims(container,options);
        debugObject('container',container);
        debugObject('containerDims',containerDims);
        var prop = getProportion(containerDims,imageDims,options);
        debug('proportions',prop);
        resize(image, imageDims, container, containerDims, prop, options);
        
    };
    function setImage(element,options) {
        var image = element.find('img').first();
        var finder = options.imageCustomFinder;
        if (!finder && options.imageUrl) {
            finder = imageUrlFinder;
        } else if(!finder && options.imageFromWrappingLink) {
            finder = imageFromWrappingLinkFinder;
        }
        if (finder) {
            var img = finder(element,options);
            debugObject('finder',img);
            if (!img) img = [];
            if (img.length>0) {
                image = img;
                image.css('display','none');
                if(!image.data('nailthumb.replaceto')) image.data('nailthumb.replaceto',element);
                image.data('nailthumb.originalImageDims',null);
            }
        }
        if (image.length==0) {
            if (element.is('img')) image = element;
        }
        
        return image;
    };
    function imageUrlFinder(element,options) {
        var image = $('<img />').attr('src',options.imageUrl).css('display','none').data('nailthumb.replaceto',element);
        element.append(image);
        return image;
    };
    function imageFromWrappingLinkFinder(element,options) {
        var image;
        var link = element.find('a').first();
        if (link.length==0 && element.is('a')) {
            link = element;
        }
        if (link.attr('href')) {
            image = $('<img />').attr('src',link.attr('href')).css('display','none').data('nailthumb.replaceto',link);
            if (link.attr('title')) image.attr('title',link.attr('title'));
            link.append(image);
        }
        return image;
    };
    function resetImage(image,options) {
        if (!options.nostyle) {
            image.css({
                'position':'relative'
            });
        }
        if (!image.data('nailthumb.originalImageDims')) {
            image.css({
                'width':'auto',
                'height':'auto',
                'top':0,
                'left':0
            }).removeAttr('width')
            .removeAttr('height');
        }
    };
    function setContainer(element,options) {
        var container = element;
        if (element.is('img')) {
            if (options.ifImageAddContainer) {
                var c = $('<div></div>');
                element.wrap(c);
            } 
            container = element.parent();
        }
        return container;
    };
    function resetContainer(container,options) {
        if (options.containerClass) container.addClass(options.containerClass);
        if (!options.nostyle) {
            container.css({
                'overflow':'hidden',
                'padding':'0px'
            });
        }
        
        if (options.replaceAnimation == 'animate') {
            if (options.width || options.height) {
                container.animate({
                    'width':options.width,
                    'height':options.height
                },options.animationTime,options.animation);
            }
        } else {
            if (options.width) container.width(options.width);
            if (options.height) container.height(options.height);
        }
        
        container.find('span.'+options.titleClass).remove();
    };
    function resize(image, imageDims, container, containerDims, prop, options) {
        var iw = imageDims.width * prop;
        var ih = imageDims.height * prop;
        var top = 0, left = 0, diff;
        var direction = getDirections(options.fitDirection);
        if (ih<containerDims.innerHeight) {
            switch (direction.v) {
                case 'center':
                    top=-(ih-containerDims.innerHeight)/2;
                    break;
                case 'bottom':
                    top=-(ih-containerDims.innerHeight);
                    diff='bottom';
                    break;
                case 'top':
                    top=0;
                    diff='top';
                    break;
                default:
                    break;
            }
        } else if (ih>containerDims.innerHeight) {
            switch (direction.v) {
                case 'center':
                    top=-(ih-containerDims.innerHeight)/2;
                    break;
                case 'bottom':
                    top=-(ih-containerDims.innerHeight);
                    break;
                default:
                    break;
            }
        }
        if (iw<containerDims.innerWidth) {
            switch (direction.h) {
                case 'center':
                    left=-(iw-containerDims.innerWidth)/2;
                    break;
                case 'right':
                    left=-(iw-containerDims.innerWidth);
                    break;
                default:
                    break;
            }
        } else if (iw>containerDims.innerWidth) {
            switch (direction.h) {
                case 'center':
                    left=-(iw-containerDims.innerWidth)/2;
                    break;
                case 'right':
                    left=-(iw-containerDims.innerWidth);
                    break;
                default:
                    break;
            }
        }
        image.addClass(options.imageClass);
        if (image.data('nailthumb.replaceto')) {
            replaceImage(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
        } else {
            showImage(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
        }
    };
    function replaceImage(image, imageDims, container, containerDims, ih, iw, left, top, diff, options) {
        var element = image.data('nailthumb.replaceto');
        var replaceto = findReplaceTo(element,options);
        image.data('nailthumb.replacing',true);
        image.load(function() {
            image.data('nailthumb.replacing',null);
        });
        if (replaceto) {
            replaceto.replaceWith(image);
        } else {
            element.append(image);
        }
        if (options.afterReplace) options.afterReplace(container, image, options);
        showImage(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
    };
    function showImage(image, imageDims, container, containerDims, ih, iw, left, top, diff, options) {
        
        if (options.replaceAnimation == 'animate') {
            image.css('display','inline');
            container.animate({
                'width':containerDims.innerWidth,
                'height':containerDims.innerHeight
            },options.animationTime,options.animation);
            image.animate({
                'width':iw,
                'height':ih,
                'top':top,
                'left':left
            },options.animationTime,options.animation,function(){
                afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
            });
        } else {
            container.css({
                'width':containerDims.innerWidth,
                'height':containerDims.innerHeight
            });
            if (options.replaceAnimation) image.css('display','none');
            image.css({
                'width':iw,
                'height':ih,
                'top':top,
                'left':left
            });
            if (options.replaceAnimation == 'fade') {
                image.fadeIn(options.animationTime,options.animation,function(){
                    afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
                });
            } else if (options.replaceAnimation == 'slide') {
                image.slideDown(options.animationTime,options.animation,function(){
                    afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
                });
            } else if (options.replaceAnimation && options.replaceAnimation instanceof Function) {
                options.replaceAnimation(image,function(){
                    afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
                },options);
                if (!options.selfStartAfterAppear) {
                    afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
                }
            } else {
                image.css('display','inline');
                afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
            }
        }
    };
    function afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options) {
        if (options.afterAppear) options.afterAppear(container, image, options);
        image.data('nailthumb.replaceto',null);
        decorate(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
    };
    function findReplaceTo(element,options) {
        var rep = null; 
        element.find('img').each(function() {
            if (!rep && !$(this).data('nailthumb.replaceto')) {
                rep = $(this);
            }
        });
        return rep;
    };
    function decorate(image, imageDims, container, containerDims, ih, iw, left, top, diff, options) {
        if (options.title || (options.titleAttr && image.attr(options.titleAttr)) ) {
            var title = options.title?options.title:image.attr(options.titleAttr);
            if (title) {
                var span = $('<span class="'+options.titleClass+'">'+title+'</span>');
                if (containerDims.innerHeight>ih) span.css('top',containerDims.innerHeight-ih);
                else span.css('top','0px');
                container.append(span);
                var tit = getHiddenDims(span);
                var im = getHiddenDims(image);
                debugObject('decorate containerDims',containerDims);
                debugObject('decorate imageDims',imageDims);
                debugObject('decorate imageDims',im);
                debugObject('decorate tit',tit);
                var outbound = containerDims.offsetTop+containerDims.innerHeight-tit.offsetTop;
                if (containerDims.height>containerDims.innerHeight) {
                    outbound+=(containerDims.height-containerDims.innerHeight)/2
                }
                span.css('top','+='+outbound);
                
                if (iw < tit.width) span.css('width',iw);
                if (left > 0) span.css('left',left);
                
                var delta = tit.height;
                if (containerDims.innerHeight>ih && diff!='bottom') {
                    delta += (containerDims.innerHeight-ih)/((diff=='top')?1:2);
                }
                
                var clone = span.clone();
                clone.css('width','auto').css('display','none').css('position','absolute');
                container.append(clone);
                var cloneDims = getHiddenDims(clone);
                clone.remove();
                debugObject('decorate cloneDims',cloneDims);
                
                if (options.titleWhen=='hover') {
                    container.unbind('mouseenter mouseleave').hover(function(){
                        span.find('span.'+options.titleScrollerClass).css('left',0);
                        containerDims = getHiddenDims(container);
                        tit = getHiddenDims(span);
                        outbound = containerDims.offsetTop+containerDims.innerHeight-tit.offsetTop;
                        if (containerDims.height>containerDims.innerHeight) {
                            outbound+=(containerDims.height-containerDims.innerHeight)/2
                        }
                        debugObject('decorate hover tit',tit);
                        debug('decorate hover outbound',tit);
                        var doubleDelta = 0;
                        if (outbound<0) {
                            span.css('top','+='+outbound);
                            doubleDelta = delta;
                        } else {
                            doubleDelta = delta-outbound;
                        }
                        if(options.animateTitle) {
                            resetScrollTitle(span,options);
                            span.stop(true).animate({
                                top:'-='+doubleDelta
                            },options.titleAnimationTime,options.titleAnimation,function(){
                                scrollTitle(span, cloneDims.width, containerDims.innerWidth, options);
                            });
                        } else {
                            span.css({
                                top:'-='+doubleDelta
                            });
                            scrollTitle(span, cloneDims.width, containerDims.innerWidth, options);
                        }
                    },function(){
                        if(options.animateTitle) {
                            resetScrollTitle(span,options);
                            span.animate({
                                top:'+='+delta
                            },options.titleAnimationTime,options.titleAnimation,function(){
                                resetScrollTitle(span,options);
                            });
                        } else {
                            resetScrollTitle(span,options);
                            span.css({
                                top:'+='+delta
                            });
                        }
                    });
                } else {
                    if(options.animateTitle) {
                        span.animate({
                            top:'-='+delta
                        },options.titleAnimationTime,options.titleAnimation,function(){
                            scrollTitle(span, cloneDims.width, containerDims.innerWidth, options);
                        });
                    } else {
                        span.css({
                            top:'-='+delta
                        });
                        scrollTitle(span, cloneDims.width, containerDims.innerWidth, options);
                    }
                }
            }
        }
        if (options.onFinish) options.onFinish(container,options);
        if (options.loadingClass) container.removeClass(options.loadingClass);
        image.data('nailthumb.working',null);
    };
    function resetScrollTitle(span,options) {
        span.find('span.'+options.titleScrollerClass).stop();
    };
    function scrollTitle(span, width, visibleWidth, options) {
        if (width > visibleWidth && options.titleScrolling) {
            if (span.find('span.'+options.titleScrollerClass).length==0) {
                span.wrapInner('<span class="'+options.titleScrollerClass+'" />');
                span.find('span.'+options.titleScrollerClass).width(width).css('position','relative').css('white-space','nowrap');
            }
            span.find('span.'+options.titleScrollerClass).css('left',0);
            setTimeout(scrollFunction(span, width, visibleWidth, options),1000);
        }
    };
    function scrollFunction(span, width, visibleWidth, options) {
        return function() {
            var indent = Number(span.find('span.'+options.titleScrollerClass).css('left').replace(/[^-\d]/g,''));
            debug('indent',indent);
            debug('width',width);
            debug('visibleWidth',visibleWidth);
            debug('width <= -indent',(width <= -indent));
            var delta = width + indent;
            if (delta <= 0) {
                span.find('span.'+options.titleScrollerClass).css('left',visibleWidth);
                delta = width + visibleWidth;
            }
            delta += 10;
            span.find('span.'+options.titleScrollerClass).animate({
                'left':'-='+delta
            },width*1000/30,'linear',scrollFunction(span, width, visibleWidth, options));
        };
    };
    function getProportion(containerDims, imageDims, options) {
        if (options.proportions != null && options.proportions > 0) {
            return options.proportions;
        } else {
            var prop = containerDims.innerWidth/imageDims.width;
            if (options.method && options.method=='resize') {
                if (containerDims.innerHeight/imageDims.height < prop) {
                    prop = containerDims.innerHeight/imageDims.height;
                }
            } else {
                if (containerDims.innerHeight/imageDims.height > prop) {
                    prop = containerDims.innerHeight/imageDims.height;
                }
            }
            if (options.maxEnlargement && options.maxEnlargement < prop) prop = options.maxEnlargement;
            if (options.maxShrink && options.maxShrink > prop) prop = options.maxShrink;
            return prop;
        }
    };
    function getDirections(option) {
        var dir = {
            h:'center',
            v:'center'
        };
        if (option) {
            var opts = option.split(' ');
            if (opts.length > 0) {
                dir = getDirection(opts[0],dir);
            }
            if (opts.length > 1) {
                dir = getDirection(opts[1],dir);
            }
        }
        return dir;
    };
    function getDirection(str,d) {
        switch (str) {
            case 'top':
                d.v = 'top';
                break;
            case 'bottom':
                d.v = 'bottom';
                break;
            case 'left':
                d.h = 'left';
                break;
            case 'right':
                d.h = 'right';
                break;
            default:
                break;
        }
        return d;
    };
    function getImageDims(image,options) {
        var imageDims;
        if (!image.data('nailthumb.originalImageDims') ) {
            imageDims = getHiddenDims(image);
            image.data('nailthumb.originalImageDims',imageDims);
            if (!options.keepImageDimensions) {
                image.one('load',function(){
                    image.data('nailthumb.originalImageDims',null);
                });
            }
        } else {
            imageDims = image.data('nailthumb.originalImageDims');
        }
        return imageDims;
    };
    function getContainerDims(container,options) {
        var containerDims = getHiddenDims(container)
        if (options.width) containerDims.innerWidth = options.width;
        if (options.height) containerDims.innerHeight = options.height;
        return containerDims;
    };
    function getDims(elem) {
        var offset = $(elem).offset();
        return {
            offsetTop: offset.top,
            offsetLeft: offset.left,
            width: $(elem).outerWidth(),
            height: $(elem).outerHeight(),
            innerWidth: $(elem).innerWidth(),
            innerHeight: $(elem).innerHeight()
        };
    };
    function getHiddenDims(elems) {
        var dims = null, i = 0, offset, elem;

        while ((elem = elems[i++])) {
            var hiddenElems = $(elem).parents().andSelf().filter(':hidden');
            if ( ! hiddenElems.length ) {
                dims = getDims(elem);
            } else {
                var backupStyle = [];
                hiddenElems.each( function() {
                    var style = $(this).attr('style');
                    style = typeof style == 'undefined'? '': style;
                    backupStyle.push( style );
                    $(this).attr( 'style', style + ' display: block !important;' );
                });

                hiddenElems.eq(0).css( 'left', -10000 );

                dims = getDims(elem);

                hiddenElems.each( function() {
                    $(this).attr( 'style', backupStyle.shift() );
                });
            }
            
        }

        return dims;
    };
    function getHiddenCloneDims(elems) {
        var dims = null, i = 0, offset, elem;

        while ((elem = elems[i++])) {
            var hiddenElems = $(elem).parents().andSelf().filter(':hidden');
            if ( ! hiddenElems.length ) {
                dims = getDims(elem);
            } else {
                var backupStyle = [];
                hiddenElems.each( function() {
                    var style = $(this).attr('style');
                    style = typeof style == 'undefined'? '': style;
                    backupStyle.push( style );
                    $(this).attr( 'style', style + ' display: block !important;' );
                });

                hiddenElems.eq(0).css( 'left', -10000 );
                
                var clone = hiddenElems.eq(0).clone();
                $('body').append(clone);

                dims = getDims(clone);

                hiddenElems.each( function() {
                    $(this).attr( 'style', backupStyle.shift() );
                });
                clone.remove();
            }
            
        }

        return dims;
    };
    $.fn.nailthumb.evalServerSideParams = function(image,container,options) {
        if (options.serverSideParams) {
            var params = {};
            if (!options.serverSideParams.noServerResize) {
                var w = null, h = null;
                if (options.serverSideParams.width) w = options.serverSideParams.width;
                else if (options.width) w = options.width;
                if (options.serverSideParams.height) h = options.serverSideParams.height;
                else if (options.height) h = options.height;
                if (!(w && h)) {
                    resetContainer(container,options);
                    var containerDims = getContainerDims(container,options);
                    w = containerDims.innerWidth;
                    h = containerDims.innerHeight;
                }
                if (w && h) {
                    params.w = w;
                    params.h = h;
                    if (options.serverSideParams.mode!='resize') {
                        if (options.method=='crop') params.mode = 'crop';
                        if (options.serverSideParams.mode) params.mode = options.serverSideParams.mode;
                    }
                }
            }
            
            $.each(options.serverSideParams, function(key,val) {
                if (key!='width' && key!='height' && key!='mode' && key!='noServerResize' && val) {
                    params[key]=val;
                }
            });
            var pars = "";
            $.each(params, function(key,val) {
                pars+=";"+key+"="+val;
            });
            debug(pars,params);
            return pars;
        } else {
            return "";
        }
    };
    $.fn.nailthumb.setServerSideParams = function(image,container,options) {
        if (options.serverSideParams) {
            var url = image.attr("src");
            if (image.data('nailthumb.originalImageUrl')) {
                url = image.data('nailthumb.originalImageUrl');
            }
            image.data('nailthumb.originalImageUrl',url);
            var pars = $.fn.nailthumb.evalServerSideParams(image,container,options);
            url += pars;
            image.attr("src",url);
        }
    };
    $.fn.nailthumb.toggleDebug = function() {
        DEBUG = !DEBUG;
    };
    $.fn.nailthumb.doThumb = function(image,container,options) {
        doThumb(image,container,options);
    };
    $.fn.nailthumb.defaults = {
        onStart: null,
        onFinish: null,
        loadingClass: 'nailthumb-loading',
        imageUrl: null,
        imageFromWrappingLink: false,
        imageCustomFinder: null/*function(element,options){
            return null;
        }*/,
        imageClass:'nailthumb-image',
        afterReplace: null,
        afterAppear: null,
        replaceAnimation: 'fade',
        selfStartAfterAppear: false,
        animationTime: 1000,
        animation: 'swing',
        keepImageDimensions: false,
        method: 'crop',
        fitDirection: null,
        proportions: null,
        ifImageAddContainer: true,
        containerClass: 'nailthumb-container',
        maxEnlargement: null,
        maxShrink: null,
        preload: true,
        nostyle: false,
        width: null,
        height: null,
        title: null,
        titleClass: 'nailthumb-title',
        titleAttr: 'title',
        titleWhen: 'hover',
        titleScrolling: true,
        titleScrollerClass: 'nailthumb-title-scroller',
        animateTitle: true,
        titleAnimationTime: 500,
        titleAnimation: 'swing',
        serverSideParams: null
    };
    function log(log, jQueryobj) {
        try {
            debug(log, jQueryobj, true);
        } catch(ex) {}
    };
    function debug(log, jQueryobj, force) {
        try {
            if ((DEBUG && window.console && window.console.log) || force)
                window.console.log(log + ': ' + jQueryobj);
        } catch(ex) {}
    };
    function debugObject(log, jQueryobj, force) {
        try {
            if (!jQueryobj) jQueryobj=log;
            debug(log, jQueryobj);
            if ((DEBUG && window.console && window.console.log) || force)
                window.console.debug(jQueryobj);
        } catch(ex) {}
    };
})(jQuery);
/*
 * END: jquery.nailthumb.1.1.js
 */

/*
Uploadify v3.2
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php>

SWFUpload: http://www.swfupload.org, http://swfupload.googlecode.com
mmSWFUpload 1.0: Flash upload dialog - http://profandesign.se/swfupload/,  http://www.vinterwebb.se/
SWFUpload is (c) 2006-2007 Lars Huring, Olov Nilzén and Mammon Media and is released under the MIT License:
http://www.opensource.org/licenses/mit-license.php
SWFUpload 2 is (c) 2007-2008 Jake Roberts and is released under the MIT License:
http://www.opensource.org/licenses/mit-license.php

SWFObject v2.2 <http://code.google.com/p/swfobject/> 
is released under the MIT License <http://www.opensource.org/licenses/mit-license.php>
*/
;var swfobject=function(){var aq="undefined",aD="object",ab="Shockwave Flash",X="ShockwaveFlash.ShockwaveFlash",aE="application/x-shockwave-flash",ac="SWFObjectExprInst",ax="onreadystatechange",af=window,aL=document,aB=navigator,aa=false,Z=[aN],aG=[],ag=[],al=[],aJ,ad,ap,at,ak=false,aU=false,aH,an,aI=true,ah=function(){var a=typeof aL.getElementById!=aq&&typeof aL.getElementsByTagName!=aq&&typeof aL.createElement!=aq,e=aB.userAgent.toLowerCase(),c=aB.platform.toLowerCase(),h=c?/win/.test(c):/win/.test(e),j=c?/mac/.test(c):/mac/.test(e),g=/webkit/.test(e)?parseFloat(e.replace(/^.*webkit\/(\d+(\.\d+)?).*$/,"$1")):false,d=!+"\v1",f=[0,0,0],k=null;if(typeof aB.plugins!=aq&&typeof aB.plugins[ab]==aD){k=aB.plugins[ab].description;if(k&&!(typeof aB.mimeTypes!=aq&&aB.mimeTypes[aE]&&!aB.mimeTypes[aE].enabledPlugin)){aa=true;d=false;k=k.replace(/^.*\s+(\S+\s+\S+$)/,"$1");f[0]=parseInt(k.replace(/^(.*)\..*$/,"$1"),10);f[1]=parseInt(k.replace(/^.*\.(.*)\s.*$/,"$1"),10);f[2]=/[a-zA-Z]/.test(k)?parseInt(k.replace(/^.*[a-zA-Z]+(.*)$/,"$1"),10):0;}}else{if(typeof af.ActiveXObject!=aq){try{var i=new ActiveXObject(X);if(i){k=i.GetVariable("$version");if(k){d=true;k=k.split(" ")[1].split(",");f=[parseInt(k[0],10),parseInt(k[1],10),parseInt(k[2],10)];}}}catch(b){}}}return{w3:a,pv:f,wk:g,ie:d,win:h,mac:j};}(),aK=function(){if(!ah.w3){return;}if((typeof aL.readyState!=aq&&aL.readyState=="complete")||(typeof aL.readyState==aq&&(aL.getElementsByTagName("body")[0]||aL.body))){aP();}if(!ak){if(typeof aL.addEventListener!=aq){aL.addEventListener("DOMContentLoaded",aP,false);}if(ah.ie&&ah.win){aL.attachEvent(ax,function(){if(aL.readyState=="complete"){aL.detachEvent(ax,arguments.callee);aP();}});if(af==top){(function(){if(ak){return;}try{aL.documentElement.doScroll("left");}catch(a){setTimeout(arguments.callee,0);return;}aP();})();}}if(ah.wk){(function(){if(ak){return;}if(!/loaded|complete/.test(aL.readyState)){setTimeout(arguments.callee,0);return;}aP();})();}aC(aP);}}();function aP(){if(ak){return;}try{var b=aL.getElementsByTagName("body")[0].appendChild(ar("span"));b.parentNode.removeChild(b);}catch(a){return;}ak=true;var d=Z.length;for(var c=0;c<d;c++){Z[c]();}}function aj(a){if(ak){a();}else{Z[Z.length]=a;}}function aC(a){if(typeof af.addEventListener!=aq){af.addEventListener("load",a,false);}else{if(typeof aL.addEventListener!=aq){aL.addEventListener("load",a,false);}else{if(typeof af.attachEvent!=aq){aM(af,"onload",a);}else{if(typeof af.onload=="function"){var b=af.onload;af.onload=function(){b();a();};}else{af.onload=a;}}}}}function aN(){if(aa){Y();}else{am();}}function Y(){var d=aL.getElementsByTagName("body")[0];var b=ar(aD);b.setAttribute("type",aE);var a=d.appendChild(b);if(a){var c=0;(function(){if(typeof a.GetVariable!=aq){var e=a.GetVariable("$version");if(e){e=e.split(" ")[1].split(",");ah.pv=[parseInt(e[0],10),parseInt(e[1],10),parseInt(e[2],10)];}}else{if(c<10){c++;setTimeout(arguments.callee,10);return;}}d.removeChild(b);a=null;am();})();}else{am();}}function am(){var g=aG.length;if(g>0){for(var h=0;h<g;h++){var c=aG[h].id;var l=aG[h].callbackFn;var a={success:false,id:c};if(ah.pv[0]>0){var i=aS(c);if(i){if(ao(aG[h].swfVersion)&&!(ah.wk&&ah.wk<312)){ay(c,true);if(l){a.success=true;a.ref=av(c);l(a);}}else{if(aG[h].expressInstall&&au()){var e={};e.data=aG[h].expressInstall;e.width=i.getAttribute("width")||"0";e.height=i.getAttribute("height")||"0";if(i.getAttribute("class")){e.styleclass=i.getAttribute("class");}if(i.getAttribute("align")){e.align=i.getAttribute("align");}var f={};var d=i.getElementsByTagName("param");var k=d.length;for(var j=0;j<k;j++){if(d[j].getAttribute("name").toLowerCase()!="movie"){f[d[j].getAttribute("name")]=d[j].getAttribute("value");}}ae(e,f,c,l);}else{aF(i);if(l){l(a);}}}}}else{ay(c,true);if(l){var b=av(c);if(b&&typeof b.SetVariable!=aq){a.success=true;a.ref=b;}l(a);}}}}}function av(b){var d=null;var c=aS(b);if(c&&c.nodeName=="OBJECT"){if(typeof c.SetVariable!=aq){d=c;}else{var a=c.getElementsByTagName(aD)[0];if(a){d=a;}}}return d;}function au(){return !aU&&ao("6.0.65")&&(ah.win||ah.mac)&&!(ah.wk&&ah.wk<312);}function ae(f,d,h,e){aU=true;ap=e||null;at={success:false,id:h};var a=aS(h);if(a){if(a.nodeName=="OBJECT"){aJ=aO(a);ad=null;}else{aJ=a;ad=h;}f.id=ac;if(typeof f.width==aq||(!/%$/.test(f.width)&&parseInt(f.width,10)<310)){f.width="310";}if(typeof f.height==aq||(!/%$/.test(f.height)&&parseInt(f.height,10)<137)){f.height="137";}aL.title=aL.title.slice(0,47)+" - Flash Player Installation";var b=ah.ie&&ah.win?"ActiveX":"PlugIn",c="MMredirectURL="+af.location.toString().replace(/&/g,"%26")+"&MMplayerType="+b+"&MMdoctitle="+aL.title;if(typeof d.flashvars!=aq){d.flashvars+="&"+c;}else{d.flashvars=c;}if(ah.ie&&ah.win&&a.readyState!=4){var g=ar("div");h+="SWFObjectNew";g.setAttribute("id",h);a.parentNode.insertBefore(g,a);a.style.display="none";(function(){if(a.readyState==4){a.parentNode.removeChild(a);}else{setTimeout(arguments.callee,10);}})();}aA(f,d,h);}}function aF(a){if(ah.ie&&ah.win&&a.readyState!=4){var b=ar("div");a.parentNode.insertBefore(b,a);b.parentNode.replaceChild(aO(a),b);a.style.display="none";(function(){if(a.readyState==4){a.parentNode.removeChild(a);}else{setTimeout(arguments.callee,10);}})();}else{a.parentNode.replaceChild(aO(a),a);}}function aO(b){var d=ar("div");if(ah.win&&ah.ie){d.innerHTML=b.innerHTML;}else{var e=b.getElementsByTagName(aD)[0];if(e){var a=e.childNodes;if(a){var f=a.length;for(var c=0;c<f;c++){if(!(a[c].nodeType==1&&a[c].nodeName=="PARAM")&&!(a[c].nodeType==8)){d.appendChild(a[c].cloneNode(true));}}}}}return d;}function aA(e,g,c){var d,a=aS(c);if(ah.wk&&ah.wk<312){return d;}if(a){if(typeof e.id==aq){e.id=c;}if(ah.ie&&ah.win){var f="";for(var i in e){if(e[i]!=Object.prototype[i]){if(i.toLowerCase()=="data"){g.movie=e[i];}else{if(i.toLowerCase()=="styleclass"){f+=' class="'+e[i]+'"';}else{if(i.toLowerCase()!="classid"){f+=" "+i+'="'+e[i]+'"';}}}}}var h="";for(var j in g){if(g[j]!=Object.prototype[j]){h+='<param name="'+j+'" value="'+g[j]+'" />';}}a.outerHTML='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"'+f+">"+h+"</object>";ag[ag.length]=e.id;d=aS(e.id);}else{var b=ar(aD);b.setAttribute("type",aE);for(var k in e){if(e[k]!=Object.prototype[k]){if(k.toLowerCase()=="styleclass"){b.setAttribute("class",e[k]);}else{if(k.toLowerCase()!="classid"){b.setAttribute(k,e[k]);}}}}for(var l in g){if(g[l]!=Object.prototype[l]&&l.toLowerCase()!="movie"){aQ(b,l,g[l]);}}a.parentNode.replaceChild(b,a);d=b;}}return d;}function aQ(b,d,c){var a=ar("param");a.setAttribute("name",d);a.setAttribute("value",c);b.appendChild(a);}function aw(a){var b=aS(a);if(b&&b.nodeName=="OBJECT"){if(ah.ie&&ah.win){b.style.display="none";(function(){if(b.readyState==4){aT(a);}else{setTimeout(arguments.callee,10);}})();}else{b.parentNode.removeChild(b);}}}function aT(a){var b=aS(a);if(b){for(var c in b){if(typeof b[c]=="function"){b[c]=null;}}b.parentNode.removeChild(b);}}function aS(a){var c=null;try{c=aL.getElementById(a);}catch(b){}return c;}function ar(a){return aL.createElement(a);}function aM(a,c,b){a.attachEvent(c,b);al[al.length]=[a,c,b];}function ao(a){var b=ah.pv,c=a.split(".");c[0]=parseInt(c[0],10);c[1]=parseInt(c[1],10)||0;c[2]=parseInt(c[2],10)||0;return(b[0]>c[0]||(b[0]==c[0]&&b[1]>c[1])||(b[0]==c[0]&&b[1]==c[1]&&b[2]>=c[2]))?true:false;}function az(b,f,a,c){if(ah.ie&&ah.mac){return;}var e=aL.getElementsByTagName("head")[0];if(!e){return;}var g=(a&&typeof a=="string")?a:"screen";if(c){aH=null;an=null;}if(!aH||an!=g){var d=ar("style");d.setAttribute("type","text/css");d.setAttribute("media",g);aH=e.appendChild(d);if(ah.ie&&ah.win&&typeof aL.styleSheets!=aq&&aL.styleSheets.length>0){aH=aL.styleSheets[aL.styleSheets.length-1];}an=g;}if(ah.ie&&ah.win){if(aH&&typeof aH.addRule==aD){aH.addRule(b,f);}}else{if(aH&&typeof aL.createTextNode!=aq){aH.appendChild(aL.createTextNode(b+" {"+f+"}"));}}}function ay(a,c){if(!aI){return;}var b=c?"visible":"hidden";if(ak&&aS(a)){aS(a).style.visibility=b;}else{az("#"+a,"visibility:"+b);}}function ai(b){var a=/[\\\"<>\.;]/;var c=a.exec(b)!=null;return c&&typeof encodeURIComponent!=aq?encodeURIComponent(b):b;}var aR=function(){if(ah.ie&&ah.win){window.attachEvent("onunload",function(){var a=al.length;for(var b=0;b<a;b++){al[b][0].detachEvent(al[b][1],al[b][2]);}var d=ag.length;for(var c=0;c<d;c++){aw(ag[c]);}for(var e in ah){ah[e]=null;}ah=null;for(var f in swfobject){swfobject[f]=null;}swfobject=null;});}}();return{registerObject:function(a,e,c,b){if(ah.w3&&a&&e){var d={};d.id=a;d.swfVersion=e;d.expressInstall=c;d.callbackFn=b;aG[aG.length]=d;ay(a,false);}else{if(b){b({success:false,id:a});}}},getObjectById:function(a){if(ah.w3){return av(a);}},embedSWF:function(k,e,h,f,c,a,b,i,g,j){var d={success:false,id:e};if(ah.w3&&!(ah.wk&&ah.wk<312)&&k&&e&&h&&f&&c){ay(e,false);aj(function(){h+="";f+="";var q={};if(g&&typeof g===aD){for(var o in g){q[o]=g[o];}}q.data=k;q.width=h;q.height=f;var n={};if(i&&typeof i===aD){for(var p in i){n[p]=i[p];}}if(b&&typeof b===aD){for(var l in b){if(typeof n.flashvars!=aq){n.flashvars+="&"+l+"="+b[l];}else{n.flashvars=l+"="+b[l];}}}if(ao(c)){var m=aA(q,n,e);if(q.id==e){ay(e,true);}d.success=true;d.ref=m;}else{if(a&&au()){q.data=a;ae(q,n,e,j);return;}else{ay(e,true);}}if(j){j(d);}});}else{if(j){j(d);}}},switchOffAutoHideShow:function(){aI=false;},ua:ah,getFlashPlayerVersion:function(){return{major:ah.pv[0],minor:ah.pv[1],release:ah.pv[2]};},hasFlashPlayerVersion:ao,createSWF:function(a,b,c){if(ah.w3){return aA(a,b,c);}else{return undefined;}},showExpressInstall:function(b,a,d,c){if(ah.w3&&au()){ae(b,a,d,c);}},removeSWF:function(a){if(ah.w3){aw(a);}},createCSS:function(b,a,c,d){if(ah.w3){az(b,a,c,d);}},addDomLoadEvent:aj,addLoadEvent:aC,getQueryParamValue:function(b){var a=aL.location.search||aL.location.hash;if(a){if(/\?/.test(a)){a=a.split("?")[1];}if(b==null){return ai(a);}var c=a.split("&");for(var d=0;d<c.length;d++){if(c[d].substring(0,c[d].indexOf("="))==b){return ai(c[d].substring((c[d].indexOf("=")+1)));}}}return"";},expressInstallCallback:function(){if(aU){var a=aS(ac);if(a&&aJ){a.parentNode.replaceChild(aJ,a);if(ad){ay(ad,true);if(ah.ie&&ah.win){aJ.style.display="block";}}if(ap){ap(at);}}aU=false;}}};}();var SWFUpload;if(SWFUpload==undefined){SWFUpload=function(b){this.initSWFUpload(b);};}SWFUpload.prototype.initSWFUpload=function(c){try{this.customSettings={};this.settings=c;this.eventQueue=[];this.movieName="SWFUpload_"+SWFUpload.movieCount++;this.movieElement=null;SWFUpload.instances[this.movieName]=this;this.initSettings();this.loadFlash();this.displayDebugInfo();}catch(d){delete SWFUpload.instances[this.movieName];throw d;}};SWFUpload.instances={};SWFUpload.movieCount=0;SWFUpload.version="2.2.0 2009-03-25";SWFUpload.QUEUE_ERROR={QUEUE_LIMIT_EXCEEDED:-100,FILE_EXCEEDS_SIZE_LIMIT:-110,ZERO_BYTE_FILE:-120,INVALID_FILETYPE:-130};SWFUpload.UPLOAD_ERROR={HTTP_ERROR:-200,MISSING_UPLOAD_URL:-210,IO_ERROR:-220,SECURITY_ERROR:-230,UPLOAD_LIMIT_EXCEEDED:-240,UPLOAD_FAILED:-250,SPECIFIED_FILE_ID_NOT_FOUND:-260,FILE_VALIDATION_FAILED:-270,FILE_CANCELLED:-280,UPLOAD_STOPPED:-290};SWFUpload.FILE_STATUS={QUEUED:-1,IN_PROGRESS:-2,ERROR:-3,COMPLETE:-4,CANCELLED:-5};SWFUpload.BUTTON_ACTION={SELECT_FILE:-100,SELECT_FILES:-110,START_UPLOAD:-120};SWFUpload.CURSOR={ARROW:-1,HAND:-2};SWFUpload.WINDOW_MODE={WINDOW:"window",TRANSPARENT:"transparent",OPAQUE:"opaque"};SWFUpload.completeURL=function(e){if(typeof(e)!=="string"||e.match(/^https?:\/\//i)||e.match(/^\//)){return e;}var f=window.location.protocol+"//"+window.location.hostname+(window.location.port?":"+window.location.port:"");var d=window.location.pathname.lastIndexOf("/");if(d<=0){path="/";}else{path=window.location.pathname.substr(0,d)+"/";}return path+e;};SWFUpload.prototype.initSettings=function(){this.ensureDefault=function(c,d){this.settings[c]=(this.settings[c]==undefined)?d:this.settings[c];};this.ensureDefault("upload_url","");this.ensureDefault("preserve_relative_urls",false);this.ensureDefault("file_post_name","Filedata");this.ensureDefault("post_params",{});this.ensureDefault("use_query_string",false);this.ensureDefault("requeue_on_error",false);this.ensureDefault("http_success",[]);this.ensureDefault("assume_success_timeout",0);this.ensureDefault("file_types","*.*");this.ensureDefault("file_types_description","All Files");this.ensureDefault("file_size_limit",0);this.ensureDefault("file_upload_limit",0);this.ensureDefault("file_queue_limit",0);this.ensureDefault("flash_url","swfupload.swf");this.ensureDefault("prevent_swf_caching",true);this.ensureDefault("button_image_url","");this.ensureDefault("button_width",1);this.ensureDefault("button_height",1);this.ensureDefault("button_text","");this.ensureDefault("button_text_style","color: #000000; font-size: 16pt;");this.ensureDefault("button_text_top_padding",0);this.ensureDefault("button_text_left_padding",0);this.ensureDefault("button_action",SWFUpload.BUTTON_ACTION.SELECT_FILES);this.ensureDefault("button_disabled",false);this.ensureDefault("button_placeholder_id","");this.ensureDefault("button_placeholder",null);this.ensureDefault("button_cursor",SWFUpload.CURSOR.ARROW);this.ensureDefault("button_window_mode",SWFUpload.WINDOW_MODE.WINDOW);this.ensureDefault("debug",false);this.settings.debug_enabled=this.settings.debug;this.settings.return_upload_start_handler=this.returnUploadStart;this.ensureDefault("swfupload_loaded_handler",null);this.ensureDefault("file_dialog_start_handler",null);this.ensureDefault("file_queued_handler",null);this.ensureDefault("file_queue_error_handler",null);this.ensureDefault("file_dialog_complete_handler",null);this.ensureDefault("upload_start_handler",null);this.ensureDefault("upload_progress_handler",null);this.ensureDefault("upload_error_handler",null);this.ensureDefault("upload_success_handler",null);this.ensureDefault("upload_complete_handler",null);this.ensureDefault("debug_handler",this.debugMessage);this.ensureDefault("custom_settings",{});this.customSettings=this.settings.custom_settings;if(!!this.settings.prevent_swf_caching){this.settings.flash_url=this.settings.flash_url+(this.settings.flash_url.indexOf("?")<0?"?":"&")+"preventswfcaching="+new Date().getTime();}if(!this.settings.preserve_relative_urls){this.settings.upload_url=SWFUpload.completeURL(this.settings.upload_url);this.settings.button_image_url=SWFUpload.completeURL(this.settings.button_image_url);}delete this.ensureDefault;};SWFUpload.prototype.loadFlash=function(){var d,c;if(document.getElementById(this.movieName)!==null){throw"ID "+this.movieName+" is already in use. The Flash Object could not be added";}d=document.getElementById(this.settings.button_placeholder_id)||this.settings.button_placeholder;if(d==undefined){throw"Could not find the placeholder element: "+this.settings.button_placeholder_id;}c=document.createElement("div");c.innerHTML=this.getFlashHTML();d.parentNode.replaceChild(c.firstChild,d);if(window[this.movieName]==undefined){window[this.movieName]=this.getMovieElement();}};SWFUpload.prototype.getFlashHTML=function(){return['<object id="',this.movieName,'" type="application/x-shockwave-flash" data="',this.settings.flash_url,'" width="',this.settings.button_width,'" height="',this.settings.button_height,'" class="swfupload">','<param name="wmode" value="',this.settings.button_window_mode,'" />','<param name="movie" value="',this.settings.flash_url,'" />','<param name="quality" value="high" />','<param name="menu" value="false" />','<param name="allowScriptAccess" value="always" />','<param name="flashvars" value="'+this.getFlashVars()+'" />',"</object>"].join("");};SWFUpload.prototype.getFlashVars=function(){var c=this.buildParamString();var d=this.settings.http_success.join(",");return["movieName=",encodeURIComponent(this.movieName),"&amp;uploadURL=",encodeURIComponent(this.settings.upload_url),"&amp;useQueryString=",encodeURIComponent(this.settings.use_query_string),"&amp;requeueOnError=",encodeURIComponent(this.settings.requeue_on_error),"&amp;httpSuccess=",encodeURIComponent(d),"&amp;assumeSuccessTimeout=",encodeURIComponent(this.settings.assume_success_timeout),"&amp;params=",encodeURIComponent(c),"&amp;filePostName=",encodeURIComponent(this.settings.file_post_name),"&amp;fileTypes=",encodeURIComponent(this.settings.file_types),"&amp;fileTypesDescription=",encodeURIComponent(this.settings.file_types_description),"&amp;fileSizeLimit=",encodeURIComponent(this.settings.file_size_limit),"&amp;fileUploadLimit=",encodeURIComponent(this.settings.file_upload_limit),"&amp;fileQueueLimit=",encodeURIComponent(this.settings.file_queue_limit),"&amp;debugEnabled=",encodeURIComponent(this.settings.debug_enabled),"&amp;buttonImageURL=",encodeURIComponent(this.settings.button_image_url),"&amp;buttonWidth=",encodeURIComponent(this.settings.button_width),"&amp;buttonHeight=",encodeURIComponent(this.settings.button_height),"&amp;buttonText=",encodeURIComponent(this.settings.button_text),"&amp;buttonTextTopPadding=",encodeURIComponent(this.settings.button_text_top_padding),"&amp;buttonTextLeftPadding=",encodeURIComponent(this.settings.button_text_left_padding),"&amp;buttonTextStyle=",encodeURIComponent(this.settings.button_text_style),"&amp;buttonAction=",encodeURIComponent(this.settings.button_action),"&amp;buttonDisabled=",encodeURIComponent(this.settings.button_disabled),"&amp;buttonCursor=",encodeURIComponent(this.settings.button_cursor)].join("");};SWFUpload.prototype.getMovieElement=function(){if(this.movieElement==undefined){this.movieElement=document.getElementById(this.movieName);}if(this.movieElement===null){throw"Could not find Flash element";}return this.movieElement;};SWFUpload.prototype.buildParamString=function(){var f=this.settings.post_params;var d=[];if(typeof(f)==="object"){for(var e in f){if(f.hasOwnProperty(e)){d.push(encodeURIComponent(e.toString())+"="+encodeURIComponent(f[e].toString()));}}}return d.join("&amp;");};SWFUpload.prototype.destroy=function(){try{this.cancelUpload(null,false);var g=null;g=this.getMovieElement();if(g&&typeof(g.CallFunction)==="unknown"){for(var j in g){try{if(typeof(g[j])==="function"){g[j]=null;}}catch(h){}}try{g.parentNode.removeChild(g);}catch(f){}}window[this.movieName]=null;SWFUpload.instances[this.movieName]=null;delete SWFUpload.instances[this.movieName];this.movieElement=null;this.settings=null;this.customSettings=null;this.eventQueue=null;this.movieName=null;return true;}catch(i){return false;}};SWFUpload.prototype.displayDebugInfo=function(){this.debug(["---SWFUpload Instance Info---\n","Version: ",SWFUpload.version,"\n","Movie Name: ",this.movieName,"\n","Settings:\n","\t","upload_url:               ",this.settings.upload_url,"\n","\t","flash_url:                ",this.settings.flash_url,"\n","\t","use_query_string:         ",this.settings.use_query_string.toString(),"\n","\t","requeue_on_error:         ",this.settings.requeue_on_error.toString(),"\n","\t","http_success:             ",this.settings.http_success.join(", "),"\n","\t","assume_success_timeout:   ",this.settings.assume_success_timeout,"\n","\t","file_post_name:           ",this.settings.file_post_name,"\n","\t","post_params:              ",this.settings.post_params.toString(),"\n","\t","file_types:               ",this.settings.file_types,"\n","\t","file_types_description:   ",this.settings.file_types_description,"\n","\t","file_size_limit:          ",this.settings.file_size_limit,"\n","\t","file_upload_limit:        ",this.settings.file_upload_limit,"\n","\t","file_queue_limit:         ",this.settings.file_queue_limit,"\n","\t","debug:                    ",this.settings.debug.toString(),"\n","\t","prevent_swf_caching:      ",this.settings.prevent_swf_caching.toString(),"\n","\t","button_placeholder_id:    ",this.settings.button_placeholder_id.toString(),"\n","\t","button_placeholder:       ",(this.settings.button_placeholder?"Set":"Not Set"),"\n","\t","button_image_url:         ",this.settings.button_image_url.toString(),"\n","\t","button_width:             ",this.settings.button_width.toString(),"\n","\t","button_height:            ",this.settings.button_height.toString(),"\n","\t","button_text:              ",this.settings.button_text.toString(),"\n","\t","button_text_style:        ",this.settings.button_text_style.toString(),"\n","\t","button_text_top_padding:  ",this.settings.button_text_top_padding.toString(),"\n","\t","button_text_left_padding: ",this.settings.button_text_left_padding.toString(),"\n","\t","button_action:            ",this.settings.button_action.toString(),"\n","\t","button_disabled:          ",this.settings.button_disabled.toString(),"\n","\t","custom_settings:          ",this.settings.custom_settings.toString(),"\n","Event Handlers:\n","\t","swfupload_loaded_handler assigned:  ",(typeof this.settings.swfupload_loaded_handler==="function").toString(),"\n","\t","file_dialog_start_handler assigned: ",(typeof this.settings.file_dialog_start_handler==="function").toString(),"\n","\t","file_queued_handler assigned:       ",(typeof this.settings.file_queued_handler==="function").toString(),"\n","\t","file_queue_error_handler assigned:  ",(typeof this.settings.file_queue_error_handler==="function").toString(),"\n","\t","upload_start_handler assigned:      ",(typeof this.settings.upload_start_handler==="function").toString(),"\n","\t","upload_progress_handler assigned:   ",(typeof this.settings.upload_progress_handler==="function").toString(),"\n","\t","upload_error_handler assigned:      ",(typeof this.settings.upload_error_handler==="function").toString(),"\n","\t","upload_success_handler assigned:    ",(typeof this.settings.upload_success_handler==="function").toString(),"\n","\t","upload_complete_handler assigned:   ",(typeof this.settings.upload_complete_handler==="function").toString(),"\n","\t","debug_handler assigned:             ",(typeof this.settings.debug_handler==="function").toString(),"\n"].join(""));};SWFUpload.prototype.addSetting=function(d,f,e){if(f==undefined){return(this.settings[d]=e);}else{return(this.settings[d]=f);}};SWFUpload.prototype.getSetting=function(b){if(this.settings[b]!=undefined){return this.settings[b];}return"";};SWFUpload.prototype.callFlash=function(functionName,argumentArray){argumentArray=argumentArray||[];var movieElement=this.getMovieElement();var returnValue,returnString;try{returnString=movieElement.CallFunction('<invoke name="'+functionName+'" returntype="javascript">'+__flash__argumentsToXML(argumentArray,0)+"</invoke>");returnValue=eval(returnString);}catch(ex){throw"Call to "+functionName+" failed";}if(returnValue!=undefined&&typeof returnValue.post==="object"){returnValue=this.unescapeFilePostParams(returnValue);}return returnValue;};SWFUpload.prototype.selectFile=function(){this.callFlash("SelectFile");};SWFUpload.prototype.selectFiles=function(){this.callFlash("SelectFiles");};SWFUpload.prototype.startUpload=function(b){this.callFlash("StartUpload",[b]);};SWFUpload.prototype.cancelUpload=function(d,c){if(c!==false){c=true;}this.callFlash("CancelUpload",[d,c]);};SWFUpload.prototype.stopUpload=function(){this.callFlash("StopUpload");};SWFUpload.prototype.getStats=function(){return this.callFlash("GetStats");};SWFUpload.prototype.setStats=function(b){this.callFlash("SetStats",[b]);};SWFUpload.prototype.getFile=function(b){if(typeof(b)==="number"){return this.callFlash("GetFileByIndex",[b]);}else{return this.callFlash("GetFile",[b]);}};SWFUpload.prototype.addFileParam=function(e,d,f){return this.callFlash("AddFileParam",[e,d,f]);};SWFUpload.prototype.removeFileParam=function(d,c){this.callFlash("RemoveFileParam",[d,c]);};SWFUpload.prototype.setUploadURL=function(b){this.settings.upload_url=b.toString();this.callFlash("SetUploadURL",[b]);};SWFUpload.prototype.setPostParams=function(b){this.settings.post_params=b;this.callFlash("SetPostParams",[b]);};SWFUpload.prototype.addPostParam=function(d,c){this.settings.post_params[d]=c;this.callFlash("SetPostParams",[this.settings.post_params]);};SWFUpload.prototype.removePostParam=function(b){delete this.settings.post_params[b];this.callFlash("SetPostParams",[this.settings.post_params]);};SWFUpload.prototype.setFileTypes=function(d,c){this.settings.file_types=d;this.settings.file_types_description=c;this.callFlash("SetFileTypes",[d,c]);};SWFUpload.prototype.setFileSizeLimit=function(b){this.settings.file_size_limit=b;this.callFlash("SetFileSizeLimit",[b]);};SWFUpload.prototype.setFileUploadLimit=function(b){this.settings.file_upload_limit=b;this.callFlash("SetFileUploadLimit",[b]);};SWFUpload.prototype.setFileQueueLimit=function(b){this.settings.file_queue_limit=b;this.callFlash("SetFileQueueLimit",[b]);};SWFUpload.prototype.setFilePostName=function(b){this.settings.file_post_name=b;this.callFlash("SetFilePostName",[b]);};SWFUpload.prototype.setUseQueryString=function(b){this.settings.use_query_string=b;this.callFlash("SetUseQueryString",[b]);};SWFUpload.prototype.setRequeueOnError=function(b){this.settings.requeue_on_error=b;this.callFlash("SetRequeueOnError",[b]);};SWFUpload.prototype.setHTTPSuccess=function(b){if(typeof b==="string"){b=b.replace(" ","").split(",");}this.settings.http_success=b;this.callFlash("SetHTTPSuccess",[b]);};SWFUpload.prototype.setAssumeSuccessTimeout=function(b){this.settings.assume_success_timeout=b;this.callFlash("SetAssumeSuccessTimeout",[b]);};SWFUpload.prototype.setDebugEnabled=function(b){this.settings.debug_enabled=b;this.callFlash("SetDebugEnabled",[b]);};SWFUpload.prototype.setButtonImageURL=function(b){if(b==undefined){b="";}this.settings.button_image_url=b;this.callFlash("SetButtonImageURL",[b]);};SWFUpload.prototype.setButtonDimensions=function(f,e){this.settings.button_width=f;this.settings.button_height=e;var d=this.getMovieElement();if(d!=undefined){d.style.width=f+"px";d.style.height=e+"px";}this.callFlash("SetButtonDimensions",[f,e]);};SWFUpload.prototype.setButtonText=function(b){this.settings.button_text=b;this.callFlash("SetButtonText",[b]);};SWFUpload.prototype.setButtonTextPadding=function(c,d){this.settings.button_text_top_padding=d;this.settings.button_text_left_padding=c;this.callFlash("SetButtonTextPadding",[c,d]);};SWFUpload.prototype.setButtonTextStyle=function(b){this.settings.button_text_style=b;this.callFlash("SetButtonTextStyle",[b]);};SWFUpload.prototype.setButtonDisabled=function(b){this.settings.button_disabled=b;this.callFlash("SetButtonDisabled",[b]);};SWFUpload.prototype.setButtonAction=function(b){this.settings.button_action=b;this.callFlash("SetButtonAction",[b]);};SWFUpload.prototype.setButtonCursor=function(b){this.settings.button_cursor=b;this.callFlash("SetButtonCursor",[b]);};SWFUpload.prototype.queueEvent=function(d,f){if(f==undefined){f=[];}else{if(!(f instanceof Array)){f=[f];}}var e=this;if(typeof this.settings[d]==="function"){this.eventQueue.push(function(){this.settings[d].apply(this,f);});setTimeout(function(){e.executeNextEvent();},0);}else{if(this.settings[d]!==null){throw"Event handler "+d+" is unknown or is not a function";}}};SWFUpload.prototype.executeNextEvent=function(){var b=this.eventQueue?this.eventQueue.shift():null;if(typeof(b)==="function"){b.apply(this);}};SWFUpload.prototype.unescapeFilePostParams=function(l){var j=/[$]([0-9a-f]{4})/i;var i={};var k;if(l!=undefined){for(var h in l.post){if(l.post.hasOwnProperty(h)){k=h;var g;while((g=j.exec(k))!==null){k=k.replace(g[0],String.fromCharCode(parseInt("0x"+g[1],16)));}i[k]=l.post[h];}}l.post=i;}return l;};SWFUpload.prototype.testExternalInterface=function(){try{return this.callFlash("TestExternalInterface");}catch(b){return false;}};SWFUpload.prototype.flashReady=function(){var b=this.getMovieElement();if(!b){this.debug("Flash called back ready but the flash movie can't be found.");return;}this.cleanUp(b);this.queueEvent("swfupload_loaded_handler");};SWFUpload.prototype.cleanUp=function(f){try{if(this.movieElement&&typeof(f.CallFunction)==="unknown"){this.debug("Removing Flash functions hooks (this should only run in IE and should prevent memory leaks)");for(var h in f){try{if(typeof(f[h])==="function"){f[h]=null;}}catch(e){}}}}catch(g){}window.__flash__removeCallback=function(c,b){try{if(c){c[b]=null;}}catch(a){}};};SWFUpload.prototype.fileDialogStart=function(){this.queueEvent("file_dialog_start_handler");};SWFUpload.prototype.fileQueued=function(b){b=this.unescapeFilePostParams(b);this.queueEvent("file_queued_handler",b);};SWFUpload.prototype.fileQueueError=function(e,f,d){e=this.unescapeFilePostParams(e);this.queueEvent("file_queue_error_handler",[e,f,d]);};SWFUpload.prototype.fileDialogComplete=function(d,f,e){this.queueEvent("file_dialog_complete_handler",[d,f,e]);};SWFUpload.prototype.uploadStart=function(b){b=this.unescapeFilePostParams(b);this.queueEvent("return_upload_start_handler",b);};SWFUpload.prototype.returnUploadStart=function(d){var c;if(typeof this.settings.upload_start_handler==="function"){d=this.unescapeFilePostParams(d);c=this.settings.upload_start_handler.call(this,d);}else{if(this.settings.upload_start_handler!=undefined){throw"upload_start_handler must be a function";}}if(c===undefined){c=true;}c=!!c;this.callFlash("ReturnUploadStart",[c]);};SWFUpload.prototype.uploadProgress=function(e,f,d){e=this.unescapeFilePostParams(e);this.queueEvent("upload_progress_handler",[e,f,d]);};SWFUpload.prototype.uploadError=function(e,f,d){e=this.unescapeFilePostParams(e);this.queueEvent("upload_error_handler",[e,f,d]);};SWFUpload.prototype.uploadSuccess=function(d,e,f){d=this.unescapeFilePostParams(d);this.queueEvent("upload_success_handler",[d,e,f]);};SWFUpload.prototype.uploadComplete=function(b){b=this.unescapeFilePostParams(b);this.queueEvent("upload_complete_handler",b);};SWFUpload.prototype.debug=function(b){this.queueEvent("debug_handler",b);};SWFUpload.prototype.debugMessage=function(h){if(this.settings.debug){var f,g=[];if(typeof h==="object"&&typeof h.name==="string"&&typeof h.message==="string"){for(var e in h){if(h.hasOwnProperty(e)){g.push(e+": "+h[e]);}}f=g.join("\n")||"";g=f.split("\n");f="EXCEPTION: "+g.join("\nEXCEPTION: ");SWFUpload.Console.writeLine(f);}else{SWFUpload.Console.writeLine(h);}}};SWFUpload.Console={};SWFUpload.Console.writeLine=function(g){var e,f;try{e=document.getElementById("SWFUpload_Console");if(!e){f=document.createElement("form");document.getElementsByTagName("body")[0].appendChild(f);e=document.createElement("textarea");e.id="SWFUpload_Console";e.style.fontFamily="monospace";e.setAttribute("wrap","off");e.wrap="off";e.style.overflow="auto";e.style.width="700px";e.style.height="350px";e.style.margin="5px";f.appendChild(e);}e.value+=g+"\n";e.scrollTop=e.scrollHeight-e.clientHeight;}catch(h){alert("Exception: "+h.name+" Message: "+h.message);}};(function(c){var b={init:function(d,e){return this.each(function(){var n=c(this);var m=n.clone();var j=c.extend({id:n.attr("id"),swf:"uploadify.swf",uploader:"uploadify.php",auto:true,buttonClass:"",buttonCursor:"hand",buttonImage:null,buttonText:"SELECT FILES",checkExisting:false,debug:false,fileObjName:"Filedata",fileSizeLimit:0,fileTypeDesc:"All Files",fileTypeExts:"*.*",height:30,itemTemplate:false,method:"post",multi:true,formData:{},preventCaching:true,progressData:"percentage",queueID:false,queueSizeLimit:999,removeCompleted:true,removeTimeout:3,requeueErrors:false,successTimeout:30,uploadLimit:0,width:120,overrideEvents:[]},d);var g={assume_success_timeout:j.successTimeout,button_placeholder_id:j.id,button_width:j.width,button_height:j.height,button_text:null,button_text_style:null,button_text_top_padding:0,button_text_left_padding:0,button_action:(j.multi?SWFUpload.BUTTON_ACTION.SELECT_FILES:SWFUpload.BUTTON_ACTION.SELECT_FILE),button_disabled:false,button_cursor:(j.buttonCursor=="arrow"?SWFUpload.CURSOR.ARROW:SWFUpload.CURSOR.HAND),button_window_mode:SWFUpload.WINDOW_MODE.TRANSPARENT,debug:j.debug,requeue_on_error:j.requeueErrors,file_post_name:j.fileObjName,file_size_limit:j.fileSizeLimit,file_types:j.fileTypeExts,file_types_description:j.fileTypeDesc,file_queue_limit:j.queueSizeLimit,file_upload_limit:j.uploadLimit,flash_url:j.swf,prevent_swf_caching:j.preventCaching,post_params:j.formData,upload_url:j.uploader,use_query_string:(j.method=="get"),file_dialog_complete_handler:a.onDialogClose,file_dialog_start_handler:a.onDialogOpen,file_queued_handler:a.onSelect,file_queue_error_handler:a.onSelectError,swfupload_loaded_handler:j.onSWFReady,upload_complete_handler:a.onUploadComplete,upload_error_handler:a.onUploadError,upload_progress_handler:a.onUploadProgress,upload_start_handler:a.onUploadStart,upload_success_handler:a.onUploadSuccess};if(e){g=c.extend(g,e);}g=c.extend(g,j);var o=swfobject.getFlashPlayerVersion();var h=(o.major>=9);if(h){window["uploadify_"+j.id]=new SWFUpload(g);var i=window["uploadify_"+j.id];n.data("uploadify",i);var l=c("<div />",{id:j.id,"class":"uploadify",css:{height:j.height+"px",width:j.width+"px"}});c("#"+i.movieName).wrap(l);l=c("#"+j.id);l.data("uploadify",i);var f=c("<div />",{id:j.id+"-button","class":"uploadify-button "+j.buttonClass});if(j.buttonImage){f.css({"background-image":"url('"+j.buttonImage+"')","text-indent":"-9999px"});}f.html('<span class="uploadify-button-text">'+j.buttonText+"</span>").css({height:j.height+"px","line-height":j.height+"px",width:j.width+"px"});l.append(f);c("#"+i.movieName).css({position:"absolute","z-index":1});if(!j.queueID){var k=c("<div />",{id:j.id+"-queue","class":"uploadify-queue"});l.after(k);i.settings.queueID=j.id+"-queue";i.settings.defaultQueue=true;}i.queueData={files:{},filesSelected:0,filesQueued:0,filesReplaced:0,filesCancelled:0,filesErrored:0,uploadsSuccessful:0,uploadsErrored:0,averageSpeed:0,queueLength:0,queueSize:0,uploadSize:0,queueBytesUploaded:0,uploadQueue:[],errorMsg:"Some files were not added to the queue:"};i.original=m;i.wrapper=l;i.button=f;i.queue=k;if(j.onInit){j.onInit.call(n,i);}}else{if(j.onFallback){j.onFallback.call(n);}}});},cancel:function(d,f){var e=arguments;this.each(function(){var l=c(this),i=l.data("uploadify"),j=i.settings,h=-1;if(e[0]){if(e[0]=="*"){var g=i.queueData.queueLength;c("#"+j.queueID).find(".uploadify-queue-item").each(function(){h++;if(e[1]===true){i.cancelUpload(c(this).attr("id"),false);}else{i.cancelUpload(c(this).attr("id"));}c(this).find(".data").removeClass("data").html(" - Cancelled");c(this).find(".uploadify-progress-bar").remove();c(this).delay(1000+100*h).fadeOut(500,function(){c(this).remove();});});i.queueData.queueSize=0;i.queueData.queueLength=0;if(j.onClearQueue){j.onClearQueue.call(l,g);}}else{for(var m=0;m<e.length;m++){i.cancelUpload(e[m]);c("#"+e[m]).find(".data").removeClass("data").html(" - Cancelled");c("#"+e[m]).find(".uploadify-progress-bar").remove();c("#"+e[m]).delay(1000+100*m).fadeOut(500,function(){c(this).remove();});}}}else{var k=c("#"+j.queueID).find(".uploadify-queue-item").get(0);$item=c(k);i.cancelUpload($item.attr("id"));$item.find(".data").removeClass("data").html(" - Cancelled");$item.find(".uploadify-progress-bar").remove();$item.delay(1000).fadeOut(500,function(){c(this).remove();});}});},destroy:function(){this.each(function(){var f=c(this),d=f.data("uploadify"),e=d.settings;d.destroy();if(e.defaultQueue){c("#"+e.queueID).remove();}c("#"+e.id).replaceWith(d.original);if(e.onDestroy){e.onDestroy.call(this);}delete d;});},disable:function(d){this.each(function(){var g=c(this),e=g.data("uploadify"),f=e.settings;if(d){e.button.addClass("disabled");if(f.onDisable){f.onDisable.call(this);}}else{e.button.removeClass("disabled");if(f.onEnable){f.onEnable.call(this);}}e.setButtonDisabled(d);});},settings:function(e,g,h){var d=arguments;var f=g;this.each(function(){var k=c(this),i=k.data("uploadify"),j=i.settings;if(typeof(d[0])=="object"){for(var l in g){setData(l,g[l]);}}if(d.length===1){f=j[e];}else{switch(e){case"uploader":i.setUploadURL(g);break;case"formData":if(!h){g=c.extend(j.formData,g);}i.setPostParams(j.formData);break;case"method":if(g=="get"){i.setUseQueryString(true);}else{i.setUseQueryString(false);}break;case"fileObjName":i.setFilePostName(g);break;case"fileTypeExts":i.setFileTypes(g,j.fileTypeDesc);break;case"fileTypeDesc":i.setFileTypes(j.fileTypeExts,g);break;case"fileSizeLimit":i.setFileSizeLimit(g);break;case"uploadLimit":i.setFileUploadLimit(g);break;case"queueSizeLimit":i.setFileQueueLimit(g);break;case"buttonImage":i.button.css("background-image",settingValue);break;case"buttonCursor":if(g=="arrow"){i.setButtonCursor(SWFUpload.CURSOR.ARROW);}else{i.setButtonCursor(SWFUpload.CURSOR.HAND);}break;case"buttonText":c("#"+j.id+"-button").find(".uploadify-button-text").html(g);break;case"width":i.setButtonDimensions(g,j.height);break;case"height":i.setButtonDimensions(j.width,g);break;case"multi":if(g){i.setButtonAction(SWFUpload.BUTTON_ACTION.SELECT_FILES);}else{i.setButtonAction(SWFUpload.BUTTON_ACTION.SELECT_FILE);}break;}j[e]=g;}});if(d.length===1){return f;}},stop:function(){this.each(function(){var e=c(this),d=e.data("uploadify");d.queueData.averageSpeed=0;d.queueData.uploadSize=0;d.queueData.bytesUploaded=0;d.queueData.uploadQueue=[];d.stopUpload();});},upload:function(){var d=arguments;this.each(function(){var f=c(this),e=f.data("uploadify");e.queueData.averageSpeed=0;e.queueData.uploadSize=0;e.queueData.bytesUploaded=0;e.queueData.uploadQueue=[];if(d[0]){if(d[0]=="*"){e.queueData.uploadSize=e.queueData.queueSize;e.queueData.uploadQueue.push("*");e.startUpload();}else{for(var g=0;g<d.length;g++){e.queueData.uploadSize+=e.queueData.files[d[g]].size;e.queueData.uploadQueue.push(d[g]);}e.startUpload(e.queueData.uploadQueue.shift());}}else{e.startUpload();}});}};var a={onDialogOpen:function(){var d=this.settings;this.queueData.errorMsg="Some files were not added to the queue:";this.queueData.filesReplaced=0;this.queueData.filesCancelled=0;if(d.onDialogOpen){d.onDialogOpen.call(this);}},onDialogClose:function(d,f,g){var e=this.settings;this.queueData.filesErrored=d-f;this.queueData.filesSelected=d;this.queueData.filesQueued=f-this.queueData.filesCancelled;this.queueData.queueLength=g;if(c.inArray("onDialogClose",e.overrideEvents)<0){if(this.queueData.filesErrored>0){alert(this.queueData.errorMsg);}}if(e.onDialogClose){e.onDialogClose.call(this,this.queueData);}if(e.auto){c("#"+e.id).uploadify("upload","*");}},onSelect:function(h){var i=this.settings;var f={};for(var g in this.queueData.files){f=this.queueData.files[g];if(f.uploaded!=true&&f.name==h.name){var e=confirm('The file named "'+h.name+'" is already in the queue.\nDo you want to replace the existing item in the queue?');if(!e){this.cancelUpload(h.id);this.queueData.filesCancelled++;return false;}else{c("#"+f.id).remove();this.cancelUpload(f.id);this.queueData.filesReplaced++;}}}var j=Math.round(h.size/1024);var o="KB";if(j>1000){j=Math.round(j/1000);o="MB";}var l=j.toString().split(".");j=l[0];if(l.length>1){j+="."+l[1].substr(0,2);}j+=o;var k=h.name;if(k.length>25){k=k.substr(0,25)+"...";}itemData={fileID:h.id,instanceID:i.id,fileName:k,fileSize:j};if(i.itemTemplate==false){i.itemTemplate='<div id="${fileID}" class="uploadify-queue-item">					<div class="cancel">						<a href="javascript:$(\'#${instanceID}\').uploadify(\'cancel\', \'${fileID}\')">X</a>					</div>					<span class="fileName">${fileName} (${fileSize})</span><span class="data"></span>					<div class="uploadify-progress">						<div class="uploadify-progress-bar"><!--Progress Bar--></div>					</div>				</div>';}if(c.inArray("onSelect",i.overrideEvents)<0){itemHTML=i.itemTemplate;for(var m in itemData){itemHTML=itemHTML.replace(new RegExp("\\$\\{"+m+"\\}","g"),itemData[m]);}c("#"+i.queueID).append(itemHTML);}this.queueData.queueSize+=h.size;this.queueData.files[h.id]=h;if(i.onSelect){i.onSelect.apply(this,arguments);}},onSelectError:function(d,g,f){var e=this.settings;if(c.inArray("onSelectError",e.overrideEvents)<0){switch(g){case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:if(e.queueSizeLimit>f){this.queueData.errorMsg+="\nThe number of files selected exceeds the remaining upload limit ("+f+").";}else{this.queueData.errorMsg+="\nThe number of files selected exceeds the queue size limit ("+e.queueSizeLimit+").";}break;case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:this.queueData.errorMsg+='\nThe file "'+d.name+'" exceeds the size limit ('+e.fileSizeLimit+").";break;case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:this.queueData.errorMsg+='\nThe file "'+d.name+'" is empty.';break;case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:this.queueData.errorMsg+='\nThe file "'+d.name+'" is not an accepted file type ('+e.fileTypeDesc+").";break;}}if(g!=SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED){delete this.queueData.files[d.id];}if(e.onSelectError){e.onSelectError.apply(this,arguments);}},onQueueComplete:function(){if(this.settings.onQueueComplete){this.settings.onQueueComplete.call(this,this.settings.queueData);}},onUploadComplete:function(f){var g=this.settings,d=this;var e=this.getStats();this.queueData.queueLength=e.files_queued;if(this.queueData.uploadQueue[0]=="*"){if(this.queueData.queueLength>0){this.startUpload();}else{this.queueData.uploadQueue=[];if(g.onQueueComplete){g.onQueueComplete.call(this,this.queueData);}}}else{if(this.queueData.uploadQueue.length>0){this.startUpload(this.queueData.uploadQueue.shift());}else{this.queueData.uploadQueue=[];if(g.onQueueComplete){g.onQueueComplete.call(this,this.queueData);}}}if(c.inArray("onUploadComplete",g.overrideEvents)<0){if(g.removeCompleted){switch(f.filestatus){case SWFUpload.FILE_STATUS.COMPLETE:setTimeout(function(){if(c("#"+f.id)){d.queueData.queueSize-=f.size;d.queueData.queueLength-=1;delete d.queueData.files[f.id];c("#"+f.id).fadeOut(500,function(){c(this).remove();});}},g.removeTimeout*1000);break;case SWFUpload.FILE_STATUS.ERROR:if(!g.requeueErrors){setTimeout(function(){if(c("#"+f.id)){d.queueData.queueSize-=f.size;d.queueData.queueLength-=1;delete d.queueData.files[f.id];c("#"+f.id).fadeOut(500,function(){c(this).remove();});}},g.removeTimeout*1000);}break;}}else{f.uploaded=true;}}if(g.onUploadComplete){g.onUploadComplete.call(this,f);}},onUploadError:function(e,i,h){var f=this.settings;var g="Error";switch(i){case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:g="HTTP Error ("+h+")";break;case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:g="Missing Upload URL";break;case SWFUpload.UPLOAD_ERROR.IO_ERROR:g="IO Error";break;case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:g="Security Error";break;case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:alert("The upload limit has been reached ("+h+").");g="Exceeds Upload Limit";break;case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:g="Failed";break;case SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND:break;case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:g="Validation Error";break;case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:g="Cancelled";this.queueData.queueSize-=e.size;this.queueData.queueLength-=1;if(e.status==SWFUpload.FILE_STATUS.IN_PROGRESS||c.inArray(e.id,this.queueData.uploadQueue)>=0){this.queueData.uploadSize-=e.size;}if(f.onCancel){f.onCancel.call(this,e);}delete this.queueData.files[e.id];break;case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:g="Stopped";break;}if(c.inArray("onUploadError",f.overrideEvents)<0){if(i!=SWFUpload.UPLOAD_ERROR.FILE_CANCELLED&&i!=SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED){c("#"+e.id).addClass("uploadify-error");}c("#"+e.id).find(".uploadify-progress-bar").css("width","1px");if(i!=SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND&&e.status!=SWFUpload.FILE_STATUS.COMPLETE){c("#"+e.id).find(".data").html(" - "+g);}}var d=this.getStats();this.queueData.uploadsErrored=d.upload_errors;if(f.onUploadError){f.onUploadError.call(this,e,i,h,g);}},onUploadProgress:function(g,m,j){var h=this.settings;var e=new Date();var n=e.getTime();var k=n-this.timer;if(k>500){this.timer=n;}var i=m-this.bytesLoaded;this.bytesLoaded=m;var d=this.queueData.queueBytesUploaded+m;var p=Math.round(m/j*100);var o="KB/s";var l=0;var f=(i/1024)/(k/1000);f=Math.floor(f*10)/10;if(this.queueData.averageSpeed>0){this.queueData.averageSpeed=Math.floor((this.queueData.averageSpeed+f)/2);}else{this.queueData.averageSpeed=Math.floor(f);}if(f>1000){l=(f*0.001);this.queueData.averageSpeed=Math.floor(l);o="MB/s";}if(c.inArray("onUploadProgress",h.overrideEvents)<0){if(h.progressData=="percentage"){c("#"+g.id).find(".data").html(" - "+p+"%");}else{if(h.progressData=="speed"&&k>500){c("#"+g.id).find(".data").html(" - "+this.queueData.averageSpeed+o);}}c("#"+g.id).find(".uploadify-progress-bar").css("width",p+"%");}if(h.onUploadProgress){h.onUploadProgress.call(this,g,m,j,d,this.queueData.uploadSize);}},onUploadStart:function(d){var e=this.settings;var f=new Date();this.timer=f.getTime();this.bytesLoaded=0;if(this.queueData.uploadQueue.length==0){this.queueData.uploadSize=d.size;}if(e.checkExisting){c.ajax({type:"POST",async:false,url:e.checkExisting,data:{filename:d.name},success:function(h){if(h==1){var g=confirm('A file with the name "'+d.name+'" already exists on the server.\nWould you like to replace the existing file?');if(!g){this.cancelUpload(d.id);c("#"+d.id).remove();if(this.queueData.uploadQueue.length>0&&this.queueData.queueLength>0){if(this.queueData.uploadQueue[0]=="*"){this.startUpload();}else{this.startUpload(this.queueData.uploadQueue.shift());}}}}}});}if(e.onUploadStart){e.onUploadStart.call(this,d);}},onUploadSuccess:function(f,h,d){var g=this.settings;var e=this.getStats();this.queueData.uploadsSuccessful=e.successful_uploads;this.queueData.queueBytesUploaded+=f.size;if(c.inArray("onUploadSuccess",g.overrideEvents)<0){c("#"+f.id).find(".data").html(" - Complete");}if(g.onUploadSuccess){g.onUploadSuccess.call(this,f,h,d);}}};c.fn.uploadify=function(d){if(b[d]){return b[d].apply(this,Array.prototype.slice.call(arguments,1));}else{if(typeof d==="object"||!d){return b.init.apply(this,arguments);}else{c.error("The method "+d+" does not exist in $.uploadify");}}};})($);

/*!
	jQuery ColorBox v1.4.3 - 2013-02-18
	(c) 2013 Jack Moore - jacklmoore.com/colorbox
	license: http://www.opensource.org/licenses/mit-license.php
*/
(function(e,t,i){function o(i,o,n){var r=t.createElement(i);return o&&(r.id=Y+o),n&&(r.style.cssText=n),e(r)}function n(e){var t=T.length,i=(A+e)%t;return 0>i?t+i:i}function r(e,t){return Math.round((/%/.test(e)?("x"===t?k.width():k.height())/100:1)*parseInt(e,10))}function h(e,t){return e.photo||e.photoRegex.test(t)}function l(e,t){return e.retinaUrl&&i.devicePixelRatio>1?t.replace(e.photoRegex,e.retinaSuffix):t}function s(e){"contains"in w[0]&&!w[0].contains(e.target)&&(e.stopPropagation(),w.focus())}function a(){var t,i=e.data(N,V);null==i?(K=e.extend({},J),console&&console.log&&console.log("Error: cboxElement missing settings object")):K=e.extend({},i);for(t in K)e.isFunction(K[t])&&"on"!==t.slice(0,2)&&(K[t]=K[t].call(N));K.rel=K.rel||N.rel||e(N).data("rel")||"nofollow",K.href=K.href||e(N).attr("href"),K.title=K.title||N.title,"string"==typeof K.href&&(K.href=e.trim(K.href))}function d(i,o){e(t).trigger(i),at.trigger(i),e.isFunction(o)&&o.call(N)}function c(){var e,t,i,o,n,r=Y+"Slideshow_",h="click."+Y;K.slideshow&&T[1]?(t=function(){clearTimeout(e)},i=function(){(K.loop||T[A+1])&&(e=setTimeout(G.next,K.slideshowSpeed))},o=function(){M.html(K.slideshowStop).unbind(h).one(h,n),at.bind(it,i).bind(tt,t).bind(ot,n),w.removeClass(r+"off").addClass(r+"on")},n=function(){t(),at.unbind(it,i).unbind(tt,t).unbind(ot,n),M.html(K.slideshowStart).unbind(h).one(h,function(){G.next(),o()}),w.removeClass(r+"on").addClass(r+"off")},K.slideshowAuto?o():n()):w.removeClass(r+"off "+r+"on")}function u(i){U||(N=i,a(),T=e(N),A=0,"nofollow"!==K.rel&&(T=e("."+Z).filter(function(){var t,i=e.data(this,V);return i&&(t=e(this).data("rel")||i.rel||this.rel),t===K.rel}),A=T.index(N),-1===A&&(T=T.add(N),A=T.length-1)),m.css({opacity:parseFloat(K.opacity),cursor:K.overlayClose?"pointer":"auto",visibility:"visible"}).show(),j||(j=q=!0,w.css({visibility:"hidden",display:"block"}),E=o(dt,"LoadedContent","width:0; height:0; overflow:hidden").appendTo(v),_=x.height()+C.height()+v.outerHeight(!0)-v.height(),z=y.width()+b.width()+v.outerWidth(!0)-v.width(),D=E.outerHeight(!0),B=E.outerWidth(!0),K.w=r(K.initialWidth,"x"),K.h=r(K.initialHeight,"y"),G.position(),lt&&k.bind("resize."+st+" scroll."+st,function(){m.css({width:k.width(),height:k.height(),top:k.scrollTop(),left:k.scrollLeft()})}).trigger("resize."+st),c(),d(et,K.onOpen),P.add(W).hide(),R.html(K.close).show(),w.focus(),t.addEventListener&&(t.addEventListener("focus",s,!0),at.one(nt,function(){t.removeEventListener("focus",s,!0)})),K.returnFocus&&at.one(nt,function(){e(N).focus()})),G.load(!0))}function f(){!w&&t.body&&(X=!1,k=e(i),w=o(dt).attr({id:V,"class":ht?Y+(lt?"IE6":"IE"):"",role:"dialog",tabindex:"-1"}).hide(),m=o(dt,"Overlay",lt?"position:absolute":"").hide(),L=o(dt,"LoadingOverlay").add(o(dt,"LoadingGraphic")),g=o(dt,"Wrapper"),v=o(dt,"Content").append(W=o(dt,"Title"),H=o(dt,"Current"),F=o("button","Previous"),S=o("button","Next"),M=o("button","Slideshow"),L,R=o("button","Close")),g.append(o(dt).append(o(dt,"TopLeft"),x=o(dt,"TopCenter"),o(dt,"TopRight")),o(dt,!1,"clear:left").append(y=o(dt,"MiddleLeft"),v,b=o(dt,"MiddleRight")),o(dt,!1,"clear:left").append(o(dt,"BottomLeft"),C=o(dt,"BottomCenter"),o(dt,"BottomRight"))).find("div div").css({"float":"left"}),I=o(dt,!1,"position:absolute; width:9999px; visibility:hidden; display:none"),P=S.add(F).add(H).add(M),e(t.body).append(m,w.append(g,I)))}function p(){function i(e){e.which>1||e.shiftKey||e.altKey||e.metaKey||(e.preventDefault(),u(this))}return w?(X||(X=!0,S.click(function(){G.next()}),F.click(function(){G.prev()}),R.click(function(){G.close()}),m.click(function(){K.overlayClose&&G.close()}),e(t).bind("keydown."+Y,function(e){var t=e.keyCode;j&&K.escKey&&27===t&&(e.preventDefault(),G.close()),j&&K.arrowKey&&T[1]&&!e.altKey&&(37===t?(e.preventDefault(),F.click()):39===t&&(e.preventDefault(),S.click()))}),e.isFunction(e.fn.on)?e(t).on("click."+Y,"."+Z,i):e("."+Z).live("click."+Y,i)),!0):!1}var m,w,g,v,x,y,b,C,T,k,E,I,L,W,H,M,S,F,R,P,K,_,z,D,B,N,A,O,j,q,U,$,G,Q,X,J={transition:"elastic",speed:300,width:!1,initialWidth:"600",innerWidth:!1,maxWidth:!1,height:!1,initialHeight:"450",innerHeight:!1,maxHeight:!1,scalePhotos:!0,scrolling:!0,inline:!1,html:!1,iframe:!1,fastIframe:!0,photo:!1,href:!1,title:!1,rel:!1,opacity:.9,preloading:!0,className:!1,retinaImage:!1,retinaUrl:!1,retinaSuffix:"@2x.$1",current:"image {current} of {total}",previous:"previous",next:"next",close:"close",xhrError:"This content failed to load.",imgError:"This image failed to load.",open:!1,returnFocus:!0,reposition:!0,loop:!0,slideshow:!1,slideshowAuto:!0,slideshowSpeed:2500,slideshowStart:"start slideshow",slideshowStop:"stop slideshow",photoRegex:/\.(gif|png|jp(e|g|eg)|bmp|ico)((#|\?).*)?$/i,onOpen:!1,onLoad:!1,onComplete:!1,onCleanup:!1,onClosed:!1,overlayClose:!0,escKey:!0,arrowKey:!0,top:!1,bottom:!1,left:!1,right:!1,fixed:!1,data:void 0},V="colorbox",Y="cbox",Z=Y+"Element",et=Y+"_open",tt=Y+"_load",it=Y+"_complete",ot=Y+"_cleanup",nt=Y+"_closed",rt=Y+"_purge",ht=!e.support.leadingWhitespace,lt=ht&&!i.XMLHttpRequest,st=Y+"_IE6",at=e({}),dt="div";e.colorbox||(e(f),G=e.fn[V]=e[V]=function(t,i){var o=this;if(t=t||{},f(),p()){if(e.isFunction(o))o=e("<a/>"),t.open=!0;else if(!o[0])return o;i&&(t.onComplete=i),o.each(function(){e.data(this,V,e.extend({},e.data(this,V)||J,t))}).addClass(Z),(e.isFunction(t.open)&&t.open.call(o)||t.open)&&u(o[0])}return o},G.position=function(e,t){function i(e){x[0].style.width=C[0].style.width=v[0].style.width=parseInt(e.style.width,10)-z+"px",v[0].style.height=y[0].style.height=b[0].style.height=parseInt(e.style.height,10)-_+"px"}var o,n,h,l=0,s=0,a=w.offset();k.unbind("resize."+Y),w.css({top:-9e4,left:-9e4}),n=k.scrollTop(),h=k.scrollLeft(),K.fixed&&!lt?(a.top-=n,a.left-=h,w.css({position:"fixed"})):(l=n,s=h,w.css({position:"absolute"})),s+=K.right!==!1?Math.max(k.width()-K.w-B-z-r(K.right,"x"),0):K.left!==!1?r(K.left,"x"):Math.round(Math.max(k.width()-K.w-B-z,0)/2),l+=K.bottom!==!1?Math.max(k.height()-K.h-D-_-r(K.bottom,"y"),0):K.top!==!1?r(K.top,"y"):Math.round(Math.max(k.height()-K.h-D-_,0)/2),w.css({top:a.top,left:a.left,visibility:"visible"}),e=w.width()===K.w+B&&w.height()===K.h+D?0:e||0,g[0].style.width=g[0].style.height="9999px",o={width:K.w+B+z,height:K.h+D+_,top:l,left:s},0===e&&w.css(o),w.dequeue().animate(o,{duration:e,complete:function(){i(this),q=!1,g[0].style.width=K.w+B+z+"px",g[0].style.height=K.h+D+_+"px",K.reposition&&setTimeout(function(){k.bind("resize."+Y,G.position)},1),t&&t()},step:function(){i(this)}})},G.resize=function(e){j&&(e=e||{},e.width&&(K.w=r(e.width,"x")-B-z),e.innerWidth&&(K.w=r(e.innerWidth,"x")),E.css({width:K.w}),e.height&&(K.h=r(e.height,"y")-D-_),e.innerHeight&&(K.h=r(e.innerHeight,"y")),e.innerHeight||e.height||(E.css({height:"auto"}),K.h=E.height()),E.css({height:K.h}),G.position("none"===K.transition?0:K.speed))},G.prep=function(t){function i(){return K.w=K.w||E.width(),K.w=K.mw&&K.mw<K.w?K.mw:K.w,K.w}function r(){return K.h=K.h||E.height(),K.h=K.mh&&K.mh<K.h?K.mh:K.h,K.h}if(j){var s,a="none"===K.transition?0:K.speed;E.empty().remove(),E=o(dt,"LoadedContent").append(t),E.hide().appendTo(I.show()).css({width:i(),overflow:K.scrolling?"auto":"hidden"}).css({height:r()}).prependTo(v),I.hide(),e(O).css({"float":"none"}),s=function(){function t(){ht&&w[0].style.removeAttribute("filter")}var i,r,s=T.length,c="frameBorder",u="allowTransparency";j&&(r=function(){clearTimeout($),L.hide(),d(it,K.onComplete)},ht&&O&&E.fadeIn(100),W.html(K.title).add(E).show(),s>1?("string"==typeof K.current&&H.html(K.current.replace("{current}",A+1).replace("{total}",s)).show(),S[K.loop||s-1>A?"show":"hide"]().html(K.next),F[K.loop||A?"show":"hide"]().html(K.previous),K.slideshow&&M.show(),K.preloading&&e.each([n(-1),n(1)],function(){var t,i,o=T[this],n=e.data(o,V);n&&n.href?(t=n.href,e.isFunction(t)&&(t=t.call(o))):t=e(o).attr("href"),t&&h(n,t)&&(t=l(n,t),i=new Image,i.src=t)})):P.hide(),K.iframe?(i=o("iframe")[0],c in i&&(i[c]=0),u in i&&(i[u]="true"),K.scrolling||(i.scrolling="no"),e(i).attr({src:K.href,name:(new Date).getTime(),"class":Y+"Iframe",allowFullScreen:!0,webkitAllowFullScreen:!0,mozallowfullscreen:!0}).one("load",r).appendTo(E),at.one(rt,function(){i.src="//about:blank"}),K.fastIframe&&e(i).trigger("load")):r(),"fade"===K.transition?w.fadeTo(a,1,t):t())},"fade"===K.transition?w.fadeTo(a,0,function(){G.position(0,s)}):G.position(a,s)}},G.load=function(t){var n,s,c,u=G.prep;q=!0,O=!1,N=T[A],t||a(),Q&&w.add(m).removeClass(Q),K.className&&w.add(m).addClass(K.className),Q=K.className,d(rt),d(tt,K.onLoad),K.h=K.height?r(K.height,"y")-D-_:K.innerHeight&&r(K.innerHeight,"y"),K.w=K.width?r(K.width,"x")-B-z:K.innerWidth&&r(K.innerWidth,"x"),K.mw=K.w,K.mh=K.h,K.maxWidth&&(K.mw=r(K.maxWidth,"x")-B-z,K.mw=K.w&&K.w<K.mw?K.w:K.mw),K.maxHeight&&(K.mh=r(K.maxHeight,"y")-D-_,K.mh=K.h&&K.h<K.mh?K.h:K.mh),n=K.href,$=setTimeout(function(){L.show()},100),K.inline?(c=o(dt).hide().insertBefore(e(n)[0]),at.one(rt,function(){c.replaceWith(E.children())}),u(e(n))):K.iframe?u(" "):K.html?u(K.html):h(K,n)?(n=l(K,n),e(O=new Image).addClass(Y+"Photo").bind("error",function(){K.title=!1,u(o(dt,"Error").html(K.imgError))}).one("load",function(){var e;K.retinaImage&&i.devicePixelRatio>1&&(O.height=O.height/i.devicePixelRatio,O.width=O.width/i.devicePixelRatio),K.scalePhotos&&(s=function(){O.height-=O.height*e,O.width-=O.width*e},K.mw&&O.width>K.mw&&(e=(O.width-K.mw)/O.width,s()),K.mh&&O.height>K.mh&&(e=(O.height-K.mh)/O.height,s())),K.h&&(O.style.marginTop=Math.max(K.mh-O.height,0)/2+"px"),T[1]&&(K.loop||T[A+1])&&(O.style.cursor="pointer",O.onclick=function(){G.next()}),ht&&(O.style.msInterpolationMode="bicubic"),setTimeout(function(){u(O)},1)}),setTimeout(function(){O.src=n},1)):n&&I.load(n,K.data,function(t,i){u("error"===i?o(dt,"Error").html(K.xhrError):e(this).contents())})},G.next=function(){!q&&T[1]&&(K.loop||T[A+1])&&(A=n(1),G.load())},G.prev=function(){!q&&T[1]&&(K.loop||A)&&(A=n(-1),G.load())},G.close=function(){j&&!U&&(U=!0,j=!1,d(ot,K.onCleanup),k.unbind("."+Y+" ."+st),m.fadeTo(200,0),w.stop().fadeTo(300,0,function(){w.add(m).css({opacity:1,cursor:"auto"}).hide(),d(rt),E.empty().remove(),setTimeout(function(){U=!1,d(nt,K.onClosed)},1)}))},G.remove=function(){e([]).add(w).add(m).remove(),w=null,e("."+Z).removeData(V).removeClass(Z),e(t).unbind("click."+Y)},G.element=function(){return e(N)},G.settings=J)})(jQuery,document,window);