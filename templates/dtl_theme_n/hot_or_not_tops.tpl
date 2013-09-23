<div class="content_alloc">
	<div class="header" style="color:#cc0000;">{$lang.hotornot.tops}</div>
	<div class="sep"></div>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		{foreach name=tops from=$tops item=item}
		<td align="left">
			<a href="{$item.view_link}"><img src="{$item.icon_path}" border="0" /></a><br />
			<div class="error_msg">{$item.rating}&nbsp;<a href="{$item.view_link}">{$item.login}</a></div>
		</td>
		{/foreach}
	</tr>
	</table>
	<div align="right"><a href="quick_search.php?sel=search_top">{$lang.hotornot.view_all}</a></div>
</div>
<div class="sep"></div>