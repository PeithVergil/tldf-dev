{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_descr}&nbsp;{$data.username}</font><br><br><br>
            <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
				<form action="{$form.action}" method="post" name="information" enctype="multipart/form-data">
				<input type="hidden" name=sel value="change">
				<input type="hidden" name=id value="{$data.id}">
                {$form.hiddens}
						{section name=f loop=$info}
						<tr bgcolor="#ffffff">
							<td width="50%" align="center" class="main_header_text"><b>{$info[f].name}</b><input type=hidden name="spr[{$smarty.section.f.index}]" value="{$info[f].id}"></td>
							<td width="50%" align="left" class="main_content_text" style="padding:3;">
							<select id="info{$smarty.section.f.index}" name="info[{$smarty.section.f.index}][]"  {if $info[f].type eq 2}multiple{/if}  style="width:200" {if $data.root eq 1}disabled{/if}>
								{if $info[f].type eq 1}<option value="">{$button.nothing}</option>{/if}
								{if $info[f].type eq 2}<option value="0" {if $info[f].sel_all}selected{/if}>{$button.all}</option>{/if}
								{html_options values=$info[f].opt_value selected=$info[f].opt_sel output=$info[f].opt_name}
							</select>
							</td>
						</tr>
						{/section}
                    </form>
            </table>
			<table><tr height="40">
			{if $data.root  ne 1}<td><input type="button" value="{$button.save}" class="button" onclick="javascript:document.information.submit()"></td>{/if}
			<td><input type="button" value="{$button.close}" class="button" onclick="javascript: window.close();opener.focus();"></td>
			</tr></table>
{include file="$admingentemplates/admin_bottom.tpl"}