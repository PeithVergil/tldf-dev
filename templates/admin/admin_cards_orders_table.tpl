{include file="$admingentemplates/admin_top.tpl"}
<link href="{$site_root}/javascript/greybox/greybox.css" rel="stylesheet" type="text/css" media="all">
<script type="text/javascript" src="{$site_root}/javascript/greybox/AmiJS.js?v=0000"></script>
<script type="text/javascript" src="{$site_root}/javascript/greybox/greybox.js?v=0000"></script>
<script language="JavaScript" type="text/javascript">
	var GB_IMG_DIR = "{$site_root}/javascript/greybox/";
</script>
{strip}
<font class=red_header>{$header.name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.orders_list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.orders_list}</div>
<div>
	<div style="padding-top: 10px;">
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $orders}
		<tr bgcolor="#ffffff">
			<td colspan=6 class="main_content_text" >
				<div style="float: left;">{if $links}{$links}{/if}</div>
			</td>
		</tr>
		<tr class="table_header">
			<td class="main_header_text" align="center">{$header.order.card_header}</td>
			<td class="main_header_text" align="center" width="50">{$header.order.card_price}</td>
			<td class="main_header_text" align="center" width="100">{$header.order.sender}</td>
			<td class="main_header_text" align="center" width="100">{$header.order.recipient}</td>
			<td class="main_header_text" align="center" width="120">{$header.order.card_image}</td>
			<td class="main_header_text" align="center" width="200">{$header.order.order_status}</td>
		</tr>
		{foreach item=item from=$orders}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center">{$item.card_header}</td>
			<td class="main_content_text" align="center">{$item.card_price}</td>
			<td class="main_content_text" align="center"><a href="admin_users.php?sel=edit&id={$item.id_sender}" target="_blank">{$item.sender_login}</a></td>
			<td class="main_content_text" align="center"><a href="admin_users.php?sel=edit&id={$item.id_user_to}" target="_blank">{$item.user_to_login}</a></td>
			<td class="main_content_text" align="center">
				<a href="#" onclick="return GB_showImage('{$item.name_unslashed}', '{$item.card_image_big}')">
					<img src="{$item.card_image_thumb}" height="100" width="100" style="border: 1px solid #cccccc;">
				</a>
			</td>
			<td class="main_content_text" align="center">
				{$item.status_lang}
			</td>
		</tr>
		{/foreach}
		{if $links}
		<tr bgcolor="#ffffff">
			<td colspan=6 class="main_content_text">{$links}</td>
		</tr>
		{/if}
	{else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="5" bgcolor="#FFFFFF">{$header.no_orders}</td>
		</tr>
	{/if}
	</table>
	</div>
</div>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}