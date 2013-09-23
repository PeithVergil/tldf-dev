<table cellpadding="0" cellspacing="2" border="0">
{if $data[0] == 'true'}
<tr>
	<td colspan="2">&nbsp;</td>
	<td height="40" align="right" id="section_1" style="border: 1px solid #CCCCCC;" bgcolor="#{$css_color.home_menu}" class="home_menu">
	<div style="padding-left: 5px; padding-right: 5px; text-align: left;">
		<div>1</div>
		<div><font class="index_top_menu">{$lang.organizer.section_name[0]}</font></div>
	</div>
	</td>
</tr>
{/if}
<tr>
	{if $data[1] == 'true' || $data[2] == 'true'}
	<td width="70" valign="top">
		<table cellpadding="3" cellspacing="1">
			{if $form.use_shoutbox_feature ne 0}
			{if $data[1] == 'true'}
			<tr>
				<td valign="top" height="147" width="70" id="section_2" style="border: 1px solid #CCCCCC;" bgcolor="#{$css_color.home_menu}" class="home_menu"><div>&nbsp;2</div><div style="padding-left: 5px;"><font class="index_top_menu">{$lang.organizer.section_name[1]}</font></div></td>
			</tr>
			{/if}
			{/if}
			{if $data[2] == 'true'}
			<tr>
				<td valign="top" height="112" width="70" id="section_3" style="border: 1px solid #CCCCCC;"><div>&nbsp;3</div><div style="margin: 0px 5px 5px 5px; height: 102px; padding: 5px;" class="content"><font style="color: #{$css_color.header};"><b>{$lang.organizer.section_name[2]}</b></font></div></td>
			</tr>
			{/if}
		</table>
	</td>
	{/if}
	{if $data[3] == 'true' || $data[4] == 'true' || $data[5] == 'true' || $data[6] == 'true'}
	<td width="210" valign="top">
		<table cellpadding="3" cellspacing="1" border="0">
			{if $data[3] == 'true'}
			<tr>
				<td valign="top" height="167" width="210" id="section_4" style="border: 1px solid #CCCCCC;"><div>&nbsp;4</div><div style="margin: 0px 5px 5px 5px; height: 157px; padding: 5px;" class="content"><font style="color: #{$css_color.header};"><b>{$lang.organizer.section_name[3]}</b></font></div></td>
			</tr>
			{/if}
			{if $data[4] == 'true'}
			<tr>
				<td valign="top" height="98" width="210" id="section_5" style="border: 1px solid #CCCCCC;"><div>&nbsp;5</div><div style="margin: 0px 5px 5px 5px; height: 88px; padding: 5px;" class="content"><font style="color: #{$css_color.header};"><b>{$lang.organizer.section_name[4]}</b></font></div></td>
			</tr>
			{/if}
			{if $data[5] == 'true'}
			<tr>
				<td valign="top" height="37" width="210" id="section_6" style="border: 1px solid #CCCCCC;"><div>&nbsp;6</div><div style="margin: 0px 5px 5px 5px; height: 27px; padding: 5px;" class="content"><font style="color: #{$css_color.header};"><b>{$lang.organizer.section_name[5]}</b></font></div></td>
			</tr>
			{/if}
			{if $data[6] == 'true'}
			<tr>
				<td valign="top" height="84" width="210" id="section_7" style="border: 1px solid #CCCCCC;"><div>&nbsp;7</div><div style="margin: 0px 5px 5px 5px; height: 74px; padding: 5px;" class="content"><font style="color: #{$css_color.header};"><b>{$lang.organizer.section_name[6]}</b></font></div></td>
			</tr>
			{/if}
		</table>
	</td>
	{/if}
	<td width="140" valign="top">
		<table cellpadding="3" cellspacing="1" border="0">
			{if $data[7] == 'true'}
			<tr>
				<td valign="top" height="336" width="140" id="section_8" style="border: 1px solid #CCCCCC;" bgcolor="#{$css_color.home_search}"><div>&nbsp;8</div><div style="padding-left: 5px;"><font style="color: #{$css_color.header};"><b>{$lang.organizer.section_name[7]}</b></font></div></td>
			</tr>
			{/if}
			{if $data[8] == 'true'}
			<tr>
				<td valign="top" height="60" width="140" id="section_9" style="border: 1px solid #CCCCCC;"><div>&nbsp;9</div><div style="margin: 0px 5px 5px 5px; height: 50px; padding: 5px;" class="content"><font style="color: #{$css_color.header};"><b>{$lang.organizer.section_name[8]}</b></font></div></td>
			</tr>
			{/if}
		</table>
	</td>
</tr>
</table>