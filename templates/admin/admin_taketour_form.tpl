{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform}</font>
	<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.area_taketour_edit}</div>
				<table border=0 cellspacing=1 cellpadding=5 width="100%">
                <form method="post" action="{$form.action}"  enctype="multipart/form-data" name="taketour">
                {$form.hiddens}
                    <tr bgcolor="#ffffff">
                        <td align="right" width="17%" class="main_header_text">&nbsp;</td>
                        <td class="main_content_text" align="left">&nbsp;<input type="checkbox" name="status" value="1" {if $data.status}checked{/if} >&nbsp;-&nbsp;{$header.status}&nbsp;
						</td>
						<td class="main_content_text" align="left" width="50%" >&nbsp;</td>
                    </tr>
                    <tr bgcolor="#ffffff" valign="top">
                        <td align="right" width="17%" class="main_header_text">{$header.file}:&nbsp;</td>
                        <td class="main_content_text" align="left"><input type="file"  name="upload_file"></td>
						<td class="main_content_text" align="left" width="50%" >&nbsp;</td>
                    </tr>
					{if $data.file_type == 'p'}
                    <tr bgcolor="#ffffff" valign="top">
                        <td align="right" width="17%" class="main_header_text">&nbsp;</td>
                        <td class="main_content_text" align="center"><img src="{$data.file_path}" border=1></td>
						<td class="main_content_text" align="left" width="50%" >&nbsp;</td>
                    </tr>
					{elseif $data.file_type == 'f'}
                    <tr bgcolor="#ffffff" valign="top">
                        <td align="right" width="17%" class="main_header_text">&nbsp;</td>
                        <td class="main_content_text" align="center">
							<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.#version=5,0,30,0" height="250" width="300">
								<param name="movie" value="{$data.file_path}">
								<param name="quality" value="best">
								<param name="play" value="true">
								<embed height="250" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" src="{$data.file_path}" type="application/x-shockwave-flash" width="300" quality="best" play="true">
							</object>
						</td>
						<td class="main_content_text" align="left" width="50%" >&nbsp;</td>
                    </tr>
					{elseif $data.file_type == 'a' || $data.file_type == 'v'}
                    <tr bgcolor="#ffffff" valign="top">
                        <td align="right" width="17%" class="main_header_text">&nbsp;</td>
                        <td class="main_content_text" align="center">
							<object id="mediaplayer1" classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#version=6,4,5,715" standby="loading microsoftr windowsr media player components..."  type="application/x-oleobject">
							<param name="autostart" value="false">
							<param name="filename" value="{$data.file_path}">
							<param name="showcontrols" value="true">
							<param name="showstatusbar" value="false">
							<embed type="application/x-mplayer2"  pluginspage="http://www.microsoft.com/windows/mediaplayer/" src="{$data.file_path}" name="mediaplayer1" autostart=1 showcontrols=0></embed>
							</object>
						</td>
						<td class="main_content_text" align="left" width="50%" >&nbsp;</td>
                    </tr>
					{/if}
                    <tr bgcolor="#ffffff" valign="top">
                        <td align="right" width="17%" class="main_header_text">&nbsp;</td>
                        <td class="main_content_text" align="left">
						<table>
							<tr bgcolor="#ffffff" valign=center>
								<td>
								<input type="button" style="width:40px" value="b" class="button" onclick="SetNewsFont('b', '')">
								</td>
								<td>
								<input type="button" style="width:40px" value="i" class="button" onclick="SetNewsFont('i', '')">
								</td>
								<td>
								<input type="button" style="width:40px" value="u" class="button" onclick="SetNewsFont('u', '')">
								</td>
								<td>
								<input type="button" style="width:40px" value="br" class="button" onclick="SetNewsFont('br', '')">
								</td>
								<td>
								<select name="color" onchange="SetNewsFont('color', this.form.color.options[this.form.color.selectedIndex].value); this.form.color.selectedIndex=0;" style="width:90">
									<option value="">{$color.select_fontcolor}</option>{include file="$admingentemplates/admin_options_color.tpl"}
								</select>&nbsp;
								</td>
								<td>
								<select name="bgc" onchange="SetNewsFont('bgc', this.form.bgc.options[this.form.bgc.selectedIndex].value); this.form.bgc.selectedIndex=0;" style="width:90">
									<option value="">{$color.select_bgcolor}</option>{include file="$admingentemplates/admin_options_color.tpl"}
								</select>&nbsp;
								</td>
							</tr>
							<tr bgcolor="#ffffff"valign=center>
								<td>
								<input type="button" style="width:40px" value="a" class="button" onclick="SetNewsFont('a', '')">
								</td>
								<td>
								<input type="button" style="width:40px" value="sup" class="button" onclick="SetNewsFont('sup', '')">
								</td>
								<td>
								<input type="button" style="width:40px" value="sub" class="button" onclick="SetNewsFont('sub', '')">
								</td>
								<td>
								<input type="button" style="width:40px" value="code" class="button" onclick="SetNewsFont('code', '')">
								</td>
								<td>
								<select name="face" onchange="SetNewsFont('face', this.form.face.options[this.form.face.selectedIndex].value); this.form.face.selectedIndex=0;" style="width:90">
									<option value="">{$font.select_fontface}</option>{include file="$admingentemplates/admin_options_face.tpl"}
								</select>&nbsp;
								</td>
								<td>
								<select name="size" onchange="SetNewsFont('size', this.form.size.options[this.form.size.selectedIndex].value); this.form.size.selectedIndex=0;" style="width:90">
									<option value="">{$font.select_fontsize}</option>{include file="$admingentemplates/admin_options_size.tpl"}
								</select>&nbsp;
								</td>
							</tr>
						</table>
						</td>
						<td class="main_content_text" align="left" width="50%" >&nbsp;</td>
                    </tr>
                    <tr bgcolor="#ffffff" valign="top">
                        <td align="right" width="17%" class="main_header_text">{$header.comment}:&nbsp;</td>
                        <td class="main_content_text" align="left"><div valign="bottom" id="view_area" style="display: {if $form.par eq 'edit'}{else}none{/if}; padding:5">{$data.comment}</div>
						<textarea  name="comment" rows="20" cols="80" onclick="MemADMINRange('taketour')" onkeydown="MemADMINRange('taketour')" style="display: {if $form.par eq 'edit'}none{else}{/if};">{$data.comment}</textarea></td>
						<td class="main_content_text" align="left" width="50%"  valign=bottom style="padding:5">
						<input type="button" id="edit_view" name="edit_view" value="{if $form.par eq 'edit'}{$header.edit}{else}{$header.view}{/if}" class="button" onclick="javascript: ViewTextArea();">
						<input type=hidden name=view_sel value="{if $form.par eq 'edit'}0{else}1{/if}"></td>
                    </tr>
                    </form>
            </table>
			<table><tr height="40">
			{if $form.par eq "edit"}
			<td><input type="button" value="{$button.save}" class="button" onclick="javascript:CheckChanges();"></td>
			<td><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$form.delete}'{literal}}{/literal}"></td>
			{else}
			<td><input type="button" value="{$button.add}" class="button" onclick="javascript:CheckChanges();"></td>
			{/if}
			<td><input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$form.back}'"></td>
			</tr></table>
	{literal}
	<script>

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
var theSelection = false;
var LAST_Range;
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;
var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));

var ADMIN_LAST_Range;

function  SetNewsFont(par, value){
	if(!ADMIN_LAST_Range){
		ADMIN_LAST_Range = document.forms['taketour'].comment.createTextRange();
	}
	if ((clientVer >= 4) && is_ie && is_win) {

		theSelection = document.selection.createRange();

		if(par == "b" || par == "i" || par == "u" || par == "sup" || par == "sub" || par == "code"){
			if(!theSelection.text){
				ADMIN_LAST_Range.text = ADMIN_LAST_Range.text + "<"+par+"></"+par+">";
			}else{
				theSelection.text ="<"+par+">" +theSelection.text+"</"+par+">";
			}
		}

		if(par == "br"){
			if(!theSelection.text){
				ADMIN_LAST_Range.text = ADMIN_LAST_Range.text + "<"+par+">";
			}else{
				theSelection.text ="<"+par+">";
			}
		}

		if(par == "a" ){
			if(!theSelection.text){
				ADMIN_LAST_Range.text = ADMIN_LAST_Range.text + "<"+par+" href=\"\"></"+par+">";
			}else{
				theSelection.text ="<"+par+" href=\"\">" +theSelection.text+"</"+par+">";
			}
		}
		if(par == "color" ){
			if(!theSelection.text){
				ADMIN_LAST_Range.text = ADMIN_LAST_Range.text + "<font color=\""+value+"\"></font>";
			}else{
				theSelection.text ="<font color=\""+value+"\">" +theSelection.text+"</font>";
			}
		}
		if(par == "size" ){
			if(!theSelection.text){
				ADMIN_LAST_Range.text = ADMIN_LAST_Range.text + "<font style=\"font-size:"+value+"pt\"></font>";
			}else{
				theSelection.text ="<font style=\"font-size:"+value+"pt\">" +theSelection.text+"</font>";
			}
		}
		if(par == "bgc" ){
			if(!theSelection.text){
				ADMIN_LAST_Range.text = ADMIN_LAST_Range.text + "<font style=\"background-color:"+value+"\"></font>";
			}else{
				theSelection.text ="<font style=\"background-color:"+value+"\">" +theSelection.text+"</font>";
			}
		}
		if(par == "face" ){
			if(!theSelection.text){
				ADMIN_LAST_Range.text = ADMIN_LAST_Range.text + "<font face=\""+value+"\"></font>";
			}else{
				theSelection.text ="<font face=\""+value+"\">" +theSelection.text+"</font>";
			}
		}

		document.forms['taketour'].comment.focus();
	}
}
function MemADMINRange(form){
	TagNameEl = event.srcElement.tagName;
///////////////////// refresh TEXT_Range on every click in text area
	if(TagNameEl == "TEXTAREA" && event.type=="click"){
		ADMIN_LAST_Range = document.forms[form].comment.createTextRange();
		ADMIN_LAST_Range.moveToPoint(event.clientX, event.clientY);
	}else if(TagNameEl == "TEXTAREA" && event.type=="keydown" && (event.keyCode == 37 || event.keyCode == 39 )){
		if(!ADMIN_LAST_Range){
			ADMIN_LAST_Range = document.forms[form].comment.createTextRange();
		}
		switch(event.keyCode){
			case 37 :  type = "character"; move = -1; break;
			case 39 :  type = "character"; move = 1; break;
		}
		ADMIN_LAST_Range.move(type, move);
	}
}

function ViewTextArea(){
	action = document.forms['taketour'].view_sel.value;	//// if action=1 - view else (=2) - edit
	if(action == 1){
		document.getElementById('edit_view').value = '{/literal}{$header.edit}{literal}';
		document.getElementById('view_area').innerHTML = document.forms['taketour'].comment.value;
		document.getElementById('view_area').style.display = '';
		document.forms['taketour'].comment.style.display = 'none';
		document.forms['taketour'].view_sel.value = 2;

	}else{
		document.getElementById('edit_view').value = '{/literal}{$header.view}{literal}';
		document.getElementById('view_area').innerHTML = '';
		document.getElementById('view_area').style.display = 'none';
		document.forms['taketour'].comment.style.display = '';
		document.forms['taketour'].view_sel.value = 1;
	}
}
function CheckChanges(){
	bp = document.forms['taketour'];
	if(bp.comment.value == ""){
		alert({/literal}"{$err.empty_comment}"{literal}); return false;
	}
	document.taketour.submit();
}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}