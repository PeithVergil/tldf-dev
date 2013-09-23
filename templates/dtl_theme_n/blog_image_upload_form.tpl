{include file="$gentemplates/index_top_popup.tpl"}
	<!-- central part -->
	<td width="100%" valign=top>
		<table width="100%" cellspacing=0>
		<!-- header -->
		<tr><td colspan=2>
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$lang.blog.image_upload}</div></div>
		</td></tr>
		{if $form.err}
		<tr><td colspan="2"><div class="error_msg">{$form.err}</div></td></tr>
		{/if}
		<tr><td colspan=2>
			<form method="post" name="image_form" id="image_form" action="blog.php?sel=upload_file&id_profile={$form.id_profile}" enctype="multipart/form-data">
			<table cellpadding="3" cellspacing="0">
				<tr>
					<td height="35"><input type="file" name="upload_file" id="upload_file"></td>
				</tr>
				<tr>
					<td><input type="submit" class="button" value="{$button.upload}">&nbsp;&nbsp;
						<!--<input type="button" class="button" onclick="javascript: parent.GB_hide();" value="{$button.close}">&nbsp;&nbsp;-->
						<input type="button" class="button" onclick="javascript: window.opener.focus(); window.close();" value="{$button.close}">&nbsp;&nbsp;
					</td>
				</tr>
			</table>
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