{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.orders_list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.gs_orders}</div>

				<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=9 align="left" class="main_content_text" >{$links}</td>
					</tr>
					{/if}
							<tr class="table_header">
								<td class="main_header_text" align="center" width="20">{$header.id}</td>
								<td class="main_header_text" align="center" width="70">{$header.date_order}</td>
								<td class="main_header_text" align="center">{$header.from}</td>
								<td class="main_header_text" align="center">{$header.to}</td>
								<td class="main_header_text" align="center">{$header.order}</td>
								<td class="main_header_text" align="center">{$header.total}</td>
								<td class="main_header_text" align="center">{$header.status}</td>
								<td class="main_header_text" align="center">{$header.delivery}</td>
								<td class="main_header_text" align="center">&nbsp;</td>
							</tr>
							{if $items}
							{section name=spr loop=$items}
							<tr bgcolor="#FFFFFF">
								<td class="main_content_text" align="center">{$items[spr].id}</td>
								<td class="main_content_text" align="center">{$items[spr].date_order}</td>
								<td class="main_content_text" align="center">{$items[spr].user_from.login}</td>
								<td class="main_content_text" align="center">{$items[spr].user_to.login}</td>
								<td class="main_content_text" align="left">{$items[spr].order}</td>
								<td class="main_content_text" align="center">{$items[spr].total} {$form.curency}</td>
								<td class="main_content_text" align="center">{$items[spr].status}</td>
								<td class="main_content_text" align="center">{$items[spr].delivery_status}</td>
								<td class="main_content_text" align="center">
								{if $items[spr].status eq '-'}
								<a href="./{$form.file_name}?sel=orders_status&id={$items[spr].id}&page={$form.page}">{$button.status}</a>&nbsp;
								{elseif $items[spr].delivery_status eq '-'}
								<a href="./{$form.file_name}?sel=orders_delivery&id={$items[spr].id}&page={$form.page}">{$header.delivery}</a>&nbsp;
								{/if}
								<a href="./{$form.file_name}?sel=orders_delete&id={$items[spr].id}&page={$form.page}">{$button.delete}</a>&nbsp;
								</td>
							</tr>
							{/section}
							{else}
							<tr height="40">
								<td class="main_error_text" align="left" colspan="8" bgcolor="#FFFFFF">{$header.category_empty}</td>
							</tr>
							{/if}
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=9 align="left"  class="main_content_text" >{$links}</td>
					</tr>
					{/if}
					</table>
	<!-- /main spr row -->
{include file="$admingentemplates/admin_bottom.tpl"}