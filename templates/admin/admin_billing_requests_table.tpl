{include file="$admingentemplates/admin_top.tpl"}
{strip}
<font class="red_header">{$header.razdel_name}</font><font class="red_sub_header">&nbsp;|&nbsp;{$header.requests_list}</font>
<div class="help_text">
<span class="help_title">{$lang.help}:</span>{$help.billing_requests}
</div>
<table width="100%">
	<tr>
		<td height="30" align="left" bgcolor="#FFFFFF" class="main_content_text">
			{$letter_links}
		</td>
		<td height="30" align="right" bgcolor="#FFFFFF" class="main_content_text">
			<form name="search_form" action="admin_pays.php" method="get">
				<input type="hidden" name="sel" value="billing_requests">
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
			<td colspan="12" height="20" align="left" class="main_content_text">{$page_links}</td>
		</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="10">{$header.number}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=2&amp;order={if $form.sorter == 2}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.name}</a>&nbsp;{if $form.sorter == 2}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=3&amp;order={if $form.sorter == 3}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.login}</a>&nbsp;{if $form.sorter == 3}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=9&amp;order={if $form.sorter == 9}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.old_group}</a>&nbsp;{if $form.sorter == 9}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=4&amp;order={if $form.sorter == 4}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.new_group}</a>&nbsp;{if $form.sorter == 4}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center">{$header.new_period}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=5&amp;order={if $form.sorter == 5}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.amount}</a>&nbsp;{if $form.sorter == 5}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=7&amp;order={if $form.sorter == 7}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.status}</a>&nbsp;{if $form.sorter == 7}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=8&amp;order={if $form.sorter == 8}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.payment_system}</a>&nbsp;{if $form.sorter == 8}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=6&amp;order={if $form.sorter == 6}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.date_send}</a>&nbsp;{if $form.sorter == 6}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center"><a href="{$sort_link}&amp;sorter=1&amp;order={if $form.sorter == 1}{$form.new_order}{else}{$form.order}{/if}" style="color:#000;">{$header.id}</a>&nbsp;{if $form.sorter == 1}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center" width="100">&nbsp;</td>
	</tr>
	{foreach item=item from=$data}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center">{$item.number}</td>
			<td class="main_content_text" align="center"><a href="admin_users.php?sel=edit&amp;id={$item.id_user}">{$item.user_fullname}</a></td>
			<td class="main_content_text" align="center"><a href="admin_users.php?sel=edit&amp;id={$item.id_user}">{$item.login}</a></td>
			<td class="main_content_text" align="center">{$item.old_group_name}</td>
			<td class="main_content_text" align="center">{$item.new_group_name}</td>
			<td class="main_content_text" align="center">{$item.new_period}</td>
			<td class="main_content_text" align="center">{$item.amount}&nbsp;{$item.currency}</td>
			<td class="main_content_text" align="center">{$item.status}</td>
			<td class="main_content_text" align="center">{$item.paysystem}<br>{$item.info}</td>
			<td class="main_content_text" align="center">{$item.date_send}</td>
			<td class="main_content_text" align="center">{$item.id}</td>
			<td class="main_content_text" align="center" width="100">
				<input type="button" value="{$header.comunicate}" class="button" onclick="window.open('{$item.comunicate_href}', 'comunicate', 'height=800, resizable=yes, scrollbars=yes,width=600, menubar=no,status=no');">
				{if $item.status == 'send'}
					<input type="button" value="{$header.approve}" class="button" onclick="window.location.href='{$item.approve_href}';">
				{/if}
			</td>
		</tr>
	{foreachelse}
		<tr height="40" bgcolor="#FFFFFF">
			<td class="main_error_text" align="left" colspan="12">No billing requests found</td>
		</tr>
	{/foreach}
	{if $page_links}
		<tr bgcolor="#ffffff">
			<td colspan="12" height="20" align="left" class="main_content_text">{$page_links}</td>
		</tr>
	{/if}
</table>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}