{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.upm_list}&nbsp;|&nbsp;{$lang.admin_menu.upm_connections}</font><br><br><br>

<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
{if $links}
<tr bgcolor="#ffffff">
  <td height="20" colspan=3 align="left" class="main_content_text">{$links}</td>
</tr>
{/if}
<tr class="table_header">
  <th width="5%"><font class="main_header_text">{$header.number}</th>
  <th width="65%"><font class="main_header_link"><div onclick="javascript:location.href='{$form.action}?sorter=user';">{$header.user}</div></th>
  <th width="30%"><font class="main_header_link"><div onclick="javascript:location.href='{$form.action}?sorter=connect';">{$header.connected}</div></th>
</tr>

{section name=num loop=$conns}

<tr bgcolor="#FFFFFF">
  <td valign="top" align=center><font class="main_content_text">{$conns[num].number}</td>
  <td valign="top" align=center><font class="main_content_text">{$conns[num].user}</td>
  <td valign="top" align=center><font class="main_content_text">{$conns[num].connected}</td>
</tr>

{sectionelse}

<tr bgcolor="#FFFFFF">
      <td align=center colspan="3"><font class="main_content_text">{$header.no_connections}</td>
</tr>

{/section}

{if $links}
<tr bgcolor="#ffffff">
  <td height="20" colspan=3 align="left" class="main_content_text">{$links}</td>
</tr>
{/if}

</table>

{include file="$admingentemplates/admin_bottom.tpl"}
