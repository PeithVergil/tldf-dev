{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_city}</font><br><br><br>

					<table border=0 cellspacing=1 cellpadding=5 width="100%">
						<form method="post" action="{$form.action}"  enctype="multipart/form-data" name="city_form">
						{$form.hiddens}
							<tr bgcolor="#ffffff">
								<td align="right" width="10%" class="main_header_text">{$header.city}:&nbsp;</td>
								<td class="main_content_text" align="left"><input type="text" name="name" value="{$data.name}" size=30></td>
								<td class="main_header_text" align="left" width="75%">&nbsp;</td>
							</tr>
							<tr bgcolor="#ffffff">
								<td align="right" width="10%" class="main_header_text">{$header.zip_code}:&nbsp;</td>
								<td class="main_content_text" align="left"><input type="text" name="zip_code" value="{$data.zip_code}" size=30></td>
								<td class="main_content_text" align="left" width="75%">&nbsp;({$header.zip_code_comment})</td>
							</tr>
							</form>
					</table>
			<table><tr height="40">
			{if $data.root  ne 1}<td><input type="button" value="{$button.save}" class="button" onclick="javascript:document.city_form.submit();"></td>{/if}
			<td><input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$form.back}'"></td>
			</tr></table>
{include file="$admingentemplates/admin_bottom.tpl"}