{include file="$gentemplates/index_top_popup.tpl"}
	<!-- central part -->
	<td width="100%" valign=top>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td valign="top" style="margin-top: 5px;">
				{if $logo_setup.site_top_logotype==''}
					<img src="{$site_root}{$template_root}/images/logotype.gif" border="0" alt="">
				{else}
					{if $logo_setup.site_logotype_format=='image'}
					<img src="{$site_root}{$logo_setup.site_top_logotype}" border="0" alt="" width="{$logo_setup.site_logotype_width}" height="{$logo_setup.site_logotype_height}">
					{/if}
				{/if}
				</td>
				<td valign="top" style="padding-top: 15px; padding-right: 40px;" align="right"><a href="#" onclick="javascript: window.print(); return false;"><b>{$lang.profile.print_link_text}</b></a></td>
			</tr>
		</table>
		<div style="width: 100%; margin-top: 5px;"></div>
		<div style="width: 100%; margin-top: 5px">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<!-- photos -->
		<tr valign=middle><td height="25" colspan="2"><div class="header">{$header.print_toptext} {$data_1.login}.</div></td></tr>
		<tr valign="top">
			<td height="30" colspan="2" style="padding-bottom: 10px;" valign="top">
			<!-- icon -->
			{if $form.icon_path}<div style="float: left; margin: 4px"><img src="{$form.icon_path}" border=0 class="icon" alt=""></div>{/if}
			<!-- /icon -->
			</td>
		</tr>
		<!-- /photos -->
		</table>
		</div>
		{include file="$gentemplates/viewprofile_view.tpl"}
		<div style="margin: 10px 10px;">
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td><font class="font_copyright">{$lang.copyright}</font>,&nbsp;<a href="http://www.datingpro.com/dating/" title="online dating software - dating script"><b>{$lang.datingpro}</b></a></td>
		   		<td align="right"><font class="font_powered">Powered by Dating Pro - online dating software</font></td>
			</tr>
		</table>
		</div>
	</td>
	<!-- /central part -->
</tr>
</table>
</div>
</body>
</html>