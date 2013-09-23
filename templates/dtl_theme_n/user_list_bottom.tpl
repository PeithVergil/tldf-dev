{strip}
<div class="clear" style="margin-top:10px;">
	<div class="_fright">
		Results Per Page:
		{foreach item=item from=$rpp_links}
			<span style="padding-left:10px;"><a href="{$item.link}" {if $item.selected}class="text_head"{/if}>{$item.name}</a></span>
		{/foreach}
	</div>
	<ol class="page-nation">
		{foreach item=item from=$links}
			<li><a href="{$item.link}" {if $item.selected}class="selected"{/if}>{$item.name}</a></li>
		{/foreach}
	</ol>
</div>
{/strip}