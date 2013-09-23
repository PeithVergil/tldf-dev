{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$lang.section.blog_new_post}</div></div>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td></tr>
	{/if}
	<tr>
		<td valign="top" class="text">
			{include file="$gentemplates/blog_menu.tpl"}
			<div class="content_2" style=" margin: 0px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding-left: 15px; padding-top: 15px;">
					<form name="post_form" id="post_form" action="blog.php?sel=save_post" method="post" enctype="multipart/form-data">
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td height="30" width="160" class="text_head">{$lang.blog.post_title}:</td>
						<td><input type="text" name="post_title" value="{$data.post_title}" style="width: 150px" maxlength="500">
							<input type="hidden" name="id_profile" value="{$form.id_profile}">
							{if $form.edit eq 1}<input type="hidden" name="edit" value="1"><input type="hidden" name="id_post" value="{$data.id}">{/if}
						</td>
					</tr>
					<tr>
						<td valign="top" class="text_head">{$lang.blog.post_body}:</td>
						<td>
							<table cellpadding="0" cellspacing="0" width="300">
							<tr>
								<td>
									<input type="button" class="button" onclick="bbstyle(0);" value="[b]" name="addbbcode0" style="width: 40px;">&nbsp;
									<input type="button" class="button" onclick="bbstyle(2);" value="[i]" name="addbbcode2" style="width: 40px;">&nbsp;
									<input type="button" class="button" onclick="bbstyle(4);" value="[u]" name="addbbcode4" style="width: 40px;">&nbsp;
									<input type="button" class="button" onclick="bbstyle(6);" value="[url]" name="addbbcode6" style="width: 40px;">&nbsp;
								</td>
								<td align="right"><input type="button" class="button" onclick="{literal} popupWin = window.open('blog.php?sel=image_upload_form&id_profile={/literal}{$form.id_profile}{literal}', 'image', 'location, width=300, height=200,top=0'); popupWin.focus(); {/literal}" value="[image]" style="width: 50px;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="2" style="padding-top: 5px;"><textarea wrap="virtual" name="post_body" id="post_body" rows="15" cols="60" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" >{$data.post_body}</textarea></td>
							</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.blog.hidden_post}:</td>
						<td><input type="checkbox" name="hidden_post" value="1"></td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.blog.can_comment}:</td>
						<td><input type="checkbox" name="can_comment" value="1" checked></td>
					</tr>
					<tr>
						<td height="30" colspan="2">
							<input type="submit" value="{$lang.button.save}" class="button">&nbsp;
							{if $form.edit eq 1}<input type="button" value="{$lang.button.back}" class="button" onclick="document.location.href='blog.php'">{/if}
						</td>
					</tr>
					</table>
					</form>
				</td>
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

function ImageForm() {
	if (document.getElementById('image_form').style.display == 'none') {
		document.getElementById('image_form').style.display = 'inline';
	} else {
		document.getElementById('image_form').style.display = 'none';
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

function bbstyle(bbnumber) {
	var txtarea = document.post_form.post_body;

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
				buttext = eval('document.post_form.addbbcode' + butnumber + '.value');
				tobtn = buttext.substr(0, 1) + buttext.substr(2,buttext.length);
				eval('document.post_form.addbbcode' + butnumber + '.value ="' + tobtn + '"');
				//eval('document.post_form.addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
				//eval('document.post_form.addbbcode' + butnumber + '.value ="' + bbtags[bbnumber] + '"');
				imageTag = false;
			}
			txtarea.focus();
			return;
	} else { // Open tags

//		if (imageTag && (bbnumber != 14)) {		// Close image tag before adding another
//			txtarea.value += bbtags[15];
//			lastValue = arraypop(bbcode) - 1;	// Remove the close image tag from the list
//			document.post_form.addbbcode14.value = "Img";	// Return button back to normal state
//			imageTag = false;
//		}
		// Open tag
		txtarea.value += bbtags[bbnumber];
//		if ((bbnumber == 14) && (imageTag == false)) imageTag = 1; // Check to stop additional tags after an unclosed image tag
		arraypush(bbcode,bbnumber+1);
		eval('document.post_form.addbbcode'+bbnumber+'.value = "' +bbtags[bbnumber+1]+ '"');
//		eval('document.post_form.addbbcode'+bbnumber+'.value += "*"');
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