<!doctype html>
<html lang="{$default_lang}">
<head>
<meta charset="{$charset}">
<meta name="google-site-verification" content="c6DPqKAWOPU1EmSXqY4QFrQhaB9SjJvrZw3Kfyr8bjk">
<meta name="Description" content="{$lang.description}">
<meta name="Keywords" content="{$lang.keywords}">
<title>{$lang.main_title}</title>
<link rel="shortcut icon" href="{$site_root}{$template_root}/images/favicon.ico" type="image/x-icon">
{*
<link rel="stylesheet" type="text/css" href="{$site_root}/css.php{if $customised == 1 && $id_customed > 0}?customised=1&amp;user_id={$id_customed}{/if}">
<link rel="stylesheet" type="text/css" href="{$site_root}/javascript/greybox/greybox.css" media="all">
*}
<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/tldf_style.css?v=0001" media="all">
{*
<link rel="stylesheet" type="text/css" href="{$site_root}/javascript/colorbox-1.4.3/colorbox.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$site_root}/javascript/uploadify-3.2/uploadify.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/jquery.tooltip.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/popup.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/jquery.alerts.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/accordion.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/bx_styles.css" media="screen">
*}
<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/easySlider.css" media="only screen and (max-width: 1020px)" />
{*
	NOT IN USE, PROBABLY REPLACED WITH bxSlider
	<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/galleria.css" media="screen">
*}
{if $tldf_offline}
	<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/jquery-ui-1.8.21.css">
{else}
	<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css">
{/if}
{*<!-- UNCOMMENT FOR TESTING EXPERIMENTAL FEATURES WHICH SHOULD NOT BE ACTIVE ON THE LIVE SITE -->*}
{*<!--
{if $smarty.const.IS_DEV_SERVER}
	<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/tldf_style_dev.css" media="all">
{/if}
-->*}
{if $tldf_offline}
	<script type="text/javascript" src="{$site_root}/javascript/jquery-1.7.2.min.js"></script>
{else}
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
{/if}
<script type="text/javascript" src="{$site_root}/javascript/jslibrary.js?v=0001"></script>{* social networking javascript library *}
{*
<script type="text/javascript" src="{$site_root}/javascript/swfobject-2.2/swfobject.js"></script><!-- upload progress flash detection, included in uploadify -->
<script type="text/javascript" src="{$site_root}/javascript/location.js?v=0000"></script><!-- cascading location selection, load multimedia tabs with ajax -->
<script type="text/javascript" src="{$site_root}/javascript/greybox/AmiJS.js?v=0000"></script><!-- greybox dependency, still used on some inactive pages -->
<script type="text/javascript" src="{$site_root}/javascript/greybox/greybox.js?v=0000"></script><!-- still used on some inactive pages -->
*}
{if $script}
	<script type="text/javascript" src="{$site_root}{$template_root}/js/{$script}.js"></script>
{/if}
{*
<script type="text/javascript" src="{$site_root}/javascript/jquery.tooltip.js?v=0000"></script><!-- general purpose tooltip -->
<script type="text/javascript" src="{$site_root}/javascript/easySlider1.7.js?v=0000"></script><!-- TLDE page -->
<script type="text/javascript" src="{$site_root}/javascript/alertr.js?v=0000"></script><!-- registration form alerts -->
<script type="text/javascript" src="{$site_root}/javascript/jquery.alerts.js?v=0000"></script><!-- general purpose alerts and confirm dialog -->
<script type="text/javascript" src="{$site_root}/javascript/popup.js?v=0000"></script><!-- success popup in express_interest_table.tpl, platinum_table.tpl -->
<script type="text/javascript" src="{$site_root}/javascript/jquery.bxSlider.js?v=0000"></script><!-- photo and video slider on profile page -->
<script type="text/javascript" src="{$site_root}/javascript/jquery.msAccordion.js?v=0000"></script><!-- How it works button -->
<script type="text/javascript" src="{$site_root}/javascript/jquery.nailthumb.1.1.js?v=0000"></script><!-- image fix on profile page 
<script type="text/javascript" src="{$site_root}/javascript/uploadify-3.2/jquery.uploadify.min.js"></script><!-- upload progress non HTML 5 -->
<script type="text/javascript" src="{$site_root}/javascript/colorbox-1.4.3/jquery.colorbox-min.js"></script><!-- general purpose lightbox -->
*}
<script type="text/javascript" src="{$site_root}/include/mp3player/ufo.js?v=0000"></script>
{*
	NOT IN USE, PROBABLY REPLACED WITH bxSlider
	<script type="text/javascript" src="{$site_root}/javascript/jquery.galleria.pack.js?v=0000"></script>
*}
<script type="text/javascript">
	var SITE_ROOT = "{$site_root}";
	var GB_IMG_DIR = "{$site_root}/javascript/greybox/";
	var autostart = false;
</script>
{$online_notice}
{*
<!-- w_communicator not in use -->
<script type="text/javascript" src="{$site_root}/w_communicator/cookies.js?v=0000"></script>
<script type="text/javascript">
	function open_im_window(user_id)
	{ldelim}
	{if $use_pilot_module_webmessenger}
		var default_width_right = 200;
		var default_width = 600;
		var default_height = 500;
		var default_pos_x = 500;
		var default_pos_y = 20;
		var min_width_right = 190;
		var min_width = 560;
		var min_height = 450;
		
		var saved_pos_x = GetCookie("flash_im_pos_x");
		var saved_pos_y = GetCookie("flash_im_pos_y");
		var saved_width = GetCookie("flash_im_width1");
		var saved_height = GetCookie("flash_im_height");
		
		if (saved_pos_x == null)  saved_pos_x = default_pos_x;
		if (saved_pos_y == null)  saved_pos_y = default_pos_y;
		if (saved_width == null)  saved_width = default_width_right;
		if (saved_height == null) saved_height = default_height;
		
		saved_width = parseInt("" + saved_width);
		saved_height = parseInt("" + saved_height);
		
		if (saved_width < min_width_right) saved_width = min_width_right;
		if (saved_height < min_height)     saved_height = min_height;
		
		var open_param = 'menubar=0,resizable=1,scrollbars=0,status=1,toolbar=0,width='+saved_width+',height='+saved_height;
		
		if ((saved_pos_x != null) && (saved_pos_y != null)) {ldelim}
			if (window.screenLeft != undefined) {ldelim}
				open_param += ',left='+saved_pos_x+',top='+saved_pos_y;
			{rdelim} else {ldelim}
				open_param += ',screenX='+saved_pos_x+',screenY='+saved_pos_y;
			{rdelim}
			alert(open_param);
			if (user_id == undefined) {ldelim}
				window.open('{$site_root}/w_communicator/flash_im.php', 'IM', open_param);
			{rdelim} else {ldelim}
				window.open('{$site_root}/w_communicator/flash_im.php?send_user='+user_id, 'IM', open_param);
			{rdelim}
		{rdelim}
		return false;
	{/if}
	{rdelim}
</script>
*}
{* GOOGLE ANALYTICS TAG REPLACED WITH GOOGLE TAG MANAGER
{if $smarty.const.IS_LIVE_SERVER}
{literal}
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-15603421-2']);
_gaq.push(['_trackPageview']);
(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
{/literal}
{/if}
*}
</head>
{php}flush();{/php}
<body {if $form.view == 'video'} onload="ShowTab(4, '{$data.viewv_link}'); return false;"{/if}{if $script == 'calculator'} onload="on_load();"{/if}>
{if $smarty.const.IS_LIVE_SERVER && $smarty.const.GOOGLE_TAG_MANAGER}
{literal}
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-JD9Z"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-JD9Z');</script>
<!-- End Google Tag Manager -->
{/literal}
{/if}
<!-- Google Analytics Tracking -->
{if $auth.id_user && !$auth.guest && !$auth.root}
	<div>
	{php}
	global $config;
	include $config['site_path'].'/include/tracking.php';
	{/php}
	</div>
{/if}
<!-- Google Analytics Tracking -->
{strip}
<div id="calendar_div" style="position:absolute;visibility:hidden;"></div>
<div id="wrapper">
	<div class="main-section">
		<div id="verytop">
			<div id="header">
				<h2 title="{$lang.logo}">
					<a href="{$site_root}/index.php"><span class="logo">{$lang.logo}</span><em class="slogan">{$lang.slogan}</em></a>
				</h2>
				<div id="user-box" class="inpage">
					{if $registered}
						<p class="tldf-group">
							{if $is_active && $active_user_info.days_remain < 8 && $active_user_info.days_remain > 0}
								{$lang.account.account_period_rest}:&nbsp;
								<span class="warning _lrg">{$active_user_info.days_remain}</span>
							{/if}
							Group : <strong>{$active_user_info.user_group}</strong>
						</p>
						<div class="user-welcome tcxf-ch-la">
							<span>{$lang.homepage.welcome}: <a href="myprofile.php" class="top_menu_1">{$auth.login}</a></span>
							{if $active_user_info.emailed_me_new_count || 1}
								<span><a href="{$site_root}/mailbox.php" class="top_menu_1">
								<img src="{$site_root}{$template_root}/images/newemails.png" alt="" style="vertical-align:middle;">
								&nbsp;&nbsp;{*<!-- <font color="#{$css_color.menu_link_1}"> -->*}{$lang.homepage.new_email}:{*<!-- </font> -->*}&nbsp;
								{$active_user_info.emailed_me_new_count}</a></span>
							{/if}
							{if $active_user_info.show_messages}
								<span><a href="javascript:void(0);" onclick="open_im_window(); return false;" id="imessages_count" class="top_menu_1">
								<img src="{$site_root}{$template_root}/images/newmessages.gif" alt="" style="vertical-align:middle;">
								&nbsp;&nbsp;{*<!-- <font color="#{$css_color.menu_link_3}"> -->*}{$lang.homepage.new_messages}:{*<!-- </font> -->*}&nbsp;
								{$active_user_info.im_me_new_count}</a>
								</span>
							{/if}
							<a class="log-off" href="{$site_root}/index.php?sel=logoff">{$lang.logoff}</a>
						</div>
					{else}
						<div>
							<strong class="login-text tcxf-ch-la"><span>{$lang.already_member}</span><a href="./index.php?sel=login" class="txtlogin">[LOGIN]</a></strong>
						</div>
					{/if}
				</div>
				<div id="topmenu">
					{include file="$gentemplates/index_top_menu.tpl"}
				</div>
			</div>
			<!-- header end -->
		</div>
		<!-- verytop end -->
		<div id="wrap_inn">
			<div class="content">
				<!-- viewed info -->
				{if $viewed_info}
					<div>
						<div class="content">
							{if $viewed_info.img_url}
								<div>
									{if $viewed_info.view_link}<a href="{$viewed_info.view_link}" onClick="{$viewed_info.onclick}"><img src="{$viewed_info.img_url}" class="icon" style="cursor:pointer;" border="0" alt="" onClick=""></a>{else}<img src="{$viewed_info.img_url}" class="icon" border="0" alt="">{/if}
								</div>
							{/if}
							<div>
								{if $viewed_info.login}
									<b>{if $viewed_info.view_link}<a href="{$viewed_info.view_link}">{$viewed_info.login}</a>{else}{$viewed_info.login}{/if} {$lang.registration.is_waiting}</b><br>
									<font class="text">{$viewed_info.country}<br><b>{$viewed_info.age} {$lang.home_page.ans}</b><br>{$viewed_info.photo_count} {$lang.settings.photo}</font>
								{/if}
								{if $viewed_info.user_comment}
									{$viewed_info.user_comment}
								{/if}
							</div>
						</div>
					</div>
				{/if}
				{if $use_pilot_module_organizer == 1 && $hide == 1}
					<div id="slider_wrap">
						<div id="user_slider">
							{include file="$gentemplates/user_slider.tpl"}
						</div>
					</div>
					{if $org_home[1] == 'true' || $org_home[2] == 'true'}
						{if $empty_right_cell == 0}
							{if $org_home[1] == 'true'}
								{*
									{if $registered == '1' && $use_shoutbox_feature == '1' && !$isFreeze}
										<label title="{$lang.title.shoutbox}">
											<div id="shoutbox" style="padding-left:10px; background-color:#{$css_color.shoutbox_color};">
												{include_php file="$site_path/shoutbox/shoutbox.php"}
											</div>
										</label>
									{/if}
									{if $top_users}
										<div style="padding-top: 12px;">
											<div class="content">
												<div class="header">{$lang.home_page.top_members}</div>
												<div style="padding-top: 10px;"><a id="topuser_link" href="{$top_users.link}"><img id="topuser_image" src="{$top_users.icon_path}" class="icon" border="0" alt=""></a></div>
												<div style="padding-top: 7px;"><a href="{$top_users.link}"><b>{$top_users.name}</b></a>&nbsp;, <font class="text">{$top_users.age} {$header.ans}</font></div>
												<div style="padding-top: 7px;" align="right"><a href="quick_search.php?sel=search_top">{$lang.home_page.view_more}</a></div>
											</div>
										</div>
									{/if}
								*}
							{/if}
							{if $org_home[2] == 'true'}
								{*
									{if $poll_bar && $show_poll == '1'}
										<div style="width:140px; margin-top:12px; padding:5px;">
											<table cellpadding="0" cellspacing="0" border="0">
												{$poll_bar}
											</table>
										</div>
									{/if}
									{if ($news && $show_news == '1') && (!$poll_bar || $show_poll != '1')}
										<div class="content" style="margin-top: 13px;">
											<div class="header">{$lang.bottom.news}</div>
											<table cellpadding="0" cellspacing="0" border="0">
												{foreach item=item from=$news}
													<tr>
														<td class="text" style="padding: 5px 0px;">
															<div style="padding-top: 5px;"><font class="text_head">{$item.date}</font></div>
															<div style="padding-top: 5px;"><a href="{$item.link_read}">{$item.text}</a></div>
														</td>
													</tr>
												{/foreach}
											</table>
										</div>
									{/if}
								*}
								{if $banner.left}
									<div>{$banner.left}</div>
								{/if}
							{/if}
						{/if}
					{/if}
				{else}
					{if $empty_right_cell == 0}
						{*
							<!-- shoutbox -->
							{if $registered == '1' && $use_shoutbox_feature == '1'}
								<div id="shoutbox" style="padding:10px; background-color:#{$css_color.shoutbox_color};">
									{include_php file="$site_path/shoutbox/shoutbox.php"}
								</div>
							{/if}
							<!-- top users -->
							{if $top_users}
								<div style="padding-top: 12px;">
									<div class="content">
										<div class="header">{$lang.home_page.top_members}</div>
										<div style="padding-top: 10px;">
											<a id="topuser_link" href="{$top_users.link}"><img id="topuser_image" src="{$top_users.icon_path}" class="icon" border="0" alt=""></a>
										</div>
										<div style="padding-top: 7px;">
											<a href="{$top_users.link}"><b>{$top_users.name}</b></a>&nbsp;, <font class="text">{$top_users.age} {$header.ans}</font>
										</div>
										<div style="padding-top: 7px;" align="right">
											<a href="quick_search.php?sel=search_top">{$lang.home_page.view_more}</a>
										</div>
									</div>
								</div>
							{/if}
							<!-- poll -->
							{if $poll_bar && $show_poll == '1'}
								<div class="content" style="margin-top: 12px;">
									<div style="padding: 10px;">
										<table cellpadding="0" cellspacing="0" border="0">
											{$poll_bar}
										</table>
									</div>
								</div>
							{/if}
							<!-- news -->
							{if ($news && $show_news == '1') && (!$poll_bar || $show_poll != '1')}
								<div class="content">
									<div class="header">{$lang.bottom.news}</div>
									<ol class="br_news">
										{foreach item=item from=$news}
											<li class="text">
												<div style="padding-top: 5px;"><font class="text_head">{$item.date}</font></div>
												<div style="padding-top: 5px;"><a href="{$item.link_read}">{$item.text}</a></div>
											</li>
										{/foreach}
									</ol>
								</div>
							{/if}
							<!-- left banner -->
							{if $banner.left}
								<div style="margin-top: 12px;">
									{$banner.left}
								</div>
							{/if}
						*}
					{/if}
				{/if}
{/strip}