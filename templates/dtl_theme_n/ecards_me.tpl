{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.back_link}
		<p class="_extra-back"><a href="{$form.back_link}">{$lang.button.back_to_organizer}</a></p>
	{/if}
	<div class="hdr2">
		{$lang.subsection.ecards_me}
	</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<div style="padding-bottom:10px;" class="det-14-2">
		{$header_s.toptext_ecards_me}
	</div>
	{include file="$gentemplates/user_list_top.tpl"}
	{if $empty}
		<div class="error_msg">
			{$header_s.ecards_me.empty_result}
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