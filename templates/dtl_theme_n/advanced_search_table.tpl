{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-profile">
	<div class="hdr2">{$lang.section.a_search}: {$lang.subsection.search_result}</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<div style="padding-bottom:10px;" class="det-14-2">{$header_s.toptext}</div>
	{include file="$gentemplates/user_list_top.tpl"}
	{if $empty}
		<div class="error_msg" style="margin-top:20px;">{$header_s.empty_result}</div>
	{/if}
	{if $search_res}
		{if $form.view == 'gallery'}
			<div class="delimiter">&nbsp;</div>
			{include file="$gentemplates/user_list_gallery.tpl"}
		{else}
			<div class="user-list">
				{include file="$gentemplates/user_list.tpl"}
			</div>
		{/if}
		{include file="$gentemplates/user_list_bottom.tpl"}
	{/if}
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}