{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.friend_login}
		<p class="_extra-back"><a href="connections.php">&laquo; {$header.back_to_connections}</a></p>
	{/if}
	<div class="hdr2">
		{if $form.friend_login}
			{$header.friend_connections_header} {$form.friend_login}
		{else}
			{$lang.subsection.connections}
		{/if}
	</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	{*<!--
	<div style="padding-bottom:10px;" class="det-14-2">
		{if $type == 1}
			{$header.toptext_connections_inbox.$user_gender}
		{elseif $type == 2}
			{$header.toptext_connections_outbox.$user_gender}
		{else}
			{$header.toptext_connections.$user_gender}
		{/if}
	</div>
	-->*}
	<div style="padding-bottom:15px;">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<a href="connections.php?sel=inbox"><img src="{$site_root}{$template_root}/images/int_1.gif" border="0" alt="" vspace="0" hspace="0"></a>
				</td>
				<td style="padding-left: 3px;" class="text">
					<a href="connections.php" {if $form.sel == ''}class="text_head"{/if}>{$header.confirmed_connections}&nbsp; ({$form.confirmed_all})</a>
				</td>
				<td style="padding-left: 25px;">
					<a href="connections.php?sel=inbox"><img src="{$site_root}{$template_root}/images/inbox_icon.png" border="0" alt="" vspace="0" hspace="0"></a>
				</td>
				<td style="padding-left: 3px;" class="text">
					<a href="connections.php?sel=inbox" {if $form.sel == 'inbox'}class="text_head"{/if}>{$header.connections_inbox}&nbsp; ({$form.inbox_all})</a>
				</td>
				<td style="padding-left: 25px;">
					<a href="connections.php?sel=outbox"><img src="{$site_root}{$template_root}/images/outbox_icon.png" border="0" alt="" vspace="0" hspace="0"></a>
				</td>
				<td style="padding-left: 3px;" class="text">
					<a href="connections.php?sel=outbox" {if $form.sel == 'outbox'}class="text_head"{/if}>{$header.connections_outbox}&nbsp; ({$form.outbox_all})</a>
				</td>
			</tr>
		</table>
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
	{elseif $form.sel != 'deleted'}
		<div class="error_msg">
			{if $type == 1}
				{$header.empty_result_inbox.$user_gender}
			{elseif $type == 2}
				{$header.empty_result_outbox.$user_gender}
			{else}
				{$header.empty_result.$user_gender}
			{/if}
		</div>
	{/if}
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}