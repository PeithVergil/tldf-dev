{include file="$gentemplates/index_top_popup.tpl"}	
			<td>{* width="330" *}
				<div class="header">{$header.top_header}</div>
				<div class="text"><br>{$header.top_text}</div>
				<br>
				{if $form.err}
					<div class="error_msg">{$form.err}</div>
				{/if}
				<form action="hotlist.php" method="get" name="hotlist" style="margin:0px">
					<input type="hidden" name="sel" value="addsave">
					<input type="hidden" name="id" value="{$form.id_user}">
					<table cellspacing="5" cellpadding="0">
						{foreach item=item from=$types}
							<tr>
								<td width="5%"><input type="radio" name="type" value="{$item.id}"></td>
								<td class="text_head">{$item.name}</td>
							</tr>
						{/foreach}
						<tr>
							<td width="5%"><input type="radio" name="type" value="0" checked="checked"></td>
							<td class="text_head">{$header.not_indicate}</td>
						</tr>
						<tr>
							<td width="5%">&nbsp;</td>
							<td style="padding-top: 10px;">
								<input type="button" class="button" onclick="javascript: document.hotlist.submit();" value="{$button.add}">
								<input type="button" class="button" onclick="javascript: parent.location.reload(); parent.GB_hide();" value="{$button.close}">
								<input type="button" class="button" onclick="javascript: parent.location.href='hotlist.php';" value="{$lang.top_menu.my_hotlist}">
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>
</div>
</body>
</html>