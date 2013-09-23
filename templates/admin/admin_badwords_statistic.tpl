{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.user_statistic} : {$data.username}</font><br><br><br>
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
<tr bgcolor="#FFFFFF">
	<td width="50%" class="main_content_text" align="center">{$header.area1}</td>
	<td width="50%" class="main_content_text" align="center">{$statistic.1.coll}</td>
</tr>
<tr bgcolor="#FFFFFF">
	<td width="50%" class="main_content_text" align="center">{$header.area4}</td>
	<td width="50%" class="main_content_text" align="center">{$statistic.4.coll}</td>
</tr>
<tr bgcolor="#FFFFFF">
	<td width="50%" class="main_content_text" align="center">{$header.area5}</td>
	<td width="50%" class="main_content_text" align="center">{$statistic.5.coll}</td>
</tr>
<tr bgcolor="#FFFFFF">
	<td width="50%" class="main_content_text" align="center">{$header.area6}</td>
	<td width="50%" class="main_content_text" align="center">{$statistic.6.coll}</td>
</tr>
{if $use_pilot_module_blog eq 1}
<tr bgcolor="#FFFFFF">
	<td width="50%" class="main_content_text" align="center">{$header.area7}</td>
	<td width="50%" class="main_content_text" align="center">{$statistic.7.coll}</td>
</tr>
{/if}
{if $use_pilot_module_club eq 1}
<tr bgcolor="#FFFFFF">
	<td width="50%" class="main_content_text" align="center">{$header.area8}</td>
	<td width="50%" class="main_content_text" align="center">{$statistic.8.coll}</td>
</tr>
{/if}
{if $use_pilot_module_giftshop eq 1}
<tr bgcolor="#FFFFFF">
	<td width="50%" class="main_content_text" align="center">{$header.area9}</td>
	<td width="50%" class="main_content_text" align="center">{$statistic.9.coll}</td>
</tr>
{/if}
{if $use_pilot_module_events eq 1}
<tr bgcolor="#FFFFFF">
	<td width="50%" class="main_content_text" align="center">{$header.area10}</td>
	<td width="50%" class="main_content_text" align="center">{$statistic.10.coll}</td>
</tr>
{/if}
{if $use_pilot_module_forum eq 1}
<tr bgcolor="#FFFFFF">
	<td width="50%" class="main_content_text" align="center">{$header.area11}</td>
	<td width="50%" class="main_content_text" align="center">{$statistic.11.coll}</td>
</tr>
{/if}
<tr bgcolor="#FFFFFF">
	<td width="50%" class="main_content_text" align="center">{$header.area12}</td>
	<td width="50%" class="main_content_text" align="center">{$statistic.12.coll}</td>
</tr>
<tr bgcolor="#FFFFFF">
	<td width="50%" align="center" class="table_main"><font class="main_content_text"><b>{$header.all_statistic}</b></font></td>
	<td width="50%" align="center" class="table_main"><font class="main_content_text">{$statistic.all}</font></td>
</tr>
</table>
<table>
<tr height="40">
	<td><a href="#" onclick="window.close();opener.focus();">{$button.close}</a></td>
</tr>
</table>
{include file="$admingentemplates/admin_bottom.tpl"}