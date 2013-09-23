{include file="$admingentemplates/admin_top.tpl"}
{strip}
<font class=red_header>{$header.name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.subcategories_list}{$header.in}{$form.category_name}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.cards_subcategories_list}</div>
<div>
	<div style="padding-bottom: 30px;">
		<div style="float: left;"><a href="admin_cards.php?sel=catalog"><b>{$header.back_to_categories_list}</b></a></div>
		<div style="float: right;"><a href="{$form.add_link}"><b>{$header.add_subcategory}</b></a></div>
	</div>
	<div>
		<span>{$header.jump_to_category}:&nbsp;</span>
		<span>
			<select name="id_category" onchange="document.location.href='admin_cards.php?sel=subcategories&id_category='+this.value;">
				{foreach item=item from=$categories}
				<option value="{$item.id}" {if $item.sel eq 1}selected{/if}>{$item.name}</option>
				{/foreach}
			</select>
		</span>
		<span style="padding-left: 30px;">{$header.jump_to_subcategory}:&nbsp;</span>
		<span>
			<select name="id_subcategory" onchange="document.location.href='admin_cards.php?sel=items&id_category={$form.id_category}&id_subcategory='+this.value;">
				<option value="">{$header.please_select}</option>
				{foreach item=item from=$subcategories}
				<option value="{$item.id}">{$item.name}</option>
				{/foreach}
			</select>
		</span>
	</div>
	<div style="padding-top: 10px;">
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $subcategories}
		<tr bgcolor="#ffffff">
			<td colspan=4 class="main_content_text" >
				<div style="float: left;">{if $links}{$links}{/if}</div>
			</td>
		</tr>
		<tr class="table_header">
			<td class="main_header_text" align="center" width="150">{$header.categories}</td>
			<td class="main_header_text" align="center"></td>
			<td class="main_header_text" align="center" width="120"></td>
			<td class="main_header_text" align="center" width="240">&nbsp;</td>
		</tr>
		{foreach item=item from=$subcategories}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center"><a href="admin_cards.php?sel=items&id_category={$form.id_category}&id_subcategory={$item.id}">{$item.name}</a></td>
			<td class="main_content_text" align="center">{$item.descr}</td>
			<td class="main_content_text" align="center">
				<a href="admin_cards.php?sel=items&id_category={$form.id_category}&id_subcategory={$item.id}">
				<img src="{$item.image}" height="100" width="100" style="border: 1px solid #cccccc;">
				</a>
			</td>
			<td class="main_content_text" align="center">
				<a href="admin_cards.php?sel=items&id_category={$form.id_category}&id_subcategory={$item.id}">{$header.view_items}</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="{$item.editlink}">{$button.edit}</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="#" onclick="if(confirm('{$header.confirm_subcategory}')){literal}{{/literal}document.location.href='{$item.deletelink}'{literal}}{/literal} else return false;">{$button.delete}</a>
			</td>
		</tr>
		{/foreach}
		{if $links}
		<tr bgcolor="#ffffff">
			<td colspan=4 class="main_content_text">{$links}</td>
		</tr>
		{/if}
	{else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="4" bgcolor="#FFFFFF">{$header.empty_subcategories}</td>
		</tr>
	{/if}
	</table>
	</div>
</div>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}