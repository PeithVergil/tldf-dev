{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header_top.razdel_name}</font>
<font class=red_sub_header>&nbsp;|&nbsp;{$header_top.mails_list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.activity_mails}</div>
<div id="demo_mail_con">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top" align="center" width="220" style="padding-top:100px;">
				<input type="button" class="button" value="{$button.back_to_mails_list}" title="{$back_to_mails_list}" onclick="window.location='{$form.backlink}'" /><br><br>
			</td>
			<td>
				<div style="padding:0px 0px 10px 12px;">
					Subject: <b>{$data.subject}</b><br>
					Template: {$mail_template}<b></b>
				</div>
				{include file="$gentemplates/$mail_template.tpl"}
			</td>
		</tr>
	</table>
</div>