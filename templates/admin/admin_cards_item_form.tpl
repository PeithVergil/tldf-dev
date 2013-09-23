{include file="$admingentemplates/admin_top.tpl"}
{strip}
<font class=red_header>{$header.name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.cards_edit}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.cards_item_edit}</div>
<div>
<form method="post" action="{$form.action}" enctype="multipart/form-data">
<input type="hidden" name="par" value="{$form.par}">
<input type="hidden" name="id_category" value="{$form.id_category}">
<input type="hidden" name="id_subcategory" value="{$form.id_subcategory}">
{if $form.par eq 'edit'}
<input type="hidden" name="id" value="{$data.id}">
{/if}
<table class="table_main" cellspacing=1 cellpadding=5 width="100%">
	<tr bgcolor="#ffffff">
		<td width="200" class="main_header_text">{$header.card_name} <font class=main_error_text>*</font>:&nbsp;</td>
		<td><input name="card_name" value="{$data.card_name}" style="width: 195px;"></td>
	</tr>

	<tr bgcolor="#ffffff">
		<td class="main_header_text">{$header.card_status}:&nbsp;</td>
		<td><input type="checkbox" name="card_status" {if $data.card_status eq 1}checked{/if} value="1"></td>
	</tr>
	<tr bgcolor="#ffffff">
		<td class="main_header_text">{$header.card_price}:&nbsp;</td>
		<td><input name="card_price" value="{$data.card_price}" style="width: 100px;">&nbsp;{$cur}</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td class="main_header_text">{$header.card_category}:&nbsp;</td>
		<td>
			<select name="id_category" onchange="ajaxRequest('admin_cards.php?sel=ajax_subcategories&id_category='+this.value+'&', 'null', document.getElementById('subcategory_div'), '{$header.loading}&nbsp;<img src=\'{$site_root}{$template_root}/images/ajax-loader.gif\'>', true);">
				{foreach item=item from=$categories}
				<option value="{$item.id}" {if $data.id_category eq $item.id}selected{/if}>{$item.name}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td class="main_header_text">{$header.card_subcategory}:&nbsp;</td>
		<td height="30">
			<div id="subcategory_div">
			<select name="id_subcategory">
				{foreach item=item from=$subcategories}
				<option value="{$item.id}" {if $data.id_subcategory eq $item.id}selected{/if}>{$item.name}</option>
				{/foreach}
			</select>
			</div>
		</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td class="main_header_text" valign="top">{$header.card_image}:&nbsp;</td>
		<td>
			<div>{if $data.card_image}<img src="{$data.card_image}">{else}{$header.no_image}{/if}</div>
			<div style="padding-top: 5px;"><input type="file" name="card_image"></div>
		</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td>&nbsp;</td>
		<td>
			{if $form.par eq 'edit'}<input type="submit" value="{$button.save}" class="button">{else}<input type="submit" value="{$button.add}" class="button">{/if}&nbsp;&nbsp;
			<input type="button" value="{$button.back}" class="button" onclick="document.location.href='{$form.back}'">
		</td>
	</tr>
</table>
</form>
</div>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}