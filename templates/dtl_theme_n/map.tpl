{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	<h2 class="hdr2">{$lang.bottom.map}</h2>
	<div id="site_map">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="33%" valign="top">
					{foreach item=item key=key from=$map_links}
						{* if $key == 20 || $key == 40 *}
						{if $item.name == $lang.index_top_big_1 || $item.name == $lang.index_top_big_2}
							</td>
							<td width="33%" valign="top" style="padding-left:10px;">
						{/if}
						<div style="margin:1px; height:20px" class="link">
							<div style="margin:3px; margin-left:{if $item.level == 1}0{elseif $item.level == 2}15{elseif $item.level == 3}30{elseif $item.level == 4}45{/if}px; padding-top:2px">
								{if $item.is_folder}&raquo;{else}&nbsp;&nbsp;{/if}
								{if $item.is_active}
									<a href="{$item.link}" class="link" {if $item.is_new_window}{if $item.on_click}onclick="{$item.on_click}"{else}onclick="javascript: window.open('{$item.link}','','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;"{/if}{/if}> {$item.name} </a>
								{else}
									<span style="color:#000;">{$item.name}</span>
								{/if}
							</div>
						</div>
					{/foreach}
				</td>
			</tr>
		</table>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}