<tr bgcolor="#ffffff">
	<td align="right" width="15%" class="main_header_text">{$header.ccbill_seller_id}:&nbsp;</td>
	<td class="main_header_text" align="left"><input type="text" size="30" name="seller_id" value="{$data.seller_id}"></td>
	<td class="main_header_text" align="left" width="70%">&nbsp;<input type=checkbox name=use value=1 {if $data.use}checked{/if}>&nbsp;{$header.use}</td>
</tr>
<tr bgcolor="#ffffff">
	<td align="right" width="15%" class="main_header_text">{$header.ccbill_seller_sub_id}:&nbsp;</td>
	<td class="main_header_text" align="left"><input type="text" size="30" name="seller_sub_id" value="{$data.seller_sub_id}"></td>
	<td class="main_header_text" align="left" width="70%">&nbsp;</td>
</tr>
<tr bgcolor="#ffffff">
	<td align="right" width="15%" class="main_header_text">{$header.ccbill_form_name}:&nbsp;</td>
	<td class="main_header_text" align="left"><input type="text" size="30" name="form_name" value="{$data.form_name}"></td>
	<td class="main_header_text" align="left" width="70%">&nbsp;</td>
</tr>
<tr bgcolor="#ffffff">
	<td align="right" width="15%" class="main_header_text">{$header.ccbill_language}:&nbsp;</td>
	<td class="main_header_text" align="left"><input type="text" size="30" name="language" {if $data.language} value="{$data.language}" {else} value="English" {/if}></td>
	<td class="main_header_text" align="left" width="70%">&nbsp;</td>
</tr>