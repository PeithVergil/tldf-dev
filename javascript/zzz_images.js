	var inc = 2;
	function showAtElement(el_near, el_this) {
		var p = getAbsolutePosY(el_near);
		el_this.style.left = p.x + "px";
		el_this.style.top = p.y + el_near.offsetHeight + "px";
	};
	function showOnElement(el_near, el_this) {
		var new_width = inc*el_near.width;
		var new_height = inc*el_near.height;

		var p = getAbsolutePos(el_near);
		var new_x = Math.floor(p.x - (new_width-el_near.width)/2)+"px";
		// mod user start
		var new_y = Math.floor(p.y - (new_height-el_near.height)/2 - 180)+"px";
		// mod user end
		// mod admin start
		//var new_y = Math.floor(p.y - (new_height-el_near.height)/2)+"px";
		// mod admin end
		el_this.style.left = new_x;
		el_this.style.top = new_y;
	};

	function getAbsolutePos(el) {
		var r = { x: el.offsetLeft, y: el.offsetTop };
		if (el.offsetParent) {
			var tmp = getAbsolutePos(el.offsetParent);
			r.x += tmp.x;
			r.y += tmp.y;
		}
		return r;
	};
	function getAbsolutePosY(el) {
		var r = { x: Math.floor((window.screen.availWidth-20-800)/2), y: el.offsetTop };
		if (el.offsetParent) {
			var tmp = getAbsolutePos(el.offsetParent);
			r.y += tmp.y;
		}
		return r;
	};

	function CreateBigImage(el_from, evt)
	{
		//SH no need to popup
		/*
		evt = (evt) ? evt : ((window.event) ? event : null);
		if(document.getElementById("big_img") != null){
			DeleteBigImage(evt);
		}
		var new_width = inc*el_from.width;
		var new_height = inc*el_from.height;
		if (el_from.src.indexOf("thumb") != -1){
			var file_name = el_from.src.replace("thumb", "big_thumb");
			var div_obj = document.createElement("div");
			div_obj.setAttribute("id", "big_img");
			div_obj.setAttribute("style", "");
			// mod user start
			el_from.parentNode.appendChild(div_obj);
			div_obj.style.left = '35px';
			// mod user end
			// mod admin start
			//div_obj.style.left = '0px';
			//div_obj.style.top = '0px';
			// mod admin end
			div_obj.style.position = 'absolute';
			div_obj.style.display = 'none';
			div_obj.style.cursor = 'pointer';
			div_obj.innerHTML= '<img src="'+file_name+'" class="icon">';
			div_obj.onmouseout = DeleteBigImage;
			div_obj.onclick = OnLink;
			if (document.layers){div_obj.captureEvents(Event.MOUSEDOWN);div_obj.onmousedown=clickNS;}
			else{div_obj.onmouseup=clickNS;div_obj.oncontextmenu=clickIE;}
			div_obj.oncontextmenu=new Function("return false")

			showOnElement(el_from, div_obj);
			div_obj.style.display = 'block';
		}
		*/
	}
	function CreateLister(el_from, evt){
		if (document.layers){el_from.captureEvents(Event.MOUSEDOWN);el_from.onmousedown=clickNS;}
		else{el_from.onmouseup=clickNS;el_from.oncontextmenu=clickIE;}
		el_from.oncontextmenu=new Function("return false")
	}
	function OnLink(evt){
		location.href=this.parentNode.href;
		return false;
	}
	function DeleteBigImage(evt){
		evt = (evt) ? evt : ((window.event) ? event : null);
		var elem = document.getElementById('big_img');
		elem.parentNode.removeChild(elem);
		evt.cancelBubble = false;
	}
	function OnRightClick(e) {
		if (navigator.appName == 'Netscape' && (e.which == 3 || e.which == 2))
			return false;
		else if (navigator.appName == 'Microsoft Internet Explorer' && 	(event.button == 2 || event.button == 3)) {
			event.cancelBubble= true;
			alert("Sorry, you do not have permission to right click.");
			return false;
		}
		return true;
	}
	function clickIE() {if (document.all) {alert(message); return false;}}
	function clickNS(e) {
		if(document.layers||(document.getElementById&&!document.all)) {
			if (e.which==2||e.which==3) {return false;}
		}
	}
	function OnLink(evt){
		if(window.jump_type==null) location.href=this.parentNode.href;
		else {
			switch(window.jump_type){
				case 'popup':
					window.open(this.parentNode.href, 'view_img', 'height=300, resizable=yes, scrollbars=no, width=400, menubar=no,status=no, left=200, top=20');
					break;
				default:
					location.href=this.parentNode.href;
					break;
			}

		}
		return false;
	}
// -->
