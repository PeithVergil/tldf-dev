{include file="$gentemplates/index_top.tpl"}
<div class="toc page-simple">
	{if $form.back_link}
		<p class="_extra-back"><a href="{$form.back_link}">{$lang.button.back_to_organizer}</a></p>
	{/if}
	<div class="hdr2">{$lang.subsection.meet_me}</div>
	{if $form.err}
		<div class="error_msg">* {$form.err}</div>
	{/if}
	<div style="padding-bottom:10px;" class="det-14-2">{$header_s.toptext_meetme.$user_gender}</div>
	{if $empty eq 1}
		<div class="error_msg">{$header_s.empty_result}</div>
	{elseif $empty eq 2}
		<div class="error_msg">{$err.back_to_my_profile} <a href="{$form.profile_link}">{$header_s.my_profile}</a></div>
	{/if}
	{strip}
	{if $search_res}
		<div class="det-14-2">{$section.search_result}: <b>{$form.pages_count} {$lang.pages}</b></div>
		<div class="user-list">
			{include file="$gentemplates/user_list.tpl"}
		</div>
		<ol class="page-nation">
			{foreach item=item from=$links}
			<li><a href="{$item.link}" {if $item.selected eq '1'} class="selected"{/if}>{$item.name}</a></li>
			{/foreach}
		</ol>
	{/if}
	{/strip}
</div>
{include file="$gentemplates/index_bottom.tpl"}