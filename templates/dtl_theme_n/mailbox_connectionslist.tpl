{include file="$gentemplates/index_top_popup.tpl"}
			<td width="100%" valign=top>
				<form name="frmList" action="">
					<table width="100%" cellspacing="1" cellpadding="3">
						<tr class="content_active">
							<td align="center" width="20"></td>
							<td class="mailbox_text_active" onclick="window.location.href='{$form.sort_link_1}';" style="cursor:pointer">{$header.to}</td>
							<td class="mailbox_text_active" align="center" onclick="window.location.href='{$form.sort_link_2}';" style="cursor:pointer">{$header.age}</td>
							<td class="mailbox_text_active" onclick="window.location.href='{$form.sort_link_3}';" style="cursor:pointer">{$header.country}</td>
						</tr>
						{foreach from=$connections item=item name=connections}
							<tr>
								<td height="20" class="mailbox_text" align="center"><input type="checkbox" name="mail{$smarty.foreach.connections.index}" value="{$smarty.foreach.connections.index}"></td>
								<td height="20" class="mailbox_text"><div id="to_fname_{$smarty.foreach.connections.index}">{$item.friend}</div></td>
								<td height="20" class="mailbox_text" align="center">&nbsp;{$item.age}</td>
								<td height="20" class="mailbox_text">{$item.country}&nbsp;<input type="hidden" id="to_id_{$smarty.foreach.connections.index}" value="{$item.id}"></td>
							</tr>
						{foreachelse}
							<tr valign="middle">
								<td height="40" colspan="4" align="center"><div class="error_msg">{$header.empty_connections}</div></td>
							</tr>
						{/foreach}
						<tr>
							<td colspan="4">
								<table align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<p class="basic-btn_here">
												<b></b><span>
													<input type="button" class="button" onclick="SendToSelect();" value="{$button.add}">
												</span>
											</p>
										</td>
										<td>&nbsp;&nbsp;</td>
										<td>
											<p class="basic-btn_here">
												<b></b><span>
													<input type="button" class="button" onclick="opener.focus();window.close();" value="{$button.back}">
												</span>
											</p>
										</td>
									</tr>
								</table>
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
<script type="text/javascript">
/*
OLD CODE WHICH USES THE USERNAME
function SendToSelect()
{
	var form = document.frmList;
	var users_str = '';
	var i = 0;
	
	for (i = 0; i < form.length; i++){
		if (form[i].name.substr(0,4) == "mail" && form[i].checked == true) {
			num = form[i].value;
			users_str = users_str + document.getElementById('user'+num).innerHTML + '; ';
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
*/
function SendToSelect()
{
	var form = document.frmList;
	var to_id = '';
	var to_fname = '';
	
	for (i = 0; i < form.length; i++){
		if (form[i].name.substr(0,4) == "mail" && form[i].checked == true) {
			num = form[i].value;
			to_id = document.getElementById('to_id_' + num).value;
			to_fname = document.getElementById('to_fname_' + num).innerHTML;
			break;
		}
	}
	
	if (to_id.length > 0) {
		opener.document.mailbox_write.to.value = to_id;
		opener.document.mailbox_write.to_fname.value = to_fname;
	}
	opener.focus();
	window.close();
}
</script>
{/literal}
</body>
</html>