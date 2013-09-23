{include file="$admingentemplates/admin_top.tpl"}
<span class="red_header">{$header.razdel_name}</span><span class="red_sub_header">&nbsp;|&nbsp;{$header.editform}</span><br />
<a href="javascript:void(0);" onclick="$('.help_text').toggle();">Help</a>
<div class="help_text" style="display:none;">
	<span class="help_title">{$lang.help}:</span>{$help.comunicate}
</div>
<form method="post" action="{$form.action}" name="comunicate">
	<input type="hidden" name="sel" value="send">
	{$form.hiddens}
	<table border="0" class="table_main" cellspacing="1" cellpadding="5" width="100%">
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">
				{$header.to}:&nbsp;
			</td>
			<td class="main_content_text" align="left">
				<input type="text" name="to" value="{$data.to}" style="width:400px">&nbsp;
			</td>
		</tr>
		<tr bgcolor="#FFFFFF" valign="top">
			<td align="right" width="17%" class="main_header_text">
				{$header.message}:&nbsp;
			</td>
			<td class="main_content_text" align="left">
				<script type="text/javascript" src="{$site_root}/javascript/tiny_mce-3.5.8/tiny_mce.js"></script>
				<script type="text/javascript">
				tinyMCE.init({ldelim}
					mode : "textareas",
					oninit: myInit,
					{include file="$admingentemplates/admin_tiny_mce_slim.tpl"}
				{rdelim});
				function myInit() {ldelim}
					tinyMCE.get('tinymce_message').setContent('{$data.message|escape:javascript}');
				{rdelim}
				</script>
				<textarea name="message" id="tinymce_message" rows="20" cols="60" style="width:400px;height:300px;"></textarea>
			</td>
		</tr>
		<tr bgcolor="#FFFFFF" height="10">
			<td align="right" width="17%" class="main_header_text">
				{$header.send_by}:&nbsp;
			</td>
			<td align="left" class="main_content_text">
				<input type="checkbox" name="spars[0]" value="1" checked="checked">&nbsp;{$header.post}&nbsp;&nbsp;
				<input type="checkbox" name="spars[1]" value="1" checked="checked">&nbsp;{$header.email}&nbsp;&nbsp;
				<input type="checkbox" name="spars[2]" value="1">&nbsp;{$header.alert};
			</td>
		</tr>
	</table>
</form>
<table>
	<tr height="40">
		<td><input type="submit" value="{$button.send}" class="button" onclick="document.comunicate.submit()"></td>
		<td><input type="button" value="{$button.close}" class="button" onclick="window.close();opener.focus();"></td>
	</tr>
</table>
{include file="$admingentemplates/admin_bottom.tpl"}