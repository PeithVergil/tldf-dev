{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.back_link}
		<p class="_extra-back"><a href="{$form.back_link}">{$lang.button.back_to_organizer}</a></p>
	{/if}
	<div class="hdr2">
		{if $form.sel == 'i'}
			{$lang.subsection.my_kiss}
		{else}
			{$lang.subsection.me_kiss}
		{/if}
	</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<div style="padding-bottom:10px;" class="det-14-2">
		{if $form.sel == 'i'}
			{$header_s.toptext_mykiss.$user_gender}
		{else}
			{$header_s.toptext_mekiss.$user_gender}
		{/if}
	</div>
	{include file="$gentemplates/user_list_top.tpl"}
	{if $empty}
		<div class="error_msg">
			{if $form.sel == 'i'}
				{$header_s.mykiss.empty_result.$user_gender}
			{else}
				{$header_s.mekiss.empty_result.$user_gender}
			{/if}
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