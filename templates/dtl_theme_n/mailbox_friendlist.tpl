{include file="$gentemplates/index_top_popup.tpl"}
			<td width="100%" valign=top>
				<form name="hotlist" action="">
					<table width="100%" cellspacing=1 cellpadding=3>
						<tr class="content_active">
							<td align="center" width="20"></td>
							<td class="mailbox_text_active" onclick="javascript: location.href='{$form.sort_link_1}';" style="cursor:pointer">{$header.to}</td>
							<td class="mailbox_text_active" align="center" onclick="javascript: location.href='{$form.sort_link_2}';" style="cursor:pointer">{$header.age}</td>
							<td class="mailbox_text_active" onclick="javascript: location.href='{$form.sort_link_3}';" style="cursor:pointer">{$header.country}</td>
						</tr>
						{section name=s loop=$hotlist}
							<tr>
								<td height="20" class="mailbox_text" align="center"><input type="checkbox" name="mail{$smarty.section.s.index}" value="{$smarty.section.s.index}"></td>
								<td height="20" class="mailbox_text"><div id="user{$smarty.section.s.index}">{$hotlist[s].friend}</div></td>
								<td height="20" class="mailbox_text" align="center">&nbsp;{$hotlist[s].age}</td>
								<td height="20" class="mailbox_text">{$hotlist[s].country}&nbsp;</td>
							</tr>
						{/section}
						{if !$hotlist}
							<tr valign="middle">
								<td height="40" colspan="4" align="center"><div class="error_msg">{$header.empty_hotlist}</div></td>
							</tr>
						{/if}
						<tr>
							<td></td>
							<td height="25" colspan="3">
								<input type="button" class="button" onclick="javascript: SendToSelect();" value="{$button.send}">
								<input type="button" class="button" onclick="javascript: opener.focus();window.close();" value="{$button.back}">
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
		<tr><td height="30%">&nbsp;</td></tr>
	</table>
</div>
{literal}
<script language="JavaScript" type="text/javascript">
function SendToSelect()
{
	var hotl = document.hotlist;
	var users_str = '';
	var i = 0;
	
	for (i = 0; i < hotl.length; i++){
		if (hotl[i].name.substr(0,4) == "mail" && hotl[i].checked == true) {
			hot_num = hotl[i].value;
			users_str = users_str + document.getElementById('user'+hot_num).innerHTML + '; ';
		}
	}
	
	old_val = opener.document.mailbox_write.to.value;
	
	if (old_val.length > 0 && users_str.length > 0) {
		val = old_val + '; ' + users_str.substr(0, users_str.length - 2);
	} else if (old_val.length > 0) {
		val = old_val;
	} else {
		val = users_str.substr(0, users_str.length - 2);
	}
	
	opener.document.mailbox_write.to.value = val;
	opener.focus();
	window.close();
}
</script>
{/literal}
</body>
</html>