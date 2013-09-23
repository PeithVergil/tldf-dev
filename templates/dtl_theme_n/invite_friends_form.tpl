{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr valign="middle"><td height="25px">
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$header.header_text}</div></div>
	</td></tr>
	<tr><td class="text">{$header.top_text}<br>{$form.comment}</td></tr>
	{if $invite_message}
		<tr><td><div class="error_msg">{$invite_message}</div></td></tr>
	{/if}
	<tr><td>
			<form name="invite" method="post" action="{$form.action}">
			{$form.hidden}
				<table cellspacing=0 cellpadding=0>
				<tr>
					<td height="35" align=left class="text_head">{$header.email}:&nbsp;&nbsp;</td>
					<td height="35" align=left class="text_head"><input type="text" size=40 name="i_email" value="" style="width: 300px"></td>
				</tr>
				<tr>
					<td height="35" align=left class="text_head">{$header.email_pwd}:&nbsp;&nbsp;</td>
					<td height="35" align=left class="text_head"><input type="password" size=40 name="i_email_pwd" value="" style="width: 300px"></td>
				</tr>
				<tr>
					<td align=left class="text_head">&nbsp;</td>
					<td align=left class="text">
						<input name="i_service" type="radio" value="aol" checked> AOL <br>
						<input name="i_service" type="radio" value="msn"> MSN <br>
						<input name="i_service" type="radio" value="yahoo"> YAHOO <br>
						<input name="i_service" type="radio" value="gmail"> GMAIL <br>
						<input name="i_service" type="radio" value="hotmail"> HOTMAIL <br>
					</td>
				</tr>
				<tr>
					<td height="35" align=left class="text_head">{$header.subj}:&nbsp;&nbsp;</td>
					<td height="35" align=left><input type="text" size=40 name="subj" value="{$form.subj}" style="width: 300px"></td>
				</tr>
				<tr>
					<td class="text_head" align=left>{$header.body}:&nbsp;&nbsp;</td>
					<td align=left><textarea name=body cols=35 rows=15 class=blackborder style="width: 300px">{$form.body}</textarea></td>
				</tr>
				<tr>
					<td height="35" class="text_head" align=left>&nbsp;</td>
					<td height="35" align=left><input type="button" class="button" onclick="javascript: document.invite.submit();" value="{$button.submit}"></td>
				</tr>
				</table>
			</form>
	</td></tr>
	</table>
	<!-- end main cell -->
</td>
{include file="$gentemplates/index_bottom.tpl"}