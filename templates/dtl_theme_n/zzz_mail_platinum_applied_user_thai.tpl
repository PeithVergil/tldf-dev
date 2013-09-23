{include file="$gentemplates/mail_header.tpl"}
{strip}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_t.hello} <b>{$data.fname}</b>,<br><br>
				{$header.apply_platinum_t.message}<br><br>
				{$header.apply_platinum_t.details}:<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$header.apply_platinum_t.data_username}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.apply_platinum_t.data_fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.apply_platinum_t.data_sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.apply_platinum_t.data_email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.apply_platinum_t.data_id_num}: &nbsp;</span></td>
						<td><b class="mtext">{$data.data_id_num}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.apply_platinum_t.data_id_type}: &nbsp;</span></td>
						<td><b class="mtext">{$data.data_id_type}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.apply_platinum_t.data_phone}: &nbsp;</span></td>
						<td><b class="mtext">{$data.data_phone}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.apply_platinum_t.data_mobile}: &nbsp;</span></td>
						<td><b class="mtext">{$data.data_mobile}</b></td>
					</tr>
					<tr>
						<td nowrap valign="top"><span class="mtext">{$header.apply_platinum_t.data_comments}: &nbsp;</span></td>
						<td><b class="mtext">{$data.data_comments|nl2br}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br><br>
				{$header.generic_t.admin_regards}<br><br>
				{$header.generic_t.company_info}</span>
			</td>
		</tr>
	</table>
</div>
{/strip}
{include file="$gentemplates/mail_footer_thai.tpl"}