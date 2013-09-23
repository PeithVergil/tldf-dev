{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform}</font>
<div class="help_text">
	<span class="help_title">{$lang.help}:</span>{if $form.par eq "edit"}{$help.groups_edit}{else}{$help.groups_add}{/if}
</div>
<form action="{$form.action}" method="post" name="groups" enctype="multipart/form-data">
	{$form.hiddens}
	<table border=0 cellspacing=1 cellpadding=5 width="100%">
		<tr bgcolor="#ffffff">
			<td align="right" width="15%" class="main_header_text">{$header.name}:&nbsp;</td>
			<td class="main_content_text" align="left"><input type="text" name="name" value="{$data.name}" size="30" {if $data.type=='r' ||  $data.type=='g'}disabled{/if}></td>
			<td class="main_header_text" align="left" width="70%">&nbsp;<input type="checkbox"name="add_default" value="1" {if $data.type=='r' ||  $data.type=='g' || $data.add_default_disable}disabled{/if}{if $data.add_default} checked{/if}>&nbsp;{$header.add_default}</td>
		</tr>
		<tr bgcolor="#ffffff">
			<td align="right" width="15%" class="main_header_text">{$header.permission}:&nbsp;</td>
			<td class="main_content_text" align="left" >
				<div id="modules"{* name="perm_modules" *}>{$data.modulestr}</div>
				<div id="add_modules"{* name="add_modules" *}>{$data.add_perm_str}</div>
			</td>
			<td align="left" width="70%">
				{if $data.type ne 'r'}
					<input type="button" value="{$button.perm}" class="button" onclick="window.open('{$data.permlink}', 'permissions', 'height=650,resizable=yes,scrollbars=yes,width=650,menubar=no,status=yes');" name=perm>
				{/if}
			</td>
		</tr>
	</table>
</form>
<table>
	<tr>
		{if $form.par eq "edit"}
			{if $data.type ne 'r'}
				<td height="40"><input type="button" value="{$button.save}" class="button" onclick="document.groups.submit();"></td>
			{/if}
			{if $data.type ne 'r' && $data.type ne 'd' &&  $data.type ne 'g' &&  $data.type ne 't' && $form.use_gender_membership ne 1}
				<td height="40"><input type="button" value="{$button.delete}" class="button" onclick="if(confirm('{$form.confirm}')) location.href='{$form.delete}';"></td>
			{/if}
		{else}
			{if $data.type ne 'r' &&  $data.type ne 'g' &&  $data.type ne 't'}
				<td height="40"><input type="button" value="{$button.add}" class="button" onclick="document.groups.submit();"></td>
			{/if}
		{/if}
		<td height="40">
			<input type="button" value="{$button.back}" class="button" onclick="location.href='{$form.back}'">
		</td>
	</tr>
</table>
{include file="$admingentemplates/admin_bottom.tpl"}