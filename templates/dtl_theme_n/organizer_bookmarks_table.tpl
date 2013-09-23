{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px;"><div style="padding: 5px 0px">{$lang.organizer.page_title}</div></div>
		</td>
	</tr>
	{if $form.err && $form.section eq ''}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td>
	</tr>
	{/if}
	<tr>
		<td style="padding: 10px 0px 3px 0px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif" hspace="0" vspace="0" border="0" alt=""></td>
				<td style="padding-left: 2px;"><a href="organizer.php">{$lang.button.back}</a></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td valign="top" class="text">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top">
					<div class="content" style=" margin: 0px; padding: 0px 15px 15px 10px;">
						<div>
						<form id="del_bookmark_form" name="del_bookmark_form" method="post" action="organizer.php" style="margin: 0px; padding: 0px;">
						<input type="hidden" name="sel" value="del_bookmark">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td style="padding-top: 10px;" class="header">{$lang.organizer.my_bookmarks}</td>
						</tr>
						<tr>
							<td style="padding-top: 5px;" align="left">
							<input type="button" value="{$lang.organizer.add_bookmark}" onclick="ShowAddBookmarkForm();">
							</td>
						</tr>
						<tr>
							<td style="padding: 10px 0px;">
								{if $user_bookmarks}
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
								{foreach item=item from=$user_bookmarks}
								<tr>
									<td valign="top" style="padding: 5px 5px;" width="15" align="center"><input type="checkbox" name="bookmark[]" value="{$item.id}"></td>
									<td valign="top" style="padding: 7px 0px; line-height: 1.2;"><a href="http://{$item.link}" target="_blank">http://{$item.link}</a>&nbsp;-&nbsp;<font class="text">{$item.descr}</font></td>
								</tr>
								{/foreach}
								</table>
								{else}
								<div class="text">{$lang.organizer.no_bookmarks}</div>
								{/if}
							</td>
						</tr>
						{strip}
						{if $user_bookmarks}
						<tr>
							<td style="padding-top: 5px;" align="left">
								<input type="button" value="{$lang.organizer.del_selected}" onclick="{literal}javascript: if(confirm({/literal}'{$lang.organizer.del_bookmark_confirm}'{literal})){ document.del_bookmark_form.submit(); }{/literal}">
							</td>
						</tr>
						{/if}
						{/strip}
						</table>
						</form>
						</div>
						
						<div id="add_bookmark_form" {if $form.err && $form.section eq '2'} style="display: inline;"{else} style="display: none;"{/if}>
						<form method="post" action="organizer.php" style="margin: 0px; padding: 0px;">
						<input type="hidden" name="sel" value="save_bookmark">
						<table cellpadding="0" cellspacing="0" border="0">
							{if $form.err && $form.section eq '2'}
							<tr>
								<td colspan="3"><div class="error_msg">{$form.err}</div></td>
							</tr>
							{/if}
							<tr>
								<td style="padding-top: 10px;" class="text" width="150">{$lang.organizer.url}:&nbsp;<font class="error">*</font>&nbsp;</td>
								<td style="padding-top: 10px;" width="40" class="text_hidden">http://&nbsp;</td>
								<td style="padding-top: 10px;"><input type="text" name="bookmark_url" style="width: 200px;" value="{$data.bookmark_url}" maxlength="255"></td>
							</tr>
							<tr>
								<td style="padding-top: 5px;" class="text" colspan="2">{$lang.organizer.description}:&nbsp;<font class="error">*</font>&nbsp;</td>
								<td style="padding-top: 5px;">
									<textarea name="bookmark_descr" style="width: 200px;" rows="5">{$data.bookmark_descr}</textarea>
								</td>
							</tr>
							<tr>
								<td style="padding-top: 5px;" colspan="3"><input type="submit" value="{$lang.organizer.save_bookmark}"></td>
							</tr>
						</table>
						</form>
						</div>
						<div style="margin-left: 0px; padding-top: 15px;" >
						{foreach item=item from=$links}
							<span style="padding-right: 15px;"><a href="{$item.link}" {if $item.selected eq '1'} class="text_head"{/if}>{$item.name}</a></span>
						{/foreach}
						</div>
					</div>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
{literal}
<script type="text/javascript">
function ShowAddBookmarkForm() {
	if (document.getElementById('add_bookmark_form').style.display == 'none') {
		document.getElementById('add_bookmark_form').style.display = 'inline';
	} else {
		document.getElementById('add_bookmark_form').style.display = 'none';
	}
	return;
}
</script>
{/literal}
{include file="$gentemplates/index_bottom.tpl"}