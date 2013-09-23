<tr bgcolor="#ffffff">
	<td class="main_header_text">{$header.paypoint_seller_id}:&nbsp;</td>
	<td class="main_content_text" align="right"><input type="text" size="30" name="value" value="{$data.value}"></td>
	<td class="main_header_text" align="left" width="70%">&nbsp;<input type=checkbox name=use value=1 {if $data.use}checked{/if}>&nbsp;{$header.use}</td>
</tr>
<tr bgcolor="#ffffff">
	<td colspan="3"><i>{$header.paypoint_seller_id_hint}</i></td>
</tr>