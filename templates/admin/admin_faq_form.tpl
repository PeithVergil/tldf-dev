{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.addition.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{if $form.type == "edit"}{$header.adv_edit}{else}{$header.adv_add}{/if}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.area_faq_edit}</div>
	{if $form.type=='edit'}
		<table cellSpacing="3" cellPadding="0"><tr>
		{section name=s loop=$lang_link}
			<td align="center"><a href="#" onclick="javascript:window.open('{$lang_link[s].link}','lang_edit', 'height=750, width=1050, left=10, top=50, resizable=yes, scrollbars=yes, menubar=no,status=no'); return false;" class=privacy_link>{$lang_link[s].name}</a></td>
			{if !$smarty.section.s.last}
			<td align="center" valign="middle" class="main_content_text">&nbsp;|&nbsp;</td>
			{/if}
		{/section}
		</tr></table>
	{/if}

	<form action="{$form.save_link}" name="edit_form" method="POST" style="margin:0px" onSubmit="return CheckChanges()" ENCTYPE="multipart/form-data">
	<table border=0 cellspacing=1 cellpadding=5>
		<tr bgcolor=#FFFFFF>
			<td class="main_content_text" align="left" bgcolor="#FFFFFF" width="150">{$header.category}</td>
			<td class="main_content_text">{$form.category}<input type="hidden" name="category" value="{$form.category_id}"></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td class="main_content_text" align="left" bgcolor="#FFFFFF" width="150">{$header.adv_name} <font class=main_error_text>*</font></td>
			<td><input type="text" name="title" value="{$form.title}" size="40"></td>
		</tr>
		<tr bgcolor="#ffffff" valign="top">
			<td class="main_content_text" width="150">{$header.adv_descr} <font class=main_error_text>*</font></td>
			<td class="main_content_text" align="left" width="700" height="505">
				{if RICH_TEXT_EDITOR == 'SPAW-1' || RICH_TEXT_EDITOR == 'SPAW-2'}
					{$editor}
				{elseif RICH_TEXT_EDITOR == 'TINYMCE'}
					<script type="text/javascript" src="{$site_root}/javascript/tiny_mce-3.5.8/tiny_mce.js"></script>
					<script type="text/javascript">
					tinyMCE.init({ldelim}
						mode : "textareas",
						oninit: myInit,
						{include file="$admingentemplates/admin_tiny_mce.tpl"}
					{rdelim});
					function myInit() {ldelim}
						tinyMCE.get('tinymce_code').setContent('{$form.body|escape:javascript}');
					{rdelim}
					</script>
					<textarea name="code" id="tinymce_code" rows="20" cols="60" style="width:700px;height:500px;"></textarea>
				{else}
					<textarea name="code" rows="20" cols="60" style="width:700px;height:500px;">{$form.body}</textarea>
				{/if}
			</td>
		</tr>
	</table>
	<table>
		<tr height="40">
			<td><input type="submit" value="{$button.save}" class="button"></td>
			<td><input type="button" value="{$lang.button.back}" class="button" onclick="javascript: location='{$form.back_link}';"></td>
		</tr>
	</table>
	</form>

{literal}
<script>
	function CheckChanges(){
		bp = document.edit_form;
		if(bp.title.value == ""){
			alert({/literal}"{$header.empty_adv_name}"{literal}); return false;
		}
		if(bp.body.value == ""){
			alert({/literal}"{$header.empty_adv_descr}"{literal}); return false;
		}
//		document.edit_form.submit();
	}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}
