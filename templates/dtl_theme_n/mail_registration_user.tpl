{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} <b>{$data.fname}</b>,<br><br>
				{$mail_main.intro}<br><br>
				{if $data.confirm_link}
					{$mail_main.confirm_instructions}<br><br>
					<a href="{$data.confirm_link}" class="mlink">{$data.confirm_link}</a><br><br>
				{else}
					{$mail_main.login_instructions}<br><br>
					<a href="{$data.login_link}" class="mlink">{$data.login_link}</a><br><br>
				{/if}
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext">{$mail_main.registration_details}<br><br></span>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					{*
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.date_birthday}: &nbsp;</span></td>
						<td><b class="mtext">{$data.date_birthday_formatted}</b></td>
					</tr>
					*}
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.login}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					{* password can't be sent on re-send because it's stored encrypted *}
					{if $data.pass}
						<tr>
							<td nowrap valign="top"><span class="mtext">{$mail_main.password}: &nbsp;</span></td>
							<td><b class="mtext">{$data.pass}</b></td>
						</tr>
					{/if}
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{if !$data.confirm && !$data.icon_path}
					{$mail_main.signup_instructions}<br>
				{elseif !$data.icon_path}
					{$mail_main.signup_instructions_1}<br>
				{elseif !$data.confirm}
					{$mail_main.signup_instructions_2}<br>
				{else}
					{$mail_main.signup_instructions_3}<br>
				{/if}
				<br></span>
			</td>
		</tr>
		<tr>
			<td>
				{if $gender == GENDER_MALE}
					<img src="{$data.server.img_root}/GraphicForEmail-E01.jpg" alt="instructions" />
				{else}
					<img src="{$data.server.img_root}/GraphicForEmail-T01.jpg" alt="instructions" />
				{/if}
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{$mail_main.finish}<br><br>
				{$mail_generic.admin_regards}<br><br>
				{$mail_generic.company_info}</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_user.tpl"}
