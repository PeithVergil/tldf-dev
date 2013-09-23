{include file="$gentemplates/index_top_popup.tpl"}
	<!-- central part -->
	<td width="100%" valign=top>
		<table cellspacing=0 width="100%">
		<!-- header -->
		<tr><td colspan=2>
		<div class="header">{$header.top_header}</div>
		</td></tr>
		<tr><td colspan=2 class="text">{$header.top_text}<br>&nbsp;</td></tr>
		{if $form.err}
		<tr><td colspan="2"><div class="error_msg">{$form.err}</div></td></tr>
		{/if}
		<tr><td colspan=2>
			<form action="{$form.action}" method="post" name="send_kiss" style="margin:0px">
			{$form.hidden}
			<table cellspacing=5 cellpadding=0>
			{section name=s loop=$types}
			<tr>
				<td width="5%"><input type="radio" name="kiss_type" value="{$types[s].id}"></td>
				{if $types[s].image_path}<td width="5%"><img src="{$types[s].image_path}" border="0" alt=""></td>{/if}
				<td class="text_head" {if !$types[s].image_path}colspan=2{/if}>{$types[s].name}</td>
			</tr>
                        {/section}
			<tr>
				<td width="5%">&nbsp;</td>
				<td colspan="2" align=left>
					<input type="button" class="button" onclick="javascript: document.send_kiss.submit();" value="{$button.send}">
					<input type="button" class="button" onclick="javascript: parent.GB_hide();" value="{$button.close}">
				</td>
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