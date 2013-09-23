{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.upm_list}&nbsp;|&nbsp;{$header.add_quickmessage}</font><br><br><br>

<form action="{$form.action}" name="form1" method="POST" style="margin:0px">
{$form.hiddens}
<table border=0 cellspacing=1 cellpadding=5>
<tr bgcolor=#FFFFFF>
	<td class="main_content_text" align="left" bgcolor="#FFFFFF" width="100px" valign="top">{$header.title} <font class=main_error_text>*</font></td>
	<td class="main_content_text"><input type="text" name="title" value="{$form.title}" size="70"></td>
</tr>
<tr bgcolor="#ffffff" valign="top">
        <td align="right" width="100px" class="main_header_text">&nbsp;</td>
        <td class="main_content_text" align="left">
		<table>
		<tr bgcolor="#ffffff" valign=center>
			<td>
			<input type="button" style="width:40px" value="{$tools.b}" class="button" onclick="SetNewsFont('b', '')">
			</td>
			<td>
			<input type="button" style="width:40px" value="{$tools.i}" class="button" onclick="SetNewsFont('i', '')">
			</td>
			<td>
			<input type="button" style="width:40px" value="{$tools.u}" class="button" onclick="SetNewsFont('u', '')">
			</td>
			<td>
			<input type="button" style="width:40px" value="{$tools.br}" class="button" onclick="SetNewsFont('br', '')">
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
			<input type="button" style="width:40px" value="{$tools.a}" class="button" onclick="SetNewsFont('a', '')">
			</td>
			<td>
			<input type="button" style="width:40px" value="{$tools.sup}" class="button" onclick="SetNewsFont('sup', '')">
			</td>
			<td>
			<input type="button" style="width:40px" value="{$tools.sub}" class="button" onclick="SetNewsFont('sub', '')">
			</td>
			<td>
			<input type="button" style="width:40px" value="{$tools.code}" class="button" onclick="SetNewsFont('code', '')">
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
</tr>
<tr bgcolor=#FFFFFF>
	<td class="main_content_text" align="left" bgcolor="#FFFFFF" width="100px" valign="top">{$header.body} <font class=main_error_text>*</font></td>
	<td class="main_content_text"><div valign="bottom" id="view_area" style="display: none; padding:5">{$form.body}</div>
		<textarea name="body" cols="70" rows="10" onclick="MemADMINRange('form1')" onkeydown="MemADMINRange('form1')">{$form.body}</textarea>
		<input id="edit_view" name="edit_view" type="button" value="{$button.view}" class="button" onclick="javascript: ViewTextArea();">		
		<input type=hidden name=view_sel value="1">
	</td>
</tr>
</table><br>
<table><tr>
<td><input type="button" value="{$button.save}" class="button" onClick="javascript: if (CheckForm()) document.form1.submit();"></td>
<td><input type="button" value="{$button.back}" class="button" onClick="javascript: location.href='{$form.back_link}'"></td>
</tr></table>

</form>

{literal}

<SCRIPT LANGUAGE="JavaScript">
<!-- //hide
function CheckForm()
{
  if (!document.forms[0].body.value)
     {
      {/literal} 
      alert('{$header.err.announcement_empty}');
      {literal} 
      return false;
     }
  return true;
}

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
		ADMIN_LAST_Range = document.forms['form1'].body.createTextRange();
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

		document.forms['form1'].body.focus();
	}
}
function MemADMINRange(form){
	TagNameEl = event.srcElement.tagName;
///////////////////// refresh TEXT_Range on every click in text area
	if(TagNameEl == "TEXTAREA" && event.type=="click"){
		ADMIN_LAST_Range = document.forms[form].body.createTextRange();
		ADMIN_LAST_Range.moveToPoint(event.clientX, event.clientY); 
	}else if(TagNameEl == "TEXTAREA" && event.type=="keydown" && (event.keyCode == 37 || event.keyCode == 39 )){
		if(!ADMIN_LAST_Range){
			ADMIN_LAST_Range = document.forms[form].body.createTextRange();
		}
		switch(event.keyCode){
			case 37 :  type = "character"; move = -1; break;
			case 39 :  type = "character"; move = 1; break;
		}
		ADMIN_LAST_Range.move(type, move); 
	}
} 

function ViewTextArea(){
	action = document.forms['form1'].view_sel.value;	//// if action=1 - view else (=2) - edit
	if(action == 1){
		document.getElementById('edit_view').value = '{/literal}{$button.edit}{literal}';
		document.all['view_area'].innerHTML = document.forms['form1'].body.value;
		document.all['view_area'].style.display = '';
		document.forms['form1'].body.style.display = 'none';
		document.forms['form1'].view_sel.value = 2;

	}else{
		document.getElementById('edit_view').value = '{/literal}{$button.view}{literal}';
		document.all['view_area'].innerHTML = '';
		document.all['view_area'].style.display = 'none';
		document.forms['form1'].body.style.display = '';
		document.forms['form1'].view_sel.value = 1;
	}
}
//-->
</SCRIPT>

{/literal}

{include file="$admingentemplates/admin_bottom.tpl"}
