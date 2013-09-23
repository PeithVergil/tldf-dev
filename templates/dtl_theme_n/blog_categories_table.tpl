{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$lang.section.blog_categories}</div></div>
		</td>
	</tr>
	<tr>
		<td valign="top" class="text">
			{include file="$gentemplates/blog_menu.tpl"}
			<div class="content_2" style=" margin: 0px;">
				<div style="padding: 15px 20px 15px 20px;">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="50%" valign="top">
							<table cellpadding="0" cellspacing="0" width="100%" border="0">
							{section name=s loop=$blog_categories start=0 max=$half_num_cat}
							<tr>
								<td valign="top" width="180" style="padding-bottom: 10px;">{if $blog_categories[s].link}<a href="{$blog_categories[s].link}"><b>{/if}{$blog_categories[s].name}{if $blog_categories[s].link}</b></a>{/if}</td>
								<td valign="top" style="font-family: Tahoma; font-size: 10px; color: #000000; font-weight: bold;text-decoration: none;" >{$blog_categories[s].blogs_count}&nbsp;{$lang.blog.blogs}</td>
							</tr>
							{/section}
							</table>
						</td>
						<td width="50%" valign="top"><table cellpadding="0" cellspacing="0">
						{section name=s loop=$blog_categories start=$half_num_cat}
							<tr>
								<td valign="top" width="180" style="padding-bottom: 10px;">{if $blog_categories[s].link}<a href="{$blog_categories[s].link}"><b>{/if}{$blog_categories[s].name}{if $blog_categories[s].link}</b></a>{/if}</td>
								<td valign="top" style="font-family: Tahoma; font-size: 10px; color: #000000; font-weight: bold; text-decoration: none;" >{$blog_categories[s].blogs_count}&nbsp;{$lang.blog.blogs}</td>
							</tr>
						{/section}
						</table></td>
					</tr>
					</table>
				</div>
			</div>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
{include file="$gentemplates/index_bottom.tpl"}