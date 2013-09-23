			<TABLE border="0" cellspacing="0" cellpadding="5">
			<TR>
				<TD><b>{$upload.login}</b><br><i>{$upload.comment}</i></TD>
			</TR>
			<TR>
				<TD><IMG alt="" src="{$upload.upload_path}"></TD>
			</TR>
			<TR>
				<TD>
					<table border="0" cellspacing="0" cellpadding="0">
					<tr align="center">
					{foreach from=$vote_arr item=item}
					<td>
						<a onclick="marks({$item},'show'); VoteAction('{$upload.id_upload}', '{$item}', '{$form.id_category}', document.getElementById('active_section'),'f');" href="#">
						<img id="mark{$item}" name="mark{$item}" src="{$form.vote_icon_0_path}" onmouseover="marks({$item},'show');" onmouseout="marks({$item},'hide');" border="0">
						</a>
					</td>
					{/foreach}
					</tr>
					</table>
				</TD>
			</TR>
			</TABLE>