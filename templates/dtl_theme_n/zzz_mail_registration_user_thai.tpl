{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_t.hello} <b>{$data.fname}</b>,<br><br>
				{$header.registration_t.congratulations}<br><br>
				{if $data.confirm_link}
					{$header.registration_t.confirm}:<br><br>
					<a href="{$data.confirm_link}" class="mlink">{$data.confirm_link}</a><br><br>
				{/if}
				{$header.registration_t.welcome}<br><br>
				{$header.registration_t.details}:<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$header.registration_t.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.registration_t.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.registration_t.date_birthday}: &nbsp;</span></td>
						<td><b class="mtext">{$data.date_birthday_formatted}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.registration_t.login}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					{* password can't be sent on re-send because it's stored encrypted *}
					{if $data.pass}
						<tr>
							<td nowrap><span class="mtext">{$header.registration_t.password}: &nbsp;</span></td>
							<td><b class="mtext">{$data.pass}</b></td>
						</tr>
					{/if}
					<tr>
						<td nowrap><span class="mtext">{$header.registration_t.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{$header.registration_t.keep_details}<br><br>
				{*
				<a href="{$data.urls.video_thai}" target="_blank"><img src="{$data.server.img_root}/mail_video2.jpg" alt="{$data.urls.video_thai}" border="0" /></a>
				*}
				<br>{$header.registration_t.what_next}<br><br>
				{if $data.confirm_link}
					{$header.registration_t.re_confirm}:<br><br>
					<a href="{$data.confirm_link}" class="mlink">{$data.confirm_link}</a><br><br>
				{/if}
				{$header.registration_t.we_look}<br><br><br>
				{$header.generic_t.admin_regards}<br><br>
				{$header.generic_t.company_info}
				{*
				<br><br>{$header.registration_t.ps}
				<br><a href="{$data.freecd_link}" class="mlink">{$data.freecd_link}</a>
				*}
				</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_thai.tpl"}