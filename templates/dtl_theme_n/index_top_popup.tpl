<!doctype html>
<html lang="{$default_lang}">
<head>
<meta charset="{$charset}">
<meta http-equiv="expires" content="0">
<meta http-equiv="pragma" content="no-cache">
<meta name="revisit-after" content="3 days">
<meta name="robot" content="All">
<meta name="Description" content="{$lang.description}">
<meta name="Keywords" content="{$lang.keywords}">
<title>{$lang.main_title}</title>
<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/tldf_style.css?v=0001" media="all">
{if $tldf_offline}
	<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/jquery-ui-1.8.21.css">
{else}
	<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css">
{/if}
<style type="text/css">
{literal}
.ui-autocomplete-loading { background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat; }
{/literal}
</style>
{if $tldf_offline}
	<script type="text/javascript" src="{$site_root}/javascript/jquery-1.7.2.min.js"></script>
{else}
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
{/if}
<script type="text/javascript" src="{$site_root}/javascript/jslibrary.js?v=0001"></script>{* social networking javascript library *}
{*
<script type="text/javascript" src="{$site_root}/javascript/location.js?v=0000"></script><!-- cascading location selection, load multimedia tabs with ajax -->
*}
{if $script}
	<script type="text/javascript" src="{$site_root}{$template_root}/js/{$script}.js"></script>
{/if}
{if $music}
	<script type="text/javascript" src="{$site_root}/include/mp3player/ufo.js?v=0000"></script>
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
<body style="background-color:#eee">
<div style="padding: 12px; background-color:#eee">
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr valign="top">
