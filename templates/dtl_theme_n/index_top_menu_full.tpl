{strip}
{if $registered}
	{if !$auth.is_applicant}
		<div id="main-navigation">
			<div>
				<ul>
				{if $auth.is_trial || $auth.is_regular_inactive || $auth.is_platinum_inactive}
					{assign var=menu_width value="16%"}
				{elseif $smarty.session.permissions.platinum}
					{assign var=menu_width value="16%"}
				{else}
					{assign var=menu_width value="20%"}
				{/if}
				{* MY PROFILE *}
				<li {* style="width:{$menu_width}px" *} class="menu_first {if $sub_menu_num == 1}menu_block_1_active{else}menu_block_1{/if}">
					<div id="menu_block_1" class="" onmouseover="ChangeTopMenu('1');" onclick="window.location.href='{$site_root}/homepage.php';">
						{$lang.index_top_big_1}
					</div>
				</li>
				{* MY COMMUNICATION *}
				<li {* style="width:{$menu_width}px" *} class="menu_inner {if $sub_menu_num == 2}menu_block_2_active{else}menu_block_2{/if}">
					<div id="menu_block_2" class="" onmouseover="ChangeTopMenu('2');" onclick="window.location.href='{$site_root}/connections.php';">
						{$lang.index_top_big_2}
					</div>
				</li>
				{* MY SEARCHES *}
				<li {* style="width:{$menu_width}px" *} class="menu_inner {if $sub_menu_num == 3}menu_block_3_active{else}menu_block_3{/if}">
					<div id="menu_block_3" class="" onmouseover="ChangeTopMenu('3');" onclick="window.location.href='{$site_root}/quick_search.php';">
						{$lang.index_top_big_3}
					</div>
				</li>
				{* MY STORE *}
				<li {* style="width:{$menu_width}px" *} class="menu_inner {if $sub_menu_num == 4}menu_block_4_active{else}menu_block_4{/if}">
					<div id="menu_block_4" class="" onmouseover="ChangeTopMenu('4');" onclick="window.location.href='{$site_root}/giftshop.php?new=Y';">
						{$lang.index_top_big_4}
					</div>
				</li>
				{* RENEW OR UPGRADE MEMBERSHIP *}
				{if $auth.is_trial || $auth.is_regular_inactive || $auth.is_platinum_inactive || $smarty.session.permissions.platinum}
					<li {* style="width:{$menu_width}px" *} class="menu_inner m-upgrade {if $sub_menu_num == 5}menu_block_5_active{else}menu_block_5{/if}">
						{*
							{if $auth.is_regular}
								{assign var=upgrade_link value="/platinum.php"}
							{else}
								{assign var=upgrade_link value="/payment.php"}
							{/if}
						*}
						{assign var=upgrade_link value="/payment.php"}
						<div id="menu_block_5" class="" onmouseover="ChangeTopMenu('5');" onclick="window.location.href='{$site_root}{$upgrade_link}';">
							{$lang.index_top_big_5}
						</div>
					</li>
				{/if}
				{* DATING EVENTS *}
				<li {* style="width:{$menu_width}px" *} class="menu_last {if $sub_menu_num == 6}menu_block_6_active{else}menu_block_6{/if}">
					<div id="menu_block_6" class="" onmouseover="ChangeTopMenu('6');" onclick="window.location.href='{$site_root}/dating_events.php';">
						{$lang.index_top_big_6}
					</div>
				</li>
				</ul>
			</div>
		</div>
	{/if}
	{if $auth.is_applicant}
		<div>
			<a class="menu_block_1_link" href="myprofile.php">{$lang.top_menu.registration_home}</a>
			{if $smarty.session.permissions.account}
				&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;
				<a class="menu_block_1_link" href="account.php">{$lang.top_menu.my_account}</a>
				&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;
				<a class="menu_block_1_link" href="account.php?sel=passw&amp;from=myprofile">{$lang.account.subheader_changepass}</a>
				{* <!--
				&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;
				<a class="menu_block_2_link" href="tell_friend.php">{$lang.tell_a_friend.top_header}</a>
				<div class="fright"><a class="menu_block_1_link" href="account.php?sel=passw&amp;from=myprofile">{$lang.account.subheader_changepass}</a></div>
				<div style="clear"></div>
				--> *}
			{/if}
		</div>
	{else}
		<div id="sub_menu_div_1" class="sub_menu" style="display:{if $sub_menu_num == 1 || $sub_menu_num == 9}block{else}none{/if};">
			{assign var='sep' value=false}
			{* HOME *}
			{if !$auth.is_applicant}
				{* <!-- Comment code on 1/2/2012
				<a class="menu_block_1_link" href="{$site_root}/homepage.php">{$lang.home}</a>
				{assign var='sep' value=true}
				--> *}
			{/if}
			{* MY PROFILE *}
			{* <!--
			{if $smarty.session.permissions.myprofile}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_1_link" href="{$site_root}/myprofile.php">{$lang.top_menu.my_profile}</a>
				{assign var='sep' value=true}
			{/if}
			--> *}
			{* MY ACCOUNT *}
			{* <!--
			{if $smarty.session.permissions.account}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_1_link" href="{$site_root}/account.php">{$lang.top_menu.my_account}</a>
				{assign var='sep' value=true}
			{/if}
			--> *}
			{* MY MULTIMEDIA ALBUM *}
			{* <!--
			{if $smarty.session.permissions.my_multimedia_album}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_1_link" href="{$site_root}/myprofile.php?sel=4">{$lang.top_menu.my_multimedia_album}</a>
				{assign var='sep' value=true}
			{/if}
			--> *}
			{* MY PHOTO GALLERY *}
			{* <!--
			{if $smarty.session.permissions.my_multimedia_album}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_1_link" href="{$site_root}/myprofile.php?sel=4">{$lang.top_menu.my_photo_gallery}</a>
				{assign var='sep' value=true}
			{/if}
			--> *}
			{* MY VIDEO GALLERY *}
			{* <!--
			{if $smarty.session.permissions.my_multimedia_album}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_1_link" href="{$site_root}/myprofile.php?sel=4&amp;sub=10">{$lang.top_menu.my_video_gallery}</a>
				{assign var='sep' value=true}
			{/if}
			--> *}
			{* ORGANIZER *}
			{if $use_pilot_module_organizer && $smarty.session.permissions.organizer}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_1_link" href="{$site_root}/organizer.php">{$lang.modules.organizer.name}</a>
				{assign var='sep' value=true}
			{/if}
		</div>
		{* permissions added by ralf *}
		<div id="sub_menu_div_2" class="sub_menu" style="display:{if $sub_menu_num == 2}block{else}none{/if};">
			{assign var='sep' value=false}
			{* MY CONNECTIONS *}
			{if $smarty.session.permissions.hotlist}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/connections.php">{$lang.top_menu.my_connections}</a>
				{assign var='sep' value=true}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/hotlist.php">{$lang.top_menu.my_hotlist}</a>
				{assign var='sep' value=true}
			{/if}
			{* MY EMAIL *}
			{if $smarty.session.permissions.mailbox}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/mailbox.php">{$lang.top_menu.my_email}</a>
				{assign var='sep' value=true}
			{/if}
			{* MY ECARDS *}
			{if $smarty.session.permissions.ecards}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/ecards.php">{$lang.top_menu.my_ecards}</a>
				{assign var='sep' value=true}
			{/if}
			{* BLOG *}
			{if $use_pilot_module_blog && $smarty.session.permissions.blog}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/blog.php">{$lang.blog.blog_menu_1}</a>
				{assign var='sep' value=true}
			{/if}
			{* MY SHOUT BOX *}
			{* <!--
			{if $smarty.session.permissions.shoutbox}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/shoutbox.php">{$lang.top_menu.my_shout_box}</a>
				{assign var='sep' value=true}
			{/if}
			--> *}
			{* BLACK LIST *}
			{if $smarty.session.permissions.hotlist}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/blacklist.php">{$lang.top_menu.my_blacklist}</a>
				{assign var='sep' value=true}
			{/if}
			{* REPORT A VIOLATION *}
			{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
			<a class="menu_block_1_link" href="{$site_root}/report_a_violation.php">{$lang.top_menu.report_a_violation}</a>
			{assign var='sep' value=true}
			{* FORUM *}
			{if $use_pilot_module_forum && $smarty.session.permissions.forum}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/forum.php">{$left_menu.forum}</a>
				{assign var='sep' value=true}
			{/if}
			{* CLUB *}
			{if $use_pilot_module_club && $smarty.session.permissions.club}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/club.php">{$left_menu.clubs}</a>
				{assign var='sep' value=true}
			{/if}
			{* TELL A FRIEND *}
			{* <!--
				{if $use_pilot_module_forum}
					{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
					<a class="menu_block_2_link" href="{$site_root}/tell_friend.php">{$lang.tell_a_friend.top_header}</a>
					{assign var='sep' value=true}
				{/if}
			--> *}
			{* VOICE OVER IP *}
			{if $voipcall_feature && $smarty.session.permissions.voip_call}
				&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;
				<a class="menu_block_2_link" href="{$site_root}/voip_call.php">{$left_menu.voip_call}</a>
				{assign var='sep' value=true}
			{/if}
			{* CHAT *}
			{if $smarty.session.permissions.chat}
				{if $use_pilot_module_flashchat}
					&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;
					<a class="menu_block_2_link" href="{$site_root}/flash_chat/flashchat.php" onClick="javascript: window.open('{$site_root}/flash_chat/flashchat.php','flash_chat','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;">flash{$left_menu.chat}</a>
					{assign var='sep' value=true}
				{elseif $use_pilot_module_webchat}
					<a class="menu_block_2_link" href="{$site_root}/videochat/vchat.php" onClick="javascript: window.open('{$site_root}/videochat/vchat.php','video_chat','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;">web{$left_menu.chat}</a>
					{assign var='sep' value=true}
				{/if}
			{/if}
			{* INSTANT MESSENGER *}
			{* <!-- Comment code on 1/2/2012
			{if $smarty.session.permissions.im}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/w_communicator/flash_im.php" target="_blank" onClick="open_im_window_userplane(); return false;">{$left_menu.im}</a>
				{assign var='sep' value=true}
				<!-- for testing purpose, so both IM and webmessenger can be evaluated -->
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/w_communicator/flash_im.php" target="_blank" onClick="open_im_window_userplane(); return false;">Webcam</a>
				{assign var='sep' value=true}
			{/if}
			--> *}
			{* VIDEO CHAT *}
			{* <!--
			{if $smarty.session.permissions.video_chat}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_2">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_2_link" href="{$site_root}/videochat/vchat.php" onlick="window.open('{$site_root}/videochat/vchat.php', 'video_chat', 'menubar=0,resizable=1,scrollbars=0,status=0,toolbar=0,width=800,height=600');return false;">{$lang.top_menu.video_chat}</a>
				{assign var='sep' value=true}
			{/if}
			--> *}
		</div>
		<div id="sub_menu_div_3" class="sub_menu" style="display:{if $sub_menu_num == 3}block{else}none{/if};">
			{assign var='sep' value=false}
			{* QUICK SEARCH *}
			{if $smarty.session.permissions.quick_search}
				<a class="menu_block_3_link" href="{$site_root}/quick_search.php">{$lang.top_menu.q_search}</a>
				{assign var='sep' value=true}
			{/if}
			{* ADVANCED SEARCH *}
			{if $smarty.session.permissions.advanced_search}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_3">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_3_link" href="{$site_root}/advanced_search.php">{$lang.section.a_search}</a>
				{assign var='sep' value=true}
			{/if}
			{* PERFECT MATCH *}
			{if $smarty.session.permissions.perfect_match}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_3">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_3_link" href="{$site_root}/perfect_match.php">{$lang.top_menu.perfect_match}</a>
				{assign var='sep' value=true}
			{/if}
			{* KEYWORD SEARCH *}
			{* <!--
			{if $smarty.session.permissions.advanced_search}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_3">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_3_link" href="{$site_root}/quick_search.php">{$lang.top_menu.keywords_search}</a>
			{/if}
			--> *}
		</div>
		<div id="sub_menu_div_4" class="sub_menu" style="display:{if $sub_menu_num == 4}block{else}none{/if};">
			{assign var='sep' value=false}
			{* MY STORE *}
			{if $smarty.session.permissions.giftshop}
				<a class="menu_block_4_link" href="{$site_root}/giftshop.php?new=Y">{$lang.top_menu.giftshop}</a>
				{assign var='sep' value=true}
			{/if}
			<font class="menu_delimiter_4" style="visibility:hidden;">|</font>
		</div>
		<div id="sub_menu_div_5" class="sub_menu" style="display:{if $sub_menu_num == 5}block{else}none{/if};">
			{assign var='sep' value=false}
			{* APPLY FOR PLATINUM, UPGRADE OR RENEW *}
			{if $auth.is_trial || $auth.is_regular_inactive || $auth.is_platinum_inactive || $smarty.session.permissions.platinum}
				{if !$auth.is_regular}
					{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_5">|</font>&nbsp;&nbsp;&nbsp;{/if}
					<a class="menu_block_5_link" href="{$site_root}/payment.php">{$lang.top_menu.upgrade_membership}</a>
					{assign var='sep' value=true}
				{/if}
				{if $smarty.session.permissions.platinum}
					{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_5">|</font>&nbsp;&nbsp;&nbsp;{/if}
					<a class="menu_block_5_link" href="{$site_root}/platinum.php">{$lang.top_menu.apply_platinum}</a>
				{/if}
			{/if}
			<font class="menu_delimiter_5" style="visibility:hidden;">|</font>
		</div>
		<div id="sub_menu_div_6" class="sub_menu" style="display:{if $sub_menu_num == 6}block{else}none{/if};">
			{*
			{assign var='sep' value=false}
			<a class="menu_block_6_link" href="{$site_root}/request_info.php">{$lang.top_menu.request_info_pack}</a>
			{assign var='sep' value=true}
			{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_6">|</font>&nbsp;&nbsp;&nbsp;{/if}
			<a class="menu_block_6_link" href="{$site_root}/dating_events.php">{$lang.top_menu.dating_events}</a>
			{assign var='sep' value=true}
			{if $smarty.session.permissions.events_booking}
				{if $sep}&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_6">|</font>&nbsp;&nbsp;&nbsp;{/if}
				<a class="menu_block_6_link" href="{$site_root}/events_booking.php">{$lang.top_menu.events_booking}</a>
				{assign var='sep' value=true}
			{/if}
			*}
		</div>
	{/if}
{/if}
{/strip}