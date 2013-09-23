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
					<a href="mailbox.php?sel=inbox" class="text_head">{$header.inbox}</a>&nbsp;({$form.inbox_new}/{$form.inbox_all})
				</td>
				{* hide outbox from applicants *}
				{if !$auth.is_applicant}
					<td style="padding-left: 15px;">
						<img src="{$site_root}{$template_root}/images/outbox_icon.gif" border="0" alt="" vspace="0" hspace="0">
					</td>
					<td style="padding-left: 5px;" class="text">
						<a href="mailbox.php?sel=outbox">{$header.outbox}</a>&nbsp;({$form.outbox_all})
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
	<div class="sep"></div>
	<form action="mailbox.php" enctype="multipart/form-data" method="post" name="delete_after_form">
		<input type="hidden" name="sel" value="save_period">
		<div style="padding-left: 10px;">
			<table cellpadding="0" cellspacing="4" border="0">
				<tr>
					<td><input type="checkbox" name="delete_after_form_status" value="1" {if $delete_after_form.status}checked="checked"{/if} onclick="javascript: switchForm(document.delete_after_form);"/></td>
					<td>{$lang.mailbox.delete_after_comment}</td>
					<td><input type="text" size="3" name="delete_after_form_amount" value="{$delete_after_form.amount}" {if ! $delete_after_form.status}disabled="disabled"{/if}/></td>
					<td>
						<select name="delete_after_form_period" {if ! $delete_after_form.status}disabled="disabled"{/if}>
							<option value="day" {if $delete_after_form.period == 'day'}selected="selected"{/if}>{$lang.periods.day}</option>
							<option value="month" {if $delete_after_form.period == 'month'}selected="selected"{/if}>{$lang.periods.month}</option>
							<option value="year" {if $delete_after_form.period == 'year'}selected="selected"{/if}>{$lang.periods.year}</option>
						</select>
					</td>
					<td><input type="button" class="normal-btn" value="{$lang.button.save}" onclick="javascript: checkForm(document.delete_after_form);"/></td>
				</tr>
			</table>
		</div>
	</form>
	<div style="padding-top: 15px;">
		{if !$maillist}
			<div class="mailbox_div" align="center">
            	<div class="error_msg">{$header.empty}</div>
			</div>
		{else}
			<form action="mailbox.php" method="post" name="mailbox_form" style="margin:0px">
				<input type="hidden" name="sel" value="delto">
				{foreach item=item from=$maillist name=maillist}
					<table width="100%" border="0" cellpadding="0" cellspacing="0"  class="inbox_mail {if $item.new}unread_mail{else}read{/if}">
						<tr>
							<td width="15" height="25" style="padding-left: 8px;">
								{if $item.attach == 1}
									{if $item.new}
										<img src="{$site_root}{$template_root}/images/message_unread_icon_attach.gif" border="0" alt="" vspace="0" hspace="0">
									{else}
										<img src="{$site_root}{$template_root}/images/message_read_icon_attach.gif" border="0" alt="" vspace="0" hspace="0">
									{/if}
								{else}
									{if $item.new}
										<img src="{$site_root}{$template_root}/images/message_unread_icon.gif" border="0" alt="" vspace="0" hspace="0">
									{else}
										<img src="{$site_root}{$template_root}/images/message_read_icon.gif" border="0" alt="" vspace="0" hspace="0">
									{/if}
								{/if}
							</td>
							<td style="padding-left: 13px;" width="135" class="text{if $item.new}_head{/if}">
								{$item.from}
							</td>
							<td style="padding-left: 10px;" class="text{if $item.new}_head{/if}"><a href="mailbox.php?sel=viewto&amp;id={$item.id}">{$item.subject}</a></td>
							<td style="padding-left: 10px;" width="135" class="text{if $item.new}_head{/if}">{$item.date}&nbsp;{$item.time}</td>
							<td align="right" width="20" style="padding-right: 10px;"><input type="checkbox" name="delete[{$smarty.foreach.maillist.iteration}]" value="{$item.id}"></td>
						</tr>						
					</table>
				{/foreach}
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td style="padding: 10px 10px 0px 10px;">
                                                    
							{if $links}
                                                            <ol class="page-nation">
								<li>{$lang.pages}:&nbsp;</li>
								{foreach item=item from=$links}
									<li><a href="{$item.link}" {if $item.selected}class="selected"{/if}>{$item.name}</a><li>
								{/foreach}
                                                                </ol>
							{else}
								
							{/if}
						</td>
						<td style="padding: 12px 0px;" align="right">
							<input type="button" class="normal-btn" onclick="javascript: document.forms['mailbox_form'].submit();" value="{$button.delete}">
						</td>
					</tr>
				</table>
			</form>
		{/if}
	</div>
<script type="text/javascript">
{literal}
function checkForm(form)
{
	amount = form.delete_after_form_amount.value = parseInt(form.delete_after_form_amount.value);
	if (isNaN(amount)) {
		alert('{/literal}{$lang.mailbox.wrong_amount}{literal}');
		return false;
	} else {
		form.submit();
	}
}
function switchForm(form)
{
	if (form.delete_after_form_status.checked) {
		form.delete_after_form_amount.disabled = false;
		form.delete_after_form_period.disabled = false;
	} else {
		form.delete_after_form_amount.disabled = true;
		form.delete_after_form_period.disabled = true;
	}
}
{/literal}
</script>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}