{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr valign="middle"><td height="25px" colspan=2>
			<div class="header">{$lang.section.success}</div>
	</td></tr>
	</table>
	{if $story}
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	{section name=s loop=$story}
	<tr>
		<td style="padding-top: 10px" class="text">
		{if $story[s].view_link_1}<a href="#" onclick="javascript: window.open('{$story[s].view_link_1}','photo_view','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;">{/if}{if $story[s].thumb_path_1}<img src="{$story[s].thumb_path_1}" border=0 alt="{$story[s].couple_name}">{/if}{if $story[s].view_link_1}</a><br><br>{/if}
		{if $story[s].view_link_2}<a href="#" onclick="javascript: window.open('{$story[s].view_link_2}','photo_view','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;">{/if}{if $story[s].thumb_path_2}<img src="{$story[s].thumb_path_2}" border=0 alt="{$story[s].couple_name}">{/if}{if $story[s].view_link_2}</a><br><br>{/if}
		{if $story[s].view_link_3}<a href="#" onclick="javascript: window.open('{$story[s].view_link_3}','photo_view','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;">{/if}{if $story[s].thumb_path_3}<img src="{$story[s].thumb_path_3}" border=0 alt="{$story[s].couple_name}">{/if}{if $story[s].view_link_3}</a><br><br>{/if}
		<td valign="top" width="100%">
		<div style="padding: 10px 0px 0px 10px" class="text_head">{$story[s].story_title}</div>
		<div style="padding: 10px 0px 0px 10px" class="text">{$story[s].description}</div>
		<div style="padding: 5px 0px 0px 10px" class="text_hidden">{$story[s].couple_name}</div>
		<div style="padding: 2px 0px 0px 10px" class="text_hidden">{$story[s].story_date}</div>
		</td>
	</tr>
	{/section}
	</table>
	<div style="margin: 0px"><div style="margin-left: 80px; margin-top: 10px">
	{foreach item=item from=$links}
		<div class="page_div{if $item.selected eq '1'}_active{/if}">
			<div style="margin: 5px"><a href="{$item.link}" class="page_link{if $item.selected eq '1'}_active{/if}">{$item.name}</a></div>
		</div>
	{/foreach}
	</div></div>
	{else}
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2"><div class="error_msg">{$lang.err.no_stories}</div></td>
	</tr>
	</table>
	{/if}
</td>
{include file="$gentemplates/index_bottom.tpl"}