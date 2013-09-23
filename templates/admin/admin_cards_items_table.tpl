{include file="$admingentemplates/admin_top.tpl"}
<link href="{$site_root}/javascript/greybox/greybox.css" rel="stylesheet" type="text/css" media="all">
<script type="text/javascript" src="{$site_root}/javascript/greybox/AmiJS.js?v=0000"></script>
<script type="text/javascript" src="{$site_root}/javascript/greybox/greybox.js?v=0000"></script>
<script type="text/javascript">
	var GB_IMG_DIR = "{$site_root}/javascript/greybox/";
</script>
{strip}
<font class=red_header>{$header.name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.subcategories_list}{$header.in}{$form.category_name}&nbsp;|&nbsp;{$header.cards_list}{$header.in}{$form.subcategory_name}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.cards_items_list}</div>
<div>
	<div style="padding-bottom: 30px;">
		<div style="float: left;"><a href="admin_cards.php?sel=catalog"><b>{$header.back_to_categories_list}</b></a>&nbsp;&nbsp;&nbsp;
		<a href="{$form.back}"><b>{$header.back_to_subcategories_list}</b></a>
		</div>
		<div style="float: right;"><a href="{$form.add_link}"><b>{$header.add_card}</b></a></div>
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
				{foreach item=item from=$subcategories}
				<option value="{$item.id}" {if $item.sel eq 1}selected{/if}>{$item.name}</option>
				{/foreach}
			</select>
		</span>
	</div>
	<div style="padding-top: 10px;">
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $cards}
		<tr bgcolor="#ffffff">
			<td colspan=5 class="main_content_text" >
				<div style="float: left;">{if $links}{$links}{/if}</div>
			</td>
		</tr>
		<tr class="table_header">
			<td class="main_header_text" align="center">{$header.cards_list}</td>
			<td class="main_header_text" align="center" width="80">{$header.card_price}</td>
			<td class="main_header_text" align="center" width="50">{$header.card_status}</td>
			<td class="main_header_text" align="center" width="120"></td>
			<td class="main_header_text" align="center" width="240">&nbsp;</td>
		</tr>
		{foreach item=item from=$cards}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center">{$item.name}</td>
			<td class="main_content_text" align="center">{$item.card_price}</td>
			<td class="main_content_text" align="center">{if $item.card_status eq 1}+{else}-{/if}</td>
			<td class="main_content_text" align="center">
				<a href="#" onclick="return GB_showImage('{$item.name_unslashed}', '{$item.card_image_big}')">
					<img src="{$item.card_image}" height="100" width="100" style="border: 1px solid #cccccc;">
				</a>
			</td>
			<td class="main_content_text" align="center">
				<a href="{$item.editlink}">{$button.edit}</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="#" onclick="if(confirm('{$header.confirm_card}')){literal}{{/literal}document.location.href='{$item.deletelink}'{literal}}{/literal} else return false;">{$button.delete}</a>
			</td>
		</tr>
		{/foreach}
		{if $links}
		<tr bgcolor="#ffffff">
			<td colspan=5 class="main_content_text">{$links}</td>
		</tr>
		{/if}
	{else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="5" bgcolor="#FFFFFF">{$header.empty_cards}</td>
		</tr>
	{/if}
	</table>
	</div>
</div>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}