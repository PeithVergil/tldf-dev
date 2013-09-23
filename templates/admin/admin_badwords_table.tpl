{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list_file}</font><br><br><br>

				<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
                    <tr height="20" bgcolor="#ffffff">
                        <td colspan=3 class="main_content_text" align="center">&nbsp;{$header.edit_comment}&nbsp;</td>
                    </tr>
                    <form id="file_form" action="{$form.action}" method="post">
					{$form.hiddens_edit}
                    <tr valign=top bgcolor="#ffffff">
                        <td align="right" width="30%" class="main_header_text">{$header.edit_file}:&nbsp;</td>
                        <td class="main_content_text" align="left" ><textarea cols="100" rows="40" name="content">{$data.content}</textarea></td>
                        <td class="main_header_text" align="left" width="20%">&nbsp;</td>
                    </tr>
                    <tr height="10" bgcolor="#ffffff">
                        <td colspan=2 align="right" class="main_content_text">
                        	<input type="submit" class="button" value="{$button.save}"></td>
						<td align="left" >&nbsp;</td>
                    </tr>
					</form>
                    <form name="upload_form" action="{$form.action}" method="post" enctype="multipart/form-data">
					{$form.hiddens_upload}
                    <tr valign=top bgcolor="#ffffff">
                        <td align="right" width="30%" class="main_header_text">{$header.upload_file}:&nbsp;</td>
                        <td class="main_content_text" align="center" ><input type=file name=upload_file >&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=upload_type value=1 checked>&nbsp;{$header.replace};&nbsp;<input type=radio name=upload_type value=2>&nbsp;{$header.add};</td>
                        <td class="main_header_text" align="left">&nbsp;</td>
                    </tr>
                    <tr height="10" bgcolor="#ffffff">
                        <td colspan=2 align="right">
                        <input type="submit" value="{$button.save}"></td>
						<td align="left">&nbsp;</td>
                    </tr>
					</form>
					</table>
{include file="$admingentemplates/admin_bottom.tpl"}