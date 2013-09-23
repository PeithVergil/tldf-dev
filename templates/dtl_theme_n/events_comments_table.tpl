{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$header.event_comments} {$event_name}</div></div>
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
		<td><div class="error_msg">{$form.err}</div></td></tr>
	{/if}
	<tr>
		<td valign="top" class="text">
			<div class="content" style=" margin: 0px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding-left: 15px; padding-top: 15px;">
					{section name=n loop=$event_comments}
					<div style="margin-top: 5px; margin-bottom: 0px">
					<table width="100%" border="0" cellpadding="5" cellspacing="0">
					<tr valign="middle">
						<td height="100px" width="18px" class="text" align=center valign="top">
							<div style="margin-top: 7px">
								{$smarty.section.n.index_next}
							</div>
						</td>
						<td height="100px" width="80px" class="text" align=center valign="top">{if $event_comments[n].profile_link}<a href="{$event_comments[n].profile_link}">{/if}<img src="{$event_comments[n].icon_path}" class="icon" alt="">{if $event_comments[n].profile_link}</a>{/if}</td>
						<td height="100px" width="100%" class="text" valign="top">
							<div style="margin-top: 0px">
								{if $event_comments[n].profile_link}<a href="{$event_comments[n].profile_link}">{/if}<b>{$event_comments[n].name}</b>{if $event_comments[n].profile_link}</a>{/if}&nbsp;
								<font class="text_head">{$event_comments[n].age} {$lang.home_page.ans}</font>&nbsp;
								<font class="{if $event_comments[n].status eq $lang.status.on}link{else}text{/if}_active">{$event_comments[n].status}</font><br>
								<font class="text">{if $event_comments[n].city}{$event_comments[n].city}, {/if}{if $event_comments[n].region}{$event_comments[n].region}, {/if}{$event_comments[n].country}</font>
							</div>
							<div style="margin-top: 5px">
								<font class="text">{$event_comments[n].comment}</font>&nbsp;
							</div>
							<div style="margin-top: 5px">
								<font class="text_hidden"><b>{$event_comments[n].type}</b></font>
							</div>
							<div style="margin-top: 2px">
								<font class="text_hidden">{$event_comments[n].date}</font>&nbsp;&nbsp;&nbsp;{if $event_comments[n].delete_link}<a href="{$event_comments[n].delete_link}">[{$button.delete}]</a>{/if}&nbsp;
							</div>
						</td>
					</tr>
					</table>
					</div>
					{/section}
				</td>
			</tr>
			<tr>
				<td height="10">&nbsp;</td>
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