{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="left">
				<span class="mtext">{$mail_generic.hello} {$mail_generic.admin_name},<br><br>
				{$mail_main.message}<br><br></span>
			</td>
        </tr>
		<tr>
			<td align="left">
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td align="left" nowrap><span class="mtext">{$mail_main.fname}: &nbsp;</span></td>
						<td align="left"><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td align="left" nowrap><span class="mtext">{$mail_main.sname}: &nbsp;</span></td>
						<td align="left"><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td align="left" nowrap><span class="mtext">{$mail_main.city}: &nbsp;</span></td>
						<td align="left"><b class="mtext">{$data.city}</b></td>
					</tr>
					<tr>
						<td align="left" nowrap><span class="mtext">{$mail_main.country}: &nbsp;</span></td>
						<td align="left"><b class="mtext">{$data.country}</b></td>
					</tr>
					<tr>
						<td align="left" nowrap><span class="mtext">{$mail_main.phone}: &nbsp;</span></td>
						<td align="left"><b class="mtext">{$data.phone}</b></td>
					</tr>
					<tr>
						<td align="left" nowrap><span class="mtext">{$mail_main.email}: &nbsp;</span></td>
						<td align="left"><b class="mtext">{$data.email}</b></td>
					</tr>
					<tr>
						<td align="left" nowrap valign="top"><span class="mtext">{$mail_main.calltime}: &nbsp;</span></td>
						<td align="left"><b class="mtext">{$data.calltime|nl2br}</b></td>
					</tr>
					<tr>
						<td align="left" nowrap valign="top"><span class="mtext">{$mail_main.comments}: &nbsp;</span></td>
						<td align="left"><b class="mtext">{$data.comments|nl2br}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>{$mail_generic.site_regards}</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_admin.tpl"}