{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font>
<font class=red_sub_header>&nbsp;|&nbsp;{$header.category_list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.gs_category}</div>
	<table border="0" class="table_main" cellspacing="1" cellpadding="5" width="100%">
		{if $links}
			<tr bgcolor="#ffffff">
				<td height="20" colspan="6" align="left"  class="main_content_text" >{$links}</td>
			</tr>
		{/if}
		<tr class="table_header">
			<td class="main_header_text" align="center" width="20">{$header.number}</td>
			<td class="main_header_text" align="center">&nbsp;</td>
			<td class="main_header_text" align="center" width="150">{$header.name}</td>
			<td class="main_header_text" align="center">{$header.comment}</td>
			<td class="main_header_text" align="center">{$header.status}</td>
			<td class="main_header_text" align="center" width="100">&nbsp;</td>
		</tr>
		{if $category}
			{section name=spr loop=$category}
				<tr bgcolor="#FFFFFF">
					<td class="main_content_text" align="center">{$category[spr].number}</td>
					<td class="main_content_text" align="center">
						{if $category[spr].thumb_icon_path}
							<img src="{$category[spr].thumb_icon_path}" border="1">
						{else}
							&nbsp;
						{/if}
					</td>
					<td class="main_content_text" align="center">
						<a href="./{$form.file_name}?sel=catedit&id={$category[spr].id}&page={$form.page}">{$category[spr].name}</a>
					</td>
					<td class="main_content_text" align="justify">
						{$category[spr].comment}
					</td>
					<td class="main_content_text" align="center">
						{$category[spr].status}
					</td>
					<td class="main_content_text" align="center">
						<table cellspacing="1" cellpadding="0">
							<tr>
								<td width="80"><input type="button" value="{$button.items}" class="button" onclick="javascript:location.href='./{$form.file_name}?sel=items&id_category={$category[spr].id}'"></td>
								<td width="80"><input type="button" value="{$button.delete}" class="button" onclick="javascript: if(confirm('{$lang.confirm.location_category}')){ldelim}location.href='./{$form.file_name}?sel=catdel&id={$category[spr].id}&page={$form.page}'{rdelim}"></td>
							</tr>
						</table>
					</td>
				</tr>
			{/section}
		{else}
			<tr height="40">
				<td class="main_error_text" align="left" colspan="6" bgcolor="#FFFFFF">{$header.category_empty}</td>
			</tr>
		{/if}
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20" colspan="6" align="left"  class="main_content_text">{$links}</td>
	</tr>
	{/if}
</table>
<table>
	<tr height="40">
		<td><input type="button" value="{$header.category_add}" class="button" onclick="javascript: location.href='{$form.add_link}'"></td>
	</tr>
</table>
<!-- /main spr row -->
{include file="$admingentemplates/admin_bottom.tpl"}