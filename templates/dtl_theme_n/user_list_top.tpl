{strip}
<div class="clear" style="margin-top:10px;">
	<div class="user-link _fright">
		<a href="{$form.view_online_link}" {if $form.filter == 'online'}class="text_head"{/if}>{$lang.search.view_online} ({$form.online_count})</a> |&nbsp;
		{*<!--
			<a href="{$form.view_photo_link}" {if $form.filter == 'photo'}class="text_head"{/if}>{$lang.search.view_photo} ({$form.with_count})</a> |&nbsp;
		-->*}
		<a href="{$form.view_all_link}" {if $form.filter == 'all'}class="text_head"{/if}>{$lang.search.view_all} ({math equation='x+y' x=$form.online_count y=$form.offline_count})</a> |&nbsp;
		{if $form.view == 'gallery'}
			<a href="{$form.view_list_link}">{$lang.search.view_list}</a>
		{else}
			<a href="{$form.view_gallery_link}">{$lang.search.view_gallery}</a>
		{/if}
	</div>
	<div class="det-14-2">{$section.search_result}: <b>{$form.pages_count|default:0} {$lang.pages}</b></div>
</div>
{/strip}