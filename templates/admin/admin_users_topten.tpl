{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.topten}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.users_top}</div>
<table width="100%">
	<tr valign=top>
	<!-- main spr row -->
	<td width="100%" colspan=2>
		<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
			{if $links}
			<tr bgcolor="#ffffff">
				<td height="35" colspan=5 align="left"  class="main_content_text" >{$links}</td>
			</tr>
			{/if}
			<tr>
				<td align="center" class="table_header" width="15"><font class="main_header_text"><b>{$header.topten_place}</b></font></td>
				<td align="center" class="table_header"><font class="main_header_text"><b>{$header.topten_login}</b></font></td>
				<td align="center" class="table_header"><font class="main_header_text"><b>{$header.topten_name}</b></font></td>
				<td align="center" class="table_header" width="20"><font class="main_header_text"><b>{$header.topten_rating}</b></font></td>
				<td align="center" class="table_header" width="70"><font class="main_header_text"><b>{$header.topten_rated}</b></font></td>
			</tr>
			{section name=s loop=$users}
			<tr>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$users[s].place}</td>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF"><a href="admin_users.php?sel=edit&id={$users[s].id}">{$users[s].login}</a></td>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$users[s].name}</td>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$users[s].rating}</td>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$users[s].rated}</td>
			</tr>
			{/section}
			{if $links}
			<tr bgcolor="#ffffff">
				<td height="35" colspan=5 align="left"  class="main_content_text" >{$links}</td>
			</tr>
			{/if}
		</table>
	</td>
	<!-- /main spr row -->
	</tr>
</table>
{include file="$admingentemplates/admin_bottom.tpl"}