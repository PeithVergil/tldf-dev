/**
 * comment by ralf
 * would be nice to replace all this ajax stuff with jquery
 * consolidated with /templates/dtl_theme_n/location.js to /javascript/location.js
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
			req.open('POST', file_name+'?rnd='+Math.random(), anisochronous);
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

