{strip}
{include file="$admingentemplates/admin_top.tpl"}
<span class="red_header">{$lang.giftshop.razdel_name}</span><span class="red_sub_header">&nbsp;|&nbsp;{$lang.giftshop.item_list}</span>
<div class="help_text">
	<div class="help_title">{$lang.help}:</div>
	{$help.gs_item}
</div>
<span class="main_header_text"><b>{$lang.giftshop.category}:</b></span>&nbsp;
<select name="id_category" onchange="location.href='admin_giftshop.php?sel=items&amp;id_category='+this.value;">
	{foreach item=item from=$categories}
		<option value="{$item.id}" {if $parent.id == $item.id}selected="selected"{/if}>{$item.name}</option>
	{/foreach}
</select>
<br><br>
<div style="border:solid 1px #E6E6E6; background-color:#FFFFFF;">
	<table border="0" cellspacing="3" cellpadding="2" width="95%">
		<tr>
			{if $parent.icon_path}
				<td class="main_content_text" align="left" rowspan="2" width="10%"><img src="{$parent.icon_path}" border="1"></td>
			{/if}
			<td class="main_content_text" align="left" valign="top"><b>{$parent.name}</b></td>
		</tr>
		<tr>
			<td class="main_content_text" align="left" valign="top"><i>{$parent.comment}</i></td>
		</tr>
	</table>
</div>
<br>
<table border="0" cellspacing="1" cellpadding="5" width="100%" class="table_main">
	{if $links}
		<tr bgcolor="#FFFFFF">
			<td colspan="7" height="20" align="left" class="main_content_text">{$links}</td>
		</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="20">{$lang.giftshop.number}</td>
		<td class="main_header_text" align="center">&nbsp;</td>
		<td class="main_header_text" align="center" width="150">{$lang.giftshop.name}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.comment}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.price}</td>
		<td class="main_header_text" align="center">{$lang.giftshop.status}</td>
		<td class="main_header_text" align="center" width="100">&nbsp;</td>
	</tr>
	{foreach item=item from=$data}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center">{$item.number}</td>
			<td class="main_content_text" align="center">{if $item.thumb_icon_path}<img src="{$item.thumb_icon_path}" border="1">{else}&nbsp;{/if}</td>
			<td class="main_content_text" align="center"><a href="admin_giftshop.php?sel=itemsedit&amp;id={$item.id}&amp;id_category={$parent.id}&amp;page={$form.page}">{$item.name}</a></td>
			<td class="main_content_text" align="justify">{$item.comment_all}</td>
			<td class="main_content_text" align="center">{$item.price}&nbsp;{$form.curency}</td>
			<td class="main_content_text" align="center">{$item.status}</td>
			<td class="main_content_text" align="center"><input type="button" value="{$lang.button.delete}" class="button" onclick="if (confirm('{$lang.confirm.giftshop_item}')) window.location.href='admin_giftshop.php?sel=itemsdel&amp;id={$item.id}&amp;page={$form.page}'"></td>
		</tr>
	{foreachelse}
		<tr bgcolor="#FFFFFF">
			<td colspan="7" height="40" align="left" class="main_error_text">{$lang.giftshop.category_empty}</td>
		</tr>
	{/foreach}
	{if $links}
		<tr bgcolor="#FFFFFF">
			<td colspan="7" height="20" align="left" class="main_content_text">{$links}</td>
		</tr>
	{/if}
</table>
<br>
<input type="button" value="{$lang.giftshop.item_add}" class="button" onclick="window.location.href='admin_giftshop.php?sel=itemsadd&amp;id_category={$parent.id}&amp;page={$form.page}';">&nbsp;&nbsp;
<input type="button" value="{$lang.button.back}" class="button" onclick="window.location.href='admin_giftshop.php';">
{include file="$admingentemplates/admin_bottom.tpl"}
{/strip}