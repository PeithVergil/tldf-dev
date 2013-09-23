{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.ref_languages}</div>

				<table cellSpacing="3" cellPadding="0">
					<form method="post" action="{$form.action}"  enctype="multipart/form-data" name="add_form">
					{$form.hiddens}
						<tr>
							<td align="right" class="main_header_text">{$header.name}:&nbsp;</td>
							<td class="main_content_text" align="left"><input type="text" name="name" value="{$name}" size=30></td>
							<td class="main_content_text" align="left" width="75%"><input type="button" value="{$header.add}" class="button" onclick="javascript: document.add_form.submit();"></td>
						</tr>
					</form>
				</table>
		<!-- /form -->
<br>
				<table cellSpacing="3" cellPadding="0">
							<tr>
							{section name=s loop=$lang_link}
								<td align="center"><a href="#" onclick="javascript:window.open('{$lang_link[s].link}','lang_edit', 'height=600, resizable=yes, scrollbars=yes,width=800, menubar=no,status=no'); return false;" class=privacy_link>{$lang_link[s].name}</a></td>
								{if !$smarty.section.s.last}
								<td align="center" valign="middle" class="main_content_text">&nbsp;|&nbsp;</td>
								{/if}
							{/section}
							</tr>
				</table>

				<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=3 align="left"  class="main_content_text" >{$links}</td>
					</tr>
					{/if}
							<tr class="table_header">
								<td class="main_header_text" align="center" width="20">{$header.number}</td>
								<td class="main_header_text" align="center">{$header.name}</td>
								<td class="main_header_text" align="center" width="100">&nbsp;</td>
							</tr>
							{if $languages}
							{section name=spr loop=$languages}
							<tr bgcolor="#FFFFFF">
								<td class="main_content_text" align="center">{$languages[spr].number}</td>
								<td class="main_content_text" align="center">{$languages[spr].name}</td>
								<td class="main_content_text" align="center"><input type="button" value="{$button.delete}" class="button" onclick="javascript: if(confirm('{$form.confirm}')){literal}{{/literal}location.href='{$languages[spr].deletelink}'{literal}}{/literal}"></td>
							</tr>
							{/section}
							{else}
							<tr height="40">
								<td class="main_error_text" align="left" colspan="3" bgcolor="#FFFFFF">{$header.empty}</td>
							</tr>
							{/if}
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=3 align="left"  class="main_content_text" >{$links}</td>
					</tr>
					{/if}
					</table>
{include file="$admingentemplates/admin_bottom.tpl"}