{include file="$gentemplates/mail_header.tpl"}
{strip}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_e.hello} <b>{$data.fname}</b>,<br><br>
				{$header.dating_video_e.message}:<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$header.dating_video_e.video}: &nbsp;</span></td>
						<td><b class="mtext">{$data.video}</b></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td nowrap><span class="mtext">{$header.dating_video_e.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.dating_video_e.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.dating_video_e.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{$header.dating_video_e.comment}:<br><br>
				<b>{$data.comment|nl2br}</b><br><br><br>
				{$header.dating_video_e.admin_contact} <a href="mailto:{$data.adminemail}">{$data.adminemail}</a><br><br><br>
				{$header.generic_e.admin_regards}<br><br>
				{$header.generic_e.company_info}</span>
			</td>
		</tr>
	</table>
</div>
{/strip}
{include file="$gentemplates/mail_footer_eng.tpl"}