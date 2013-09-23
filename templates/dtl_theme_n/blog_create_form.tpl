{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$lang.section.blog_create}</div></div>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td></tr>
	{/if}
	{if $form.inactive ne 1}
	<tr>
		<td valign="top" class="text">
			{include file="$gentemplates/blog_menu.tpl"}
			<div class="content_2" style=" margin: 0px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding-left: 15px; padding-top: 15px;">
				<form name="blog_form" id="blog_form" action="blog.php?sel=add" method="post" enctype="multipart/form-data">
					<table border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td height="30" width="200" class="text_head">{$lang.blog.blog_name}:</td>
						<td><input type="text" name="blog_name" value="{$data.blog_name}" style="width: 150px" maxlength="500"></td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.blog.category}:</td>
						<td>
						<select name="blog_category">
						{foreach from=$categories item=item}
							<option value="{$item.id}" {if $item.sel eq 1}selected{/if}>{$item.name}</option>
						{/foreach}
						</select>
						</td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.blog.hidden_blog}:</td>
						<td><input type="checkbox" name="hidden_blog" value="1"></td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr><td class="text_head">{$lang.blog.description}:</td></tr>
							<tr><td class="text">{$lang.blog.no_html}</td></tr>
						</table>
						</td>
						<td><textarea name="description" id="description" style="width: 300px;" rows="7">{$data.description}</textarea></td>
					</tr>
					<tr>
						<td height="30" colspan="2"><input type="submit" value="{$lang.button.submit}" class="button"></td>
					</tr>
					</table>
				</form>
				</td>
			</tr>
			</table>
			</div>
		</td>
	</tr>
	{/if}
	</table>
	<!-- end main cell -->
</td>
{include file="$gentemplates/index_bottom.tpl"}