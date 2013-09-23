{strip}
{if $par eq 'new_subcategory'}
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td valign="top">
	<form action="{$file_name}" method="post" id="new_theme" name="new_theme">
	<input type="hidden" value="{$data.category_id}" id="id_category" name="id_category">
	<input type="hidden" value="new_subcategory_post" id="sel" name="sel">
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td valign="top" width="100" class="text_head">{$lang.forum.topic_title}:&nbsp;</td>
			<td valign="top"><input size="50" type="text" class="str" name="subcategory_name" id="subcategory_name" value="{$data.subcategory_name}"></td>
		</tr>
		<tr>
			<td valign="top" style="padding-top: 7px;" class="text_head">{$lang.forum.message_title}:&nbsp;</td>
			<td valign="top" style="padding-top: 7px;"><input size="50" type="text" class="str" name="post_name" id="post_name" value="{$data.post_name}"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="padding-top: 5px; padding-bottom: 5px;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="left">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input type="button" name=b value="b" onclick="return insertBB(this.name)" style="font-weight: bold; width: 30px; height: 20px;">
							</td>
							<td style="padding-left: 5px;">
								<input type="button" name=i value="i" onclick="return insertBB(this.name)" style="font-style: italic; width: 30px; height: 20px;">
							</td>
							<td style="padding-left: 5px;">
								<input type="button" name=u value="u" onclick="return insertBB(this.name)" style="text-decoration: underline; width: 30px; height: 20px;">
							</td>
						</tr>
					</table>
					</td>
					<td align="right">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input type="button" name=url value="url" onclick="return insertBB(this.name)" style="width: 30px; height: 20px;">
							</td>
							<td style="padding-left: 5px;">
								<input type="button" name=img value="img" onclick="return insertBB(this.name)" style="width: 30px; height: 20px;">
							</td>
							<td style="padding-left: 5px;">
								<div>
								<input type=image id=smileLayer src="{$site_root}{$template_root}/images/img_smile.gif" onclick="return displaySmileLayer();">
								</div>
								<div id=smiles style="position: absolute; display: none; border-bottom: 1px solid #666666;border-right: 1px solid #666666;">
								<table cellspacing=5 cellpadding=5 style="border: 1px solid #A5ACB2; background-color: #FFFFFF">
									<tr>
										<td><input type=image name=s1 src="{$site_root}{$template_root}/images/smiles/1.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s2 src="{$site_root}{$template_root}/images/smiles/2.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s3 src="{$site_root}{$template_root}/images/smiles/3.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s4 src="{$site_root}{$template_root}/images/smiles/4.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s5 src="{$site_root}{$template_root}/images/smiles/5.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
									</tr>
									<tr>
										<td><input type=image name=s6 src="{$site_root}{$template_root}/images/smiles/6.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s7 src="{$site_root}{$template_root}/images/smiles/7.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s8 src="{$site_root}{$template_root}/images/smiles/8.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s9 src="{$site_root}{$template_root}/images/smiles/9.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s10 src="{$site_root}{$template_root}/images/smiles/10.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
									</tr>
								</table>
							</div>
							</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td valign="top" style="padding-top: 7px;"><font class="text_head">{$lang.forum.message}</font>:&nbsp;<br><font class="text_hidden">{$lang.forum.bbcode_enabled}</font>&nbsp;</td>
			<td style="padding-top: 7px;"><textarea class="str" name="message" id="message" cols="50" rows="10">{$data.message}</textarea></td>
		</tr>
		{if $guest eq 1}
		<tr>
			<td valign="top" class="text_head" style="padding-top: 7px;">{$lang.forum.security_code}:</td>
			<td valign="top" style="padding-top: 7px;">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="{$data.kcaptcha}" alt="{$lang.forum.security_code}"></td>
				</tr>
				<tr>
					<td><input type="text" style="width: 200px" name="keystring"></td>
				</tr>
				</table>
			</td>
		</tr>
		{/if}
		<tr>
			<td>&nbsp;</td>
			<td align="left" style="padding-top: 7px;">
				<input type="submit" value="{$lang.button.save}">
			</td>
		</tr>
	</table>
	</form>
	</td>
</tr>
</table>
{elseif $par eq 'new_post' || $par eq 'quote' || $par eq 'edit_post'}
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td>
	<form action="{$file_name}" method="post" id="new_post" name="new_post">
	<input type="hidden" value="{$data.subcategory_id}" id="id_subcategory" name="id_subcategory">
	{if $par eq 'edit_post'}
		<input type="hidden" value="edit_post_save" id="sel" name="sel">
		<input type="hidden" value="{$data.id_post}" id="id_post" name="id_post">
	{else}
		<input type="hidden" value="new_post_save" id="sel" name="sel">
	{/if}
	<table cellpadding="0" cellspacing="0"  border="0">
		<tr>
			<td valign="top" style="padding-top: 7px;" class="text_head" width="100">{$lang.forum.message_title}:&nbsp;</td>
			<td valign="top" style="padding-top: 7px;"><input size="50" type="text" class="str" name="post_name" id="post_name" value="{$data.post_name}"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="padding-top: 5px; padding-bottom: 5px;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="left">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input type="button" name=b value="b" onclick="return insertBB(this.name)" style="font-weight: bold; width: 30px; height: 20px;">
							</td>
							<td style="padding-left: 5px;">
								<input type="button" name=i value="i" onclick="return insertBB(this.name)" style="font-style: italic; width: 30px; height: 20px;">
							</td>
							<td style="padding-left: 5px;">
								<input type="button" name=u value="u" onclick="return insertBB(this.name)" style="text-decoration: underline; width: 30px; height: 20px;">
							</td>
						</tr>
					</table>
					</td>
					<td align="right">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input type="button" name=url value="url" onclick="return insertBB(this.name)" style="width: 30px; height: 20px;">
							</td>
							<td style="padding-left: 5px;">
								<input type="button" name=img value="img" onclick="return insertBB(this.name)" style="width: 30px; height: 20px;">
							</td>
							<td style="padding-left: 5px;">
								<div>
								<input type=image id=smileLayer src="{$site_root}{$template_root}/images/img_smile.gif" onclick="return displaySmileLayer();">
								</div>
								<div id=smiles style="position: absolute; display: none; border-bottom: 1px solid #666666;border-right: 1px solid #666666;">
								<table cellspacing=5 cellpadding=5 style="border: 1px solid #A5ACB2; background-color: #FFFFFF">
									<tr>
										<td><input type=image name=s1 src="{$site_root}{$template_root}/images/smiles/1.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s2 src="{$site_root}{$template_root}/images/smiles/2.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s3 src="{$site_root}{$template_root}/images/smiles/3.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s4 src="{$site_root}{$template_root}/images/smiles/4.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s5 src="{$site_root}{$template_root}/images/smiles/5.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
									</tr>
									<tr>
										<td><input type=image name=s6 src="{$site_root}{$template_root}/images/smiles/6.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s7 src="{$site_root}{$template_root}/images/smiles/7.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s8 src="{$site_root}{$template_root}/images/smiles/8.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s9 src="{$site_root}{$template_root}/images/smiles/9.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
										<td><input type=image name=s10 src="{$site_root}{$template_root}/images/smiles/10.gif" onclick="displaySmileLayer();return insertBB(this.name)"></td>
									</tr>
								</table>
							</div>
							</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td valign="top" style="padding-top: 7px;"><font class="text_head">{$lang.forum.message}:&nbsp;<br><font class="text_hidden">{$lang.forum.bbcode_enabled}</font>&nbsp;</td>
			<td><textarea class="str" name="message" id="message" cols="50" rows="10">{$data.message}</textarea></td>
		</tr>
		{if $guest eq 1}
		<tr>
			<td valign="top" class="text_head" style="padding-top: 7px;">{$lang.forum.security_code}:</td>
			<td valign="top" style="padding-top: 7px;">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="{$data.kcaptcha}" alt="{$lang.forum.security_code}"></td>
				</tr>
				<tr>
					<td><input type="text" style="width: 200px" name="keystring"></td>
				</tr>
				</table>
			</td>
		</tr>
		{/if}
		<tr>
			<td>&nbsp;</td>
			<td style="padding-top: 7px;">
				<input type="submit" class="btn_small" value="{$lang.button.save}">
			</td>
		</tr>
	</table>
	</form>
	</td>
</tr>
</table>
{/if}
{/strip}
<script type="text/javascript">

var	smileLayer	= 0;

var	bbcode		= Array();
bbcode['b']		= Array('[b]', '[/b]');
bbcode['i']		= Array('[i]', '[/i]');
bbcode['u']		= Array('[u]', '[/u]');
bbcode['img']	= Array('[img]', '[/img]');
bbcode['url']	= Array('[url={$lang.forum.insert_link_url}]', '{$lang.forum.insert_link_name}[/url]');

bbcode['s1']	= Array(' :) ');
bbcode['s2']	= Array(' ;) ');
bbcode['s3']	= Array(' :D ');
bbcode['s4']	= Array(' 8) ');
bbcode['s5']	= Array(' :] ');
bbcode['s6']	= Array(' :O ');
bbcode['s7']	= Array(' :/ ');
bbcode['s8']	= Array(' :( ');
bbcode['s9']	= Array(' ;( ');
bbcode['s10']	= Array(' O_O ');

{literal}
function insertBB(code)
{
	var tag1 = bbcode[code][0];
	var tag2 = bbcode[code][1];
	var txta = document.getElementById('message');
	txta.focus();

	if (typeof document.selection != 'undefined') {
		var range = document.selection.createRange();
		var sel = range.text;
		range.text = tag2 ? tag1 + sel + tag2 : tag1;
		range = document.selection.createRange();
		if (tag2 && !sel.length) range.move('character', -tag2.length);
		else if (tag2) range.move('character', tag1.length + sel.length + tag2.length);
		range.select();
	}
	else if (typeof txta.selectionStart != 'undefined') {
		var scroll = txta.scrollTop;
		var start  = txta.selectionStart;
		var end    = txta.selectionEnd;
		var before = txta.value.substring(0, start);
		var sel    = txta.value.substring(start, end);
		var after  = txta.value.substring(end, txta.textLength);
		txta.value = tag2 ? before + tag1 + sel + tag2 + after	: before + tag1 + after;
		var caret = sel.length == 0	? start + tag1.length : start + tag1.length + sel.length + tag2.length;
		txta.selectionStart = caret;
		txta.selectionEnd = caret;
		txta.scrollTop = scroll;
	}
	return false;
}

function displaySmileLayer()
{
	if (document.getElementById('smiles').style.display == 'none') {
		document.getElementById('smiles').style.display = 'inline';
	} else {
		document.getElementById('smiles').style.display = 'none';
	}
	return false;
}

</script>
{/literal}