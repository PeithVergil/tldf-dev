{if $form.type eq 'main_page'}
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>{$lang.admin_title_main}{$header.subtitle}</title>
		<link href="{$site_root}{$template_root}/css/style.css" rel="stylesheet" type="text/css">
	</head>
	<frameset framespacing=0 name="mainframe" id="mainframe" rows="93%,7%" border=0 scrolling=yes>
		<frameset  framespacing=0 name="mainframe" id="mainframe" cols="40%,60%" border=0 scrolling=yes>
			<frame src="{$form.frame_link_1}" name="frame1" id="frame1" frameborder="0" noresize scrolling=yes>
			<frame src="{$form.frame_link_2}" name="frame2" id="frame2" frameborder="0" noresize scrolling=yes>
		</frameset>
		<frame src="{$form.frame_link_3}" name="frame3" id="frame3" frameborder="0" noresize scrolling=no>
	</frameset>
{elseif $form.type eq 'frame_buttons'}
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
		<table width="100%">
			<tr height="40">
				<td width="40%">&nbsp;</td>
				<td width="60%" style="padding-left:20px">
					<input type="button" value="{$button.close}" class="button" onclick="parent.window.close();parent.opener.focus();">
				</td>
			</tr>
		</table>
	</body>
	</html>
{elseif $form.type eq 'frame_read'}
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$form.charset}">
		<title>{$lang.admin_title_main}{$header.subtitle}</title>
		<link href="{$site_root}{$template_root}/css/style.css" rel="stylesheet" type="text/css">
	</head>
	{literal}
	<style>
	body {
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
								<tr>
									<td width="14%"><img src="{$site_root}{$template_root}/images/icon_yf.gif" border="0"></td>
									<td align="left" width="86%" class=red_sub_header>{$form.language}</td>
								</tr>
								<tr>
									<td colspan="2" style="padding-top: 9px">
										<div align="center">
											<table class="table_main" cellspacing=1 cellpadding=5 width="100%">
												<tr bgcolor="#FFFFFF">
													<td class="main_header_text">
														{$data.name}
													</td>
												</tr>
												<tr bgcolor="#FFFFFF">
													<td class="forumbodytext">
														{$data.content}
													</td>
												</tr>
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
{elseif $form.type eq 'frame_save'}
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$form.charset}">
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
		<tr>
			<td width="100%" height="100%" class="white_block">
				<table width="100%" border=0 cellspacing="3px" cellpadding="10px" height=100%>
					<tr>
						<td width="100%" class="blue_block" valign="top">
							<font class="text">
							<table width="100%">
								<tr>
									<td width="14%">
										<img src="{$site_root}{$template_root}/images/icon_yf.gif" border="0">
									</td>
									<td align="left" width="86%" class=red_sub_header>
										{$form.language}
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<div align="center">
										<form name=lang_save action="{$form.action}" method=post onSubmit="return CheckChanges()" ENCTYPE="multipart/form-data" style="margin-top:5px">
											{$form.hidden}
											<table class="table_main" cellspacing=1 cellpadding=5 width="100%" height="580px">
												{if !($data.id_info==1 && $table_key==1)}
													<tr bgcolor="#FFFFFF" height="20px">
														<td>
															<input type="text" name="name" value="{$data.name}" size="40">
														</td>
													</tr>
												{/if}
												<tr bgcolor="#FFFFFF">
													<td class="forumbodytext">
														{if RICH_TEXT_EDITOR == 'SPAW-1' || RICH_TEXT_EDITOR == 'SPAW-2'}
															{$editor}
														{elseif RICH_TEXT_EDITOR == 'TINYMCE'}
															<script type="text/javascript" src="{$site_root}/javascript/tiny_mce-3.5.8/tiny_mce.js"></script>
															<script type="text/javascript">
															tinyMCE.init({ldelim}
																mode : "textareas",
																oninit: myInit,
																{include file="$admingentemplates/admin_tiny_mce_slim.tpl"}
															{rdelim});
															function myInit() {ldelim}
																tinyMCE.get('tinymce_code').setContent('{$data.content|escape:javascript}');
															{rdelim}
															</script>
															<textarea name="code" id="tinymce_code" rows="20" cols="60" style="width:500px;height:400px;"></textarea>
														{else}
															<textarea name="code" rows="20" cols="60" style="width:500px;height:400px;">{$data.content}</textarea>
														{/if}
													</td>
												</tr>
											</table>
											<div align="left" style="margin-top: 3px;">
												<input type="submit" value="{$button.save}" class="button">
											</div>
										</form>
										</div>
									</td>
								</tr>
							</table>
							</font>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</center>
	{literal}
	<script>
	function CheckChanges() {
		bp = document.forms['lang_save'];
		if (bp.name.value == "") {
			alert({/literal}"{$lang.err.invalid_fields} {$lang.addition.name}"{literal});
			return false;
		}
		return true;
	}
	</script>
	{/literal}
	</body>
	</html>
{/if}