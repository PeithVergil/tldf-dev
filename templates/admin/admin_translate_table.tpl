{if $form.type eq 'main_page'}
<html>
<head>
	<meta http-equiv="Content-Language" content="{$default_lang}">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>{$lang.admin_title_main}{$header.subtitle}</title>
	<link href="{$site_root}{$template_root}/css/style.css" rel="stylesheet" type="text/css">
</head>
<frameset framespacing=0 name="mainframe" id="mainframe" rows="93%,7%" border=0 scrolling=yes>
	<frameset  framespacing=0 name="mainframe" id="mainframe" cols="50%,50%" border=0 scrolling=yes>
		<frame src="{$form.frame_link_1}" name="frame1" id="frame1" frameborder="0" noresize scrolling=yes>
		<frame src="{$form.frame_link_2}" name="frame2" id="frame2" frameborder="0" noresize scrolling=yes>
	</frameset>
	<frame src="{$form.frame_link_3}" name="frame3" id="frame3" frameborder="0" noresize scrolling=no>
</frameset>
{/if}

{if $form.type eq 'frame_buttons'}
<html>
<head>
	<link href="{$site_root}{$template_root}/css/style.css" rel="stylesheet" type="text/css">
</head>
{literal}
<style>
body{
	scrollbar-3dlight-color: #ffffff;
	scrollbar-arrow-color: #ffffff;
	scrollbar-darkshadow-color: #ffffff;
	scrollbar-face-color: #EDEDE6;
	scrollbar-highlight-color: #ffffff;
	scrollbar-shadow-color: #ffffff;
	scrollbar-track-color: #ffffff;
}
</style>
{/literal}
<body>
<center>
			<table><tr height="40">
			<td><input type="button" value="{$button.save}" class="button" onclick="javascript: parent.frame2.document.lang_save.submit()"></td>
			<td><input type="button" value="{$button.close}" class="button" onclick="javascript:  parent.window.close();parent.opener.focus();"></td>
			</tr></table>
</center>
</body>
</html>

{/if}


{if $form.type eq 'frame_read'}
<html>
<head>
	<meta http-equiv="Content-Language" content="{$form.lang}">
	<meta http-equiv="Content-Type" content="text/html; charset={$form.charset}">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="pragma" content="no-cache">
	<meta name="revisit-after" content="3 days">
	<meta name="robot" content="All">
	<title>{$lang.admin_title_main}{$header.subtitle}</title>
	<link href="{$site_root}{$template_root}/css/style.css" rel="stylesheet" type="text/css">
</head>
{literal}
<style>
body{
	scrollbar-3dlight-color: #ffffff;
	scrollbar-arrow-color: #ffffff;
	scrollbar-darkshadow-color: #ffffff;
	scrollbar-face-color: #EDEDE6;
	scrollbar-highlight-color: #ffffff;
	scrollbar-shadow-color: #ffffff;
	scrollbar-track-color: #ffffff;
}
</style>
{/literal}
<body>
		<center>
		<table width="98%" height="100%" cellspacing="0" cellpadding="0">
			<tr>
			<td width="100%" height="100%" class="white_block">

				<table width="100%" border=0 cellspacing="3px" cellpadding="10px" height=100%>
				<tr>
					<td width="100%" class="blue_block" valign="top"><font class="text">
						<table width="100%">
											<tr><td width="14%"><img src="{$site_root}{$template_root}/images/icon_yf.gif" border="0"></td><td align="left" width="86%" class=red_sub_header>{$form.language}</td></tr>
											<tr>
												<td colspan="2" style="padding-top: 9px">
													<div align="center">
														<table class="table_main" cellspacing=1 cellpadding=5 width="100%">
														{section name=s loop=$lang_arr}
														<tr bgcolor="#FFFFFF">
															<td class="forumbodytext" align="right" width="10">{$smarty.section.s.index_next}</td>
															<td class="forumlittletext">{$lang_arr[s].name}</td>
														</tr>
														{/section}
														</table>
													</div>
												</td>
											</tr>
						</table>
					</font></td>
				</tr>
				</table>
			</td>
			</tr>
		</table>
	</center>
</body>
</html>
{/if}

{if $form.type eq 'frame_save'}
<html>
<head>
	<meta http-equiv="Content-Language" content="{$form.lang}">
	<meta http-equiv="Content-Type" content="text/html; charset={$form.charset}">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="pragma" content="no-cache">
	<meta name="revisit-after" content="3 days">
	<meta name="robot" content="All">
	<title>{$lang.admin_title_main}{$header.subtitle}</title>
	<link href="{$site_root}{$template_root}/css/style.css" rel="stylesheet" type="text/css">
</head>
{if $form.close eq '1'}
<script>
	parent.window.close();
	parent.opener.focus();
</script>
{/if}
{literal}
<style>
body{
	scrollbar-3dlight-color: #ffffff;
	scrollbar-arrow-color: #ffffff;
	scrollbar-darkshadow-color: #ffffff;
	scrollbar-face-color: #EDEDE6;
	scrollbar-highlight-color: #ffffff;
	scrollbar-shadow-color: #ffffff;
	scrollbar-track-color: #ffffff;
}
</style>
{/literal}
<body>
		<center>
		<table width="98%" height="100%" cellspacing="0" cellpadding="0">
			<tr><td width="100%" height="100%" class="white_block">

				<table width="100%" border=0 cellspacing="3px" cellpadding="10px" height=100%>
				<tr>
					<td width="100%" class="blue_block" valign="top"><font class="text">
						<table width="100%">
											<tr><td width="14%"><img src="{$site_root}{$template_root}/images/icon_yf.gif" border="0"></td><td align="left" width="86%" class=red_sub_header>{$form.language}</td></tr>
											<tr>
												<td colspan="2" style="padding-top: 9px">
													<div align="center">
													<form name=lang_save action="{$form.action}" method=post>
													{$form.hidden}
														<table class="table_main" cellspacing=1 cellpadding=5 width="100%">
														{section name=s loop=$lang_arr}
														<tr bgcolor="#FFFFFF">
															<td class="forumbodytext" align="right" width="10">{$smarty.section.s.index_next}</td>
															<td class="forumlittletext"><input type=text name="lang[{$lang_arr[s].id}]" value="{$lang_arr[s].name}" style="width:200"></td>
														</tr>
														{/section}
														</table>
													</form>
													</div>
												</td>
											</tr>
						</table>
					</font></td>
				</tr>
				</table>
			</td></tr>
		</table>
	</center>
</body>
</html>
{/if}