{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.edit}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.moderators_edit}</div>
<div>
	<form method="post" action="admin_moderators.php?sel=save" style="margin: 0px; padding: 0px;">
	<input type="hidden" value="{$form.par}" name="par">
	{if $form.par eq 'edit'}
	<input type="hidden" value="{$data.id}" name="id">
	{/if}
	<div>
	<table cellspacing=0 cellpadding=5>
	<tr>
		<td width="120" class="main_header_text">{$header.login}:&nbsp;</td>
		<td><input type="text" name="login" value="{$data.login}" style="width: 200px;"></td>
	</tr>
	<tr>
		<td class="main_header_text">{$header.email}:&nbsp;</td>
		<td><input type="text" name="email" value="{$data.email}" style="width: 200px;"></td>
	</tr>
	<tr>
		<td class="main_header_text">{$header.fname}:&nbsp;</td>
		<td><input type="text" name="fname" value="{$data.fname}" style="width: 200px;"></td>
	</tr>
	<tr>
		<td class="main_header_text">{$header.sname}:&nbsp;</td>
		<td><input type="text" name="sname" value="{$data.sname}" style="width: 200px;"></td>
	</tr>
	<tr>
		<td class="main_header_text">{$header.status}:&nbsp;</td>
		<td><input type="checkbox" name="status" value="1" {if $data.status eq 1 || !$data.status}checked{/if}></td>
	</tr>
	<tr>
		<td class="main_header_text">{if $form.par eq 'edit'}{$header.new_password}{else}{$header.password}{/if}:&nbsp;</td>
		<td><input type="password" name="pass" value=""></td>
	</tr>
	</table>
	</div>
	<div style="padding-top: 10px; padding-left: 5px;"><font class="main_header_text">{$header.permissions}</font></div>
	<div style="padding-top: 5px; padding-left: 5px;">
	<table border=0 class="table_main" cellspacing=1 cellpadding=5>
	{foreach item=item from=$rights}
		<tr bgcolor="#ffffff">
			<td width="120">{$item.name}</td>
			<td width="20"><input type="checkbox" name="rights[]" value="{$item.id}" {if $item.sel eq 1} checked {/if}></td>
			<td>{$item.comment}</td>
		</tr>
	{/foreach}
	</table>
	</div>
	<div style="padding-top: 5px; padding-left: 5px;">
		<input type="submit" value="{$header.save}" class="button">&nbsp;{if $form.par eq 'edit'}<input type="button" value="{$header.delete}" class="button" onclick="document.location.href='admin_moderators.php?sel=delete&id={$data.id}'">{/if}
	</div>
	</form>
</div>
<div style="padding-top: 10px;">
	<a href="admin_moderators.php?sel=list" title="">{$header.back_to_moderators_list}</a>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}