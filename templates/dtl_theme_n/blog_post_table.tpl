{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$blog_info.title}</div></div>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td></tr>
	{/if}
	<tr>
		<td valign="top" class="text">
			{include file="$gentemplates/blog_menu.tpl"}
			<div class="content_2" style="padding-top: 15px; padding-left: 15px;">
			{if $form.blog_page ne '3'}
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><font class="text_head">{$blog_info.title}</font></td>
			</tr>
			<tr>
				<td style="padding-top: 10px;"><font class="text_head"><a href="blog.php?sel=post">{$lang.blog.blog_menu_3}</font></td>
			</tr>
			</table>
			{/if}
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td colspan="2"><div style="height: 1px; margin: 15px 15px 10px 0px;" class="delimiter"></div></td>
			</tr>
			<tr>
				<td valign="top" style="padding-bottom: 10px;"><font class="text_hidden">{$lang.blog.posted_at}&nbsp;{$data.creation_date}&nbsp;&nbsp;{$data.creation_time}{if $data.show eq 0}&nbsp;&nbsp;&nbsp;{$lang.blog.hidden_post}{/if}</font></td>
				<td valign="top" align="right" style="padding: 0px 15px 10px 0px;">{if $data.is_user eq '1'}<a href="{$data.edit_link}">{$lang.blog.edit_post}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{$data.delete_link}">{$lang.blog.delete_post}</a>{/if}</td>
			</tr>
			<tr>
				<td colspan="2">
					<table cellpadding="0" cellspacing="0" width="100%" border="0">
						<tr>
							<td class="text_head" style="padding-bottom: 5px;">{$data.title}</td>
						</tr>
						<tr>
							<td class="text" style="line-height: 1.5;">{$data.body}</td>
						</tr>
						<tr>
							<td style="padding-top: 5px;">{if $data.can_comment eq '1' || $data.is_user eq '1'}<span class="link" onclick="ShowCommentForm('main');" style="text-decoration: underline; cursor: pointer;">{$lang.blog.add_comment}</span>{/if}</td>
						</tr>
						<tr>
							<td><div style="height: 1px; margin: 15px 15px 10px 0px;" class="delimiter"></div></td>
						</tr>
						<tr>
							<td valign="top" id="comment_main_form" {if $form.edit eq 1 && $form.id_reply eq 'main'} style="display: inline;" {else} style="display: none;" {/if}>
								<form name="post_main_form" id="post_main_form" action="blog.php?sel=save_comment" method="post" enctype="multipart/form-data">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td height="30" width="120" class="text_head">{$lang.blog.comment_title}:</td>
									<td><input type="text" name="comment_title" value="{$data.comment_title}" style="width: 150px" maxlength="500">
										<input type="hidden" name="id_post" value="{$form.id_post}">
									</td>
								</tr>
								<tr>
									<td valign="top" class="text_head">{$lang.blog.comment_body}:</td>
									<td>
										<table cellpadding="0" cellspacing="0" width="300">
										<tr>
											<td>
												<input type="button" class="button" onclick="bbstyle(0, 'main');" value="[b]" name="addbbcode0" style="width: 40px;">&nbsp;
												<input type="button" class="button" onclick="bbstyle(2, 'main');" value="[i]" name="addbbcode2" style="width: 40px;">&nbsp;
												<input type="button" class="button" onclick="bbstyle(4, 'main');" value="[u]" name="addbbcode4" style="width: 40px;">&nbsp;
												<input type="button" class="button" onclick="bbstyle(6, 'main');" value="[url]" name="addbbcode6" style="width: 40px;">&nbsp;
											</td>
										</tr>
										<tr>
											<td style="padding-top: 5px;"><textarea wrap="virtual" name="comment_body" style="width: 350px;" rows="10" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);">{$data.comment_body}</textarea></td>
										</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td height="30" colspan="2">
										<input type="submit" value="{$lang.button.save}" class="button">&nbsp;
									</td>
								</tr>
								</table>
								</form>
							</td>
						</tr>
						{if $data.blog_comments ne 'empty'}
						<tr>
							<td>
							{section name=b loop=$data.blog_comments}
							<div>
								<table cellpadding="0" cellspacing="0" width="100%" border="0">
								<tr>
									<td style="padding-left: {$data.blog_comments[b].sub_level*20}px; padding-right: 10px; padding-top: 10px; padding-bottom: 10px;" valign="top">{if $data.blog_comments[b].deleted eq '1'}<img src="{$form.default_icon}" class="icon" alt="">{else}{if $data.blog_comments[b].is_user eq 0}<a href="{$data.blog_comments[b].profile_link}" target="_blank">{/if}<img src="{$data.blog_comments[b].comment_icon}" class="icon" alt="">{if $data.blog_comments[b].is_user eq 0}</a>{/if}{/if}</td>
									<td valign="top" width="100%" style="padding-top: 10px; padding-bottom: 10px;">
										<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td style="padding-bottom: 5px;">{if $data.blog_comments[b].deleted!='1'}<font class="text_head">{$data.blog_comments[b].title}</font>&nbsp;&nbsp;&nbsp;{$lang.blog.posted_by}&nbsp;<font class="text_head">{if $data.blog_comments[b].is_user eq 0}<a href="{$data.blog_comments[b].profile_link}" target="_blank">{/if}{$data.blog_comments[b].login}{if $data.blog_comments[b].is_user eq 0}</a>{/if}</font>{else}{$lang.blog.no_title}{/if}</td>
										</tr>
										<tr>
											<td class="text_hidden" style="padding-bottom: 2px;">{$data.blog_comments[b].creation_date}&nbsp;{$data.blog_comments[b].creation_time}</td>
										</tr>
										<tr>
											<td {if $data.blog_comments[b].deleted eq '1'}class="error"{/if} style="line-height: 1.5;">{$data.blog_comments[b].body}</td>
										</tr>
										{if $data.blog_comments[b].deleted ne '1'}
										<tr>
											<td style="padding-top: 2px;">{if $data.can_comment}<span class="link" onclick="ShowCommentForm('{$data.blog_comments[b].id}');" style="text-decoration: underline; cursor: pointer;">{$lang.blog.leave_reply}</span>&nbsp;&nbsp;{/if}{if $data.blog_comments[b].can_edit eq 1}|&nbsp;&nbsp;<a href="{$data.blog_comments[b].delete_link}">{$lang.blog.delete_comment}</a>{/if}</td>
										</tr>
										{/if}
										</table>
									</td>
								</tr>
								</table>
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td valign="top" id="comment_form_{$data.blog_comments[b].id}" {if $form.edit eq 1 && $form.id_reply eq $data.blog_comments[b].id} style="display: inline;" {else} style="display: none;" {/if}>
										<form name="post_form_{$data.blog_comments[b].id}" id="post_form_{$data.blog_comments[b].id}" action="blog.php?sel=save_comment&id_reply={$data.blog_comments[b].id}" method="post" enctype="multipart/form-data">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td height="30" width="120" class="text_head">{$lang.blog.comment_title}:</td>
											<td><input type="text" name="comment_title" value="{$data.comment_title}" style="width: 150px" maxlength="500">
												<input type="hidden" name="id_post" value="{$form.id_post}">
											</td>
										</tr>
										<tr>
											<td valign="top" class="text_head">{$lang.blog.comment_body}:</td>
											<td>
												<table cellpadding="0" cellspacing="0" width="300">
												<tr>
													<td>
														<input type="button" class="button" onclick="bbstyle(0, '{$data.blog_comments[b].id}');" value="[b]" name="addbbcode0" style="width: 40px;">&nbsp;
														<input type="button" class="button" onclick="bbstyle(2, '{$data.blog_comments[b].id}');" value="[i]" name="addbbcode2" style="width: 40px;">&nbsp;
														<input type="button" class="button" onclick="bbstyle(4, '{$data.blog_comments[b].id}');" value="[u]" name="addbbcode4" style="width: 40px;">&nbsp;
														<input type="button" class="button" onclick="bbstyle(6, '{$data.blog_comments[b].id}');" value="[url]" name="addbbcode6" style="width: 40px;">&nbsp;
													</td>
												</tr>
												<tr>
													<td style="padding-top: 5px;"><textarea wrap="virtual" name="comment_body" style="width: 350px;" rows="10" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);">{$data.comment_body}</textarea></td>
												</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td height="30" colspan="2">
												<input type="submit" value="{$lang.button.save}" class="button">&nbsp;
											</td>
										</tr>
										</table>
										</form>
									</td>
								</tr>
								</table>
								{if $data.blog_comments[b].sub_comments !=''}
									{foreach item=item from=$data.blog_comments[b].sub_comments}
									<table cellpadding="0" cellspacing="0" width="100%" border="0">
									<tr>
										<td style="padding-left: {$item.sub_level*20}px; padding-right: 10px; padding-top: 10px; padding-bottom: 10px;" valign="top">{if $item.deleted eq '1'}<img src="{$form.default_icon}" class="icon" alt="">{else}{if $item.is_user eq 0}<a href="{$item.profile_link}" target="_blank">{/if}<img src="{$item.comment_icon}" class="icon" alt="">{if $item.is_user eq 0}</a>{/if}{/if}</td>
										<td valign="top" width="100%" style="padding-top: 10px; padding-bottom: 10px;">
											<table cellpadding="0" cellspacing="0" border="0">
											<tr>
												<td style="padding-bottom: 5px;">{if $item.title}<font class="text_head">{$item.title}</font>&nbsp;&nbsp;&nbsp;{$lang.blog.posted_by}&nbsp;<font class="text_head">{if $item.is_user eq 0}<a href="{$item.profile_link}" target="_blank">{/if}{$item.login}{if $item.is_user eq 0}</a>{/if}</font>{else}<font class="text">{$lang.blog.no_title}{/if}</font></td>
											</tr>
											<tr>
												<td class="text_hidden" style="padding-bottom: 2px;">{$item.creation_date}&nbsp;{$item.creation_time}</td>
											</tr>
											<tr>
												<td {if $item.deleted eq '1'} class="error" {/if} style="line-height: 1.5;">{$item.body}</td>
											</tr>
											{if $item.deleted ne '1'}
											<tr>
												<td style="padding-top: 2px;">{if $data.can_comment}<span class="link" onclick="ShowCommentForm('{$item.id}');" style="text-decoration: underline; cursor: pointer;">{$lang.blog.leave_reply}</span>&nbsp;&nbsp;|&nbsp;&nbsp;{/if}{if $item.can_edit eq 1}<a href="{$item.delete_link}">{$lang.blog.delete_comment}</a>{/if}</td>
											</tr>
											{/if}
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="2">
										<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td valign="top" id="comment_form_{$item.id}" {if $form.edit eq 1 && $form.id_reply eq $item.id} style="display: inline;" {else} style="display: none;" {/if}>
												<form name="post_form_{$item.id}" id="post_form_{$item.id}" action="blog.php?sel=save_comment&id_reply={$item.id}" method="post" enctype="multipart/form-data">
												<table width="100%" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td height="30" width="120" class="text_head">{$lang.blog.comment_title}:</td>
													<td><input type="text" name="comment_title" value="{$data.comment_title}" style="width: 150px" maxlength="500">
														<input type="hidden" name="id_post" value="{$form.id_post}">
													</td>
												</tr>
												<tr>
													<td valign="top" class="text_head">{$lang.blog.comment_body}:</td>
													<td>
														<table cellpadding="0" cellspacing="0" width="300">
														<tr>
															<td>
																<input type="button" class="button" onclick="bbstyle(0, '{$item.id}');" value="[b]" name="addbbcode0" style="width: 40px;">&nbsp;
																<input type="button" class="button" onclick="bbstyle(2, '{$item.id}');" value="[i]" name="addbbcode2" style="width: 40px;">&nbsp;
																<input type="button" class="button" onclick="bbstyle(4, '{$item.id}');" value="[u]" name="addbbcode4" style="width: 40px;">&nbsp;
																<input type="button" class="button" onclick="bbstyle(6, '{$item.id}');" value="[url]" name="addbbcode6" style="width: 40px;">&nbsp;
															</td>
														</tr>
														<tr>
															<td style="padding-top: 5px;"><textarea wrap="virtual" name="comment_body" style="width: 350px;" rows="10" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);">{$data.comment_body}</textarea></td>
														</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td height="30" colspan="2">
														<input type="submit" value="{$lang.button.save}" class="button">&nbsp;
													</td>
												</tr>
												</table>
												</form>
											</td>
										</tr>
										</table>
										</td>
									</tr>
									</table>
									{/foreach}
								{/if}
							</div>
							{/section}
							</td>
						</tr>
							{if $links}
						<tr>
							<td>
							<div style="margin: 0px"><div style="margin-left: 10px">
							{foreach item=item from=$links}
								<div class="page_div{if $item.selected eq '1'}_active{/if}">
									<div style="margin: 5px"><a href="{$item.link}" class="page_link{if $item.selected eq '1'}_active{/if}">{$item.name}</a></div>
								</div>
							{/foreach}
							</div></div>
							</td>
						</tr>
							{/if}
						{/if}
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2"><div style="height: 10px; margin: 0px"><img src="{$site_root}{$template_root}/images/empty.gif" height="10px" alt=""></div></td>
			</tr>
			</table>
			</div>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
{literal}
<script type="text/javascript">
function ShowCommentForm(par) {
	if (par == 'main') {
		document.getElementById('comment_main_form').style.display = 'inline';
		document.getElementById('post_main_form').comment_title.value = '';
		document.getElementById('post_main_form').comment_body.value = '';
		{/literal}
		{section name=b loop=$data.blog_comments}
			document.getElementById('comment_form_{$data.blog_comments[b].id}').style.display = 'none';
			document.getElementById('post_form_{$data.blog_comments[b].id}').comment_title.value = '';
			document.getElementById('post_form_{$data.blog_comments[b].id}').comment_body.value = '';
			{foreach item=item from=$data.blog_comments[b].sub_comments}
				document.getElementById('comment_form_{$item.id}').style.display = 'none';
				document.getElementById('post_form_{$item.id}').comment_title.value = '';
				document.getElementById('post_form_{$item.id}').comment_body.value = '';
			{/foreach}
		{/section}
		{literal}
	} else {
		document.getElementById('comment_main_form').style.display = 'none';
		document.getElementById('post_main_form').comment_title.value = '';
		document.getElementById('post_main_form').comment_body.value = '';
		{/literal}
		{section name=b loop=$data.blog_comments}
			document.getElementById('post_form_{$data.blog_comments[b].id}').comment_title.value = '';
			document.getElementById('post_form_{$data.blog_comments[b].id}').comment_body.value = '';
			{literal}
			if (par == {/literal}{$data.blog_comments[b].id}{literal}){
				document.getElementById('comment_form_{/literal}{$data.blog_comments[b].id}{literal}').style.display = 'inline';
			} else {
				document.getElementById('comment_form_{/literal}{$data.blog_comments[b].id}{literal}').style.display = 'none';
			}
			{/literal}
			{foreach item=item from=$data.blog_comments[b].sub_comments}
			document.getElementById('post_form_{$item.id}').comment_title.value = '';
			document.getElementById('post_form_{$item.id}').comment_body.value = '';
			{literal}
			if (par == {/literal}{$item.id}{literal}){
				document.getElementById('comment_form_{/literal}{$item.id}{literal}').style.display = 'inline';
			} else {
				document.getElementById('comment_form_{/literal}{$item.id}{literal}').style.display = 'none';
			}
			{/literal}
			{/foreach}
		{/section}
		{literal}
	}
	return;
}

// Startup variables
var imageTag = false;
var theSelection = false;
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;

var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);

bbcode = new Array();
bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[url]','[/url]');
imageTag = false;

// Replacement for arrayname.length property
function getarraysize(thearray) {
	for (i = 0; i < thearray.length; i++) {
		if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
			return i;
		}
	return thearray.length;
}

// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
	thearray[ getarraysize(thearray) ] = value;
}

// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
	thearraysize = getarraysize(thearray);
	retval = thearray[thearraysize - 1];
	delete thearray[thearraysize - 1];
	return retval;
}

function bbstyle(bbnumber, par) {
	if (par == 'main') {
		var txtarea = document.post_main_form.comment_body;
	} else {
		{/literal}
		{section name=b loop=$data.blog_comments}
			{literal}
			if (par == '{/literal}{$data.blog_comments[b].id}{literal}'){
				var txtarea = document.post_form_{/literal}{$data.blog_comments[b].id}{literal}.comment_body;
			}
			{/literal}
			{foreach item=item from=$data.blog_comments[b].sub_comments}
			{literal}
			if (par == '{/literal}{$item.id}{literal}'){
				var txtarea = document.post_form_{/literal}{$item.id}{literal}.comment_body;
			}
			{/literal}
			{/foreach}
		{/section}
		{literal}
	}

	txtarea.focus();
	donotinsert = false;
	theSelection = false;
	bblast = 0;

	if ((clientVer >= 4) && is_ie && is_win)
	{
		theSelection = document.selection.createRange().text; // Get text selection
		if (theSelection) {
			// Add tags around selection
			document.selection.createRange().text = bbtags[bbnumber] + theSelection + bbtags[bbnumber+1];
			txtarea.focus();
			theSelection = '';
			return;
		}
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, bbtags[bbnumber], bbtags[bbnumber+1]);
		return;
	}

	// Find last occurance of an open tag the same as the one just clicked
	for (i = 0; i < bbcode.length; i++) {
		if (bbcode[i] == bbnumber+1) {
			bblast = i;
			donotinsert = true;
		}
	}

	if (donotinsert) {		// Close all open tags up to the one just clicked & default button names
		while (bbcode[bblast]) {
				butnumber = arraypop(bbcode) - 1;
				txtarea.value += bbtags[butnumber + 1];
				if (par == 'main') {
					buttext = eval('document.post_main_form.addbbcode' + butnumber + '.value');
				} else {
					buttext = eval('document.post_form_'+ par +'.addbbcode' + butnumber + '.value');
				}
				tobtn = buttext.substr(0, 1) + buttext.substr(2,buttext.length);
				if (par == 'main') {
					eval('document.post_main_form.addbbcode' + butnumber + '.value ="' + tobtn + '"');
				} else {
					eval('document.post_form_'+ par +'.addbbcode' + butnumber + '.value ="' + tobtn + '"');
				}

				imageTag = false;
			}
			txtarea.focus();
			return;
	} else { // Open tags

		txtarea.value += bbtags[bbnumber];
		arraypush(bbcode,bbnumber+1);
		if (par == 'main') {
			eval('document.post_main_form.addbbcode'+bbnumber+'.value = "' +bbtags[bbnumber+1]+ '"');
		} else {
			eval('document.post_form_'+ par +'.addbbcode'+bbnumber+'.value = "' +bbtags[bbnumber+1]+ '"');
		}
		txtarea.focus();
		return;
	}
	storeCaret(txtarea);
}

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
{
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	if (selEnd == 1 || selEnd == 2)
		selEnd = selLength;

	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);
	txtarea.value = s1 + open + s2 + close + s3;
	return;
}

// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}


//-->
</script>
{/literal}
{include file="$gentemplates/index_bottom.tpl"}