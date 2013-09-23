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
