{include file="$admingentemplates/admin_top.tpl"}
<font class="red_header">{$header.razdel_name}</font><font class="red_sub_header">&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text">
<span class="help_title">{$lang.help}:</span>{$help.billing}
</div>
<table width="100%">
	<tr>
		<td height="30" align="left" bgcolor="#FFFFFF" class="main_content_text">
			{$letter_links}
		</td>
		<td height="30" align="right" bgcolor="#FFFFFF" class="main_content_text">
			<form name="search_form" action="admin_pays.php" method="get">
				<input type="hidden" name="sorter" value="{$form.sorter}">
				<input type="hidden" name="order" value="{$form.order}">
				<input type="hidden" name="letter" value="{$form.letter}">
				<table>
					<tr>
						<td class="main_content_text">
							<input type="text" name="search" value="{$form.search}">
						</td>
						<td class="main_content_text">
							<select name="search_type">
							{foreach item=type from=$types name=types}
							<option value="{$smarty.foreach.types.iteration}"{if $type.sel} selected="selected"{/if}>{$type.value}</option>
							{/foreach}
							</select>
						</td>
						<td class="main_content_text">
							<input type="button" value="{$lang.button.search}" class="button" onclick="javascript: document.search_form.submit();" name="search_submit">
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<br>
<table border="0" class="table_main" cellspacing="1" cellpadding="5" width="100%">
	{if $page_links}
		<tr bgcolor="#ffffff">
			<td colspan="11" height="20" align="left" class="main_content_text">{$page_links}</td>
		</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="10">{$header.number}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=1&amp;order={if $form.sorter == 1}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.id}</a>&nbsp;{if $form.sorter == 1}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=4&amp;order={if $form.sorter == 4}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.name}</a>&nbsp;{if $form.sorter == 4}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=6&amp;order={if $form.sorter == 6}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.login}</a>&nbsp;{if $form.sorter == 6}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=5&amp;order={if $form.sorter == 5}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.account}</a>&nbsp;{if $form.sorter == 5}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=7&amp;order={if $form.sorter == 7}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.membership}</a>&nbsp;{if $form.sorter == 7}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center">{$header.period_type}</td>
		<td class="main_header_text" align="center">{$header.period}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=2&amp;order={if $form.sorter == 2}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.present_group}</a>&nbsp;{if $form.sorter == 2}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=3&amp;order={if $form.sorter == 3}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.status}</a>&nbsp;{if $form.sorter == 3}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center" width="100">&nbsp;</td>
	</tr>
	{foreach item=user from=$user}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center">{$user.number}</td>
			<td class="main_content_text" align="center">{$user.id}</td>
			<td class="main_content_text" align="center">
				{if $user.system_user}{$user.name}{else}<a href="{$user.edit_link}">{$user.name}</a>{/if}
			</td>
			<td class="main_content_text" align="center">
				{if $user.system_user}{$user.login}{else}<a href="{$user.edit_link}">{$user.login}</a>{/if}
			</td>
			<td class="main_content_text" align="center">{$user.account}&nbsp;{$form.costunits}</td>
			<td class="main_content_text" align="right">
				{$user.membership_payments}&nbsp;{$form.costunits}<br>
				{$user.membership_payments_2}&nbsp;{$form.costunits_2}
			</td>
			<td class="main_content_text" align="center">{$user.period}</td>
			<td class="main_content_text" align="center">{$user.dates}</td>
			<td class="main_content_text" align="center">{$user.groups}</td>
			<td class="main_content_text" align="center">
				{if $user.status == 1}{$header.status_active}{else}{$header.status_inactive}{/if}
			</td>
			<td class="main_content_text" align="center" width="100">
				{if !$user.root_user}
					<input type="button" value="{$header.comunicate}" class="button" onclick="javascript:window.open('{$user.comunicate}','comunicate', 'height=800, resizable=yes, scrollbars=yes,width=600, menubar=no,status=no');">
				{/if}
			</td>
		</tr>
	{foreachelse}
		<tr height="40" bgcolor="#FFFFFF">
			<td class="main_error_text" align="left" colspan="11">{$header.empty}</td>
		</tr>
	{/foreach}
	{if $page_links}
		<tr bgcolor="#ffffff">
			<td colspan="11" height="20" align="left" class="main_content_text">{$page_links}</td>
		</tr>
	{/if}
</table>
{include file="$admingentemplates/admin_bottom.tpl"}