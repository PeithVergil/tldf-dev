{strip}
{include file="$admingentemplates/admin_top.tpl"}
<span class="red_header">{$lang.giftshop.razdel_name}</span><span class="red_sub_header">&nbsp;|&nbsp;{$lang.giftshop.category_list}</span>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.gs_category}</div>
<table border="0" cellspacing="1" cellpadding="5" width="100%" class="table_main">
	{if $links}
		<tr bgcolor="#FFFFFF">
			<td colspan="6" height="20" align="left" class="main_content_text">{$links}</td>
		</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="20">{$lang.giftshop.number}</td>
		<td class="main_header_text" align="center">&nbsp;</td>
		<td class="main_header_text" align="center" width="150">{$lang.giftshop.name}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.comment}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.status}</td>
		<td class="main_header_text" align="center" width="100">&nbsp;</td>
	</tr>
	{foreach item=item from=$data}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center">{$item.number}</td>
			<td class="main_content_text" align="center">{if $item.thumb_icon_path}<img src="{$item.thumb_icon_path}" border="1">{else}&nbsp;{/if}</td>
			<td class="main_content_text" align="center"><a href="admin_giftshop.php?sel=catedit&amp;id={$item.id}&amp;page={$form.page}">{$item.name}</a></td>
			<td class="main_content_text" align="justify">{$item.comment_all}</td>
			<td class="main_content_text" align="center">{$item.status}</td>
			<td class="main_content_text" align="center">
				<table cellspacing="1" cellpadding="0">
					<tr>
						<td width="80"><input type="button" value="{$lang.button.items}" class="button" onclick="window.location.href='admin_giftshop.php?sel=items&amp;id_category={$item.id}';"></td>
						<td width="80"><input type="button" value="{$lang.button.delete}" class="button" onclick="if (confirm('{$lang.confirm.giftshop_category}')) window.location.href='admin_giftshop.php?sel=catdel&amp;id={$item.id}&amp;page={$form.page}';"></td>
					</tr>
				</table>
			</td>
		</tr>
	{foreachelse}
		<tr bgcolor="#FFFFFF">
			<td colspan="6" height="40" align="left" class="main_error_text">{$lang.giftshop.category_empty}</td>
		</tr>
	{/foreach}
	{if $links}
		<tr bgcolor="#FFFFFF">
			<td colspan="6" height="20" align="left" class="main_content_text">{$links}</td>
		</tr>
	{/if}
</table>
<br>
<input type="button" value="{$lang.giftshop.category_add}" class="button" onclick="window.location.href='admin_giftshop.php?sel=catadd&amp;page={$form.page}';">
{include file="$admingentemplates/admin_bottom.tpl"}
{/strip}