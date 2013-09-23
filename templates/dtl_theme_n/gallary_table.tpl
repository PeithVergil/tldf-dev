{include file="$gentemplates/index_top.tpl"}
<div class="toc page-simple gallery">
{strip}
	<!-- begin main cell -->
	<div class="content" style="margin: 0px; padding: 15px;">
		<div style="margin: 0px; padding: 0px;">
		<div class="hdr2">{$lang.gallary.last_uploads}</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td style="padding-top: 25px;" valign="top">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					{foreach item=item from=$last_uploads}
					<td width="{$form.top_percent}%" align="left" valign="top" class="text">
						<div><a href="#" onclick="window.open('{$item.view_link}', 'view', 'height=400, resizable=yes, scrollbars=yes, width=400, menubar=no,status=no, left=200, top=20'); return false;"><img src="{$item.upload_path}" border="0" class="icon"></a></div>
						<div style="padding-top: 5px;">{$lang.gallary.category}:&nbsp;<a href="gallary.php?sel=category&id_category={$item.category_id}&upload_type={$form.upload_type}">{$item.category_name}</a></div>
						<div style="padding-top: 3px;">{$lang.gallary.author}:&nbsp;<a href="viewprofile.php?id={$item.author_id}&sel=4" target="_blank">{$item.author_login}</a></div>
					</td>
					{/foreach}
				</tr>
			</table>
			</td>
		</tr>
		</table>
		</div>
	</div>
	<div class="content" style="margin-top: 12px; padding: 15px;">
		<div style="margin: 0px; ">
		<div class="hdr2">{$lang.gallary.popular_categories}</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				{section name=c loop=$categories start=0 max=$form.top_count}
				{if $smarty.section.c.index is div by 4}<tr>{/if}
					<td width="25%" valign="top" class="text" style="padding-top: 25px;">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td valign="top"><a href="#" onclick="window.open('{$categories[c].view_link}', 'view', 'height=400, resizable=yes, scrollbars=yes, width=400, menubar=no,status=no, left=200, top=20'); return false;"><img src="{$categories[c].icon}" border="0" class="icon"></a></td>
								<td valign="top" style="padding-left: 10px;">
									<div><font class="text">Category:</font>&nbsp;<a href="gallary.php?sel=category&id_category={$categories[c].id}&upload_type={$form.upload_type}">{$categories[c].name}</a></div>
									<div style="padding-top: 5px;"><font class="text_hidden">Photos:&nbsp;{$categories[c].photo_count}</font></div>
									<div style="padding-top: 5px;"><font class="text">Author:&nbsp;<a href="viewprofile.php?id={$categories[c].author_id}&sel=4" target="_blank">{$categories[c].author_login}</a></font></div>
								</td>
							</tr>
						</table>
					</td>
				{if $smarty.section.c.index_next is div by 4 || $smarty.section.c.last}</tr>{/if}
				{/section}
			</table>
			</td>
		</tr>
		</table>
		</div>
	</div>
	<div class="content" style="margin-top: 12px; padding: 15px;">
		<div style="margin: 0px; ">
		<div class="hdr2">{$lang.gallary.other_categories}</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td style="padding-top: 25px;">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="33%" valign="top">
						<table cellpadding="0" cellspacing="0">
						{section name=c loop=$categories start=$form.top_count max=$form.categories_1_limit}
						<tr>
							<td width="120" align="left" valign="top" style="padding-bottom: 7px;"><a href="gallary.php?sel=category&id_category={$categories[c].id}&upload_type={$form.upload_type}"><b>{$categories[c].name}</b></a></td>
							<td valign="top" class="text">{$categories[c].photo_count}&nbsp;{if $form.upload_type eq 'f'}{$lang.gallary.photos}{elseif $form.upload_type eq 'v'}{$lang.gallary.videos}{/if}</td>
						</tr>
						{/section}
						</table>
					</td>
					<td width="33%" valign="top">
						<table cellpadding="0" cellspacing="0">
						{section name=c loop=$categories start=$form.categories_2_start max=$form.categories_2_limit}
						<tr>
							<td width="120" align="left" valign="top" style="padding-bottom: 7px;"><a href="gallary.php?sel=category&id_category={$categories[c].id}&upload_type={$form.upload_type}"><b>{$categories[c].name}</b></a></td>
							<td valign="top" class="text">{$categories[c].photo_count}&nbsp;{if $form.upload_type eq 'f'}{$lang.gallary.photos}{elseif $form.upload_type eq 'v'}{$lang.gallary.videos}{/if}</td>
						</tr>
						{/section}
						</table>
					</td>
					<td width="33%" valign="top">
						<table cellpadding="0" cellspacing="0">
						{section name=c loop=$categories start=$form.categories_3_start}
						<tr>
							<td width="120" align="left" valign="top" style="padding-bottom: 7px;"><a href="gallary.php?sel=category&id_category={$categories[c].id}&upload_type={$form.upload_type}"><b>{$categories[c].name}</b></a></td>
							<td valign="top" class="text">{$categories[c].photo_count}&nbsp;{if $form.upload_type eq 'f'}{$lang.gallary.photos}{elseif $form.upload_type eq 'v'}{$lang.gallary.videos}{/if}</td>
						</tr>
						{/section}
						</table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</div>
	</div>
	<!-- end main cell -->
{/strip}
</div>
{include file="$gentemplates/index_bottom.tpl"}