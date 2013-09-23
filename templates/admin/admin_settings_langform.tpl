{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.lang_editform}</font><br><br>
        <div align="center">
            <table class="cell header" borderColor="#FFFFFF" cellSpacing="0" cellPadding="0" border="1" id="table4" width="100%">
                    <form name="lang_form" action="{$form.action}" method="post" style="margin:0px" enctype="application/x-www-form-urlencoded">
					{$form.hiddens}
                    <tr height="20">
                        <td colspan=3 class="main_content_text" align="left"><span lang="{$data.lang_code}">&nbsp;</span></td>
                    </tr>
                    <tr valign=top>
                        <td class="main_content_text" align="left" ><textarea cols="125" rows="38" name="langfile">{$data.langfile}</textarea></td>
                    </tr>
                    <tr height="10">
                        <td align="right">
				<input type="button" value="{$button.save}" class="button" onclick="javascript:document.lang_form.submit();">
				<input type="button" value="{$button.close}" class="button" onclick="javascript: window.close(); opener.focus();">
			</td>
                    </tr>
		</form>
            </table>
        </div>
{include file="$admingentemplates/admin_bottom.tpl"}