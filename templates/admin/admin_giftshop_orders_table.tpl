{strip}
{include file="$admingentemplates/admin_top.tpl"}
<span class="red_header">{$lang.giftshop.razdel_name}</span><span class="red_sub_header">&nbsp;|&nbsp;{$lang.giftshop.orders_list}</span>
<div class="help_text">
	<div class="help_title">{$lang.help}:</div>
	{$help.gs_orders}
</div>
<table border="0" class="table_main" cellspacing="1" cellpadding="5" width="100%">
	{if $links}
		<tr bgcolor="#ffffff">
			<td height="20" colspan="11" align="left" class="main_content_text">{$links}</td>
		</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="20">{$lang.giftshop.id}</td>
		<td class="main_header_text" align="center" width="70">{$lang.giftshop.date_order}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.from}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.to}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.order}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.total}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.paid}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.procured}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.shipped}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.delivery}</td>
		<td class="main_header_text" align="center">&nbsp;</td>
	</tr>
	{foreach item=item from=$items}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center">{$item.id}</td>
			<td class="main_content_text" align="center">{$item.date_order}</td>
			<td class="main_content_text" align="center">{$item.user_from.login}</td>
			<td class="main_content_text" align="center">{$item.user_to.login}</td>
			<td class="main_content_text" align="left">{$item.order}</td>
			<td class="main_content_text" align="center" nowrap="nowrap">{$item.total} {$form.curency}</td>
			<td class="main_content_text" align="center">{$item.paid_status}</td>
			<td class="main_content_text" align="center">{$item.procured_status}</td>
			<td class="main_content_text" align="center">{$item.shipped_status}</td>
			<td class="main_content_text" align="center">{$item.delivery_status}</td>
			<td class="main_content_text" align="center">
				{* <!--
				{if $item.paid_status == '-'}
					<a href="admin_giftshop.php?sel=orders_status&amp;id={$item.id}&amp;page={$form.page}">{$lang.button.status}</a>&nbsp;
				{elseif $item.delivery_status == '-'}
				--> *}
				{if $item.paid_status == '+' && $item.procured_status == '-'}
					<a href="admin_giftshop.php?sel=orders_procured&amp;id={$item.id}&amp;page={$form.page}">{$lang.giftshop.procured}</a>&nbsp;
				{else}
					{$lang.giftshop.procured}&nbsp;
				{/if}
				{if $item.paid_status == '+' && $item.procured_status == '+' && $item.shipped_status == '-'}
					<a href="admin_giftshop.php?sel=orders_shipped&amp;id={$item.id}&amp;page={$form.page}">{$lang.giftshop.shipped}</a>&nbsp;
				{else}
					{$lang.giftshop.shipped}&nbsp;
				{/if}
				{if $item.paid_status == '+' && $item.procured_status == '+' && $item.shipped_status == '+' && $item.delivery_status == '-'}
					<a href="admin_giftshop.php?sel=orders_delivery&amp;id={$item.id}&amp;page={$form.page}">{$lang.giftshop.delivery}</a>
				{else}
					{$lang.giftshop.delivery}
				{/if}
				&nbsp;<a href="admin_giftshop.php?sel=orders_delete&amp;id={$item.id}&amp;page={$form.page}">{$lang.button.delete}</a>
			</td>
		</tr>
	{foreachelse}
		<tr>
			<td height="40" colspan="11" class="main_error_text" align="left" bgcolor="#FFFFFF">{$lang.giftshop.order_list_empty}</td>
		</tr>
	{/foreach}
	{if $links}
		<tr bgcolor="#ffffff">
			<td height="20" colspan="11" align="left" class="main_content_text">{$links}</td>
		</tr>
	{/if}
</table>
{include file="$admingentemplates/admin_bottom.tpl"}
{/strip}