{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.ref_countries}</div>

					<table  cellSpacing="3" cellPadding="0" >
						<form method="post" action="{$form.action}"  enctype="multipart/form-data" name=add_form>
						{$form.hiddens}
							<tr>
								<td align="right" class="main_header_text">
								&nbsp;{$header.name}:&nbsp;
								</td>
								<td class="main_content_text" align="left"><input type="text" name="name" value="{$name}" size=30></td>
								<td class="main_content_text" align="left"><input type="button" value="{$header.add}" class="button" onclick="javascript: document.add_form.submit();"></td>
							</tr>
							</form>
					</table>
		<!-- /form -->
<br>
				<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=3 align="left"  class="main_content_text" >{$links}</td>
					</tr>
					{/if}
							<tr class="table_header">
								<td class="main_header_text" align="center" width="5%">{$header.number}</td>
								<td class="main_header_text" align="center" width="85%">{$header.country}</td>
								<td class="main_header_text" align="center" width="10%">&nbsp;</td>
							</tr>
							{if $countries}
							{section name=spr loop=$countries}
							<tr bgcolor="#FFFFFF">
								<td class="main_content_text" align="center">{$countries[spr].number}</td>
								<td class="main_content_text" align="center">
									<div id="inform_div{$countries[spr].id}" class="error_msg" style="display:none"></div>
									<a id="link{$countries[spr].id}" href="{$countries[spr].editlink}">{$countries[spr].name}</a>
									<input id="text_input{$countries[spr].id}" type="text" value="{$countries[spr].name}" style="display:none" onkeypress="javscript: if(event.keyCode == 13) saveAction({$countries[spr].id}); "/>
								</td>
								<td class="main_content_text" align="center">
									<input id="edit_button{$countries[spr].id}" type="button" value="{$lang.button.edit}" onclick="javascript: editAction({$countries[spr].id});" class="button" />
									<input id="save_button{$countries[spr].id}" type="button" style="display:none;" value="{$lang.button.save}" onclick="javascript: saveAction({$countries[spr].id});" class="button" />
									<input type="button" value="{$button.delete}" class="button" onclick="javascript: if(confirm('{$form.confirm}')){literal}{{/literal}location.href='{$countries[spr].deletelink}'{literal}}{/literal}"></td>
							</tr>
							{/section}
							{else}
							<tr height="40">
								<td class="main_error_text" align="left" colspan="4" bgcolor="#FFFFFF">{$header.empty}</td>
							</tr>
							{/if}
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=3 align="left"  class="main_content_text" >{$links}</td>
					</tr>
					{/if}
					</table>
<script type="text/javascript">
{literal}
function editAction(id){
	link_obj = getById('link'+id);
	text_input_obj = getById('text_input'+id);
	edit_button_ogj = getById('edit_button'+id);
	save_button_ogj = getById('save_button'+id);
	
	link_obj.style.display = 'none';
	text_input_obj.style.display = '';
	edit_button_ogj.style.display = 'none';
	save_button_ogj.style.display = '';

	text_input_obj.focus();
}

function saveAction(id){
	inform_div_obj = getById('inform_div'+id);
	link_obj = getById('link'+id);
	text_input_obj = getById('text_input'+id);
	edit_button_ogj = getById('edit_button'+id);
	save_button_ogj = getById('save_button'+id);
	value = text_input_obj.value;
	
	file_name = '{/literal}{$form.action}{literal}';
	str = 'sel=rename_country&id='+id+'&value='+value;
	destination_odj = inform_div_obj;
	tmp_text = 'Saving...';
	anisochronous = false;
	
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	
	inform_div_obj.style.display = '';
	
	saved_obj = getById('saved'+id);
	saved = saved_obj.value;
	if (saved == '1'){
		link_obj.style.display = '';
		link_obj.innerHTML = value;
		text_input_obj.style.display = 'none';
		edit_button_ogj.style.display = '';
		save_button_ogj.style.display = 'none';
		edit_button_ogj.focus();
	}
}

function getById(name){
	return document.getElementById(name);
}
{/literal}
</script>
{include file="$admingentemplates/admin_bottom.tpl"}