{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} {$mail_generic.admin_name},<br><br>
				{$mail_main.message}<br><br></span>
			</td>
		<tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td valign="top" nowrap><span class="mtext">{$mail_main.name}: &nbsp;</span></td>
						<td valign="top"><b class="mtext">{$data.name}</b></td>
					</tr>
					<tr>
						<td valign="top" nowrap><span class="mtext">{$mail_main.email}: &nbsp;</span></td>
						<td valign="top"><b class="mtext">{$data.email}</b></td>
					</tr>
					<tr>
						<td valign="top" nowrap><span class="mtext">{$mail_main.city}: &nbsp;</span></td>
						<td valign="top"><b class="mtext">{$data.city}</b></td>
					</tr>
					<tr>
						<td valign="top" nowrap><span class="mtext">{$mail_main.country}: &nbsp;</span></td>
						<td valign="top"><b class="mtext">{$data.country}</b></td>
					</tr>
					<tr>
						<td valign="top" nowrap><span class="mtext">{$mail_main.phone}: &nbsp;</span></td>
						<td valign="top"><b class="mtext">{$data.phone}</b></td>
					</tr>
					<tr>
						<td valign="top" nowrap><span class="mtext">{$mail_main.best_times}: &nbsp;</span></td>
						<td valign="top"><b class="mtext">{$data.best_times}</b></td>
					</tr>
					<tr>
						<td valign="top" nowrap><span class="mtext">{$mail_main.interest}: &nbsp;</span></td>
						<td valign="top"><b class="mtext">{$data.interest}</b></td>
					</tr>
					<tr>
						<td valign="top" nowrap><span class="mtext">{$mail_main.marital}: &nbsp;</span></td>
						<td valign="top"><b class="mtext"><b>{$data.marital}</b></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td valign="top"><span class="mtext">{$mail_main.main_thing}: &nbsp;</span></td>
						<td valign="top"><b class="mtext">{$data.main_thing|nl2br}</b></td>
					</tr>
					{*
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td valign="top"><span class="mtext">{$mail_main.about_me}: &nbsp;</span></td>
						<td valign="top"><b class="mtext">{$data.about_me|nl2br}</b></td>
					</tr>
					*}
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