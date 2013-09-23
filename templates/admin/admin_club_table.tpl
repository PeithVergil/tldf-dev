{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.club.admin.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$club.club_name}</font><br><br>
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top" style="padding: 5px 0px 10px 0px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif" hspace="0" vspace="0" border="0" alt=""></td>
				<td style="padding-left: 2px;"><a href="admin_club.php">{$lang.club.back_to_all_club}</a></td>
			</tr>
			</table>
		</td>
	</tr>
	{if $data.agree_form eq 1}
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding: 15px;">
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<textarea style="width: 450px;" rows="5" readonly>{$data.agreement_text}</textarea>
						</td>
					</tr>
					</table>
					<table border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td><input type="checkbox" value="1" id="agree" onclick="JoinBtnFunc()"></td>
						<td>{$lang.club.i_agree_text}</td>
					</tr>
					<tr>
						<td colspan="2"><input type="button" value="{$lang.club.join_club}" id="btn_join" disabled onclick="document.location.href='{$data.agree_link}'"></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			</div>
		</td>
	</tr>
	{else}
	<tr>
		<td valign="top" class="text">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding-left: 15px; padding-top: 15px;">
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td valign="top" width="{$form.icon_max_width+10}" align="center"><img src="{$club.icon_path}" class="icon" alt=""></td>
						<td valign="top">
							<div>
								<font class="text_head">{$lang.club.club_category}:</font> <font class="text">{$club.category}</font><br><br>
								<font class="text_head">{$lang.club.club_type}:</font> <font class="text">{if $club.is_open eq 1}{$lang.club.public}{else}{$lang.club.private}{/if}</font><br>
								<font class="text_head">{$lang.club.founded}:</font> <font class="text">{$club.creation_date}</font><br>
								<font class="text_head">{$lang.club.location}:</font> <font class="text">{if $club.city}{$club.city},{/if} {if $club.region}{$club.region}, {/if}{if $club.country}{$club.country}{else}{$lang.club.undefined}{/if}</font><br>
								<font class="text_head">{$lang.club.members}:</font> <font class="text">{$club.members_count}</font><br>
							</div>
						</td>
						<td valign="top">
							<div>
						{if $club.user_in_club eq 0}
							<input type="button" value="{$lang.club.join_club}" onclick="document.location.href='{$club.join_link}';">
						{else}
							<font class="text_head">{$lang.club.joined}</font>{if $club.user_is_leader eq 0}<br><a href="{$club.leave_link}">{$lang.club.leave_club}{/if}</a>
						{/if}
							</div>
						</td>
						<td valign="top" width="30%">
							<div>
						{if $club.user_is_leader eq 0}
                        		<table cellpadding="3" cellspacing="0" border="0">
								<tr><td class="text_head">{$lang.club.club_leader}:</td></tr>
								<tr><td><a href="{$club.leader_profile_link}" target="_blank"><img src="{$club.leader_icon}" class="icon" alt=""></a></td></tr>
								<tr><td class="text_head"><a href="{$club.leader_profile_link}" target="_blank">{$club.club_leader}</a></td></tr>
								<tr><td style="padding-top: 3px;"><font class="text_head">{$club.club_leader_age} {$header.ans}</font></td></tr>
								<tr><td style="padding-top: 3px;"><font class="text">{if $base_lang.city[$club.club_leader_id_city]}{$base_lang.city[$club.club_leader_id_city]}, {/if}{if $base_lang.region[$club.club_leader_id_region]}{$base_lang.region[$club.club_leader_id_region]}, {/if}{$base_lang.country[$club.club_leader_id_country]}</font></td></tr>
							</table>
                        {else}
							<div><font class="text_head">{$lang.club.u_are_leader}</font></div>
							<div style="padding-top: 3px;"><input type="button" value="{$lang.club.edit_club}" onclick="document.location.href='{$club.edit_link}'"></div>
							<div style="padding-top: 3px;">
								<div>
								<input type="button" value="{$lang.club.delete_club}" onclick="document.location.href='{$club.delete_link}'"><br><br>
								<input type="button" value="{$lang.club.change_club_icon}" onclick="ShowClubIconDiv();">
								</div>
								<div style="display: none;" id="club_icon_div">
									<div>
									<form action="{$file_name}" method="POST" style="padding: 0px; margin: 0px;" enctype="multipart/form-data">
									<div style="padding-top: 10px;">
										<input type="hidden" name="sel" value="save_icon">
										<input type="hidden" name="id_club" value="{$club.id}">
										<input type="file" name="club_icon">
									</div>
									<div>
										<input type="submit" value="{$lang.button.upload}">
									</div>
									</form>
									</div>
								</div>
							</div>
						{/if}
							</div>
						</td>
					</tr>
					</table>
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td valign="top"><font class="text">{$club.description}</font></td>
					</tr>
					{if $club.user_is_leader eq 1 || ($club.user_in_club eq 1 && $club.can_invite eq 1)}
					<tr>
						<td valign="top"><input type="button" value="{$lang.club.invite_to_club}" onclick="document.location.href='{$club.invite_link}'"></td>
					</tr>
					{/if}
					{if $club.user_is_leader eq 1 || ($club.user_in_club eq 1 && $club.can_post_images eq 1)}
					<tr>
						<td valign="top">
							<div><input type="button" value="{$lang.club.upload_photo}" onclick="ShowUploadForm();"></div>
							<div id="upload_form_div" {if $show_upload_form ne 1} style="display: none;" {/if}>
							<form action="{$club.upload_photo_link}" enctype="multipart/form-data" method="post">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td height="30" width="160" class="text_head">{$lang.club.photo_path}:</td>
									<td height="30"><input type="file" name="upload"></td>
								</tr>
								<tr>
									<td class="text_head">{$lang.club.comment_to_photo}:</td>
									<td><textarea name="comment_to_upload" id="comment_to_upload" style="width: 200px;" rows="5">{$data.comment_to_upload}</textarea></td>
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
					{if $club.user_is_leader eq 1}
					<tr>
						<td valign="top">
							<div><input type="button" value="{$lang.club.add_news}" onclick="ShowNewsForm();"></div>
							<div id="news_form_div" {if $show_news_form ne 1} style="display: none;" {/if}>
							<form action="{$club.upload_news_link}" enctype="multipart/form-data" method="post">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td height="30" width="160" class="text_head">{$lang.club.news_name}:</td>
									<td height="30"><input type="text" value="{$data.news_name}" name="news_name" id="news_name" maxlength="500"></td>
								</tr>
								<tr>
									<td class="text_head">{$lang.club.news_text}:</td>
									<td><textarea name="news_text" id="news_text" style="width: 200px;" rows="5">{$data.news_text}</textarea></td>
								</tr>
								<tr>
									<td colspan="2"><input type="submit" value="{$lang.button.add}"></td>
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
							<div><font class="text_head">{$lang.club.club_members}:</font> <font class="text">{$club.members_count}</font></div>
							<div style="height: 10px; margin: 0px"><img src="{$site_root}{$template_root}/images/empty.gif" height="10px" alt=""></div>
							{if $club_users_num>0}
							<div>
								<table cellpadding="5" cellspacing="0" border="0" width="100%">
									<tr>
										{foreach item=item from=$club_users}
										<td valign="top" width="20%">
										<table cellpadding="0" cellspacing="0" width="100%" border="0">
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
					{if $club.link_more_users}
					<tr>
						<td style="padding-left: 10px; padding-bottom: 10px;"><a href="{$club.link_more_users}">{$lang.club.all_users}</a></td>
					</tr>
					{/if}
					</table>
					<div style="height: 1px; margin: 0px 12px 0px 0px;" class="delimiter"></div>
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td valign="top">
							<div><font class="text_head">{$lang.club.club_photos}:</font> <font class="text">{$club.photos_count}</font></div>
							<div style="height: 10px; margin: 0px"><img src="{$site_root}{$template_root}/images/empty.gif" height="10px" alt=""></div>
							{if $club_photos_num>0}
							<div>
								<table cellpadding="5" cellspacing="0" border="0">
									<tr>
										{section name=u loop=$club_photos}
										<td width="{$form.icon_max_width+40}">
										<div>{if $club_photos[u].view_link}<a href="#" onclick="javascript: window.open('{$club_photos[u].view_link}','photo_view','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;">{/if}<img src="{$club_photos[u].upload_thumb_path}" class="icon" alt="">{if $club_photos[u].view_link}</a>{/if}</div>
										{if $club.user_is_leader eq 1}<div style="padding-top: 3px;"><input type="button" value="{$button.delete}" onclick="document.location.href='{$club_photos[u].del_link}';"></div>{/if}
										</td>
										{/section}
									</tr>
								</table>
							</div>
							{/if}
						</td>
					</tr>
					{if $club.link_more_photos}
					<tr>
						<td style="padding-left: 10px; padding-bottom: 10px;"><a href="{$club.link_more_photos}">{$lang.club.all_photos}</a></td>
					</tr>
					{/if}
					</table>
					{if $club_news_num>0}
					<div style="height: 1px;  margin: 0px 12px 0px 0px;" class="delimiter"></div>
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td valign="top" width="100%">
							<div><font class="text_head">{$lang.club.club_news}:</font></div>
							<div>
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
									{section name=n loop=$club_news}
									<tr>
										<td style="padding-top: 5px;">
											<span class="text_head">{$club_news[n].news_name}</span>
											{if $club.user_is_leader eq 1}
											<span style="padding-left: 20px;"><a href="#" onclick="ShowNewsEditDiv({$club_news[n].id});">{$lang.button.edit}</a>&nbsp;|&nbsp;
											<a href="{$club_news[n].del_link}">{$lang.button.delete}</a></span>
											{/if}
										</td>
									</tr>
									<tr>
										<td><font class="text_hidden">{$club_news[n].creation_date}</font></td>
									</tr>
									<tr>
										<td><font class="text">{$club_news[n].news_text}</font></td>
									</tr>
									<tr>
										<td>
											<div style="display: none;" id="news_edit_div_{$club_news[n].id}">
												<form action="{$file_name}" method="POST" style="padding: 10px 0px 10px 0px; margin: 0px;">
												<input type="hidden" name="sel" value="save_edited_news">
												<input type="hidden" name="id_new" value="{$club_news[n].id}">
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td height="30" class="text_head">{$lang.club.news_name}:&nbsp;</td>
														<td height="30"><input type="text" value="{$club_news[n].news_name}" name="news_name" id="news_name" maxlength="500"></td>
													</tr>
													<tr>
														<td class="text_head">{$lang.club.news_text}:&nbsp;</td>
														<td><textarea name="news_text" id="news_text" style="width: 300px;" rows="7">{$club_news[n].news_text}</textarea></td>
													</tr>
													<tr>
														<td></td>
														<td style="padding-top: 5px;"><input type="submit" value="{$lang.button.save}"></td>
													</tr>
												</table>
												</form>
											</div>
											<div style="height: 1px;  margin: 5px 12px 5px 0px;" class="delimiter"></div>
										</td>
									</tr>
									{/section}
								</table>
							</div>
						</td>
					</tr>
					{if $club.link_more_news}
					<tr>
						<td style="padding-left: 10px; padding-bottom: 10px;"><a href="{$club.link_more_news}">{$lang.club.all_news}</a></td>
					</tr>
					{/if}
					</table>
					{/if}
				</td>
			</tr>
			</table>
		</td>
	</tr>
	{/if}
	</table>
{literal}
<script type="text/javascript">
function ShowUploadForm() {
	if (document.getElementById('upload_form_div').style.display == 'none'){
		document.getElementById('upload_form_div').style.display = 'inline';
	} else {
		document.getElementById('upload_form_div').style.display = 'none';
	}
	return false;
}
function ShowNewsForm() {
		if (document.getElementById('news_form_div').style.display == 'none'){
		document.getElementById('news_form_div').style.display = 'inline';
	} else {
		document.getElementById('news_form_div').style.display = 'none';
	}
	return false;
}

function JoinBtnFunc() {
	if (document.getElementById('agree').checked) {
		document.getElementById('btn_join').disabled = false;
	} else {
		document.getElementById('btn_join').disabled = true;
	}
	return false;
}
function ShowNewsEditDiv(id_new) {
	if (document.getElementById('news_edit_div_'+id_new).style.display == 'none') {
		document.getElementById('news_edit_div_'+id_new).style.display = 'inline';
	} else {
		document.getElementById('news_edit_div_'+id_new).style.display = 'none';
	}
	return false;
}
function ShowClubIconDiv() {
	if (document.getElementById('club_icon_div').style.display == 'none') {
		document.getElementById('club_icon_div').style.display = 'inline';
	} else {
		document.getElementById('club_icon_div').style.display = 'none';
	}
	return false;
}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}