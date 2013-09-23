{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.area_how_works_edit}</div>
<form method="post" action="{$form.action}"  enctype="multipart/form-data" name="how_works">
	{$form.hiddens}
	<table border=0 cellspacing=1 cellpadding=5 width="100%">
		<tr>
			<td align="right" class="main_header_text">{$header.status}:</td>
			<td>
				<input type="checkbox" name="status" value="1" {if $data.status}checked{/if} >
			</td>
			<td>&nbsp;</td>
			<td><b>Thai Version</b></td>
		</tr>
		<tr>
			<td width="75" align="right" class="main_header_text">{$header.title}:</td>
			<td width="400" class="main_content_text" align="left">
				<input type="text" name="title" style="width:400px;" value="{$data.title}" >
			</td>
			<td width="70">&nbsp;</td>
			<td class="main_content_text" align="left">
				<input type="text" name="title_t" style="width:400px;" value="{$data.title_t}" >
			</td>
			<td width="50">&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" align="right" class="main_header_text">{$header.video}:</td>
			<td class="main_content_text" align="left">
				<textarea name="video" style="width:400px; height:130px;" >{$data.video}</textarea>
			</td>
			<td>&nbsp;</td>
			<td class="main_content_text" align="left">
				<textarea name="video_t" style="width:400px; height:130px;" >{$data.video_t}</textarea>
			</td>
		</tr>
		<tr valign="top">
			<td align="right"  class="main_header_text">{$header.description}:&nbsp;</td>
			<td class="main_content_text" align="left">
				<textarea name="description" rows="15" cols="70" style="width:400px; height:150px;">{$data.description}</textarea>
			</td>
			<td>&nbsp;</td>
			<td class="main_content_text" align="left">
				<textarea name="description_t" rows="15" cols="70" style="width:400px; height:150px;">{$data.description_t}</textarea>
			</td>
		</tr>
		{*<!--
		<tr valign="top">
			<td align="right" class="main_header_text">&nbsp;</td>
			<td class="main_content_text" align="left">
				<table>
					<tr valign=center>
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
					</tr>
					<tr>
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
							<select name="size" onchange="SetNewsFont('size', this.form.size.options[this.form.size.selectedIndex].value); this.form.size.selectedIndex=0;" style="width:90">
								<option value="">{$font.select_fontsize}</option>
								{include file="$admingentemplates/admin_options_size.tpl"}
							</select>
						</td>
					</tr>
				</table>
			</td>
			<td>&nbsp;</td>
			<td class="main_content_text" align="left">
				<table>
					<tr bgcolor="#ffffff" valign=center>
						<td>
							<input type="button" style="width:40px" value="b" class="button" onclick="SetNewsFontT('b', '')">
						</td>
						<td>
							<input type="button" style="width:40px" value="i" class="button" onclick="SetNewsFontT('i', '')">
						</td>
						<td>
							<input type="button" style="width:40px" value="u" class="button" onclick="SetNewsFontT('u', '')">
						</td>
						<td>
							<input type="button" style="width:40px" value="br" class="button" onclick="SetNewsFontT('br', '')">
						</td>
					</tr>
					<tr>
						<td>
							<input type="button" style="width:40px" value="a" class="button" onclick="SetNewsFontT('a', '')">
						</td>
						<td>
							<input type="button" style="width:40px" value="sup" class="button" onclick="SetNewsFontT('sup', '')">
						</td>
						<td>
							<input type="button" style="width:40px" value="sub" class="button" onclick="SetNewsFontT('sub', '')">
						</td>
						<td>
							<input type="button" style="width:40px" value="code" class="button" onclick="SetNewsFontT('code', '')">
						</td>
						<td>
							<select name="size" onchange="SetNewsFont('size', this.form.size.options[this.form.size.selectedIndex].value); this.form.size.selectedIndex=0;" style="width:90">
								<option value="">{$font.select_fontsize}</option>
								{include file="$admingentemplates/admin_options_size.tpl"}
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor="#ffffff" valign="top">
			<td align="right"  class="main_header_text">{$header.description}:&nbsp;</td>
			<td class="main_content_text" align="left">
				<div valign="bottom" id="view_area" style="display: {if $form.par eq 'edit'}{else}none{/if}; padding:5">{$data.description}</div>
				<textarea name="description" rows="15" cols="70" onclick="MemADMINRange('how_works')" onkeydown="MemADMINRange('how_works')" style="width:400px; height:150px; display: {if $form.par eq 'edit'}none{else}{/if};">{$data.description}</textarea>
			</td>
			<td class="main_content_text" align="left" valign=bottom>
				<input type="button" id="edit_view" name="edit_view" value="{if $form.par eq 'edit'}{$header.edit}{else}{$header.view}{/if}" class="button" onclick="javascript: ViewTextArea();">
				<input type=hidden name=view_sel value="{if $form.par eq 'edit'}0{else}1{/if}">
			</td>
		</tr>
		-->*}
		<tr height="40">
			<td colspan="2">
				{if $form.par eq "edit"}
					<input type="button" value="{$button.save}" class="button" onclick="javascript:CheckChanges();">
					&nbsp;
					<input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$form.delete}'{literal}}{/literal}">
				{else}
					<input type="button" value="{$button.add}" class="button" onclick="javascript:CheckChanges();">
					&nbsp;
				{/if}
				<input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$form.back}'">
			</td>
		</tr>
	</table>
</form>
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
		ADMIN_LAST_Range = document.forms['how_works'].description.createTextRange();
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

		document.forms['how_works'].description.focus();
	}
}

function MemADMINRange(form){
	TagNameEl = event.srcElement.tagName;
///////////////////// refresh TEXT_Range on every click in text area
	if(TagNameEl == "TEXTAREA" && event.type=="click"){
		ADMIN_LAST_Range = document.forms[form].description.createTextRange();
		ADMIN_LAST_Range.moveToPoint(event.clientX, event.clientY);
	}else if(TagNameEl == "TEXTAREA" && event.type=="keydown" && (event.keyCode == 37 || event.keyCode == 39 )){
		if(!ADMIN_LAST_Range){
			ADMIN_LAST_Range = document.forms[form].description.createTextRange();
		}
		switch(event.keyCode){
			case 37 :  type = "character"; move = -1; break;
			case 39 :  type = "character"; move = 1; break;
		}
		ADMIN_LAST_Range.move(type, move);
	}
}

function ViewTextArea(){
	action = document.forms['how_works'].view_sel.value;	//// if action=1 - view else (=2) - edit
	if(action == 1){
		document.getElementById('edit_view').value = '{/literal}{$header.edit}{literal}';
		document.getElementById('view_area').innerHTML = document.forms['how_works'].description.value;
		document.getElementById('view_area').style.display = '';
		document.forms['how_works'].description.style.display = 'none';
		document.forms['how_works'].view_sel.value = 2;

	}else{
		document.getElementById('edit_view').value = '{/literal}{$header.view}{literal}';
		document.getElementById('view_area').innerHTML = '';
		document.getElementById('view_area').style.display = 'none';
		document.forms['how_works'].description.style.display = '';
		document.forms['how_works'].view_sel.value = 1;
	}
}
function CheckChanges(){
	bp = document.forms['how_works'];
	if(bp.title.value == "" || bp.title_t.value == ""){
		alert({/literal}"{$err.empty_title}"{literal}); return false;
	}
	if(bp.description.value == "" || bp.description_t.value == ""){
		alert({/literal}"{$err.empty_description}"{literal}); return false;
	}
	document.how_works.submit();
}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}