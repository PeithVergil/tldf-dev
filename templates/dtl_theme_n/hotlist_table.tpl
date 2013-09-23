{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.back_link}
		<p class="_extra-back"><a href="{$form.back_link}">{$lang.button.back_to_organizer}</a></p>
	{/if}
	<div class="hdr2">
		{if $form.friend_login}
			{$header.friend_hotlist_header} {$form.friend_login}
		{else}
			{$lang.subsection.hotlist}
		{/if}
	</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<div style="padding-bottom:10px;" class="det-14-2">
		{$header.toptext_hotlist.$user_gender}
	</div>
	{include file="$gentemplates/user_list_top.tpl"}
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
	{else}
		<div class="error_msg">
			{$header.empty_result_hotlist.$user_gender}
		</div>
	{/if}
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}