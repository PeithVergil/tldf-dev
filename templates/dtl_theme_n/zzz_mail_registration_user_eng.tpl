{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_e.hello} <b>{$data.fname}</b>,<br><br>
				{$header.registration_e.congratulations}<br><br>
				{if $data.confirm_link}
					{$header.registration_e.confirm}:<br><br>
					<a href="{$data.confirm_link}" class="mlink">{$data.confirm_link}</a><br><br>
				{/if}
				{$header.registration_e.welcome}<br><br>
				{$header.registration_e.details}:<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$header.registration_e.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.registration_e.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.registration_e.date_birthday}: &nbsp;</span></td>
						<td><b class="mtext">{$data.date_birthday_formatted}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.registration_e.login}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					{* password can't be sent on re-send because it's stored encrypted *}
					{if $data.pass}
						<tr>
							<td nowrap><span class="mtext">{$header.registration_e.password}: &nbsp;</span></td>
							<td><b class="mtext">{$data.pass}</b></td>
						</tr>
					{/if}
					<tr>
						<td nowrap><span class="mtext">{$header.registration_e.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{$header.registration_e.keep_details}<br><br>
				{*
				<a href="{$data.urls.video_eng}" target="_blank"><img src="{$data.server.img_root}/mail_video1.jpg" alt="{$data.urls.video_eng}" border="0" /></a><br><br>
				*}
				<br>{$header.registration_e.what_next}<br><br>
				{if $data.confirm_link}
					{$header.registration_e.re_confirm}:<br><br>
					<a href="{$data.confirm_link}" class="mlink">{$data.confirm_link}</a><br><br>
				{/if}
				{$header.registration_e.we_look}<br><br><br>
				{$header.generic_e.admin_regards}<br><br>
				{$header.generic_e.company_info}
				{*
				<br><br>{$header.registration_e.ps}
				<br><a href="{$data.freecd_link}" class="mlink">{$data.freecd_link}</a>
				*}
				</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_eng.tpl"}