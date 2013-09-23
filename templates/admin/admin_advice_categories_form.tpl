{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.addition.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{if $form.type == "edit"}{$header.cat_edit}{else}{$header.cat_add}{/if}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.area_advice_category_edit}</div>
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
			<td class="main_content_text" align="left" bgcolor="#FFFFFF" width="150">{$header.cat_name} <font class=main_error_text>*</font></td>
			<td><input type="text" name="name" value="{$form.name}" size="40"></td>
		</tr>
		<tr bgcolor="#ffffff" valign="top">
			<td class="main_content_text" align="left" bgcolor="#FFFFFF" width="150">{$header.cat_descr}</td>
			<td class="main_content_text" align="left" width="700" height="305">
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
						tinyMCE.get('tinymce_code').setContent('{$form.descr|escape:javascript}');
					{rdelim}
					</script>
					<textarea name="code" id="tinymce_code" rows="20" cols="60" style="width:700px;height:500px;"></textarea>
				{else}
					<textarea name="code" rows="20" cols="60" style="width:700px;height:500px;">{$form.descr}</textarea>
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
<script type="text/javascript">
	function CheckChanges(){
		bp = document.edit_form;
		if(bp.name.value == ""){
			alert({/literal}"{$header.empty_cat_name}"{literal});
			return false;
		}
	}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}