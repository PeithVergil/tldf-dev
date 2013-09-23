<tr bgcolor="#ffffff">
	<td colspan=3>
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="main_content_text">{$header.create_account}&nbsp;&nbsp;&nbsp;</td>
			<td class="main_content_text"><a href="http://www.e-gold.com/e-gold.asp?cid=4506679" target="_blank"><img src="{$site_root}{$template_root}/images/egold_logo.gif" border=0></a></td>
		</tr>
		</table>
	</td>
</tr>
<tr bgcolor="#ffffff">
	<td align="right" width="15%" class="main_header_text">{$header.egold_seller_id}:&nbsp;</td>
	<td class="main_content_text" align="right"><input type="text" size="30" name="value" value="{$data.value}"></td>
	<td class="main_header_text" align="left" width="70%">&nbsp;<input type=checkbox name=use value=1 {if $data.use}checked{/if}>&nbsp;{$header.use}</td>
</tr>