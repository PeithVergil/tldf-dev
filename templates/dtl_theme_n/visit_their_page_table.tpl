{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.back_link}
		<p class="_extra-back"><a href="{$form.back_link}">{$lang.button.back_to_organizer}</a></p>
	{/if}
	<div class="hdr2">
		{$lang.subsection.visited_their_page}
	</div>
	<div style="padding-bottom:10px;" class="det-14-2">
		{$header_s.toptext_visited_their_page.$user_gender}
	</div>
	{include file="$gentemplates/user_list_top.tpl"}
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	{if $empty}
		<div class="error_msg">
			{$header_s.visited_their_page.empty_result.$user_gender}
		</div>
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