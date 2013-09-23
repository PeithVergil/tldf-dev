{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font>
<font class=red_sub_header>&nbsp;|&nbsp;{$header.item_list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.gs_item}</div>
<font class="main_header_text"><b>{$header.category}:</b></font>&nbsp;
<select name="id_category" onchange="javascript:location.href='./{$form.file_name}?sel=items&id_category='+this.value;">
{section name=s loop=$category}
	<option value="{$category[s].id}" {if $parent.id == $category[s].id}selected{/if}>{$category[s].name}</option>
{/section}
</select>
<br><br>
<div style="border: solid 1px #E6E6E6; width:100%; background-color: #FFFFFF">
	<table border="0" cellspacing="3" cellpadding="2" width="95%">
		<tr valign="center">
			{if $parent.thumb_icon_path}
				<td class="main_content_text" align="left" rowspan="2" width="10%">
					<img src="{$parent.thumb_icon_path}" border="1">
				</td>
			{/if}
			<td class="main_content_text" align="left" valign="top"><b>{$parent.name}</b></td>
		</tr>
		<tr valign="center">
			<td class="main_content_text" align="left" valign="top"><i>{$parent.comment}</i></td>
		</tr>
	</table>
</div>
<br>
<table border="0" class="table_main" cellspacing="1" cellpadding="5" width="100%">
	{if $links}
		<tr bgcolor="#ffffff">
			<td height="20"  colspan="7" align="left"  class="main_content_text" >{$links}</td>
		</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="20">{$header.number}</td>
		<td class="main_header_text" align="center">&nbsp;</td>
		<td class="main_header_text" align="center" width="150">{$header.name}</td>
		<td class="main_header_text" align="center">{$header.comment}</td>
		<td class="main_header_text" align="center">{$header.price}</td>
		<td class="main_header_text" align="center">{$header.status}</td>
		<td class="main_header_text" align="center" width="100">&nbsp;</td>
	</tr>
	{if $items}
		{section name=spr loop=$items}
			<tr bgcolor="#FFFFFF">
				<td class="main_content_text" align="center">{$items[spr].number}</td>
				<td class="main_content_text" align="center">
					{if $items[spr].thumb_icon_path}
						<img src="{$items[spr].thumb_icon_path}" border="1">
					{else}
						&nbsp;
					{/if}
				</td>
				<td class="main_content_text" align="center"><a href="./{$form.file_name}?sel=itemsedit&id={$items[spr].id}&id_category={$parent.id}&page={$form.page}">{$items[spr].name}</a></td>
				<td class="main_content_text" align="justify">{$items[spr].comment}</td>
				<td class="main_content_text" align="center">{$items[spr].price}&nbsp;{$form.curency}</td>
				<td class="main_content_text" align="center">{$items[spr].status}</td>
				<td class="main_content_text" align="center"><input type="button" value="{$button.delete}" class="button" onclick="javascript: if(confirm('{$lang.confirm.location_item}')){ldelim}location.href='./{$form.file_name}?sel=itemsdel&id={$items[spr].id}&page={$form.page}'{rdelim}"></td>
			</tr>
		{/section}
	{else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="7" bgcolor="#FFFFFF">{$header.category_empty}</td>
		</tr>
	{/if}
	{if $links}
		<tr bgcolor="#ffffff">
			<td height="20" colspan="7" align="left"  class="main_content_text">{$links}</td>
		</tr>
	{/if}
</table>
<table>
	<tr height="40">
		<td>
			<input type="button" value="{$header.item_add}" class="button" onclick="javascript: location.href='{$form.add_link}'">&nbsp;&nbsp;
			<input type="button" value="{$lang.button.back}" class="button" onclick="javascript: document.location.href='admin_citylocation.php';">
		</td>
	</tr>
</table>
<!-- /main spr row -->
{include file="$admingentemplates/admin_bottom.tpl"}