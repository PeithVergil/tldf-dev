{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple mailbox">
	<div class="hdr2">{$lang.section.email}</div>
	{if $form.back_link}
		<div style="padding-top: 3px;">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif" border="0" alt="" vspace="0" hspace="0"></td>
					<td>&nbsp;<a href="{$form.back_link}">{$lang.button.back_to_organizer}</a></td>
				</tr>
			</table>
		</div>
	{/if}
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
					<a href="mailbox.php?sel=inbox">{$header.inbox}</a>&nbsp;({$form.inbox_new}/{$form.inbox_all})
				</td>
				{* hide outbox from applicants *}
				{if !$auth.is_applicant}
					<td style="padding-left: 15px;">
						<img src="{$site_root}{$template_root}/images/outbox_icon.gif" border="0" alt="" vspace="0" hspace="0">
					</td>
					<td style="padding-left: 5px;" class="text">
						<a href="mailbox.php?sel=outbox" class="text_head">{$header.outbox}</a>&nbsp;({$form.outbox_all})
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
	<div style="padding-top: 15px;">
		{if !$maillist}
			<div align="center">
            	<div class="error_msg">{$header.empty_outbox.$user_gender}</div>
			</div>
		{else}
			<form action="mailbox.php" method="post" name="mailbox_form" style="margin:0px">
				<input type="hidden" name="sel" value="delfrom">
				{foreach key=key item=item from=$maillist name=maillist}
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="inbox_mail">
						<tr>
							<td width="15" height="25" style="padding-left: 8px;">
								<img src="{$site_root}{$template_root}/images/message_reply_icon.gif" border="0" alt="" vspace="0" hspace="0">
							</td>
							<td style="padding-left: 13px;" width="135" class="text">{$item.from}</td>
							<td style="padding-left: 10px;"><a href="mailbox.php?sel=viewfrom&amp;id={$item.id}">{$item.subject}</a></td>
							<td style="padding-left: 10px;" width="135" class="text">{$item.date}&nbsp;{$item.time}</td>
							<td style="padding-left: 10px; padding-right: 25px;" width="17">
								{if $item.was_read == '1'}
									<img src="{$site_root}{$template_root}/images/read_icon.gif" border="0" alt="" vspace="0" hspace="0">
								{/if}
							</td>
							<td align="right" width="20" style="padding-right: 15px;">
								<input type="checkbox" name="delete[{$smarty.foreach.maillist.iteration}]" value="{$item.id}">
							</td>
						</tr>
						
					</table>
				{/foreach}
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td style="padding: 10px 10px 0px 10px;">
							{if $links}
                                                            <ol class="page-nation">
								<li>{$lang.pages}:&nbsp;</li>
								{foreach item=item from=$links name=links}
									<li><a href="{$item.link}" {if $item.selected}class="text"{/if}>{$item.name}</a></li>
								{/foreach}
                                                                </ol>
							{/if}
						</td>
						<td style="padding: 12px 0px;" align="right">
							<input type="button" class="normal-btn" onclick="javascript: this.form.submit();" value="{$button.delete}">
						</td>
					</tr>
				</table>
			</form>
		{/if}
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}