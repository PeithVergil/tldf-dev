{include file="$admingentemplates/admin_top.tpl"}
<font class="red_header">{if $section_active eq 4}{$lang.uploads.razdel_name}{else}{$header.razdel_name}{/if}</font>
{foreach name=s item=section key=key from=$sections}
	{if $section.sel}
		<font class=red_sub_header>&nbsp;|&nbsp;{$section.name}</font>
		{if $key != 8}
			<div class="help_text"><span class="help_title">{$lang.help}:</span>{$section.help}</div>
		{else}
			<br><br>
		{/if}
	{/if}
{/foreach}
<table cellSpacing="0" cellPadding="0" border="0" width="100%" >
	{if $section_active eq 1 || $section_active eq 2 || $section_active eq 5 || $section_active eq 7 || $section_active eq 9}
		<form name="sections_form" action="{$form.action}" method="post">
			<tr bgcolor="#FFFFFF">
				<td height="200%" colspan="2" align="left" class="main_content_text"><b>{$header.section}:</b>&nbsp;&nbsp;&nbsp; <a href="admin_settings.php?section=1">{$lang.settings.section_name.1}</a>&nbsp;&nbsp;&nbsp; <a href="admin_settings.php?section=2">{$lang.settings.section_name.2}</a>&nbsp;&nbsp;&nbsp; <a href="admin_settings.php?section=5">{$lang.settings.section_name.5}</a>&nbsp;&nbsp;&nbsp; <a href="admin_settings.php?section=7">{$lang.settings.section_name.7}</a>&nbsp;&nbsp;&nbsp;
					{if $use_pilot_module_webchat}<a href="admin_settings.php?section=8">{$lang.settings.section_name.8}</a>&nbsp;&nbsp;&nbsp;{/if}
					{* if $use_pilot_module_webmessenger *}<a href="admin_settings.php?section=10">{$lang.settings.section_name.10}</a>&nbsp;&nbsp;&nbsp;{* /if *} <a href="admin_settings.php?section=9">{$lang.settings.section_name.9}</a>&nbsp;&nbsp;&nbsp; <br>
					<br>
				</td>
			</tr>
		</form>
	{/if}
	<tr bgcolor="#FFFFFF">
		<td height="200%" colspan="2" align="left">
			<div align="left">
				<table border=0 cellspacing=1 cellpadding=5>
				{if $section_active == 1}
					<form name="email_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="email">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="1">
						<tr bgcolor="#FFFFFF">
							<td align="right"  nowrap class="main_header_text">{$header.email}:&nbsp;</td>
							<td class="main_content_text" align="left" >
								<input type="text" name="email" value="{$data.email}" size=30>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="button" value="{$button.save}" class="button" onclick="javascript:document.email_form.submit();">
							</td>
						</tr>
					</form>
				{elseif $section_active == 2}
					<form name="aname_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="aname">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="2">
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.login}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="login" value="{$data.login}" size=30>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.fname}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="fname" value="{$data.fname}" size=30>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.sname}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="sname" value="{$data.sname}" size=30>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="button" value="{$button.save}" class="button" onclick="javascript:document.aname_form.submit();">
							</td>
						</tr>
					</form>
					<form name="pass_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="pass">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="2">
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.pass}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="password" name="pass" value="{$data.pass}" size=30>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.repass}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="password" name="repass" value="{$data.repass}" size=30>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.oldpass}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="password" name="oldpass" value="{$data.oldpass}" size=30>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="button" value="{$button.save}" class="button" onclick="javascript:document.pass_form.submit();">
							</td>
						</tr>
					</form>
				{elseif $section_active == 3}
					<form name="lang_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="lang">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="3">
						<tr bgcolor="#FFFFFF" valign=center>
							<td align="right" width="15%" class="main_header_text">{$header.lang}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<select name="def_l" style="width:195">
									{section name=s loop=$language}
									<option value="{$language[s].value}"{if $language[s].sel}selected{/if}><span lang="{$language[s].value}">{$language[s].name}</span></option>
									{/section}
								</select>
							</td>
							<td align="left" width="60%">
								<table border="0" cellSpacing="0" cellPadding="0">
									<tr>
										<td>
											<input type="button" value="{$header.langfile}" class="button" onclick="javascript: lang=document.lang_form.def_l.value; window.open('{$data.langfile_link}'+'&l='+lang,'langfile', 'height=750, resizable=yes, scrollbars=yes,width=750, menubar=no,status=no');">
										</td>
										<td style="padding-left:5px;">
											<input type="button" value="{$header.langmail}" class="button" onclick="javascript: lang=document.lang_form.def_l.value; window.open('{$data.langfile_link}'+'&l='+lang+'&mail','langfile', 'height=750, resizable=yes, scrollbars=yes,width=750, menubar=no,status=no');">
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.visible_lang}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<table border="0" width="100%" height="100%" cellSpacing="0" cellPadding="0">
									{section name=s loop=$language}
										<tr bgcolor="#FFFFFF">
											<td width=5 height="25px">
												<input type=checkbox name="visible[{$smarty.section.s.index}]" value="{$language[s].value}" {if $language[s].visible}checked{/if}>
											</td>
											<td class="main_content_text">&nbsp;{$language[s].name}</td>
											<td height="25px" align="right">
												<input type="button" value="{$button.edit}" class="button" onclick="{literal}javascript:EditLang({/literal}'{$language[s].value}'{literal}){/literal}">
											</td>
											<td height="25px" align="right">
												<input type="button" value="{$lang.lang_ident_feature.edit_countries}" class="button" onclick="javascript: window.open('admin_settings.php?sel=list_ident_countries&id={$language[s].value}','','width=800, height=600, resizable=1, scrollbars=1');">
											</td>
										</tr>
									{/section}
								</table>
							</td>
							<td valign=bottom class="main_header_text" align="left" width="60%">
								<table border="0" cellSpacing="1" cellPadding="0" width="100%" height="100%">
									{section name=s loop=$language}
										<tr valign=center align="center">
											<td class="main_content_text" height="24px">
												<div id="div_lang_name[{$language[s].value}]" style="display: none;"><b>{$header.lang_name}:</b>
													<input type="text" name="lang_name[{$language[s].value}]" value="{$language[s].name}" size="15" onchange="{literal}javascript:CheckEmpty({/literal}'lang_name[{$language[s].value}]'{literal}){/literal}">
												</div>
											</td>
											<td class="main_content_text" height="24px">
												<div id="div_lang_code[{$language[s].value}]" style="display: none;"><b>{$header.lang_code}:</b>
													<input type="text" name="lang_code[{$language[s].value}]" value="{$language[s].code}" size="10" onchange="{literal}javascript:CheckEmpty({/literal}'lang_code[{$language[s].value}]'{literal}){/literal}">
												</div>
											</td>
											<td class="main_content_text" height="24px">
												<div id="div_lang_charset[{$language[s].value}]" style="display: none;"><b>{$header.lang_charset}:</b>
													<input type="text" name="lang_charset[{$language[s].value}]" value="{$language[s].charset}" size="15" readonly>
												</div>
											</td>
										</tr>
									{/section}
								</table>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="button" value="{$button.save}" class="button" onclick="javascript:document.lang_form.submit();">
							</td>
							<td align="left" width="60%">&nbsp;</td>
						</tr>
					</form>
					<form name="lang_add_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="lang_add">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="3">
						<tr bgcolor="#FFFFFF" valign=center>
							<td align="right" width="15%" class="main_header_text">{$header.lang_add}:&nbsp;</td>
							<td class="main_content_text" align="left" colspan="2">
								<table cellSpacing="1" cellPadding="0" width="100%">
									<tr valign=center class="table_main" align="center">
										<td class="main_header_text">{$header.lang_name}</td>
										<td class="main_header_text">{$header.lang_code}</td>
										<td class="main_header_text">{$header.lang_charset}</td>
									</tr>
									<tr valign=center>
										<td class="main_content_text">
											<input type="text" name="name" value="" size="15"> e.g. english
										</td>
										<td class="main_content_text">
											<input type="text" name="code" value="" size="10"> e.g. en-us
										</td>
										<td class="main_content_text">
											<input type="text" name="charset" value="UTF-8" size="15" readonly> UTF-8
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=3 align="right">
								<input type="button" value="{$button.add}" class="button" onclick="javascript: document.lang_add_form.submit();">
							</td>
						</tr>
					</form>
				{elseif $section_active == 4}
					<form name="image_form" action="{$form.action}" method="post"  enctype="multipart/form-data">
						<input type="hidden" name="par" value="upload">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="4">
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.thumb}:&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_width}:&nbsp;
								<input type="text" size="15" name="thumb_width" value="{$data.thumb_max_width}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_height}:&nbsp;
								<input type="text" size="15" name="thumb_height" value="{$data.thumb_max_height}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.icon}:&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_size}:&nbsp;
								<input type="text" size="15" name="icon_size" value="{$data.icon_max_size}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_width}:&nbsp;
								<input type="text" size="15" name="icon_width" value="{$data.icon_max_width}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_height}:&nbsp;
								<input type="text" size="15" name="icon_height" value="{$data.icon_max_height}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.big_icon}:&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_width}:&nbsp;
								<input type="text" size="15" name="big_thumb_max_width" value="{$data.big_thumb_max_width}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_height}:&nbsp;
								<input type="text" size="15" name="big_thumb_max_height" value="{$data.big_thumb_max_height}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.photo}:&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_size}:&nbsp;
								<input type="text" size="15" name="photo_size" value="{$data.photo_max_size}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_width}:&nbsp;
								<input type="text" size="15" name="photo_width" value="{$data.photo_max_width}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_height}:&nbsp;
								<input type="text" size="15" name="photo_height" value="{$data.photo_max_height}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						{if $use_pilot_module_club}
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.club}:&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_size}:&nbsp;
								<input type="text" size="15" name="club_photo_size" value="{$data.club_photo_max_size}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_width}:&nbsp;
								<input type="text" size="15" name="club_photo_width" value="{$data.club_photo_max_width}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_height}:&nbsp;
								<input type="text" size="15" name="club_photo_height" value="{$data.club_photo_max_height}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						{/if}
						{if $data.site_logo_enabled}
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.site_logo}:&nbsp;</td>
							<td class="main_content_text" align="right">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>{$header.insert_text}&nbsp;</td>
										<td>
											<input type="radio" name="site_logo_type" value="1" {if $data.site_logo_type eq 'text'} checked {/if} onclick="SiteLogoChange('1');">
											&nbsp;
										</td>
										<td><img src="{$data.site_logo}" name="pict_text" id="pict_text"></td>
									</tr>
								</table>
							</td>
							<td class="main_header_text" align="left" width="60%">
								<table cellSpacing="0" cellPadding="0">
									<tr>
										<td>
											<select name="font-size" onchange="" id="font-size" {if $data.site_logo_type eq 'image'} disabled {/if}>
												<option value="">font-size</option>
												{include file="$admingentemplates/admin_options_size.tpl"}
											</select>
											&nbsp;</td>
										<td>
											<select name="font-face" onchange="" id="font-face" {if $data.site_logo_type eq 'image'} disabled {/if}>
												<option value="">font-face</option>
												{include file="$admingentemplates/admin_options_face_forlogo.tpl"}
											</select>
											&nbsp;</td>
										<td>
											<input value="{$site_logo_name}" name="site_logo_name" type="text" size="15" id="site_logo_name"  {if $data.site_logo_type eq 'image'} disabled {/if}>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">&nbsp;</td>
							<td class="main_content_text" align="right">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>{$header.upload_image}&nbsp;</td>
										<td>
											<input type="radio" name="site_logo_type" value="2" {if $data.site_logo_type eq 'image'} checked {/if} onclick="SiteLogoChange('2');">
											&nbsp;</td>
										<td><img src="{$data.site_logo}" name="pict_image" id="pict_image"></td>
									</tr>
								</table>
							</td>
							<td class="main_header_text" align="left" width="60%">
								<table cellSpacing="0" cellPadding="0">
									<tr>
										<td>
											<input type="file" name="site_logo_picture" id="site_logo_picture"  {if $data.site_logo_type eq 'text'} disabled {/if}>
										</td>
										<td>&nbsp;<i>{$lang.settings.watermark_hint}</i></td>
									</tr>
								</table>
							</td>
						</tr>
						{/if}
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.icon_default_female}:&nbsp;</td>
							<td class="main_content_text" align="right">{$data.female_icon}</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;
								<input type="file" name="upload_icon_female">
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.icon_default_male}:&nbsp;</td>
							<td class="main_content_text" align="right">{$data.male_icon}</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;
								<input type="file" name="upload_icon_male">
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.upload_default}:&nbsp;</td>
							<td class="main_content_text" align="right">{$data.upload_image}</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;
								<input type="file" name="upload_file">
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.icon_default_adult}:&nbsp;</td>
							<td class="main_content_text" align="right">{$data.adult_icon}</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;
								<input type="file" name="upload_icon_adult">
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.newsletter}:&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_size}:&nbsp;
								<input type="text" size="15" name="newsletter_size" value="{$data.subscrimage_max_size}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_width}:&nbsp;
								<input type="text" size="15" name="newsletter_width" value="{$data.subscrimage_max_width}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_height}:&nbsp;
								<input type="text" size="15" name="newsletter_height" value="{$data.subscrimage_max_height}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.audio}:&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_size}:&nbsp;
								<input type="text" size="15" name="audio_size" value="{$data.audio_max_size}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="15%" class="main_header_text">{$header.video}:&nbsp;</td>
							<td class="main_content_text" align="right">{$header.upload_size}:&nbsp;
								<input type="text" size="15" name="video_size" value="{$data.video_max_size}">
							</td>
							<td class="main_header_text" align="left" width="60%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="button" value="{$button.save}" class="button" onclick="javascript:document.image_form.submit();">
							</td>
							<td align="left">&nbsp;</td>
						</tr>
					</form>
				{elseif $section_active eq 5}
					<form name="db_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="db">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="5">
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.dbhost}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="dbhost" value="{$data.dbhost}" size=40>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.dbuname}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="dbuname" value="{$data.dbuname}" size=40>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.dbname}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="dbname" value="{$data.dbname}" size=40>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.prefix}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="prefix" value="{$data.prefix}" size=40>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="button" value="{$button.save}" class="button" onclick="javascript:document.db_form.submit();">
							</td>
						</tr>
					</form>
					<form name="dbpass_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="dbpass">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="5">
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.dbpass}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="password" name="dbpass" value="" size=40>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.repass}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="password" name="dbrepass" value="" size=40>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.oldpass}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="password" name="dboldpass" value="" size=40>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="button" value="{$button.save}" class="button" onclick="javascript:document.dbpass_form.submit();">
							</td>
						</tr>
					</form>
					<tr bgcolor="#FFFFFF">
						<td colspan=2 align="left" width="350px"><font face="Tahoma" style="font-size: 11px"><b>{$lang.hint}:</b>&nbsp;&nbsp;{$header.backup_comment}</font></td>
					</tr>
					<tr bgcolor="#FFFFFF" height="10">
						<td colspan=2 align="right">
							<input type="button" value="{$header.backup}" class="button" onclick="javascript:  window.open('{$data.backup_link}','basefile', 'height=750, resizable=yes, scrollbars=yes,width=750, menubar=no,status=no');">
						</td>
					</tr>
				{elseif $section_active == 6}
					<form name="other_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="other">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="6">
						<tr bgcolor="#FFFFFF">
							<td align="left" nowrap class="main_content_text" colspan=2>{$header.general_profile}&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.profile_limit}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="text" name="profile_limit" value="{$data.profile_limit}" size=10>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.mail_attaches_limit}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="text" name="mail_attaches_limit" value="{$data.mail_attaches_limit}" size=10>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.min_age_limit}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="text" name="min_age_limit" value="{$data.min_age_limit}" size=10>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.max_age_limit}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="text" name="max_age_limit" value="{$data.max_age_limit}" size=10>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.show_users_name_str}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="show_users_name_str" value="1" {if $data.show_users_name_str}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.show_users_sname_str}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="show_users_sname_str" value="1" {if $data.show_users_sname_str}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.show_users_zipcode_str}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="show_users_zipcode_str" value="1" {if $data.show_users_zipcode_str}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.show_users_birthdate_str}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="show_users_birthdate_str" value="1" {if $data.show_users_birthdate_str}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.show_users_connection_str}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="show_users_connection_str" value="1" {if $data.show_users_connection_str}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.show_users_comments}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="show_users_comments" value="1" {if $data.show_users_comments}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.show_users_group_str}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="show_users_group_str" value="1" {if $data.show_users_group_str}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="left" nowrap class="main_content_text" colspan=2>{$header.general_approvals}&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_icon_approve}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_icon_approve" value="1" {if $data.use_icon_approve}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_photo_approve}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_photo_approve" value="1" {if $data.use_photo_approve}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_audio_approve}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_audio_approve" value="1" {if $data.use_audio_approve}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_video_approve}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_video_approve" value="1" {if $data.use_video_approve}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_registration_confirmation}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_registration_confirmation" value="1" {if $data.use_registration_confirmation}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_registration_approve}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_registration_approve" value="1" {if $data.use_registration_approve}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="left" nowrap class="main_content_text" colspan=2>{$header.general_site}&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_image_resize}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_image_resize" value="1" {if $data.use_image_resize}checked{/if} {if $data.use_image_resize_disabled}disabled{/if} >
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_embedded_audio}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_embedded_audio" value="1" {if $data.use_embedded_audio}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_photo_logo}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_photo_logo" value="1" {if $data.use_photo_logo}checked{/if} {if $data.use_photo_logo_disabled}disabled{/if} >
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_horoscope_feature}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_horoscope_feature" value="1" {if $data.use_horoscope_feature}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_shoutbox_feature}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_shoutbox_feature" value="1" {if $data.use_shoutbox_feature}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_success_stories}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_success_stories" value="1" {if $data.use_success_stories}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_kiss_types}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_kiss_types" value="1" {if $data.use_kiss_types}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_friend_types}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_friend_types" value="1" {if $data.use_friend_types}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_refer_friend}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input id="use_refer_friend_feature_id" type="checkbox" name="use_refer_friend_feature" value="1" {if $data.use_refer_friend_feature}checked{/if} onclick="changeReferFriendPriceIdStatus(this.checked);">
								&nbsp;
								<input id="refer_friend_price_id" type="text" name="refer_friend_price" value="{$data.refer_friend_price}" size="3" {if !$data.use_refer_friend_feature}disabled{/if} />
								&nbsp;{$data.site_unit_costunit}&nbsp;{$lang.refer_friend.foreach}
								<script type="text/javascript">
								{literal}
								function changeReferFriendPriceIdStatus(checked) {
									if (checked) {
										document.getElementById('refer_friend_price_id').disabled = false;
									} else {
										document.getElementById('refer_friend_price_id').disabled = true;
									}
								}
								{/literal}
								</script>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_hide_profile_feature}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_hide_profile_feature" value="1" {if $data.use_hide_profile_feature}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_freetrial_membership}:&nbsp;</td>
							<td class="main_content_text" align="center" valign="middle">
								<table cellspacing=2 cellpadding=0>
									<tr>
										<td>
											<input type="checkbox" name="use_freetrial_membership" value="1" {if $data.use_freetrial_membership}checked{/if} onclick="javascript: if (freetrial_period.disabled) freetrial_amount.disabled = false, freetrial_period.disabled = false; else freetrial_amount.disabled = true, freetrial_period.disabled = true;">
										</td>
										<td>
											<input type="text" name="freetrial_amount" size="4" maxlength="4" value="{$data.freetrial_amount}" {if !$data.use_freetrial_membership}disabled{/if}>
										</td>
										<td>
											<select name="freetrial_period" {if !$data.use_freetrial_membership}disabled{/if}>
												{foreach key=value item=name from=$data.freetrial_periods}
													<option value="{$value}" {if $data.freetrial_period eq $value}selected{/if}>{$name}</option>
												{/foreach}
											</select>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.use_gender_membership}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_gender_membership" value="1" {if $data.use_gender_membership}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.use_lift_up_in_search_service}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_lift_up_in_search_service" value="1" {if $data.use_lift_up_in_search_service}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.use_user_banners_feature}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="user_banners_feature" value="1" {if $data.user_banners_feature}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.use_lang_ident_feature}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="lang_ident_feature" value="1" {if $data.lang_ident_feature}checked{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.use_voipcall_feature}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="voipcall_feature" value="1" {if $data.voipcall_feature}checked{/if}>
							</td>
						</tr>
						{* new settings *}
						<tr bgcolor="#FFFFFF">
							<td align="left" nowrap="nowrap" class="main_content_text" colspan="2">Custom Settings</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap="nowrap" class="main_header_text">Use credits for membership payment:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="use_credits_for_membership_payment" value="1" {if $data.use_credits_for_membership_payment}checked="checked"{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" class="main_header_text">Admin must approve offline payment before user can hit Verify button:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="must_approve_payment_before_verify" value="1" {if $data.must_approve_payment_before_verify}checked="checked"{/if}>
							</td>
						</tr>
						{* end new settings *}
						{*
							<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.show_online_users_str}:&nbsp;</td>
							<td class="main_content_text" align="center"><input type="checkbox" name="show_online_users_str" value="1" {if $data.show_online_users_str}checked{/if}></td>
							</tr>
						*}
						<tr bgcolor="#FFFFFF">
							<td align="left" nowrap class="main_content_text" colspan=2>{$header.general_misc}&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.featured_users_slider_speed}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="text" name="featured_users_slider_speed" value="{$data.featured_users_slider_speed}" size=10>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.zip_code_count}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="text" name="zip_count" value="{$data.zip_count}" size=10>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.zip_code_simvols}:&nbsp;</td>
							<td class="main_content_text" align="center">
								<input type="checkbox" name="zip_letters" value="1" {if $data.zip_letters}checked{/if}>
							</td>
						</tr>
						<!-- map application id -->
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.google_maps_api_key}:&nbsp;<br>
								<div style=" text-align: justify; font-weight: 100;"><i>{$header.google_maps_hint} <a href='http://www.google.com/apis/maps/signup.html' target='_blank'>Google</a></i></div>
							</td>
							<td class="main_content_text" align="center" valign=top>
								<input type="radio" name="map_radio" value="google" {if $data.map_type eq 'google'} checked {/if} onclick="MapChange(this.value);">
								<input type="text" name="google_app_id" id="google_app_id" value="{$data.google_app_id}" {if $data.map_type eq 'yahoo'} disabled {/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text" width="350">{$header.application_id}:&nbsp;<br>
								<div style=" text-align: justify; font-weight: 100;"><i>{$header.application_id_hint} <a href='http://api.search.yahoo.com/webservices/register_application' target='_blank'>Yahoo</a></i></div>
							</td>
							<td class="main_content_text" align="center" valign=top>
								<input type="radio" name="map_radio" value="yahoo" {if $data.map_type eq 'yahoo'} checked {/if} onclick="MapChange(this.value);">
								<input type="text" name="map_app_id" id="map_app_id" value="{$data.map_app_id}"  {if $data.map_type eq 'google'} disabled {/if}>
							</td>
						</tr>
						<!--// end map application id -->
						<tr bgcolor="#FFFFFF" valign=top>
							<td align="right" nowrap class="main_header_text">{$header.date_format}:&nbsp;<br>
								<div style=" text-align: justify; font-weight: 100;"><i>{$header.date_format_hint}</i></div>
							</td>
							<td class="main_content_text" align="center">
								<input type="text" name="date_format" value="{$data.date_format}" onkeyup="javascript: UpdateDate(this);" id=date_format>
								<br>
								<table>
									<tr>
										<td class="main_content_text"><i>{$header.date_example}:</i></td>
										<td class="main_content_text"><i>
											<div id="date_example">{$data.date_format_example}</div>
										</i></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_header_text">{$header.use_ffmpeg}:&nbsp;</td>
							<td class="main_header_text" align="left">
								<input type="checkbox" name="use_ffmpeg" value="1" {if $data.use_ffmpeg == 1}checked="checked"{/if}>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" nowrap class="main_content_text">{$header.path_to_ffmpeg}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" size="15" name="path_to_ffmpeg" value="{$data.path_to_ffmpeg}">
								&nbsp;<i>{$header.path_to_ffmpeg_example}</i>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_output_dimension}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" size="15" name="flv_output_dimension" value="{$data.flv_output_dimension}">
								&nbsp;<i>{$header.flv_output_dimension_example}</i>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_output_preset}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<select name="flv_output_preset">
									<option value="placebo" {if $data.flv_output_preset == 'placebo'}selected="selected"{/if}>placebo</option>
									<option value="veryslow" {if $data.flv_output_preset == 'veryslow'}selected="selected"{/if}>veryslow</option>
									<option value="slower" {if $data.flv_output_preset == 'slower'}selected="selected"{/if}>slower</option>
									<option value="slow" {if $data.flv_output_preset == 'slow'}selected="selected"{/if}>slow</option>
									<option value="medium" {if $data.flv_output_preset == 'medium'}selected="selected"{/if}>medium</option>
									<option value="fast" {if $data.flv_output_preset == 'fast'}selected="selected"{/if}>fast</option>
									<option value="faster" {if $data.flv_output_preset == 'faster'}selected="selected"{/if}>faster</option>
									<option value="veryfast" {if $data.flv_output_preset == 'veryfast'}selected="selected"{/if}>veryfast</option>
									<option value="superfast" {if $data.flv_output_preset == 'superfast'}selected="selected"{/if}>superfast</option>
									<option value="ultrafast" {if $data.flv_output_preset == 'ultrafast'}selected="selected"{/if}>ultrafast</option>
								</select>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_output_profile}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<select name="flv_output_profile">
									<option value="baseline" {if $data.flv_output_profile == 'baseline'}selected="selected"{/if}>baseline</option>
									<option value="main" {if $data.flv_output_profile == 'main'}selected="selected"{/if}>main</option>
									<option value="high" {if $data.flv_output_profile == 'high'}selected="selected"{/if}>high</option>
									<option value="high10" {if $data.flv_output_profile == 'high10'}selected="selected"{/if}>high10</option>
									<option value="high422" {if $data.flv_output_profile == 'high422'}selected="selected"{/if}>high422</option>
									<option value="high444" {if $data.flv_output_profile == 'high444'}selected="selected"{/if}>high444</option>
								</select>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_output_fps}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" size="15" name="flv_output_fps" value="{$data.flv_output_fps}">
								&nbsp;<i>{$header.flv_output_fps_example}</i>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_output_gop}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" size="15" name="flv_output_gop" value="{$data.flv_output_gop}">
								&nbsp;<i>{$header.flv_output_gop_example}</i>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_output_video_bit_rate}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" size="15" name="flv_output_video_bit_rate" value="{$data.flv_output_video_bit_rate}">
								&nbsp;<i>{$header.flv_output_video_bit_rate_example}</i>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_output_audio_sampling_rate}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" size="15" name="flv_output_audio_sampling_rate" value="{$data.flv_output_audio_sampling_rate}">
								&nbsp;<i>{$header.flv_output_audio_sampling_rate_example}</i>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_output_audio_bit_rate}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" size="15" name="flv_output_audio_bit_rate" value="{$data.flv_output_audio_bit_rate}">
								&nbsp;<i>{$header.flv_output_audio_bit_rate_example}</i>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_output_foto_dimension}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" size="15" name="flv_output_foto_dimension" value="{$data.flv_output_foto_dimension}">
								&nbsp;<i>{$header.flv_output_foto_dimension_example}</i>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_grab_photo_at_second}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" size="15" name="flv_grab_photo_at_second" value="{$data.flv_grab_photo_at_second}">
								&nbsp;<i>{$header.flv_grab_photo_at_second_example}</i>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_player_width}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" size="15" name="flv_player_width" value="{$data.flv_player_width}">
								&nbsp;<i>{$header.flv_player_width_example}</i>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td class="main_content_text" align="right">{$header.flv_player_height}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" size="15" name="flv_player_height" value="{$data.flv_player_height}">
								&nbsp;<i>{$header.flv_player_height_example}</i>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="button" value="{$button.save}" class="button" onclick="javascript:document.other_form.submit();">
							</td>
						</tr>
					</form>
				{elseif $section_active == 7}
					<form name="color_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="color">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="7">
						{*
							<tr bgcolor="#FFFFFF"><td colspan=3><div class="help_text"><i>{$header.template_hint}</i></div></td></tr>
						*}
						<tr bgcolor="#FFFFFF">
							<td align="right" width="17%" class="main_header_text">{$header.color_theme}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<select name=color_theme style="width: 250px">
								{foreach item=item from=$color_themes}
									<option value="{$item.name}" {if $item.sel eq 1}selected{/if}>{$item.title}</option>
								{/foreach}
								</select>
							</td>
							<td class="main_header_text" align="left" width="65%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="submit" value="{$button.save}" class="button">
							</td>
							<td align="left">&nbsp;</td>
						</tr>
					</form>
					<form name="template_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="template">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="7">
						<tr bgcolor="#FFFFFF">
							<td colspan=3>
								<div class="help_text"><i>{$header.template_hint}</i></div>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="17%" class="main_header_text">{$header.site_template}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<select name=template style="width: 250px">		
								{section name=s loop=$templates}
									<option value="{$templates[s].value}" {if $templates[s].sel eq 1}selected{/if}>{$templates[s].name}</option>
								{/section}
								</select>
							</td>
							<td class="main_header_text" align="left" width="65%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="button" value="{$button.save}" class="button" onclick="javascript:document.template_form.submit();">
							</td>
							<td align="left">&nbsp;</td>
						</tr>
					</form>
					<form name="add_template_form" action="{$form.action}" method="post">
						<input type="hidden" name="sel" value="add_template">
						<input type="hidden" name="section" value="7">
						<tr bgcolor="#FFFFFF">
							<td align="right" width="17%" class="main_header_text">{$header.template_name}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="templ_name" value="{$data.templ_name}" size=45>
							</td>
							<td class="main_header_text" align="left" width="65%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="17%" class="main_header_text">{$header.template_path}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="templ_path" value="{$data.templ_path}" size=45>
							</td>
							<td class="main_content_text" align="left" width="65%">&nbsp;<i>{$header.template_comment}</i></td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="button" value="{$header.add_new_template}" class="button" onclick="javascript:document.add_template_form.submit();">
							</td>
							<td align="left">&nbsp;</td>
						</tr>
					</form>
					<tr bgcolor="#FFFFFF" height="10">
						<td colspan=3>&nbsp;</td>
					</tr>
					<form name="theme_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="theme">
						<input type="hidden" name="theme_tpl" value="{$data.theme_tpl}">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="7">
						<tr bgcolor="#FFFFFF">
							<td colspan=3>
								<div class="help_text"><i>{$header.theme_hint}</i></div>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="17%" class="main_header_text">{$header.site_theme}:&nbsp;</td>
							<td class="main_content_text" align="left">
								{if $themes}
									<select name=theme style="width: 250px">
									{section name=s loop=$themes}
										<option value="{$themes[s].value}" {if $themes[s].sel eq 1}selected{/if}>{$themes[s].name}</option>
									{/section}
									</select>
								{else}
									{$header.no_themes}
								{/if}
							</td>
							<td class="main_header_text" align="left" width="65%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								{if $themes}
									<input type="button" value="{$button.save}" class="button" onclick="javascript:document.theme_form.submit();">
								{else}
									&nbsp;
								{/if}
							</td>
							<td align="left">&nbsp;</td>
						</tr>
					</form>
					<form name="add_theme_form" action="{$form.action}" method="post">
						<input type="hidden" name="sel" value="add_theme">
						<input type="hidden" name="theme_tpl" value="{$data.theme_tpl}">
						<input type="hidden" name="section" value="7">
						<tr bgcolor="#FFFFFF">
							<td align="right" width="17%" class="main_header_text">{$header.theme_name}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="theme_name" value="{$data.theme_name}" size=45>
							</td>
							<td class="main_header_text" align="left" width="65%">&nbsp;</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="17%" class="main_header_text">{$header.theme_css_path}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="theme_css_path" value="{$data.theme_css_path}" size=45>
							</td>
							<td class="main_content_text" align="left" width="65%">&nbsp;<i>{$header.theme_css_comment}</i></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right" width="17%" class="main_header_text">{$header.theme_images_path}:&nbsp;</td>
							<td class="main_content_text" align="left">
								<input type="text" name="theme_images_path" value="{$data.theme_images_path}" size=45>
							</td>
							<td class="main_content_text" align="left" width="65%">&nbsp;<i>{$header.theme_images_comment}</i></td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td colspan=2 align="right">
								<input type="button" value="{$header.add_new_theme}" class="button" onclick="javascript:document.add_theme_form.submit();">
							</td>
							<td align="left">&nbsp;</td>
						</tr>
					</form>
				{elseif $section_active == 8}
					<form name="chat_form" action="{$form.action}" method="post">
						<input type="hidden" name="par" value="chat">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="8">
						<tr bgcolor="#FFFFFF">
							<td class="main_header_text">
								<input type="radio" name="selected_chat" value="flashchat" {if ($data.use_pilot_module_flashchat)}checked{/if}>
								&nbsp;&nbsp;{$header.use_flashchat}<br>
								<br>
								<input type="radio" name="selected_chat" value="webchat" {if (!$data.use_pilot_module_flashchat)}checked{/if}>
								&nbsp;&nbsp;{$header.use_webchat}
							</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td align="right">
								<input type="button" value="{$button.save}" class="button" onclick="javascript:document.chat_form.submit();">
							</td>
						</tr>
					</form>
				{elseif $section_active == 9}
					<tr>
						<td colspan="3">
							<form name="images_form" action="{$form.action}" method="post" enctype="multipart/form-data">
								<input type="hidden" name="par" value="site_images">
								<input type="hidden" name="sel" value="change">
								<input type="hidden" name="section" value="9">
								<table width="100%" cellpadding="5" cellspacing="1">
									<tr bgcolor="#FFFFFF">
										<td align="right" nowrap class="main_header_text">{$header.site_top_logo}:&nbsp;</td>
										<td class="main_content_text" align="left">
											<input type="file" name="site_logotype">
										</td>
										<td class="main_header_text" align="left">
											<input type="checkbox" name="restore_logotype" value="1">
											&nbsp;{$header.restore_logotype}
										</td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td align="right" nowrap class="main_header_text">{$header.site_logotype_width}:&nbsp;</td>
										<td class="main_content_text" align="left">
											<input type="text" name="site_logotype_width" value="{$logo_setup.site_logotype_width}" size="15">
										</td>
										<td class="main_header_text" align="left" width="65%">&nbsp;</td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td align="right" nowrap class="main_header_text">{$header.site_logotype_height}:&nbsp;</td>
										<td class="main_content_text" align="left">
											<input type="text" name="site_logotype_height" value="{$logo_setup.site_logotype_height}" size="15">
										</td>
										<td class="main_header_text" align="left" width="65%">&nbsp;</td>
									</tr>
									{if $logo_setup.site_top_logotype != ''}
										<tr>
											<td colspan="3">
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td style="border: 1px solid silver; padding: 1px;">
															{if $logo_setup.site_logotype_format == 'image'}
																<a href="{$site_root}/index.php"><img src="{$site_root}{$logo_setup.site_top_logotype}" border="0" alt="" width="{$logo_setup.site_logotype_width}" height="{$logo_setup.site_logotype_height}"></a>
															{else}
																<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="780" height="233" type="application/x-shockwave-flash">
																	<param name="movie" value="{$site_root}{$logo_setup.site_top_logotype}" />
																	<param name="quality" value="high" />
																	<param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" />
																	<embed src="{$site_root}{$logo_setup.site_top_logotype}" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="{$logo_setup.site_logotype_width}" height="{$logo_setup.site_logotype_height}"></embed>
																</object>
															{/if}
														</td>
													</tr>
												</table>
											</td>
										</tr>
									{/if}
									{*
										<!-- SITE BANNER NOT IN USE ON TLDF -->
										<tr bgcolor="#FFFFFF">
											<td align="right" nowrap class="main_header_text">{$header.site_banner}:&nbsp;</td>
											<td class="main_content_text" align="left">
												<input type="file" name="site_banner">
											</td>
											<td class="main_header_text" align="left" width="65%">
												<input type="checkbox" name="restore_banner" value="1">
												&nbsp;{$header.restore_banner}
											</td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td align="right" nowrap class="main_header_text">{$header.site_banner_width}:&nbsp;</td>
											<td class="main_content_text" align="left">
												<input type="text" name="site_banner_width" value="{$logo_setup.site_banner_width}" size="15">
											</td>
											<td class="main_header_text" align="left" width="65%">&nbsp;</td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td align="right" nowrap class="main_header_text">{$header.site_banner_height}:&nbsp;</td>
											<td class="main_content_text" align="left">
												<input type="text" name="site_banner_height" value="{$logo_setup.site_banner_height}" size="15">
											</td>
											<td class="main_header_text" align="left" width="65%">&nbsp;</td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td align="right" nowrap class="main_header_text">{$header.site_banner_color}:&nbsp;</td>
											<td class="main_content_text" align="left"> #&nbsp;
												<input type="text" name="site_banner_color" value="{$logo_setup.site_banner_color}" size="15" maxlength="6">
											</td>
											<td>
												<div style=" text-align: justify; font-weight: 100;"><i>{$lang.settings.site_banner_color_hint}</i></div>
											</td>
										</tr>
										{if $logo_setup.site_banner != ''}
											<tr>
												<td colspan="3">
													<table cellpadding="0" cellspacing="0">
														<tr>
															<td style="border: 1px solid silver; padding: 1px;">
																{if $logo_setup.site_banner_format == 'image'}
																	<img src="{$site_root}{$logo_setup.site_banner}" border="0" alt="" width="{$logo_setup.site_banner_width}" height="{$logo_setup.site_banner_height}">
																{else}
																	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="780" height="233" type="application/x-shockwave-flash">
																		<param name="movie" value="{$site_root}{$logo_setup.site_banner}" />
																		<param name="quality" value="high" />
																		<param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" />
																		<embed src="{$site_root}{$logo_setup.site_banner}" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="{$logo_setup.site_banner_width}" height="{$logo_setup.site_banner_height}"></embed>
																	</object>
																{/if}
															</td>
														</tr>
													</table>
												</td>
											</tr>
										{/if}
									*}
									<tr bgcolor="#FFFFFF" height="10">
										<td colspan=2 align="right">
											<input type="submit" value="{$button.save}" class="button">
										</td>
										<td>&nbsp;</td>
									</tr>
								</table>
							</form>
						</td>
					</tr>
				{elseif $section_active == 10}
					<form action="{$form.action}" method="post">
						<input type="hidden" name="par" value="messenger">
						<input type="hidden" name="sel" value="change">
						<input type="hidden" name="section" value="10">
						<tr bgcolor="#FFFFFF">
							<td class="main_header_text">
								<input type="radio" name="selected_messenger" value="flashim" {if (!($data.use_pilot_module_webmessenger))}checked{/if}>
								&nbsp;&nbsp;{$header.use_flashim}<br>
								<br>
								<input type="radio" name="selected_messenger" value="webim" {if ($data.use_pilot_module_webmessenger)}checked{/if}>
								&nbsp;&nbsp;{$header.use_webim}
							</td>
						</tr>
						<tr bgcolor="#FFFFFF" height="10">
							<td align="right">
								<input type="submit" value="{$button.save}" class="button">
							</td>
						</tr>
					</form>
				{/if}
				</table>
			</div>
		</td>
	</tr>
</table>
<script>
	{literal}
	function UpdateDate(obj){
		var str = obj.value;
		var expr = new Array('Y', 'y', 'd', 'e', 'm', 'c');
		var newDateObj = new Date();
		var day = newDateObj.getDate();
		day = day.toString( );
		var month = newDateObj.getMonth();
		month = month.toString( );
		var year = newDateObj.getYear();
		year = year.toString( );
		var rep = new Array(year, year.substr(2), ZeroFormat(day, 2), day, ZeroFormat(month, 2), month);
		for (i in expr) {
			while (str.lastIndexOf(expr[i]) >= 0) {
				str = str.replace(expr[i], rep[i]);
			}
		}
		var date_div = document.getElementById('date_example');
		date_div.innerHTML = str;
		return;
	}
	
	function ZeroFormat(str, num) {
		var len = str.length;
		num = num - len;
		for (var i=0; i < num; i++) {
			str = '0' + str;
		}
		return str;
	}
	
	//UpdateDate(document.getElementById('date_format'));
	
	function EditLang (id){
		document.all['div_lang_name['+id+']'].style.display = '';
		document.all['div_lang_code['+id+']'].style.display = '';
		document.all['div_lang_charset['+id+']'].style.display = '';
	}
	
	function CheckEmpty (name){
		if(document.lang_form[name].value ==""){
				alert({/literal}"{$lang.err.empty_language}"{literal});
				return false;
			}
	}

	function SiteLogoChange(section){
		if (section == '1') {
			document.getElementById('site_logo_name').disabled = false;
			document.getElementById('font-size').disabled = false;
			document.getElementById('font-face').disabled = false;
			document.getElementById('site_logo_picture').disabled = true;
			HighLight('pict_image',1);
			HighLight('pict_text',2);
		} else if (section == '2') {
			document.getElementById('site_logo_name').disabled = true;
			document.getElementById('font-size').disabled = true;
			document.getElementById('font-face').disabled = true;
			document.getElementById('site_logo_picture').disabled = false;
			HighLight('pict_text',1);
			HighLight('pict_image',2);
		}
		return;
	}

	function HighLight(section, state) {
		if (document.getElementById(section)) {
			if (state == 1) {
				setElementOpacity(section, 0.4);
			} else {
				setElementOpacity(section, 1.0);
			}
		}
		return;
	}

	function setElementOpacity(sElemId, nOpacity) {
		var opacityProp = getOpacityProperty();
		var elem = document.getElementById(sElemId);
		
		if (!elem || !opacityProp) return; // error
		
		if (opacityProp == "filter") {
			// Internet Explorer 5.5+
			nOpacity *= 100;
			var oAlpha = elem.filters['DXImageTransform.Microsoft.alpha'] || elem.filters.alpha;
			if (oAlpha)
				oAlpha.opacity = nOpacity;
			else
				elem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+nOpacity+")";
		} else {
			elem.style[opacityProp] = nOpacity;
		}
	}

	function getOpacityProperty() {
		if (typeof document.body.style.opacity == 'string') {
			// CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9, IE7)
			return 'opacity';
		} else if (typeof document.body.style.MozOpacity == 'string') {
			// Mozilla 1.6, Firefox 0.8
			return 'MozOpacity';
		} else if (typeof document.body.style.KhtmlOpacity == 'string') {
			// Konqueror 3.1, Safari 1.1
			return 'KhtmlOpacity';
		} else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) {
			// Internet Explorer 5.5+
			return 'filter'
		} else {
			return false;
		}
	}
	
	{/literal}
	{if $section_active == 4 && $data.site_logo_enabled}
		{if $data.site_logo_type == 'text'}
			HighLight('pict_image',1);
		{else}
			HighLight('pict_text',1);
		{/if}
	{/if}
	{literal}
	
	function MapChange(map_id) {
		if (map_id == 'google') {
			document.getElementById('google_app_id').disabled = false;
			document.getElementById('map_app_id').disabled = true;
		} else if (map_id == 'yahoo') {
			document.getElementById('google_app_id').disabled = true;
			document.getElementById('map_app_id').disabled = false;
		}
		return;
	}
{/literal}
</script>
{include file="$admingentemplates/admin_bottom.tpl"}