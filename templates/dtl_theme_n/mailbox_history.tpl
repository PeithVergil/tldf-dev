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
					<a href="mailbox.php?sel=inbox">{$header.inbox}</a>  ({$form.inbox_new}/{$form.inbox_all})
				</td>
				{* hide outbox from applicants *}
				{if !$auth.is_applicant}
					<td style="padding-left: 15px;">
						<img src="{$site_root}{$template_root}/images/outbox_icon.gif" border="0" alt="" vspace="0" hspace="0">
					</td>
					<td style="padding-left: 5px;" class="text">
						<a href="mailbox.php?sel=outbox">{$header.outbox}</a>  ({$form.outbox_all})
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
	<div style="padding-top: 10px;">
		<div class="content" style="padding: 10px 10px;">
			<table border="0" cellpadding="0" width="100%" cellspacing="0">
				<tr>
					<td width="95" valign="top" class="user-icon">
						{if ! $data.root_user}<a href="{$data.profile_link}">{/if}
						<img src="{$data.big_icon_path}" class="icon" alt="" style="width:114px;">
						{if !$data.root_user}</a>{/if}
					</td>
					<td valign="top" class="text" style="padding-left: 10px;" width="70%">
						<p style="margin-top: 7px">
							{if ! $data.root_user}<a href="{$data.profile_link}">{/if}<b> {$data.name} </b>{if ! $data.root_user}</a>{/if}      
							{if $form.show_users_group_str eq '1'}<font class="text_head"><b> {$data.group} </b></font>      {/if}
							<span class="{if $data.status eq $lang.status.on}link{else}text{/if}_active"> {$data.status} </span>  
						</p>
						<p style="margin-top: 2px">
							{if $data.city}{$data.city}, {/if}{if $data.region}{$data.region}, {/if}{$data.country}
						</p>
						<p class="det-14">
							<font class="text_head"> {$data.age} {$lang.home_page.ans} </font>      
							<font class="text"> {$lang.users.gender_search}  {$data.gender_search} {$lang.users.from} {$data.age_min} {$lang.users.to} {$data.age_max} </font>  
						</p>
						<p style="margin-top: 2px">
							<span> {$data.photo_count} {$lang.users.upload_1} </span>  
						</p>
						{*<!--
						<div style="margin-top: 2px">
							<font class="text">{$lang.homepage.completion}:  {$data.completion}%</font>  
						</div>
						-->*}
					</td>
					<td valign="top" align="right" style="padding-left: 10px;">
						{if $data.connected_status == CS_CONNECTED}
							<img src="{$site_root}{$template_root}/images/connections_icon.png" alt="{$lang.search.added_to_hotlist}">
						{elseif $data.hotlisted}
							<img src="{$site_root}{$template_root}/images/hotlist_icon.png" alt="{$lang.search.added_to_hotlist}">
						{/if}
					</td>
				</tr>
				<tr>
					<td colspan="3" style="padding-top: 5px;">
						{if $data.add_hotlist_link}
							{if $form.use_friend_types}
								<a href="#" class="normal-btn" onclick="javascript:return GB_show('', '{$data.add_hotlist_link}', 400, 300);">{$header.add_hotlist}</a>
							{else}
								<a href="{$data.add_hotlist_link}" class="normal-btn">{$header.add_hotlist}</a>
							{/if}
							{assign var=sep value='1'}
						{/if}
						{if $data.add_connection_link}
							{if $sep}    |    {/if}
							{if $form.use_friend_types}
								<a href="#" class="normal-btn" onclick="javascript:return GB_show('', '{$data.add_connection_link}', 400, 300);">{$header.add_connections}</a>
							{else}
								<a href="{$data.add_connection_link}" class="normal-btn">{$header.add_connections}</a>
							{/if}
							{assign var=sep value='1'}
						{/if}
						{if $data.add_blacklist_link && ! $data.root_user}
							{if $sep}    |    {/if}
							<a href="{$data.add_blacklist_link}" class="normal-btn">{$header.add_blacklist}</a>
						{/if}
					</td>
				</tr>
			</table>
		</div>
	</div>
	{if $links}
		<ol class="page-nation">
			<li>{$lang.pages}:  </li>
			{foreach item=item from=$links}
				<li><a href="{$item.link}" {if $item.selected}class="selected"{/if}>{$item.name}</a>  </li>
			{/foreach}
		</ol>
	{/if}
	{foreach item=item from=$history name=history}
		<div style="padding: 10px;" class="message_form">
			<div class="clear hist_head">
				<p class="_fright"><b> {$item.date_msg} </b></p>
				<p><strong class="hd14"> {$item.name_msg} </strong>  <em> {$item.subject_msg}</em></p>											
			</div>
			<div>
				{$item.text_msg}
			</div>
		</div>
		{if ! $smarty.foreach.history.last}
			<div></div>
		{/if}
	{/foreach}
	{if $links}
		<ol class="page-nation">
			<li>{$lang.pages}:  </li>
			{foreach item=item from=$links}
				<li><a href="{$item.link}" {if $item.selected}class="selected"{/if}>{$item.name}</a>  </li>
			{/foreach}
		</ol>
	{/if}
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}