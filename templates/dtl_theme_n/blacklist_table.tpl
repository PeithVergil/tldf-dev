{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.back_link}
		<p class="_extra-back"><a href="{$form.back_link}">{$lang.button.back_to_organizer}</a></p>
	{/if}
	<div class="hdr2">
		<label title="{$lang.section.black_list_thai}">{$lang.section.black_list}</label>
	</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<div style="padding-bottom:10px;" class="det-14-2">
		<label title="{$header_s.toptext_black_thai}">
			{$header_s.toptext_black.$user_gender}
		</label>
	</div>
	{include file="$gentemplates/user_list_top.tpl"}
	{if $empty}
		<div class="error_msg">
			{$header_s.empty_result_blacklist.$user_gender}
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