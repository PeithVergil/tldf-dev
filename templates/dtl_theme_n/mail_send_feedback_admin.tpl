{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} {$mail_generic.admin_name},<br><br>
				{$mail_main.message}:<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$mail_main.name}: &nbsp;</span></td>
						<td><b class="mtext">{$data.name}</b></td>
					</tr>
						<td nowrap><span class="mtext">{$mail_main.email}: &nbsp;</span></td>
						<td><b class="mtext"><a href="mailto:{$data.email}" class="mtext">{$data.email}</a></b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td colspan="2"><b class="mtext">{$mail_main.rating}:</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.question_1} &nbsp;</span></td>
						<td><b class="mtext">{$data.question_1}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.question_2} &nbsp;</span></td>
						<td><b class="mtext">{$data.question_2}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.question_3} &nbsp;</span></td>
						<td><b class="mtext">{$data.question_3}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.question_4} &nbsp;</span></td>
						<td><b class="mtext">{$data.question_4}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.question_5} &nbsp;</span></td>
						<td><b class="mtext">{$data.question_5}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td valign="top"><span class="mtext">{$mail_main.comments}: &nbsp;</span></td>
						<td><b class="mtext">{$data.comments|nl2br}</b></td>
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