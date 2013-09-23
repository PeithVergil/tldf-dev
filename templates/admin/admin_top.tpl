<!doctype html>
<html lang="{$default_lang}">
<head>
<meta charset="{$charset}">
<title>{$lang.admin_title_main}{$header.subtitle}</title>
<link rel="stylesheet" type="text/css" href="{$site_root}/templates/admin/css/style.css">
<link rel="stylesheet" type="text/css" href="{$site_root}/admin/admin_menu.php?css">
<link rel="stylesheet" type="text/css" href="{$site_root}/javascript/colorbox-1.4.3/colorbox.css" media="screen">
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script type="text/javascript" src="{$site_root}/flash_chat/admin/funcs.js?v=0000"></script>
<script type="text/javascript" src="{$site_root}/templates/admin/js/yahoo.js?v=0000"></script>
<script type="text/javascript" src="{$site_root}/templates/admin/js/treeview.js?v=0000"></script>
<script type="text/javascript" src="{$site_root}/include/mp3player/ufo.js?v=0000"></script>
<script type="text/javascript">
var autostart = false;
</script>
<script type="text/javascript" src="{$site_root}/admin/admin_menu.php?js"></script>
{if $tldf_offline}
	<script type="text/javascript" src="{$site_root}/javascript/jquery-1.7.2.min.js"></script>
{else}
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
{/if}
<script type="text/javascript" src="{$site_root}/javascript/jslibrary.js?v=0001"></script>
<script src="{$site_root}/javascript/jquery-ui-1.8.21.min.js"></script>
{*
<script type="text/javascript" src="{$site_root}/javascript/location.js?v=0000"></script>
<script type="text/javascript" src="{$site_root}/javascript/colorbox-1.4.3/jquery.colorbox-min.js"></script>
*}
{if $script}
	<script type="text/javascript" src="{$site_root}/templates/admin/js/{$script}.js"></script>
{/if}
{if $data.videoplay == 'download'}
	{if $smarty.const.VIDEO_PLAYER_PROGRESSIVE_DOWNLOAD == 'mediaelement-js'}
		<script type="text/javascript" src="{$site_root}/javascript/mediaelement-js-2.10.3/build/mediaelement-and-player.min.js"></script>
		<link href="{$site_root}/javascript/mediaelement-js-2.10.3/build/mediaelementplayer.min.css" rel="stylesheet" type="text/css" />
	{/if}
{elseif $data.videoplay == 'RTMP'}
	{if $smarty.const.VIDEO_PLAYER_RTMP == 'flowplayer_scripted'}
		<script type="text/javascript" src="{$site_root}/javascript/flowplayer-3.2.16/flowplayer-3.2.12.min.js"></script>
	{/if}
{/if}
</head>
<body bgcolor="#FFFFFF">
<!-- Google Analytics Tracking -->
{if $smarty.session.ga_event_code}
	<div>
	{php}
	global $config;
	include $config['site_path'].'/include/tracking.php';
	{/php}
	</div>
{/if}
<!-- Google Analytics Tracking -->
{if !$superlight}
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td width="100%" height="28" style="padding-left:10px; padding-right:10px; background-image:url({$site_root}{$template_root}/images/menu/header_top.gif);">
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<tr>
						<td><img src="{$site_root}{$template_root}/images/menu/site_label.gif" border="0" alt="site_label"></td>
						{* <!--
						{if !$light && $new_version}
							<td align="right">
								<div onClick="document.location.href='http://www.pilotgroup.net/support/';" style="cursor: pointer;">
									<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td height="28" width="6" style="background-image:url({$site_root}{$template_root}/images/ver_left.gif);"></td>
											<td height="28" style="background-image:url({$site_root}{$template_root}/images/ver_center.gif);">
												New version {$new_version} is available. Please contact support team.
											</td>
											<td height="28" width="6" style="background-image:url({$site_root}{$template_root}/images/ver_right.gif);"></td>
										</tr>
									</table>
								</div>
							</td>
						{/if}
						--> *}
					</tr>
				</table>
			</td>
		</tr>
		{if !$light}
			<tr>
				<td width="100%" height="25" style="background-image:url({$site_root}{$template_root}/images/menu/header_bottom.gif);" align="right" class="top_menu">
					<div style="padding-right:10px">
						<table cellpadding="0" cellspacing="0" align="right" border="0">
							<tr>
								{* <!--
								<td width="8" style="padding-right:5px"><a href="http://www.datingpro.com/dating/manuals.php" class="top_menu"><img src="{$site_root}{$template_root}/images/menu/icon_help.gif" border="0" alt="icon_help"></a></td>
								<td style="padding-right:3px"><a href="http://www.datingpro.com/dating/manuals.php" class="top_menu">{$lang.help}</a></td>
								<td width="8" style="padding-left:5px;"><img src="{$site_root}{$template_root}/images/menu/dots.gif" border="0" alt="dots"></td>
								<td width="8" style="padding-right:5px"><a href="http://www.pilotgroup.net/support/" class="top_menu"><img src="{$site_root}{$template_root}/images/menu/icon_support.gif" border="0" alt="icon_support"></a></td>
								<td style="padding-right:3px"><a href="http://www.pilotgroup.net/support/" class="top_menu">{$lang.support}</a></td>
								<td width="8" style="padding-left:5px;"><img src="{$site_root}{$template_root}/images/menu/dots.gif" border="0" alt="dots"></td>
								--> *}
								{if $langs_link}
									<td width="8" style="padding-right:5px">
										<img src="{$site_root}{$template_root}/images/menu/language.gif" border="0" alt="language">
									</td>
									<td style="padding-right:3px">
										<select name="lang_select" onChange="javascript: document.location=this.value;">
										{foreach item=link from=$langs_link}
										<option value="{$link.link}"{if $default_lang == $link.code} selected="selected"{/if}>{$link.name}</option>
										{/foreach}
										</select>
									</td>
									<td width="8" style="padding-left:5px;">
										<img src="{$site_root}{$template_root}/images/menu/dots.gif" border="0" alt="dots">
									</td>
								{/if}
								{* <!--
								<td style="padding-left:3px;"><a href="{$logoff.touser}" class="top_menu"><img src="{$site_root}{$template_root}/images/menu/display.gif" border="0" alt="display"></a></td>
								<td style="padding-right:7px;"><a href="{$logoff.touser}" class="top_menu">&nbsp;&nbsp;{$lang.switch_to_user_mode}</a></td>
								<td width="8" style="padding-right:3px;"><img src="{$site_root}{$template_root}/images/menu/dots.gif" border="0" alt="dots"></td>
								--> *}
								<td>
									<a href="{$logoff.link}" class="top_menu"><img src="{$site_root}{$template_root}/images/menu/log_off.gif" border="0" alt="log_off"></a>
								</td>
								<td>
									<a href="{$logoff.link}" class="top_menu">&nbsp;&nbsp;<b>{$logoff.value}</b></a>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		{/if}
	</table>
{/if}
<table width="100%" cellpadding="0" cellspacing="10">
	<tr>
		{if !$light}
			<td width="200" valign="top">
				<!-- Left panel -->
				<div>
					<div class="expander"><a href="javascript:tree.expandAll()">
						<img src="{$site_root}{$template_root}/images/menu/expand_all.gif" onmouseover="this.src='{$site_root}{$template_root}/images/menu/expand_all_over.gif'" onmouseout="this.src='{$site_root}{$template_root}/images/menu/expand_all.gif'" border="0" alt="Expand All"></a><img src="{$site_root}{$template_root}/images/menu/expand_spacer.gif" alt="expand_spacer"><a href="javascript:tree.collapseAll()"><img src="{$site_root}{$template_root}/images/menu/collapse_all.gif" border="0" onMouseOver="this.src='{$site_root}{$template_root}/images/menu/collapse_all_over.gif'" onMouseOut="this.src='{$site_root}{$template_root}/images/menu/collapse_all.gif'" alt="Collapse All" /></a>
					</div>
					<div id="content" class="content"><div id="treeDiv1"></div></div>
					<div class="expander"><img src="{$site_root}{$template_root}/images/menu/container_bottom.gif" alt="container_bottom"></div>
					<script type="text/javascript">menuInit();</script>
				</div>
			</td>
			<td width="100%" valign="top">
		{else}
			<td colspan="2" width="100%" valign="top">
		{/if}
		{if $form.err || $form.msg || $error || $notice}
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="9"><img src="{$site_root}{$template_root}/images/corner_bw_topleft.gif" width="9" height="9" border="0" alt="corner_bw_topleft"></td>
					<td width="100%" height="8" class="line_bw_top">&nbsp;</td>
					<td width="9"><img src="{$site_root}{$template_root}/images/corner_bw_topright.gif" width="9" height="9" alt="corner_bw_topright"></td>
				</tr>
				<tr>
					<td width="9" class="line_bw_left">&nbsp;</td>
					<td width="100%" style="background-color: yellow; padding: 5px;">
						{if $form.err}
							<font class="error_msg"><b>{$form.err}</b></font>
						{elseif $error}
							<font class="error_msg"><b>{$error}</b></font>
						{elseif $notice}
							<font class="error_msg"><b>{$notice}</b></font>
						{elseif $form.msg}
							<font color="blue"><b>{$form.msg}</b></font>
						{/if}
					</td>
					<td width="9" class="line_bw_right">&nbsp;</td>
				</tr>
				<tr>
					<td width="9"><img src="{$site_root}{$template_root}/images/corner_bw_bottomleft.gif" width="9" height="9" border="0" alt="corner_bw_bottomleft"></td>
					<td width="100%" height="8" class="line_bw_bottom">&nbsp;</td>
					<td width="9"><img src="{$site_root}{$template_root}/images/corner_bw_bottomright.gif" width="9" height="9" alt="corner_bw_bottomright"></td>
				</tr>
			</table>
			<br>
		{/if}
		<!-- Center panel -->
		<table width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="9"><img src="{$site_root}{$template_root}/images/corner_bw_topleft.gif" width="9" height="9" border="0" alt="corner_bw_topleft"></td>
				<td width="100%" height="8" class="line_bw_top">&nbsp;</td>
				<td width="9"><img src="{$site_root}{$template_root}/images/corner_bw_topright.gif" width="9" height="9" alt="corner_bw_topright"></td>
			</tr>
			<tr>
				<td width="9" class="line_bw_left">&nbsp;</td>
				<td width="100%" height="100%" class="white_block">
					{if !$superlight}
						<table width="100%" border="0" cellspacing="3" cellpadding="10">
							<tr>
								<td width="100%" class="blue_block" valign="top">
					{/if}