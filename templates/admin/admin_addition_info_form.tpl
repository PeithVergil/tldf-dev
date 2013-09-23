{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font>
<font class=red_sub_header>
	&nbsp;|&nbsp;
    {if $data.id eq 1}
    	{$header.editform_help}
	{else}
		{$header.info}&nbsp;|&nbsp;{if $form.par eq "edit"}{$data.name}{else}{$header.add}{/if}
	{/if}
</font>
<div class="help_text">
	<span class="help_title">{$lang.help}:</span>{$help.area_info_edit}
</div>
{if $form.par eq "edit"}
    <table cellSpacing="3" cellPadding="0">
        <tr>
        	{section name=s loop=$lang_link}
            	<td align="center"><a href="#" onclick="javascript:window.open('{$lang_link[s].link}','lang_edit', 'height=750, width=1050, left=10, top=50, resizable=yes, scrollbars=yes, menubar=no,status=no'); return false;" class=privacy_link>{$lang_link[s].name}</a></td>
                {if !$smarty.section.s.last}
                	<td align="center" valign="middle" class="main_content_text">&nbsp;|&nbsp;</td>
                {/if}
            {/section}
        </tr>
    </table>
{/if}
<form name="area_form" action="{$form.action}" method="post" {if $data.id != 1}onSubmit="return CheckChanges();"{/if} enctype="multipart/form-data" style="margin:0px;">
    {$form.hiddens}
    <table border="0" cellspacing=1 cellpadding=5 width="100%">
        {if $data.id != 1}
            <tr bgcolor="#ffffff">
                <td colspan=2 class="main_content_text" align="left">
                    <input type="text" name="name" value="{$data.name}" size="40">
                    <input type="checkbox" name="status" value="1" {if $data.status}checked{/if} >
                    &nbsp;-&nbsp;{$header.status}&nbsp;
                </td>
            </tr>
        {/if}
        <tr bgcolor="#ffffff" valign="top">
            <td colspan=2 class="main_content_text" align="left" width="700px" height="505px">
				{if RICH_TEXT_EDITOR == 'SPAW-1' || RICH_TEXT_EDITOR == 'SPAW-2'}
					{$editor}
				{elseif RICH_TEXT_EDITOR == 'TINYMCE'}
					<script type="text/javascript" src="{$site_root}/javascript/tiny_mce-3.5.8/tiny_mce.js"></script>
					<script type="text/javascript">
					{literal}
					tinyMCE.init({
						mode: "exact",
						elements: "tinymce_code",
						oninit: myInit,
						{/literal}{include file="$admingentemplates/admin_tiny_mce.tpl"}{literal}
					});
					function myInit() {
						tinyMCE.get('tinymce_code').setContent('{$data.content|escape:javascript}');
					}
					{/literal}
					</script>
					<textarea name="code" id="tinymce_code" rows="20" cols="60" style="width:700px;height:500px;"></textarea>
				{else}
					<textarea name="code" rows="20" cols="60" style="width:700px;height:500px;">{$data.content}</textarea>
				{/if}
			</td>
        </tr>
        <tr bgcolor="#ffffff">
            <td align="right" width="15%" class="main_header_text">{$header.title}:&nbsp;</td>
            <td class="main_content_text" align="left">
                <input type="text" name="title" value="{$data.title}">
            </td>
        </tr>
        <tr bgcolor="#ffffff">
            <td align="right" width="15%" class="main_header_text">{$header.description}:&nbsp;</td>
            <td class="main_content_text" align="left">
                <textarea name="description" rows="2" cols="85">{$data.description}</textarea>
            </td>
        </tr>
        <tr bgcolor="#ffffff">
            <td align="right" width="15%" class="main_header_text">{$header.keywords}:&nbsp;</td>
            <td class="main_content_text" align="left">
                <textarea name="keywords" rows="2" cols="85">{$data.keywords}</textarea>
            </td>
        </tr>
    </table>
    <table cellspacing="0" cellpadding="0">
        <tr height="40">
            <td>
                <input type="submit" value="{$button.save}" class="button">
            </td>
            {if $data.id neq 1}
            <td>
                <input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$form.back}'">
            </td>
            {/if}
        </tr>
    </table>
</form>
{literal}
<script>
function CheckChanges()
{
	bp = document.forms['area_form'];
	if(bp.name.value == "")
	{
		alert({/literal}"{$err.invalid_fields} {$header.name}"{literal});
		return false;
	}
	return true;
}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}