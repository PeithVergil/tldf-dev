			<table cellpadding=0 cellspacing=0 border=0 width="100%">
			<tr>
				<td class="text">
					<div id="user_info_area" class="content_active"><div style="padding: 0px 10px 0px 10px;">
					<div class="text">{$lang.homepage.welcome}</div>
					<div class="text_head"><a href="{$site_root}/homepage.php"><b>{$auth.login}</b></a></div>
					{if $active_user_info.show_online_str}
						<div style="margin-top: 10px">
						<div class="text">{$lang.homepage.allusers}: {$active_user_info.all}</div>
						<div class="text">{$lang.homepage.activeusers}: {$active_user_info.active}</div>
						</div>
					{/if}
					{if $active_user_info.show_visits}
						<div style="margin-top: 10px">
						<div class="text">{$lang.homepage.visit_my_page}: <a href="{$active_user_info.visitors_link}">{$active_user_info.visit_my_page}</a></div>
						</div>
					{/if}
					{if $active_user_info.show_week_visits ||  $active_user_info.show_month_visits}
						{if $active_user_info.show_week_visits}
							<div class="text">{$lang.homepage.visit_my_page_1}: <a href="{$active_user_info.visitors_link}">{$active_user_info.visit_my_page_1}</a></div>
						{/if}
						{if $active_user_info.show_month_visits}
							<div class="text">{$lang.homepage.visit_my_page_2}: <a href="{$active_user_info.visitors_link}">{$active_user_info.visit_my_page_2}</a></div>
						{/if}
					{/if}
					{if $active_user_info.show_kisses}
						<div style="margin-top: 10px">
						<div class="text">{$lang.homepage.kiss_me}: <a href="{$active_user_info.kisses_link}">{$active_user_info.kiss_me_count}</a></div>
						</div>
					{/if}
					<div style="margin-top: 10px; display:{if $active_user_info.emailed_me_new_count || 1}block{else}none{/if}">
						<div class="text"><table cellpadding=0 cellspacing=0><tr><td><img src="{$site_root}{$template_root}/images/newemails.gif" alt=""></td><td class="text" style="padding-left:10px">{$lang.homepage.new_email}: <a href="{$site_root}/mailbox.php">{$active_user_info.emailed_me_new_count}</a></td></tr></table></div>
					</div>
					<div id="imessages" style="margin-top: 10px; display:{if $active_user_info.show_messages}block{else}none{/if}">
						<div class="text"><table cellpadding=0 cellspacing=0><tr><td><img src="{$site_root}{$template_root}/images/newmessages.gif" alt=""></td><td class="text" style="padding-left:10px">{$lang.homepage.new_messages}: <a href="#" onclick="javascript: open_im_window(); return false;" id="imessages_count">{$active_user_info.im_me_new_count}</a></td></tr></table></div>
					</div>
					</div></div>
				</td>
			</tr>
			</table>
			{*
			<script language="JavaScript" type="text/javascript">
				//Nifty("div#user_info_area");
			</script>
			*}