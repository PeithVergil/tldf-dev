{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="150" align="center">
							<img src="{$data.icon}" alt="{$data.fname}">
						</td>
						<td width="380" align="center">
							<span class="mtext" style="font-size:26px; color:#9b234f;">{$data.subject_2}</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{$mail_generic.hello} <b>{$data.fname}</b>,<br><br>
				{$data.message}<br><br></span>
			</td>
		</tr>
		{if $data.from_icon}
			<tr>
				<td align="center">
					<a href="{$data.from_link}" target="_blank"><img src="{$data.from_icon}" style="border:#7b426b 2px solid;" alt="{$data.from_fname}"></a><br>
					<a href="{$data.from_link}" target="_blank">{$mail_generic.view_profile}</a><br><br>
				</td>
			</tr>
		{/if}
		<tr>
			<td>
				<span class="mtext">
				{if $data.message_sub}
					{$data.message_sub}<br><br>
				{/if}
				{$mail_generic.admin_regards}<br><br>
				{$mail_generic.company_info}</span>
			</td>
		</tr>
		<tr>
			<td>
				<div style="background:#cdecfb; margin-top:10px; padding:3px; text-align:center;">
					<span class="mtext" style="font-size:18px;">{$mail_generic.what_next_head}</span>
				</div>
				<div style="padding:10px 0;">
					<span class="mtext">{$mail_generic.what_next_text}</span>
					<div align="center">
						<a href="{$data.urls.login}"><img src="{$data.server.img_root}/login_now.png" alt="LOGIN NOW"></a>
					</div>
					<div style="background:#fcfbdf; margin:5px auto 0 auto; width:250px; padding:5px; text-align:center;">
						<span class="mtext">{$mail_generic.your_login_is}: {$data.login}</span>
					</div>
				</div>
				<div style="background:#cdecfb; margin-top:10px; padding:3px; text-align:center;">
					<span class="mtext" style="font-size:18px;">{$mail_generic.want_meet_head}</span>
				</div>
				<div style="padding:10px 0;">
					<span class="mtext">{$mail_generic.want_meet_text}</span>
				</div>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_user.tpl"}