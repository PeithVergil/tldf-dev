{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font><br><br><br>
				<table><tr>
				<form name="areas_form" action="{$form.action}" method="post">
				{$form.hiddens}
					<td class="main_content_text" >&nbsp;{$header.areas}:&nbsp;</td>
					<td class="main_content_text" ><select name="area" onchange="javascript: document.areas_form.submit();">
					<option value="1" {if $data.area eq 1}selected{/if}>{$header.area1}</option>
					<option value="4" {if $data.area eq 4}selected{/if}>{$header.area4}</option>
					<option value="5" {if $data.area eq 5}selected{/if}>{$header.area5}</option>
					<option value="6" {if $data.area eq 6}selected{/if}>{$header.area6}</option>
					{if $use_pilot_module_blog eq 1}<option value="7" {if $data.area eq 7}selected{/if}>{$header.area7}</option>{/if}
					{if $use_pilot_module_club eq 1}<option value="8" {if $data.area eq 8}selected{/if}>{$header.area8}</option>{/if}
					{if $use_pilot_module_giftshop eq 1}<option value="9" {if $data.area eq 9}selected{/if}>{$header.area9}</option>{/if}
					{if $use_pilot_module_events eq 1}<option value="10" {if $data.area eq 10}selected{/if}>{$header.area10}</option>{/if}
					{if $use_pilot_module_forum eq 1}<option value="11" {if $data.area eq 11}selected{/if}>{$header.area11}</option>{/if}
					<option value="12" {if $data.area eq 12}selected{/if}>{$header.area12}</option>
					</select></td>
				</form>
				</tr></table>
				<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
							{if $links}
							<tr bgcolor="#ffffff">
								<td height="20"  colspan=6 align="left"  class="main_content_text" >{$links}</td>
							</tr>
							{/if}
							<tr class="table_header">
								<td class="main_header_text" align="center" width="10">{$header.number}</td>
								<td class="main_header_link" align="middle"><div onclick="javascript:location.href='{$form.orderby_1}';">{$header.name}</div></td>
								<td class="main_header_link" align="middle"><div onclick="javascript:location.href='{$form.orderby_2}';">{$header.date}</div></td>
								<td class="main_header_text" align="middle">&nbsp;</td>
								<td class="main_header_text" align="middle">&nbsp;</td>
								<td class="main_header_text" align="middle">&nbsp;</td>
							</tr>
							{if $vialocations}
							{section name=spr loop=$vialocations}
							<tr bgcolor="#ffffff">
								<td class="main_content_text" align="center">{$vialocations[spr].number}</td>
								<td class="main_content_text" align="center">{$vialocations[spr].name}</td>
								<td class="main_content_text" align="center">{$vialocations[spr].date}</td>
								<td width=80 align="center"><a href="#" onclick="window.open('{$vialocations[spr].statistic_link}','statistic', 'height=475, resizable=yes, scrollbars=yes,width=400, menubar=no,status=no');">{$button.statistic}</a></td>
								<td width=102 align="center"><a href="#" onclick="window.open('{$vialocations[spr].communicate_link}','comunicate', 'height=600, resizable=yes, scrollbars=yes,width=600, menubar=no,status=no');">{$header.comunicate}</a></td>
								<td width=80 align="center"><a href="{$vialocations[spr].delete_link}">{$button.delete}</a></td>
							</tr>
							{/section}
							{else}
							<tr bgcolor="#ffffff" height="40">
								<td class="main_error_text" align="left" colspan="6" bgcolor="#FFFFFF">{$header.empty}</td>
							</tr>
							{/if}
							{if $links}
							<tr bgcolor="#ffffff">
								<td height="20"  colspan=6 align="left"  class="main_content_text" >{$links}</td>
							</tr>
							{/if}
						</form>
					</table>
{include file="$admingentemplates/admin_bottom.tpl"}