{include file="$admingentemplates/admin_top.tpl"}
{strip}
<font class=red_header>{$header.name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.categories_list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.cards_categories_list}</div>
<div>
	<div style="padding-bottom: 30px;">
		<a href="{$form.add_link}"><b>{$header.add_category}</b></a>&nbsp;&nbsp;&nbsp;
		<a href="admin_cards.php?sel=settings_import">{$header.ecards_import}</a>
	</div>
	<div>
		<span>{$header.jump_to_category}:&nbsp;</span>
		<span>
			<select name="id_category" onchange="document.location.href='admin_cards.php?sel=subcategories&id_category='+this.value;">
				<option value="">{$header.please_select}</option>
				{foreach item=item from=$categories}
				<option value="{$item.id}">{$item.name}</option>
				{/foreach}
			</select>
		</span>
	</div>
	<div style="padding-top: 10px;">
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $categories}
		<tr bgcolor="#ffffff">
			<td colspan=4 class="main_content_text">
				{if $links}{$links}{/if}
			</td>
		</tr>
		<tr class="table_header">
			<td class="main_header_text" align="center" width="150">{$header.categories}</td>
			<td class="main_header_text" align="center"></td>
			<td class="main_header_text" align="center" width="120"></td>
			<td class="main_header_text" align="center" width="230">&nbsp;</td>
		</tr>
		{foreach item=item from=$categories}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center"><a href="admin_cards.php?sel=subcategories&id_category={$item.id}">{$item.name}</a></td>
			<td class="main_content_text" align="center">{$item.descr}</td>
			<td class="main_content_text" align="center">
				<a href="admin_cards.php?sel=subcategories&id_category={$item.id}">
				<img src="{$item.image}" height="100" width="100" style="border: 1px solid #cccccc;">
				</a>
			</td>
			<td class="main_content_text" align="center">
				<a href="admin_cards.php?sel=subcategories&id_category={$item.id}">{$header.subcategories}</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="{$item.editlink}">{$button.edit}</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="#" onclick="if(confirm('{$header.confirm}')){literal}{{/literal}document.location.href='{$item.deletelink}'{literal}}{/literal} else return false;">{$button.delete}</a>
			</td>
		</tr>
		{/foreach}
		<tr bgcolor="#ffffff">
			<td colspan=4 class="main_content_text">{$links}</td>
		</tr>
	{else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="4" bgcolor="#FFFFFF">{$header.empty_categories}</td>
		</tr>
	{/if}
	</table>
	</div>
</div>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}