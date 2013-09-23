{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list_city}{$region_name}, {$country_name}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.ref_cities}</div>

				<table border=0 cellspacing=1 cellpadding=5 width="100%">
						<form method="post" action="{$form.action}"  enctype="multipart/form-data" name="add_form">
						{$form.hiddens}
							<tr bgcolor="#ffffff">
								<td colspan=3 class="main_header_text" align="left" >{$header.editform_city}</td>
							</tr>
							<tr bgcolor="#ffffff">
								<td align="right" width="10%" class="main_header_text">{$header.city}:&nbsp;</td>
								<td class="main_content_text" align="left"><input type="text" name="name" value="{$name}" size=30></td>
								<td class="main_header_text" align="left" width="75%">&nbsp;</td>
							</tr>
							<tr bgcolor="#ffffff">
								<td align="right" width="10%" class="main_header_text">{$header.zip_code}:&nbsp;</td>
								<td class="main_content_text" align="left"><input type="text" name="zip_code" value="{$zip_code}" size=30></td>
								<td class="main_content_text" align="left" width="75%">&nbsp;({$header.zip_code_comment})</td>
							</tr>
							</form>
					</table>
<br>
<input type="button" value="{$header.add}" class="button" onclick="javascript: document.add_form.submit();">
<br><br>

				<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=4 align="left"  class="main_content_text" >{$links}</td>
					</tr>
					{/if}
							<tr>
								<td class="main_header_text" align="center" bgcolor="#FFFFFF" width="20">{$header.number}</td>
								<td class="main_header_text" align="middle" bgcolor="#FFFFFF" >{$header.city}</td>
								<td class="main_header_text" align="middle" bgcolor="#FFFFFF" >{$header.zip_code}</td>
								<td class="main_header_text" align="middle" bgcolor="#FFFFFF" width="100">&nbsp;</td>
							</tr>
							{if $cities}
							{section name=spr loop=$cities}
							<tr>
								<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$cities[spr].number}</td>
								<td class="main_content_text" align="center" bgcolor="#FFFFFF"><a href="{$cities[spr].edit_link}">{$cities[spr].name}</a></td>
								<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$cities[spr].zip_code}</td>
								<td class="main_content_text" align="middle" bgcolor="#FFFFFF"><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$cities[spr].deletelink}'{literal}}{/literal}"></td>
							</tr>
							{/section}
							{else}
							<tr height="40">
								<td class="main_error_text" align="left" colspan="4" bgcolor="#FFFFFF">{$header.empty_city}</td>
							</tr>
							{/if}
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=4 align="left"  class="main_content_text" >{$links}</td>
					</tr>
					{/if}
					</table><br>
<input type="button" value="{$header.back_to_regions}" class="button" onclick="javascript: location.href='{$back_link}'">

{include file="$admingentemplates/admin_bottom.tpl"}