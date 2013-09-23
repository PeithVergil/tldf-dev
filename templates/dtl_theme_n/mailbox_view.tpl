{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple mailbox">
	<div class="hdr2">{$lang.section.email}</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<div style="padding-top: 10px;">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td style="padding-left: 10px;">
					<img src="{$site_root}{$template_root}/images/inbox_icon.gif" border="0" alt="" vspace="0" hspace="0">
				</td>
				<td style="padding-left: 5px;" class="text">
					<a href="mailbox.php?sel=inbox" {if $par == 'to'} class="text_head" {/if}>{$header.inbox}</a> ({$form.inbox_new}/{$form.inbox_all})
				</td>
				{* hide outbox from applicants *}
				{if !$auth.is_applicant}
					<td style="padding-left: 15px;">
						<img src="{$site_root}{$template_root}/images/outbox_icon.gif" border="0" alt="" vspace="0" hspace="0">
					</td>
					<td style="padding-left: 5px;" class="text">
						<a href="mailbox.php?sel=outbox" {if $par == 'from'} class="text_head" {/if}>{$header.outbox}</a> ({$form.outbox_all})
					</td>
				{/if}
				{* permission added by ralf *}
				{if $smarty.session.permissions.email_compose && !$auth.is_applicant}
					<td style="padding-left: 15px;">
						<img src="{$site_root}{$template_root}/images/compose_icon.gif" border="0" alt="" vspace="0" hspace="0">
					</td>
					<td style="padding-left: 5px;" class="text">
						<a href="mailbox.php?sel=write">{$header.compose}</a>
					</td>
				{/if}
			</tr>
		</table>
	</div>
	<p>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><img src="{$site_root}{$template_root}/images/btn_back.gif" border="0" alt="" vspace="0" hspace="0"></td>
				<td style="padding-left: 3px;">
					{if $par == 'to'}
						<a href="mailbox.php?sel=inbox">{$header.back_to_inbox}</a>
					{elseif $par == 'from'}
						<a href="mailbox.php?sel=outbox">{$header.back_to_outbox}</a>
					{/if}
				</td>
			</tr>
		</table>
	</p>
	<div>
		<table border="0" cellpadding="0" width="100%" cellspacing="0">
			<tr>
				<td width="120" valign="top" class="user-icon">
					{if ! $data.root_user}<a href="viewprofile.php?id={$data.id}">{/if}
					<img src="{$data.big_icon_path}" class="icon" alt="" style="width:114px;">
					{if ! $data.root_user}</a>{/if}
				</td>
				<td valign="top" class="text" style="padding-left: 10px;" width="70%">
					<div style="margin-top: 7px">
						{if $data.root_user}
							<b> {$data.name} </b>
						{else}
							<a href="viewprofile.php?id={$data.id}"> <b>{$data.name}</b> </a>
							{if $form.show_users_group_str == '1'}
								<font class="text_head"> {$data.group} </font>
								<font class="text_head"> {$data.age} {$lang.home_page.ans} </font>
							{/if}
							<span class="{if $data.status == $lang.status.on}link{else}text{/if}_active"> {$data.status} </span>
						{/if}
					</div>
					{if ! $data.root_user}
						<p class="det-14">
							{if $data.city}{$data.city}, {/if}{if $data.region}{$data.region}, {/if}{$data.country}
						</p>
						<p>
							{* <font class="text_head">{$data.age} {$lang.home_page.ans}</font> *}
							{$lang.users.gender_search} {$data.gender_search} {$lang.users.from} {$data.age_min} {$lang.users.to} {$data.age_max} 
						</p>
					{/if}
					<p>
						<font class="text_hidden">{$data.photo_count} {if $data.photo_count == 1}{$lang.users.upload_1}{else}{$lang.users.photos_count}{/if}</font>
					</p>
					{if $smarty.const.MM_DISPLAY_PROFILE_COMPLETION && ! $data.root_user}
						{*<!--
						<div style="margin-top: 2px">
							<font class="text">{$lang.homepage.completion}: {$data.completion}%</font>
						</div>
						-->*}
					{/if}
				</td>
				<td valign="top" align="right" style="padding-left: 10px;">
					{if $data.connected_status == CS_CONNECTED}
						<img src="{$site_root}{$template_root}/images/connections_icon.png" alt="{$lang.search.added_to_hotlist}">
					{elseif $data.hotlisted}
						<img src="{$site_root}{$template_root}/images/hotlist_icon.png" alt="{$lang.search.added_to_hotlist}">
					{/if}
				</td>
			</tr>
		</table>
		{if ! $data.root_user}
			<div style="padding-top: 10px;">
				{if $data.add_hotlist_link}
					{if $form.use_friend_types}
						<a href="#" class="normal-btn" onclick="javascript:return GB_show('', '{$data.add_hotlist_link}', 400, 300);">{$header.add_hotlist}</a>
					{else}
						<a href="{$data.add_hotlist_link}" class="normal-btn">{$header.add_hotlist}</a>
					{/if}
				{/if}
				{if $data.add_connection_link}
					{if $form.use_friend_types}
						<a href="#" class="normal-btn" onclick="javascript:return GB_show('', '{$data.add_connection_link}', 400, 300);">{$header.add_connections}</a>
					{else}
						<a href="{$data.add_connection_link}" class="normal-btn">{$header.add_connections}</a>
					{/if}
				{/if}
				{if $data.add_blacklist_link}
					<a href="{$data.add_blacklist_link}" class="normal-btn">{$header.add_blacklist}</a>
				{/if}
			</div>
		{/if}
	</div>
	<div class="message_form">
		<div class="clear hist_head">
			<p class="_fright"><b>{$data.date_msg}</b></p>
			<p><b> {$data.subject_msg} </b></p>
		</div>
		<div style="padding: 10px;">{$data.text_msg}</div>
	</div>
	{if $data.attaches}
		<div style="padding-top: 5px; padding-left: 15px;">
			<div class="text">{$header.attaches}</div>
				{foreach item=item from=$data.attaches}
					<div style="margin: 0px; padding-top: 5px;">
						<a href="{$item.path}" target="_blank">{$item.name}</a>
					</div>
				{/foreach}
			</div>
		</div>
	{/if}
	<div style="padding: 15px 0 0 15px">
		{* permission added by ralf *}
		{if $par == 'to'}
			{if $data.root_user}
				<input type="button" class="normal-btn" value="{$header.reply}" onclick="window.location.href='contact.php';">
			{elseif $smarty.session.permissions.email_compose}
				<input type="button" class="normal-btn" value="{$header.reply}" onclick="window.location.href='mailbox.php?sel=reply&amp;id={$data.id_mail}';">
			{/if}
			<input type="button" class="normal-btn" value="{$header.delete}" onclick="DeleteMessageTo({$data.id_mail});">
		{elseif $par == 'from' && $smarty.session.permissions.email_compose}
			<input type="button" class="normal-btn" value="{$header.resend}" onclick="window.location.href='mailbox.php?sel=resend&amp;id={$data.id_mail}';">
			<input type="button" class="normal-btn" value="{$header.delete}" onclick="DeleteMessageFrom({$data.id_mail});">
		{/if}
	</div>
	{if $data.name_msg_last}
		<div style="padding: 10px 10px;" class="message_form">
			<div class="clear hist_head">
				<p class="_fright">{$data.date_msg_last}</p>
				<p><b>{$header.previous_email}:</b> {$data.subject_msg_last}</p>
			</div>
			<div style="padding: 10px;">{$data.text_msg_last}</div>
		</div>
	{/if}
	<div style="padding: 15px 0 0 15px">
		<a href="mailbox.php?sel=history&amp;id={$data.id}" class="normal-btn">{$header.history}</a>
	</div>
</div>
<script type="text/javascript">
{literal}
function DeleteMessageTo(id)
{
	jConfirm(
		"{/literal}{$lang.confirm.delete_message}{literal}",
		"{/literal}{$lang.confirm.delete_message_title}{literal}",
		function(result) { if (result) window.location.href = "mailbox.php?sel=delto&id=" + id; }
	);
}
function DeleteMessageFrom(id)
{
	jConfirm(
		"{/literal}{$lang.confirm.delete_message}{literal}",
		"{/literal}{$lang.confirm.delete_message_title}{literal}",
		function(result) { if (result) window.location.href = "mailbox.php?sel=delfrom&id=" + id; }
	);
}
{/literal}
</script>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}