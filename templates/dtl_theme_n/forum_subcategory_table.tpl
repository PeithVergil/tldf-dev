<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td>
	<table cellpadding="0" cellspacing="2" width="100%" border="0">
		<tr bgcolor="#FFFFFF">
			<td width="130" height="27" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.author}</b></td>
			<td height="27" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.message}</b></td>
		</tr>
		{section name=f loop=$forum}
		<tr bgcolor="#FFFFFF">
			<td valign="top" align="left" style="padding-left: 13px;">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td style="padding-top: 7px; padding-right: 7px;">{if $forum[f].poster_profile eq 1}<a href="viewprofile.php?id={$forum[f].id_user}" target="_blank">{/if}<b>{$forum[f].login_user}</b>{if $forum[f].poster_profile eq 1}</a>{/if}</td>
				</tr>
				<tr>
					<td style="padding-top: 5px; padding-bottom: 10px;">{$forum[f].date}</td>
				</tr>
				</table>
			</td>
			<td valign="top" style="padding-left: 13px;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="left" class="header" style="padding-top: 7px;">{$forum[f].subject}</td>
				</tr>
				<tr>
					<td align="left" style="padding-bottom: 10px; padding-top: 7px; padding-right: 7px;">{$forum[f].message}</td>
				</tr>
				</table>
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<tr>
						<td height="20">&nbsp;</td>
						<td style="padding-top: 7px; padding-right: 7px; padding-bottom: 10px;" align="right">
							<a href="{$file_name}?sel=quote&amp;id_post={$forum[f].id}">{$lang.forum.quote}</a>
							{if $forum[f].is_author eq 1}
								&nbsp;|&nbsp;<a href="{$file_name}?sel=edit_post&id_post={$forum[f].id}">{$lang.forum.edit}</a>
								&nbsp;|&nbsp;<a href="#" onclick="{literal} if (confirm('{/literal}{$lang.forum.del_confirm}{literal}')) { document.location.href='{/literal}{$file_name}?sel=delete_post&id_post={$forum[f].id}{literal}'}; return false;{/literal}">{$lang.forum.delete}</a>
							{/if}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor="#f2f2f2">
			<td colspan="2" height="2"></td>
		</tr>
		{/section}
	</table>
	</td>
</tr>
{if $links}
<tr>
	<td>
	<div style="margin-left: 0px; padding-top: 15px;" >
	{foreach item=item from=$links}
		<span style="padding-right: 15px;"><a href="{$item.link}" {if $item.selected eq '1'} class="text_head"{/if}>{$item.name}</a></span>
	{/foreach}
	</div>
	</td>
</tr>
{/if}
</table>