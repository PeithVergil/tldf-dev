{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="150" align="center">
							<img src="{$data.icon_path}" alt="{$data.nick}">
						</td>
						<td width="380" align="center">
							<span class="mtext" style="font-size:26px; color:#9b234f;">{$data.subject}</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>{$header.generic_e.hello} <b>{$data.fname}</b>,<br><br>
				{$data.message}<br><br></span>
			</td>
		</tr>
		<tr>
			<td align="center">
				<a href="{$data.fromlink}" target="_blank"><img src="{$data.fromicon}" style="border:#7b426b 2px solid;" alt="{$data.sender_fname}"></a><br>
				<a href="{$data.fromlink}" target="_blank">{$header.generic_e.view_profile}</a><br><br>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext">{$header.accepted_e.message_sub}<br><br><br>
				{$header.generic_e.admin_regards}<br><br>
				{$header.generic_e.company_info}</span>
			</td>
		</tr>
		<tr>
			<td>
				<div style="background:#cdecfb; margin-top:10px; padding:3px; text-align:center;">
					<span class="mtext" style="font-size:18px;">{$header.generic_e.what_next_head}</span>
				</div>
				<div style="padding:10px 0;">
					<span class="mtext">{$header.generic_e.what_next_text}</span>
					<div align="center">
						<a href="{$data.urls.login}"><img src="{$data.server.img_root}/login_now.png" alt="LOGIN NOW"></a>
					</div>
					<div style="background:#fcfbdf; margin:5px auto 0 auto; width:250px; padding:5px; text-align:center;">
						<span class="mtext">{$header.generic_e.your_login_is}: {$data.login}</span>
					</div>
				</div>
				<div style="background:#cdecfb; margin-top:10px; padding:3px; text-align:center;">
					<span class="mtext" style="font-size:18px;">{$header.generic_e.want_meet_head}</span>
				</div>
				<div style="padding:10px 0;">
					<span class="mtext">{$header.generic_e.want_meet_text}</span>
				</div>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_eng.tpl"}