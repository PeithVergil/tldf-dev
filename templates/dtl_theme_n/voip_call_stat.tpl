{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr valign="middle"><td height="25px">
			<div class="header">{$lang.section.voip_call} {$lang.voip.statistic}</div>
	</td></tr>
	<tr><td class="text" style="padding-top:5px">
			{include file="$gentemplates/voip_user_stat_table.tpl"}
	</td></tr>
	</table>
	<!-- end main cell -->
</td>
{include file="$gentemplates/index_bottom.tpl"}