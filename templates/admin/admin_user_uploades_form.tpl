{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_upload}&nbsp;{$data.username}</font><br><br><br>
			<form action="{$form.action}" method="post" name="type">
			<input type="hidden" name=id value="{$data.id}">
            <table border=0  cellspacing=1 cellpadding=5 width="100%">
					<tr valign=center bgcolor="#ffffff">
						<td colspan=3 align="left" class="main_header_text"><b>{$header.type_upload}:</b>&nbsp;
						<select name="type_upload" onchange="javascript: document.type.submit();">
							<option value=1{if $type_upload eq "1"} selected{/if}>{$header.upload_1}</option>
							<option value=2{if $type_upload eq "2"} selected{/if}>{$header.upload_2}</option>
							<option value=3{if $type_upload eq "3"} selected{/if}>{$header.upload_3}</option>
						</select>
						</td>
					</tr>
			</table>
			</form>
			<form action="{$form.action}" method="post" enctype="multipart/form-data" name="upload_form">
			<input type="hidden" name=sel value="change">
			<input type="hidden" name=id value="{$data.id}">
			<input type="hidden" name=upload_type value="{$type_upload}">
            <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
					{if $type_upload eq "1" || $type_upload eq ""}
                    <tr bgcolor="#ffffff" height="10">
                        <td colspan=3 align="center" class="main_content_text" height=20>&nbsp;{$data.icon_comment}&nbsp;</td>
                    </tr>
					<tr bgcolor="#ffffff">
						<td width="30%" class="main_header_text">&nbsp;<b>{$header.icon_name}</b>&nbsp;</td>
						<td width="70%" class="main_header_text" colspan=2>&nbsp;</td>
					</tr>
					<tr bgcolor="#ffffff">
						<td width="30%"  align="center" class="main_header_text">&nbsp;<b>{$icon.file}</b>&nbsp;</td>
						<td class="main_content_text" colspan=2>&nbsp;<input type="file" name="icon" size="40" {if $data.root eq 1}disabled{/if}>{if $icon.delete_link}&nbsp;<br><br><a href="{$icon.delete_link}">{$button.delete}</a>{/if}&nbsp;</td>
					</tr>
					{/if}
                    <tr bgcolor="#ffffff" height="10">
                        <td colspan=3 align="center" class="main_content_text" height=20>&nbsp;{$data.comment}&nbsp;</td>
                    </tr>
					<tr bgcolor="#ffffff">
						<td align="center" class="main_header_text">&nbsp;</td>
						<td align="center" class="main_header_text">&nbsp;</td>
						<td align="center" class="main_header_text">&nbsp;{$header.allow_type}&nbsp;</td>
					</tr>
					{if $upload ne 'empty'}
					{section name=f loop=$upload}
					<tr bgcolor="#ffffff">
						<td width="30%" align="center" class="main_header_text">
						<a href="admin_user_upload.php?sel=view&id_file={$upload[f].id}">
						{if $type_upload eq 1}
						<img src="{$upload[f].file_path}" border="0">
						{else}
						<b>{$upload[f].file_path}</b>
						{/if}
						</a>
						</td>
						<td class="main_content_text">&nbsp;<input type="file" size="40" name="upload{$smarty.section.f.index}"  {if $data.root eq 1}disabled{/if}>{if $upload[f].id}
						<br><br><div>{$lang.profile.comment_photo}<br><textarea name="user_comments[{$smarty.section.f.index}]" cols="40">{$upload[f].user_comment}</textarea></div>
						<br><a href="{$upload[f].del_link}">{$button.delete}</a>{/if}</td>
						<td class="main_content_text">&nbsp;<input type=hidden name="id_files[{$smarty.section.f.index}]" value="{$upload[f].id}">
						<select name="upload_allow[{$smarty.section.f.index}]"  {if $data.root eq 1}disabled{/if} style="width: 100">
							<option value=1{if $upload[f].allow eq 1} selected{/if}>{$header.allow_1}</option>
							<option value=2{if $upload[f].allow eq 2} selected{/if}>{$header.allow_2}</option>
							<option value=3{if $upload[f].allow eq 3} selected{/if}>{$header.allow_3}</option>
						</select>
						</td>
					</tr>
					{/section}
					{/if}
            </table>
			<table><tr height="40">
			{if $data.root  ne 1}<td><input type="button" value="{$button.save}" class="button" onclick="javascript:document.upload_form.submit();"></td>{/if}
			<td><input type="button" value="{$button.close}" class="button" onclick="javascript: window.close();opener.focus();"></td>
			</tr></table>
			</form>
{include file="$admingentemplates/admin_bottom.tpl"}