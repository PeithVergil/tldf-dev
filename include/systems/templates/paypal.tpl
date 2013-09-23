<tr>
	<td colspan="3">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td class="main_content_text">{$header.create_account}&nbsp;&nbsp;&nbsp;</td>
				<td class="main_content_text"><a href="https://www.paypal.com/row/mrb/pal=A3DN2HQEBPALA" target="_blank"><img src="{$site_root}{$template_root}/images/paypal_logo.gif" border=0></a></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td align="right" width="15%" class="main_header_text">{$header.paypal_seller_id}:&nbsp;</td>
	<td class="main_content_text" align="right"><input type="text" size="50" name="value" value="{$data.value}" /></td>
	<td class="main_header_text" align="left" width="70%">&nbsp;<input type="checkbox" name="use" value="1" {if $data.use}checked="checked"{/if} />&nbsp;{$header.use}</td>
</tr>
{* RS: TLDF uses both recurring and non-recurring periods, a global flag does not make sense
<tr>
	<td align="right" width="15%" class="main_header_text">{$header.paypal_recurring}:&nbsp;</td>
	<td class="main_content_text" align="left"><input type="checkbox" name="recurring" value="1" {if $data.recurring}checked="checked"{/if} /></td>
	<td class="main_header_text" align="left" width="70%">&nbsp;</td>
</tr>
*}
<tr>
	<td align="right" width="15%" class="main_header_text">{$header.paypal_sandbox}:&nbsp;</td>
	<td colspan="2" class="main_content_text" align="left">
		<input type="checkbox" name="sandbox" value="1" {if $data.sandbox}checked="checked"{/if} /> {$header.paypal_sandbox_info}
	</td>
</tr>
<tr>
	<td align="right" width="15%" class="main_header_text">{$header.paypal_debug}:&nbsp;</td>
	<td colspan="2" class="main_content_text" align="left">
		<input type="checkbox" name="debug" value="1" {if $data.debug}checked="checked"{/if} /> {$header.paypal_debug_info}
	</td>
</tr>