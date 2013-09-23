{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_personal}&nbsp;{$data.username}</font><br><br><br>
	<form action="{$form.action}" method="post" name="information" enctype="multipart/form-data">
            <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
				<input type="hidden" name=sel value="change">
				<input type="hidden" name=id value="{$data.id}">
						{section name=f loop=$personal}
						<tr bgcolor="#ffffff">
							<td width="50%" align="center" class="main_header_text"><b>{$personal[f].name}</b><input type=hidden name="p_spr[{$smarty.section.f.index}]" value="{$personal[f].id}"></td>
							<td width="50%" align="left" class="main_content_text" style="padding:3;">
							<select name="personal[{$smarty.section.f.index}][]" style="width:200">
								<option value="">{$button.nothing}</option>
								{html_options values=$personal[f].opt_value selected=$personal[f].opt_sel output=$personal[f].opt_name}
							</select>
						</tr>
						{/section}
            </table>
	<br><font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_portrait}&nbsp;{$data.username}</font><br><br><br>
            <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
						{section name=f loop=$portrait}
						<tr bgcolor="#ffffff">
							<td width="25%" align="center" class="main_header_text"><b>{$portrait[f].name_1}</b><input type=hidden name="port_spr[{$portrait[f].num_1}]" value="{$portrait[f].id_1}"></td>
							<td width="25%" align="left" class="main_content_text" style="padding:3;">
							<select  name="portrait[{$portrait[f].num_1}][]"  style="width:150">
								<option value="">{$button.nothing}</option>
								{html_options values=$portrait[f].opt_value_1 selected=$portrait[f].opt_sel_1 output=$portrait[f].opt_name_1}
							</select>
							</td>
							<td width="25%" align="center" class="main_header_text">{if $portrait[f].num_2}<b>{$portrait[f].name_2}</b><input type=hidden name="port_spr[{$portrait[f].num_2}]" value="{$portrait[f].id_2}">{else}&nbsp;{/if}</td>
							<td width="25%" align="left" class="main_content_text" style="padding:3;">
							{if $portrait[f].num_2}
							<select name="portrait[{$portrait[f].num_2}][]"  style="width:150">
								<option value="">{$button.nothing}</option>
								{html_options values=$portrait[f].opt_value_2 selected=$portrait[f].opt_sel_2 output=$portrait[f].opt_name_2}
							</select>
							{else}&nbsp;{/if}
							</td>
						</tr>
						{/section}
            </table>
	<br><font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_interests}&nbsp;{$data.username}</font><br><br><br>
            <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
						{section name=f loop=$interests}
						<tr bgcolor="#ffffff">
							<td width="15%" align="center" class="main_header_text"><b>{$interests[f].name_1}</b><input type=hidden name="int_spr[{$interests[f].num_1}]" value="{$interests[f].id_1}"></td>
							<td align="center" class="main_content_text" style="padding:3;">
							<input type="radio" name="interests[{$interests[f].num_1}]" value="1"{if $interests[f].sel_1 eq 1} checked{/if}>&nbsp;
							<input type="radio" name="interests[{$interests[f].num_1}]" value="2"{if $interests[f].sel_1 eq 2} checked{/if}>&nbsp;
							<input type="radio" name="interests[{$interests[f].num_1}]" value="3"{if $interests[f].sel_1 eq 3} checked{/if}>
							</td>
							
							<td width="15%" align="center" class="main_header_text">{if $interests[f].num_2}<b>{$interests[f].name_2}</b><input type=hidden name="int_spr[{$interests[f].num_2}]" value="{$interests[f].id_2}">{else}&nbsp;{/if}</td>
							<td align="center" class="main_content_text" style="padding:3;">{if $interests[f].num_2}
							<input type="radio" name="interests[{$interests[f].num_2}]" value="1"{if $interests[f].sel_2 eq 1} checked{/if}>&nbsp;
							<input type="radio" name="interests[{$interests[f].num_2}]" value="2"{if $interests[f].sel_2 eq 2} checked{/if}>&nbsp;
							<input type="radio" name="interests[{$interests[f].num_2}]" value="3"{if $interests[f].sel_2 eq 3} checked{/if}>{/if}&nbsp;
							</td>
							<td width="15%" align="center" class="main_header_text">{if $interests[f].num_3}<b>{$interests[f].name_3}</b><input type=hidden name="int_spr[{$interests[f].num_3}]" value="{$interests[f].id_3}">{else}&nbsp;{/if}</td>
							<td align="center" class="main_content_text" style="padding:3;">{if $interests[f].num_3}
							<input type="radio" name="interests[{$interests[f].num_3}]" value="1"{if $interests[f].sel_3 eq 1} checked{/if}>&nbsp;
							<input type="radio" name="interests[{$interests[f].num_3}]" value="2"{if $interests[f].sel_3 eq 2} checked{/if}>&nbsp;
							<input type="radio" name="interests[{$interests[f].num_3}]" value="3"{if $interests[f].sel_3 eq 3} checked{/if}>{/if}&nbsp;
							</td>
							
						</tr>
						{/section}
            </table>
			<table><tr height="40">
			<td><input type="button" value="{$button.save}" class="button" onclick="javascript:document.information.submit();"></td>
			<td><input type="button" value="{$button.close}" class="button" onclick="javascript: window.close();opener.focus();"></td>
			</tr></table>
</form>
{include file="$admingentemplates/admin_bottom.tpl"}