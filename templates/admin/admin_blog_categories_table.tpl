{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.blog.admin.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$lang.blog.admin.categories_list}</font><br><br>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.blog_categories_list}</div>
<div>
	<div>
		<input type="button" value="{$lang.button.add}" class="button" onclick="javascript: location.href='{$form.add_link}'">
	</div>
	<div style="padding-top: 10px;">
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
		{if $links}
		<tr bgcolor="#ffffff">
			<td height="20"  colspan=4 align="left"  class="main_content_text" >{$links}</td>
		</tr>
		{/if}
		<tr class="table_header">
			<td class="main_header_text" align="center" width="20">{$lang.blog.admin.number}</td>
			<td class="main_header_text" align="center">{$lang.blog.admin.category}</td>
			<td class="main_header_text" align="center" width="100">{$lang.blog.admin.blogs_count}</td>
			<td class="main_header_text" align="center" width="100">&nbsp;</td>
		</tr>
		{if $blog_categories}
		{section name=spr loop=$blog_categories}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center">{$blog_categories[spr].number}</td>
			<td class="main_content_text" align="center"><a href="{$blog_categories[spr].editlink}">{$blog_categories[spr].name}</a></td>
			<td class="main_content_text" align="center">{$blog_categories[spr].blogs_count}</td>
			<td class="main_content_text" align="center"><input type="button" value="{$button.delete}" class="button" onclick="javascript: if(confirm('{if $blog_categories[spr].blogs_count > 0}{$lang.blog.confirm_category_non_null}{else}{$lang.blog.confirm}{/if}')){literal}{{/literal}location.href='{$blog_categories[spr].deletelink}'{literal}}{/literal}"></td>
		</tr>
		{/section}
		{else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="4" bgcolor="#FFFFFF">{$header.empty_categories}</td>
		</tr>
		{/if}
		{if $links}
		<tr bgcolor="#ffffff">
			<td height="20"  colspan=4 align="left"  class="main_content_text" >{$links}</td>
		</tr>
		{/if}
	</table>
	</div>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}