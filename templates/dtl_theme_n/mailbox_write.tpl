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
				<td style="padding-left: 10px;"><img src="{$site_root}{$template_root}/images/inbox_icon.gif" border="0" alt="" vspace="0" hspace="0"></td>
				<td style="padding-left: 5px;" class="text"><a href="mailbox.php?sel=inbox">{$header.inbox}</a>  ({$form.inbox_new}/{$form.inbox_all})</td>
				<td style="padding-left: 15px;"><img src="{$site_root}{$template_root}/images/outbox_icon.gif" border="0" alt="" vspace="0" hspace="0"></td>
				<td style="padding-left: 5px;" class="text"><a href="mailbox.php?sel=outbox">{$header.outbox}</a>  ({$form.outbox_all})</td>
				<td style="padding-left: 15px;"><img src="{$site_root}{$template_root}/images/compose_icon.gif" border="0" alt="" vspace="0" hspace="0"></td>
				<td style="padding-left: 5px;" class="text"><a href="mailbox.php?sel=write" class="text_head">{$header.compose}</a></td>
			</tr>
		</table>
	</div>
	<form action="mailbox.php" method="post" name="mailbox_write" id="mailbox_write" style="margin:0px;" enctype="multipart/form-data">
		<input type="hidden" name="sel" value="send">
		<input type="hidden" name="act" id="act" value="">
		<input type="hidden" name="to" id="to" value="{$data.to}">
		<input type="hidden" name="id_attach" id="id_attach" value="">
		{if $form.par}
			<input type="hidden" name="par" value="{$form.par}">
		{/if}
		{if $form.par == 'reply'}
			<input type="hidden" name="id" value="{$form.reply_id}">
		{/if}
		{if $form.par == 'from_search' || $form.par == 'err' && $smarty.post.from_search}
			<input type="hidden" name="from_search" value="1">
			<input type="hidden" name="id" value="{$smarty.request.id}">
			{if $smarty.request.search_type}
				<input type="hidden" name="search_type" value="{$smarty.request.search_type}">
			{/if}
		{/if}
		{if $form.temp_attach_id}
			<input type="hidden" name="temp_attach_id" value="{$form.temp_attach_id}">
		{/if}
		<div style="padding-top: 15px;">
			<div class="content" style="padding: 15px 0px 15px 20px;">
				<div class="text">{$header.to}</div>
				<div style="padding-top: 3px;">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td><input type="text" maxlength="100" name="to_fname" value="{$data.to_fname}" style="width:200px;" readonly></td>
							{if $form.par != 'reply'}
								<td style="padding-left: 10px;">
									<a href="#" onclick="window.open('mailbox.php?sel=connections', 'description', 'height=500,resizable=yes,scrollbars=yes,width=500,menubar=no,status=no'); return false;">
										<img src="{$site_root}{$template_root}/images/btn_back.gif" border="0" alt="" vspace="0" hspace="0">
									</a>
								</td>
								<td style="padding-left: 5px;">
									<a href="#" onclick="window.open('mailbox.php?sel=connections', 'description', 'height=500,resizable=yes,scrollbars=yes,width=500,menubar=yes,status=yes'); return false;">
										{$header.add_from_connections}
									</a>
								</td>
							{/if}
							{if $form.back_link_profile}
								<td style="padding-left: 15px;">
									<a href="viewprofile.php?id={$smarty.request.id}{if $smarty.request.search_type}&amp;search_type={$smarty.request.search_type}{/if}">{$header.back_profile}</a>
								</td>
							{/if}
							{if $form.back_link_list}
								<td style="padding-left: 15px;">
									<a href="{$form.back_link_list}">{$header.back_list}</a>
								</td>
							{/if}
						</tr>
					</table>
				</div>
				<div class="text" style="padding-top: 5px;">{$header.subject}</div>
				<div class="text" style="padding-top: 3px;">
					<input type="text" maxlength="250" name="subject" value="{$data.subject}" style="width: 650px">
				</div>
				<div class="text" style="padding-top: 10px;">{$header.attach_file}</div>
				<div class="text" style="padding-top: 5px;">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td><input type="file" name="attach" id="attach"></td>
							<td width="5"></td>
							<td><input type="button" value="{$button.upload}" onclick="document.getElementById('act').value='upload_attach'; document.getElementById('mailbox_write').submit();"></td>
						</tr>
					</table>
				</div>
				{if $data.attaches}
					<div style="padding-top: 5px;">
						<div class="text">{$header.attaches}</div>
						{foreach item=item from=$data.attaches}
							<div style="padding-top: 3px;">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td width="150"><a href="{$item.path}" target="_blank">{$item.name}</a></td>
										<td style="padding-left: 15px;"><a href="#" onclick="document.getElementById('act').value='delete_attach'; document.getElementById('id_attach').value='{$item.id}'; document.getElementById('mailbox_write').submit();">Delete</a></td>
									</tr>
								</table>
							</div>
						{/foreach}
					</div>
				{/if}
				<div style="padding-top: 10px;">
					<textarea name="body" style="width:650px; height:100px;">{$data.body}</textarea>
				</div>
				<div style="width:650px; padding:10px;" class="center">
                                    <p class="basic-btn_here">
						<b></b><span>
							<input type="button" onclick="document.mailbox_write.submit();" value="{$button.send}">
						</span>
					</p>
				</div>
			</div>
		</div>
	</form>
	{if $data.reply}
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td bgcolor="#{$css_color.home_search}" style="padding: 10px 10px;" class="message_form">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td style="padding-bottom: 10px;"><b>{$header.received_email}</b>:  {$data.subject_msg_last}</td>
							<td style="padding-bottom: 10px;" align="right">{$data.date_msg_last}</td>
						</tr>
						<tr>
							<td colspan="2">
								<div style="background-color: #FFFFFF; padding: 10px;">{$data.text_msg_last}</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	{/if}
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}