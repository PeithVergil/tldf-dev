{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<div style="margin: 0px; height: 25px;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top"><div class="header">{$lang.section.events}:&nbsp;{$event.event_name}</div></td>
		<td valign="top" align="right"><a href="{$calendar_link}"><b>{$lang.section.calendar}</b></a>
				<div id="tool_tip_4"><label title="{$lang.events.help_tip.my_calendar}"><img src="{$site_root}{$template_root}/images/question_icon.gif"></label></div>
		{literal}
		<script type="text/javascript">
		$(function() {
		$('#tool_tip_4 *').tooltip();
		});
		</script>
		{/literal}
		</td>
	</tr>
	</table>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td style="padding-bottom:7px;"><a href="events.php">{$lang.button.back_to_events}</a></td></tr>
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
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td valign="top">
							<div>
								<font class="text_head">{$header.type}:</font> <font class="text">{$event.type}</font><br><br>
								<font class="text_head">{$header.event_date_begin}:</font> <font class="text">{$event.date_begin}</font><br>
								<font class="text_head">{$header.event_date_end}:</font> <font class="text">{$event.date_end}</font><br>
								<font class="text_head">{$header.event_periodicity}:</font> <font class="text">{$event.periodicity} {if $event.date_die}{$header.until} {$event.date_die}{/if}</font><br><br>
								<font class="text_head">{$header.location}:</font> <font class="text">{$event.location}</font><br>
								<font class="text_head">{$header.event_place}:</font> <font class="text">{$event.place}</font><br>
								{if $event.flyer}<br><a href="{$event.flyer}" target="_blank">{$header.flyer_print}</a><br>{/if}
							</div>
						</td>
						<td valign="top">
							<div>
						{if $event.user_join_event eq 0}
							<a href="{$event.join_link}">{$header.join_event}</a>
						{else}
							<font class="text_head">{$header.already_joined}</font>{if $event.user_is_creator eq 0}<br><a href="{$event.leave_link}">{$header.leave_event}</a>{/if}
							{if $event.can_invite eq 1 || $event.user_is_creator eq 1}
							<div style="padding-top: 5px;"><input type="button" value="{$header.invite_to_event}" onclick="document.location.href='{$event.invite_link}'"></div>
							{/if}
							{if $event.can_post_images eq 1 || $event.user_is_creator eq 1}
							<div style="padding-top: 5px;"><input type="button" value="{$header.upload_photo}" onclick="ShowUploadForm();"></div>
							{/if}
							<div style="padding-top: 5px;"><input type="button" value="{$header.add_comment}" onclick="ShowCommentForm();"></div>
						{/if}
							</div>
						</td>
						<td valign="top">
							<div>
						{if $event.user_is_creator eq 0}
							<table cellpadding="0" cellspacing="0" border="0">
								<tr><td class="text_head">{$header.event_creator}:</td></tr>
								<tr><td style="padding-top: 5px;"><a href="{$event.creator_profile_link}" target="_blank"><img src="{$event.creator_icon}" class="icon" alt=""></a></td></tr>
								<tr><td style="padding-top: 5px;" class="text_head"><a href="{$event.creator_profile_link}" target="_blank">{$event.event_creator}</a></td></tr>
								<tr><td style="padding-top: 5px;"><font class="text_head">{$event.event_creator_age} {$header.ans}</font></td></tr>
								<tr><td style="padding-top: 5px;"><font class="text">{if $event.event_creator_city}{$event.event_creator_city}, {/if}{if $event.event_creator_region}{$event.event_creator_region}, {/if}{$event.event_creator_country}</font></td></tr>
							</table>
						{else}
							<div><font class="text_head">{$header.u_are_creator}</font></div>
							<div style="padding-top: 3px;"><input type="button" value="{$header.delete_event}" onclick="document.location.href='{$event.delete_link}'"></div>
						{/if}
							</div>
						</td>
					</tr>
					</table>
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td valign="top"><font class="text">{$event.contain}</font></td>
					</tr>
					{if $event.user_is_creator eq 1 || ($event.user_join_event eq 1 && $event.can_post_images eq 1)}
					<tr>
						<td valign="top">
							<div id="upload_form_div" {if $show_upload_form ne 1} style="display: none;" {/if}>
							<form action="{$event.upload_link}" enctype="multipart/form-data" method="post">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td height="30" width="160" class="text_head">{$header.photo_path}:</td>
									<td height="30"><input type="file" name="upload"></td>
								</tr>
								<tr>
									<td class="text_head">{$header.comment_to_photo}:</td>
									<td><textarea name="comment_to_upload" id="comment_to_upload" style="width: 240px;" rows="5">{$data.comment_to_upload}</textarea></td>
								</tr>
								<tr>
									<td colspan="2"><input type="submit" value="{$lang.button.upload}"></td>
								</tr>
							</table>
							</form>
							</div>
						</td>
					</tr>
					{/if}
					{if $event.user_join_event eq 1}
					<tr>
						<td valign="top">
							<div id="comment_form_div" {if $show_comment_form ne 1} style="display: none;" {/if}>
							<form action="{$event.add_comment_link}" method="post" name="comment_form">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td width="160" class="text_head">{$header.comment_to_event}:</td>
									<td width="250">
										<select name="comment_type">
											<option value="comment">{$header.comment}</option>
											<option value="feedback">{$header.feedback}</option>
										</select><br>
										<textarea name="comment_to_event" id="comment_to_event" style="width: 240px;" rows="7">{$data.comment_to_event}</textarea>
									</td>
									<td align="center">
										<div style="margin: 0px 15px;">
										<table cellpadding="3" cellspacing="0" border="0">
										{section name=sm loop=$smiles}
										{if $smarty.section.sm.index is div by 6 || $smarty.section.sm.first}<tr>{/if}
										<td><a style="cursor:pointer" onclick="document.getElementById('comment_to_event').value=document.getElementById('comment_to_event').value+'{$smiles[sm].value}'"><img src="{$site_root}{$template_root}/emoticons/{$smiles[sm].file}" alt=""></a></td>
										{if ($smarty.section.sm.index_next is div by 6) || $smarty.section.sm.last}</tr>{/if}
										{/section}
										</table>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="2"><input type="submit" value="{$lang.button.submit}"></td>
								</tr>
							</table>
							</form>
							</div>
						</td>
					</tr>
					{/if}
					</table>
					<div style="height: 1px; margin-top: 5px; margin-right: 12px;" class="delimiter"></div>
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td valign="top">
							<div><font class="text_head">{$header.event_members}:</font> <font class="text">{$event.members_count}</font></div>
							<div style="height: 10px; margin: 0px"><img src="{$site_root}{$template_root}/images/empty.gif" height="10px" alt=""></div>
							{if $event_users_num>0}
							<div>
								<table cellpadding="5" cellspacing="0" border="0">
									<tr>
										{foreach item=item from=$event_users}
										<td valign="top" width="120">
										<table cellpadding="0" cellspacing="0" border="0" width="100%">
											<tr><td><a href="{$item.profile_link}" target="_blank"><img src="{$item.icon}" class="icon" alt=""></a></td></tr>
											<tr><td style="padding-top: 3px;" class="text_head"><a href="{$item.profile_link}" target="_blank">{$item.login}</a></td></tr>
											<tr><td style="padding-top: 3px;"><font class="text_head">{$item.age} {$header.ans}</font></td></tr>
											<tr><td style="padding-top: 3px;"><font class="text">{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}</font></td></tr>
										</table>
										</td>
										{/foreach}
									</tr>
								</table>
							</div>
							{/if}
						</td>
					</tr>
					{if $event.link_more_users}
					<tr>
						<td style="padding-left: 10px; padding-bottom: 10px;"><a href="{$event.link_more_users}">{$header.all_users}</a></td>
					</tr>
					{/if}
					</table>
					<div style="height: 1px; margin: 0px 12px 0px 0px;" class="delimiter"></div>
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td valign="top">
							<div><font class="text_head">{$header.event_photos}:</font> <font class="text">{$event.photos_count}</font></div>
							<div style="height: 10px; margin: 0px"><img src="{$site_root}{$template_root}/images/empty.gif" height="10px" alt=""></div>
							{if $event_photos_num>0}
							<div>
								<table cellpadding="5" cellspacing="0" border="0">
									<tr>
										{section name=u loop=$event_photos}
										<td width="{$form.icon_max_width+50}">
										<div>{if $event_photos[u].view_link}<a href="#" onclick="javascript: window.open('{$event_photos[u].view_link}','photo_view','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;">{/if}<img src="{$event_photos[u].upload_thumb_path}" class="icon" alt="">{if $event_photos[u].view_link}</a>{/if}</div>
										{if ($event.user_is_creator eq 1) || ($event_photos[u].user_upload eq 1)}<div style="padding-top: 3px;"><input type="button" value="{$button.delete}" onclick="document.location.href='{$event_photos[u].del_link}';"></div>{/if}
										</td>
										{/section}
									</tr>
								</table>
							</div>
							{/if}
						</td>
					</tr>
					{if $event.link_more_photos}
					<tr>
						<td style="padding-left: 10px; padding-bottom: 10px;"><a href="{$event.link_more_photos}">{$header.all_photos}</a></td>
					</tr>
					{/if}
					</table>
					{if $event_comments_num>0}
					<div style="height: 1px;  margin: 0px 12px 0px 0px;" class="delimiter"></div>
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td valign="top" width="100%">
							<div><font class="text_head">{$header.event_comments}:</font></div>
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
					{if $event.link_more_comments}
					<tr>
						<td style="padding-left: 10px; padding-bottom: 10px;"><a href="{$event.link_more_comments}">{$header.all_comments}</a></td>
					</tr>
					{/if}
					</table>
					</div>
					{/if}
				</td>
			</tr>
			</table>
			</div>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
{literal}
<script type="text/javascript">
function ShowUploadForm() {
	if (document.getElementById('upload_form_div').style.display == 'none'){
		document.getElementById('upload_form_div').style.display = 'inline';
	} else {
		document.getElementById('upload_form_div').style.display = 'none';
	}
	return;
}
function ShowCommentForm() {
	if (document.getElementById('comment_form_div').style.display == 'none'){
		document.getElementById('comment_form_div').style.display = 'inline';
	} else {
		document.getElementById('comment_form_div').style.display = 'none';
	}
	return;
}
</script>
{/literal}
{include file="$gentemplates/index_bottom.tpl"}