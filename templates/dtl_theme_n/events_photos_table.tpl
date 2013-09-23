{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$header.event_photos} {$event_name}</div></div>
		</td>
	</tr>
	<tr>
		<td valign="top" style="padding: 5px 0px 10px 0px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif" hspace="0" vspace="0" border="0" alt=""></td>
				<td style="padding-left: 2px;"><a href="events.php?sel=event&id_event={$id_event}">{$header.back_to_event}</a></td>
			</tr>
			</table>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td>
	</tr>
	{/if}
	<tr>
		<td valign="top" class="text">
			<div class="content" style=" margin: 0px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding-left: 15px; padding-top: 15px;">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						{section name=u loop=$event_photos}
						{if $smarty.section.u.index is div by 5}<tr>{/if}
						<td style="padding-bottom: 15px;">
						<div>{if $event_photos[u].view_link}<a href="#" onclick="javascript: window.open('{$event_photos[u].view_link}','photo_view','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;">{/if}<img src="{$event_photos[u].upload_thumb_path}" class="icon" alt="">{if $event_photos[u].view_link}</a>{/if}</div>
						{if ($user_is_creator eq 1) || ($event_photos[u].user_upload eq 1)}<div style="padding-top: 3px;"><input type="button" value="{$button.delete}" onclick="document.location.href='{$event_photos[u].del_link}';"></div>{/if}
						</td>
						{if $smarty.section.u.index_next is div by 5 || $smarty.section.u.last}</tr>{/if}
						{/section}
					</table>
				</td>
			</tr>
			</table>
			</div>
			<div style="height: 10px; margin: 0px"><img src="{$site_root}{$template_root}/images/empty.gif" height="10px" alt=""></div>
			{if $links}
			<div style="margin: 0px"><div style="margin-left: 10px">
			{foreach item=item from=$links}
				<div class="page_div{if $item.selected eq '1'}_active{/if}">
					<div style="margin: 5px"><a href="{$item.link}" class="page_link{if $item.selected eq '1'}_active{/if}">{$item.name}</a></div>
				</div>
			{/foreach}
			</div></div>
			{/if}
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
{include file="$gentemplates/index_bottom.tpl"}