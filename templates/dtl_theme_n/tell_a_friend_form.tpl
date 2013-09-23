{strip}
{include file="$gentemplates/index_top.tpl"}
<td valign="top">
	<div class="header">{$form.top_header}</div>
	<div class="sep"></div>
	<div class="text">{$form.comment}<br>&nbsp;</div>
	{if $form.err}
		<div class="error_msg" style="padding-left:15px;">{$form.err}<br>&nbsp;</div>
	{/if}
	<form action="{$form.action}" method="post" name="mailbox_write">
		{$form.hidden}
		<input type="hidden" name="send" value="1">
		<table cellspacing="3" cellpadding="0">
			<tr>
				<td class="text_head">&nbsp;{$header.name}:&nbsp;&nbsp;&nbsp;</td>
				<td colspan="2">
					<input type="text" name="name" style="width:500px" value="{$form.name}">
				</td>
			</tr>
			<tr>
				<td class="text_head">&nbsp;{$header.email}:&nbsp;&nbsp;&nbsp;</td>
				<td colspan="2">
					<input type="text" name="email" style="width:500px" value="{$form.email}">
				</td>
			</tr>
			<tr>
				<td class="text_head">&nbsp;{$lang.tell_a_friend.to_email}:&nbsp;&nbsp;&nbsp;</td>
				<td colspan="2">
					<input type="text" name="to" style="width:500px" value="{$form.to}">
				</td>
			</tr>
			<tr>
				<td class="text_head">&nbsp;{$header.subject}:&nbsp;&nbsp;&nbsp;</td>
				<td colspan="2">
					<input type="text" name="subject" style="width:500px" value="{$form.subject}">
				</td>
			</tr>
			<tr>
				<td class="text_head" valign="top">&nbsp;{$header.body}:&nbsp;&nbsp;&nbsp;</td>
				<td colspan="2">
					<textarea name="body" style="width:500px; height:200px;">{$form.body}</textarea>
				</td>
			</tr>
			<tr>
				<td class="text_head">&nbsp;{$header.security_code}:&nbsp;&nbsp;&nbsp;</td>
				<td><img src="{$form.kcaptcha}" alt="{$header.security_code}"></td>
				<td>
					<input type="text" style="width:200px" name="keystring">
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td align="center" colspan="2">
					<div class="center">
						<div class="btnwrap" style="width:100px;">
							<span><span>
								<input type="submit" class="btn_org" style="width:80px;" value="{$button.send}">
							</span></span>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</td>
{include file="$gentemplates/index_bottom.tpl"}
{/strip}