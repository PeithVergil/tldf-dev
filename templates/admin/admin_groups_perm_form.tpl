{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_perm}{$data.groupname}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.groups_perm}</div>
<form action="{$form.action}" method="post" name="permissions" enctype="multipart/form-data">
	<input type="hidden" name=sel value="permchange">
	{$form.hiddens}
	<table border="0" class="table_main" cellspacing="1" cellpadding="5" width="100%">
		<tr class="table_header">
			<td width="25%" class="main_header_text"align="center"><b>{$header.module}</b></td>
			<td width="35%" class="main_header_text" align="center"><b>{$header.module_comment}</b></td>
			<td width="20%" class="main_header_text" align="center"><b>{$header.module_allow}</b></td>
			<td width="20%" class="main_header_text" align="center"></td>
		</tr>
		{foreach item=item from=$perm name=p}
			<tr bgcolor="#ffffff">
				<td width="25%" align="center" class="main_header_text">
					<div id="module{$item.id}" style="font-weight:bold;">{$item.name|escape}</div>
				</td>
				<td width="35%" align="left" class="main_content_text" style="padding:3px;">{$item.comment|escape}</td>
				<td width="20%" align="center" class="main_header_text">
					<input type=checkbox name="perm_{$smarty.foreach.p.index}" id="perm_{$smarty.foreach.p.index}" value="{$item.id}" {if $item.checked == 1}checked="checked"{/if} />
				</td>
				<td width="20%" align="center" class="main_header_text"></td>
			</tr>
		{/foreach}
		{foreach item=item from=$add_perm name=a}
			<tr bgcolor="#ffffff">
				<td width="25%" align="center" class="main_header_text"><div><b>{$item.name|escape}</b></div></td>
				<td width="35%" align="left" class="main_content_text" style="padding:3px;">{$item.comment|escape}</td>
				<td width="20%" align="center" class="main_header_text">
					<input type="checkbox" name="add_perm_{$smarty.foreach.a.index}" id="add_perm_{$smarty.foreach.a.index}" value="{$item.id}" {if $item.active == 1}checked="checked"{/if} onclick="changeAddPerm({$smarty.foreach.a.index});">
				</td>
				<td width="20%" align="center" class="main_header_text">
					<input type="hidden" id="first_text_add_perm_{$smarty.foreach.a.index}" name="first_text_add_perm_{$smarty.foreach.a.index}" value="{$item.name}:&nbsp;" />
					<input type="hidden" id="second_text_add_perm_{$smarty.foreach.a.index}" name="second_text_add_perm_{$smarty.foreach.a.index}" value="&nbsp;{$item.value_name}" />
					<input type="text" name="count_add_perm_{$smarty.foreach.a.index}" id="count_add_perm_{$smarty.foreach.a.index}" value="{$item.value}" {if $item.active == 0}disabled="disabled"{/if} style="width:35px;" />
				</td>
			</tr>
		{/foreach}
	</table>
</form>
<table>
	<tr>
		<td height="40"><input type="button" value="{$button.save}" class="button" onclick="update_permissions();"></td>
		<td><input type="button" value="{$button.close}" class="button" onclick="window.close();opener.focus();"></td>
	</tr>
</table>
{literal}
<script type="text/javascript">
// if disable permiss_checkbox => disable demo_checkbox
var permiss = document.permissions;
for (i = 0; i < permiss.length; i++) {
	if (permiss[i].name.substr(0, 4) == 'perm') {
		id = permiss[i].name.substr(4);
	}
}

function changeAddPerm(id)
{
	if (document.getElementById('add_perm_' + id).checked == false) {
		document.getElementById('count_add_perm_' + id).disabled = true;
	} else {
		document.getElementById('count_add_perm_' + id).disabled = false;
	}
}

function update_permissions()
{
	var permiss = document.permissions;
	var perm_str = '';
	var module_str = '';
	var add_perm_str = '';
	
	for (i = 0; i < permiss.length; i++) {
		if (permiss[i].name.substr(0,5) == 'perm_') {
			if (permiss[i].checked == true) {
				perm_id = permiss[i].value;
				perm_str = perm_str + '<input type="hidden" name="perm[]" value="' + perm_id + '" />';
				var module_element = document.getElementById('module' + perm_id);
				module_str = module_str + module_element.innerHTML + '<br>';
			}
		}
		
		if (permiss[i].name.substr(0,9) == 'add_perm_') {
			if (permiss[i].checked == true) {
				perm_id = permiss[i].value;
				id = permiss[i].name.substr(9);
				perm_count = document.getElementById('count_add_perm_' + id).value;
				text_1 = document.getElementById('first_text_add_perm_' + id).value;
				text_2 = document.getElementById('second_text_add_perm_' + id).value;
				add_perm_str = add_perm_str + '<input type="hidden" name="add_perm[]" value="' + perm_id + '" />';
				add_perm_str = add_perm_str + '<input type="hidden" name="add_perm_count[]" value="' + perm_count + '" />';
				add_perm_str = add_perm_str + text_1 + perm_count + text_2 + '<br>';
			}
		}
	}
	
	var opener_modules_element = window.opener.document.getElementById('modules');
	opener_modules_element.innerHTML = module_str + perm_str;
	window.opener.document.getElementById('add_modules').innerHTML = add_perm_str;
	window.close();
	opener.focus();
}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}