{include file="$gentemplates/index_top_popup.tpl"}	
	<!-- central part -->
	<td width="100%" valign=top>
		<table width="100%" cellspacing=0 width="500px">
		<!-- header -->
		<tr><td colspan=2>
		<div class="header">{$form.top_header}</div>
		<div class="sep"></div>
		</td></tr>
		<tr><td colspan=2 class="text">{$form.comment}<br>&nbsp;</td></tr>
		{if $form.err}
		<tr><td colspan="2"><div class="error_msg">{$form.err}</div></td></tr>
		{/if}
		<tr><td colspan=2>
			<form action="{$form.action}" method="post" name="mailbox_write">
			{$form.hidden}
			<!-- write form -->
			<table cellspacing=0 cellpadding=0>
			<tr>
				<td height="35px" class="text_head">{$header.to}:&nbsp;&nbsp;&nbsp;</td>
				<td height="35px" align=left><input type="text" style="width: 400px" name="to" value="{$data.to}"></td>
			</tr>
			<tr>
				<td height="35px" class="text_head">{$header.subject}:&nbsp;&nbsp;&nbsp;</td>
				<td height="35px" align=left><input type="text" style="width: 400px" name="subject" value="{$form.subject}"></td>
			</tr>
			<tr>
				<td height="35px">&nbsp;</td>
				<td height="35px" align=left><textarea name=body cols=70 rows=15  style="width: 400px">{$form.body}</textarea><br></td>
			</tr>
			<tr>
				<td height="35px">&nbsp;</td>
				<td height="35px" align=left>
					<input type=hidden name=send value=1>
					<input type="button" class="button" onclick="javascript: document.mailbox_write.submit();" value="{$button.send}">
					<input type="button" class="button" onclick="javascript: parent.GB_hide();" value="{$button.close}">
				</td>
			</table>
			<!-- /write form -->
			</form>
		</td></tr>
		</table>
	</td>
	<!-- /central part -->
</tr>
</table>
</div>
</body>
</html>