{include file="$gentemplates/mail_header.tpl"}
{strip}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_e.hello} <b>{$data.fname}</b>,<br><br>
				{if $data.status == 1}
					{$header.status_change_e.approved}<br><br>
					<a href="{$data.urls.video_eng}" target="_blank">
						<img src="{$data.server.img_root}/mail_video1.jpg" alt="{$data.urls.video_eng}" border="0" />
					</a>
					<br><br>
					{$header.status_change_e.approved_btm}<br><br>
				{else}
					{*
					{$header.status_change_e.suspended}<br><br>
					*}
				{/if}
				{$header.status_change_e.details}:<br><br>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$header.status_change_e.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.status_change_e.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.status_change_e.login}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.status_change_e.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{if $data.status == 1}
					{$header.status_change_e.approved_btm_2}<br><br>
				{/if}
				{if $data.confirm_link}
					{$header.status_change_e.confirm}:<br><br>
					<a href="{$data.confirm_link}" class="mlink">{$data.confirm_link}</a><br><br>
				{/if}
				<br>
				{$header.generic_e.admin_regards}<br><br>
				{$header.generic_e.company_info}<br><br><br>
				{$header.status_change_e.ps}<br><br>
				{$header.status_change_e.pps}</span>
			</td>
		</tr>
	</table>
</div>
{/strip}
{include file="$gentemplates/mail_footer_eng.tpl"}