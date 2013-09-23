{include file="$gentemplates/mail_header.tpl"}
{strip}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} <b>{$data.fname}</b>,<br><br>
				{if $data.status == 1}
					{$mail_main.approved}<br><br>
					{if $gender == 1}
						<a href="{$data.urls.video_eng}" target="_blank">
							<img src="{$data.server.img_root}/mail_video1.jpg" alt="{$data.urls.video_eng}" border="0" />
						</a>
						<br><br>
						{$mail_main.approved_below_video}<br><br>
					{else}
						{* VIDEO NO LONGER IN USE
						<a href="{$data.urls.video_thai}" target="_blank">
							<img src="{$data.server.img_root}/mail_video2.jpg" alt="{$data.urls.video_thai}" border="0" />
						</a>
						*}
					{/if}
				{else}
					{*
					{$mail_main.suspended}<br><br>
					*}
				{/if}
				{$mail_main.details}:<br><br>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$mail_main.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.login}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{if $data.status == 1}
					{$mail_main.approved_below_data}<br><br>
				{/if}
				{if $data.confirm_link}
					{$mail_main.confirm}:<br><br>
					<a href="{$data.confirm_link}" class="mlink">{$data.confirm_link}</a><br><br>
				{/if}
				{$mail_generic.admin_regards}<br><br>
				{$mail_generic.company_info}<br><br><br>
				{$mail_main.ps}<br><br>
				{$mail_main.pps}</span>
			</td>
		</tr>
	</table>
</div>
{/strip}
{include file="$gentemplates/mail_footer_user.tpl"}