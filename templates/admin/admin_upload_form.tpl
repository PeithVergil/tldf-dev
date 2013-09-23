{include file="$admingentemplates/admin_top.tpl"}
<font class="red_header">{$header.razdel_name}</font><font class="red_sub_header">&nbsp;|&nbsp;{$header.editform}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.uploads_approve}</div>
<form action="{$form.action}" method="post" name="type">
	<input type="hidden" name=id value="{$data.id}" />
	<table border="0" cellspacing="1" cellpadding="5" width="100%">
		<tr valign="center" bgcolor="#ffffff">
			<td colspan="3" align="left" class="main_content_text">
				<b>{$header.type_upload}:</b>&nbsp;&nbsp;&nbsp;
				<a href="admin_upload.php?type_upload=1" {if $type_upload eq 1} style="font-weight: bold;"{/if}>{$header.upload_1} ({$data.upload_1_count})</a>&nbsp;&nbsp;&nbsp;
				<a href="admin_upload.php?type_upload=2" {if $type_upload eq 2} style="font-weight: bold;"{/if}>{$header.upload_2} ({$data.upload_2_count})</a>&nbsp;&nbsp;&nbsp;
				<a href="admin_upload.php?type_upload=3" {if $type_upload eq 3} style="font-weight: bold;"{/if}>{$header.upload_3} ({$data.upload_3_count})</a>&nbsp;&nbsp;&nbsp;
				<a href="admin_upload.php?type_upload=5" {if $type_upload eq 5} style="font-weight: bold;"{/if}>{$header.upload_5} ({$data.upload_5_count})</a>&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
	</table>
</form>
<form action="{$form.action}" method="post" enctype="multipart/form-data" name="uploades">
	<input type="hidden" name="sel" value="change" />
	<input type="hidden" name="upload_type" value="{$type_upload}" />
	<input type="hidden" name="page" value="{$page}" />
	<table border="0" class="table_main" cellspacing="1" cellpadding="5" width="100%">
		{if $links}
			<tr bgcolor="#ffffff">
				<td height="20" colspan="4" align="left" class="main_content_text">{$links}</td>
			</tr>
		{/if}
		{if $upload}
			<tr height="10" bgcolor="#ffffff">
				<td align="center">&nbsp;</td>
				<td align="center"><input type="button" value="{$button.activate}" class="button" onclick="javascript:document.uploades.submit();"></td>
				<td align="center"><input type="button" value="{$button.adult_content}" class="button" onclick="javascript: document.uploades.sel.value='adult'; document.uploades.submit();"></td>
				<td align="center"><input type="button" value="{$button.delete}" class="button" onclick="Javascript: document.uploades.sel.value='delete'; document.uploades.submit();"></td>
			</tr>
		{/if}
		{section name=f loop=$upload}
			<tr bgcolor="#ffffff">
				<td align="center" class="main_header_text">
					<br>&nbsp;<b>{$upload[f].file}</b>
					{if $type_upload == 1 || $type_upload == 4 || $type_upload == 5}<br>{else}&nbsp;{/if}
					<a href="{$upload[f].userlink}">{$upload[f].username}</a><br><br>
					<input type="hidden" name="id_files[{$smarty.section.f.index}]" value="{$upload[f].id}" />
				</td>
				<td width="10%" align="center" class="main_content_text">&nbsp;<input type=checkbox name="activate[{$smarty.section.f.index}]" value="1"></td>
				<td width="10%" align="center" class="main_content_text">&nbsp;<input type=checkbox name="adult[{$smarty.section.f.index}]" value="1"></td>
				<td width="10%" align="center" class="main_content_text">&nbsp;<input type=checkbox name="delete[{$smarty.section.f.index}]" value="1"></td>
			</tr>
		{/section}
		{if $upload}
			<tr height="10" bgcolor="#ffffff">
				<td align="center">&nbsp;</td>
				<td align="center"><input type="button" value="{$button.activate}" class="button" onclick="javascript:document.uploades.submit();"></td>
				<td align="center"><input type="button" value="{$button.adult_content}" class="button" onclick="javascript: document.uploades.sel.value='adult'; document.uploades.submit();"></td>
				<td align="center"><input type="button" value="{$button.delete}" class="button" onclick="Javascript: document.uploades.sel.value='delete'; document.uploades.submit();"></td>
			</tr>
		{else}
			<tr height="10" bgcolor="#ffffff">
				<td align="center" class="main_error_text" colspan=4 height="30">&nbsp;{$header.empty}&nbsp;</td>
			</tr>
		{/if}
		{if $links}
			<tr bgcolor="#ffffff">
				<td height="20"  colspan=4 align="left"  class="main_content_text" >{$links}</td>
			</tr>
		{/if}
	</table>
</form>
<script type="text/javascript">
{literal}
$(document).ready(function() {
	$(".video_colorbox").colorbox({iframe:true, innerWidth:700, innerHeight:470});
});
{/literal}
</script>
{include file="$admingentemplates/admin_bottom.tpl"}