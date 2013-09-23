{include file="$gentemplates/index_top.tpl"}
<div class="toc page-simple gallery">
    <div class="hdr2">{if $form.upload_type eq 'v'}{$lang.gallary.videos_in}{else}{$lang.gallary.photos_in}{/if}  {$category.name}</div>
{strip}
	<!-- begin main cell -->
	<div class="content" style="margin: 0px; padding: 12px;">
		<div style="margin: 0px; padding: 0px;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td valign="top">
						
						<div style="padding-top: 5px;">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif"></td>
								<td><a href="gallary.php?upload_type={$form.upload_type}">{$lang.gallary.back_to_categories_list}</a>      </td>
							</tr>
						</table>
						</div>
					</td>
					<td align="right" valign="top" class=""user-link>
						{$lang.gallary.sort_by}:    
						<a href="gallary.php?sel=category&id_category={$category.id}&sorter=1&upload_type={$form.upload_type}" {if $form.sorter eq 1}class="text_head"{/if}><b>{$lang.gallary.by_rating}</b></a>    |    
						<a href="gallary.php?sel=category&id_category={$category.id}&sorter=2&upload_type={$form.upload_type}" {if $form.sorter eq 2}class="text_head"{/if}><b>{$lang.gallary.by_last}</b></a>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="padding-top: 12px;" valign="top">
			{if $uploads ne 'empty'}
				<div>
				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="gall-listing">
				{section name=s loop=$uploads}
				{if $smarty.section.s.index is div by 5}<tr>{/if}
				<td width="120" align="center" valign="top" style="padding-top: 5px;" class="text">
					<div><a href="#" onclick="window.open('{$uploads[s].view_link}', 'view', 'height=400, resizable=yes, scrollbars=yes, width=400, menubar=no,status=no, left=200, top=20'); return false;"><img src="{$uploads[s].upload_path}" border="0" class="icon"></a></div>
					<div style="padding-top: 3px;">{$lang.gallary.author}:  <a href="viewprofile.php?id={$uploads[s].author_id}" target="_blank">{$uploads[s].author_login}</a></div>
					<div style="padding-top: 3px;">{$lang.gallary.rating}:  {$uploads[s].rate}  {$lang.gallary.points}</div>
				</td>
				{if $smarty.section.s.index_next is div by 5 || $smarty.section.s.last}</tr>{/if}
				{/section}
				</table>
				</div>
				{if $links}
				<ol class="page-nation">
				{foreach item=item from=$links}
					<li><a href="{$item.link}" {if $item.selected eq '1'} class="selected"{/if}>{$item.name}</a></li>
				{/foreach}
				</ol>
				{/if}
				{if $form.show_rate_button eq 1}
				<div width="100%" align="right" style="padding-top: 5px;"><input type="button" class="normal-btn" onclick="document.location.href='gallary.php?sel=begin_vote&id_category={$category.id}'" value="{$lang.gallary.begin_vote}"></div>
				{/if}
			{else}
				{$lang.gallary.no_photos}
			{/if}
			</td>
		</tr>
		</table>
		</div>
	</div>
	<!-- end main cell -->
{/strip}
</div>
{include file="$gentemplates/index_bottom.tpl"}