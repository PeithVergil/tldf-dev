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
						<td nowrap><span class="mtext">{$mail_main.login}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.contact}: &nbsp;</span></td>
						<td><b class="mtext">{$data.contact}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.expected_dates}: &nbsp;</span></td>
						<td>
							<span class="mtext">{$mail_main.date_from}&nbsp;<b>{$data.date_from}</b>&nbsp;
							{$mail_main.date_to}&nbsp;<b>{$data.date_to}</b></span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br><b>{$mail_main.data}</b><br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td><span class="mtext">&nbsp;{$mail_main.user_id}&nbsp;</span></td>
						<td><span class="mtext">&nbsp;{$mail_main.user_fname}&nbsp;</span></td>
						<td><span class="mtext">&nbsp;{$mail_main.user_age}&nbsp;</span></td>
					</tr>
					{foreach item=item from=$data.ladies_arr}
						<tr>
							<td><b class="mtext">{$item.id}</b></td>
							<td><b class="mtext">{$item.nick}</b></td>
							<td><b class="mtext">{$item.age}</b></td>
						</tr>
					{/foreach}
				</table>
			</td>
		</tr>
		{if $data.other_ladies}
			<tr>
				<td>
					<span class="mtext"><br><b>{$mail_main.other_ladies}</b><br><br></span>
				</td>
			</tr>
		{/if}
		{if $data.like_to_know}
			<tr>
				<td>
					<span class="mtext">{$mail_main.like_to_know}:<br>
					<b>{$data.like_to_know}</b><br></span>
				</td>
			</tr>
		{/if}
		<tr>
			<td>
				<br>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$mail_main.best_number}: &nbsp;</span></td>
						<td><b class="mtext">{$data.best_number}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.best_time}: &nbsp;</span></td>
						<td><b class="mtext">{$data.best_time}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		{if $data.send_info}
			<tr>
				<td><span class="mtext"><br><b>{$mail_main.send_info}</b></span></td>
			</tr>
		{/if}
		<tr>
			<td>
				<span class="mtext"><br>{$mail_generic.site_regards}</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_admin.tpl"}