{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.list}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.moderators}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.chat_moderators}</div>
	<table border=0 cellspacing=1 cellpadding=3 width="100%">
	<tr height="40" bgcolor="#FFFFFF">
        <td height="30"  align="left" bgcolor="#FFFFFF" class="main_content_text" >
		<table><tr>
		<form name="search_form" action="{$form.action}" method="post">
		<input type="hidden" name=sel value="">
		<td class="main_content_text" >&nbsp;<B>{$lang.button.add}:</B>&nbsp;</td>
		<td class="main_content_text" ><input type="text" name="search" value="{$search}"></td>
		<td class="main_content_text" >
		<select name="s_type" style="">
			{section name=s loop=$types}
			<option value="{$smarty.section.s.index_next}" {if $types[s].sel}selected{/if}>{$types[s].value}</option>
			{/section}
		</select></td>
		<td class="main_content_text"><input type="button" onclick="javascript: document.search_form.sel.value='search'; document.search_form.submit()" name="search_submit" value="{$lang.button.search}"></td>
		{if $search_select}
		<td class="main_content_text" ><select name="search_select" style="">
		{section name=s loop=$search_select}
		<option value="{$search_select[s].id}">{$search_select[s].name}</option>
		{/section}
		</select></td>
		<td class="main_content_text" ><input type="button" onclick="javascript: document.search_form.sel.value='add'; document.search_form.submit();" name="add_submit" value="{$lang.button.add}"></td>
		{/if}
		</form>
		</tr></table>
        </td>
	</tr>
	</table>
	<form id="modlist" name="modlist" action="{$form.action}" method="post" enctype="multipart/form-data">
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $mods}
		<tr class="table_header">
			<th width="20%" class="main_header_text" align="center">{$header.nick}</th>
			<th width="50%" class="main_header_text" align="center">{$header.name}</th>
			<th width="10%" class="main_header_text" align="center">{$header.created}</th>
			<th width="10%" class="main_header_text" align="center">{$header.status}</th>
			<th width="10%" class="main_header_text" align="center">{$header.delete_small}</th>
		</tr>
		{foreach from=$mods item=mod}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center">{$mod.login}</td>
			<td class="main_content_text" align="center">{$mod.name}</td>
			<td class="main_content_text" align="center">{$mod.date}</td>
			<td class="main_content_text" align="center"><input type="checkbox" name="{$mod.status_name}" {if $mod.status}checked{/if} {if $mod.root_user}disabled{/if}></td>
			<td class="main_content_text" align="center"><input type="checkbox" name="{$mod.delete_name}" {if $mod.root_user}disabled{/if}></td>
			<input type="hidden" name="{$mod.id_name}" value="{$mod.id}">
		</tr>
		{/foreach}
	</table>
	<table>
	<tr height="40">
		<td><input type="hidden" name=sel value="submit_all"><input type="button" onclick="javascript: document.modlist.submit();" value="{$header.submit_all_btn}"></td>
	</tr>
{else}
	<tr height="40" bgcolor="#FFFFFF">
		<td class="main_error_text" align="left" colspan="4">{$header.err_no_moderators_found}</td>
	</tr>
{/if}
	</table>
	<br><br>
	</form>
{include file="$admingentemplates/admin_bottom.tpl"}