{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.upm_list}&nbsp;|&nbsp;{$lang.admin_menu.upm_quickmessages}</font><br><br><br>

<table><tr>
<td><input type="button" value="{$header.add_quickmessage}" class="button" onClick="javascript: location.href='{$form.add_link}'"></td>
</tr></table>
<br>

<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
<tr class="table_header">
  <th width="5%"><font class="main_header_text">{$header.number}</th>
  <th width="30%"><font class="main_header_text">{$header.title}</th>
  <th width="50%"><font class="main_header_text">{$header.body}</th>
  <th width="10%"><font class="main_header_text">{$header.created}</th>
  <th width="5%">&nbsp;</th>
</tr>

{section name=num loop=$messages}

<tr bgcolor="#FFFFFF">
  <td valign="top" align=center><font class="main_content_text">{$messages[num].number}</td>
  <td valign="top"><font class="main_content_text">{$messages[num].title}</td>
  <td valign="top"><font class="main_content_text">{$messages[num].body}</td>
  <td valign="top" align=center><font class="main_content_text">{$messages[num].created}</td>
  <td valign="top" align=center><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$header.delete_confirm}'{literal})){location.href={/literal}'{$messages[num].delete_link}'{literal}}{/literal}"></td>
</tr>

{sectionelse}

<tr bgcolor="#FFFFFF">
      <td align=center colspan="5"><font class="main_content_text">{$header.no_quickmessages}</td>
</tr>

{/section}

</table>

{include file="$admingentemplates/admin_bottom.tpl"}