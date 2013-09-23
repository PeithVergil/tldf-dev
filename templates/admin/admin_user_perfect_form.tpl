{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_perfect}&nbsp;{$data.username}</font><br><br><br>
	<form action="{$form.action}" method="post" name="information" enctype="multipart/form-data">
	<input type=hidden name=sel value=change>
	<input type=hidden name=id value="{$data.id}">
            <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
						<tr bgcolor="#ffffff">
							<td width="50%" class="main_header_text" align="center">{$header.weight}&nbsp;</td>
							<td width="50%" class="main_content_text" align="left" style="padding:3;">
								<select name="id_weight"  {if $data.root eq 1}disabled{/if} style="width:200">
									<option value="0">{$button.all}</option>
									{section name=s loop=$weight}
									<option value="{$weight[s].id}" {if $weight[s].sel}selected{/if}>{$weight[s].value}</option>
									{/section}
								</select>
							</td>
						</tr>
						<tr bgcolor="#ffffff">
							<td width="50%" class="main_header_text" align="center">{$header.height}&nbsp;</td>
							<td width="50%" class="main_content_text" align="left" style="padding:3;">
								<select name="id_height"  {if $data.root eq 1}disabled{/if} style="width:200">
									<option value="0">{$button.all}</option>
									{section name=s loop=$height}
									<option value="{$height[s].id}" {if $height[s].sel}selected{/if}>{$height[s].value}</option>
									{/section}
								</select>&nbsp;
							</td>
						</tr>
						<tr bgcolor="#ffffff">
							<td width="50%" class="main_header_text" align="center">{$header.country}&nbsp;</td>
							<td width="50%" class="main_content_text" align="left" style="padding:3;">
							<select name="id_country[]" {if $data.root eq 1}disabled{/if} style="width:200" multiple>
								<option value="0" {if $default.id_country eq 1}selected{/if}>{$button.all}</option>
								{section name=s loop=$country}
								<option value="{$country[s].id}" {if $country[s].sel}selected{/if}>{$country[s].value}</option>
								{/section}
							</select>&nbsp;
							</td>
						</tr>
						<tr bgcolor="#ffffff">
							<td width="50%" class="main_header_text" align="center">{$header.nationality}&nbsp;</td>
							<td width="50%" class="main_content_text" align="left" style="padding:3;">
								<select name="id_nation[]" {if $data.root eq 1}disabled{/if} style="width:200" multiple>
									<option value="0" {if $default.id_nation eq 1}selected{/if}>{$button.all}</option>
									{section name=s loop=$nation}
									<option value="{$nation[s].id}" {if $nation[s].sel}selected{/if}>{$nation[s].value}</option>
									{/section}
								</select>&nbsp;
							</td>
						</tr>
						<tr bgcolor="#ffffff">
							<td width="50%" class="main_header_text" align="center">{$header.language}&nbsp;</td>
							<td width="50%" class="main_content_text" align="left" style="padding:3;">
								<select name="id_lang[]" {if $data.root eq 1}disabled{/if} style="width:200" multiple>
									<option value="0" {if $default.id_lang eq 1}selected{/if}>{$button.all}</option>
									{section name=s loop=$lang_sel}
									<option value="{$lang_sel[s].id}"{if $lang_sel[s].sel} selected{/if}>{$lang_sel[s].value}</option>
									{/section}
								</select>&nbsp;
							</td>
						</tr>
						{section name=f loop=$info}
						<tr bgcolor="#ffffff">
							<td width="50%" align="center" class="main_header_text"><b>{$info[f].name}</b><input type=hidden name="spr[{$smarty.section.f.index}]" value="{$info[f].id}"></td>
							<td width="50%" align="left" class="main_content_text" style="padding:3;">
							<select id="info{$smarty.section.f.index}" name="info[{$smarty.section.f.index}][]"  multiple  style="width:200" {if $data.root eq 1}disabled{/if}>
								<option value="0"{if $info[f].sel_all} selected{/if}>{$button.all}</option>
								{html_options values=$info[f].opt_value selected=$info[f].opt_sel output=$info[f].opt_name}
							</select>
							</td>
						</tr>
						{/section}
            </table>
<br>	
			<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_interests}</font><br><br><br>
            <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
						{section name=f loop=$interests}
						<tr bgcolor="#ffffff">
							<td width="15%" align="center" class="main_header_text"><b>{$interests[f].name_1}</b><input type=hidden name="int_spr[{$interests[f].num_1}]" value="{$interests[f].id_1}"></td>
							<td align="center" class="main_content_text" style="padding:3;">
							<input type="checkbox" name="interests[{$interests[f].num_1}][1]" value="1"{if $interests[f].sel_1_1 eq 1} checked{/if}>&nbsp;
							<input type="checkbox" name="interests[{$interests[f].num_1}][2]" value="1"{if $interests[f].sel_2_1 eq 1} checked{/if}>&nbsp;
							<input type="checkbox" name="interests[{$interests[f].num_1}][3]" value="1"{if $interests[f].sel_3_1 eq 1} checked{/if}>
							</td>
							
							<td width="15%" align="center" class="main_header_text">{if $interests[f].num_2}<b>{$interests[f].name_2}</b><input type=hidden name="int_spr[{$interests[f].num_2}]" value="{$interests[f].id_2}">{else}&nbsp;{/if}</td>
							<td align="center" class="main_content_text" style="padding:3;">{if $interests[f].num_2}
							<input type="checkbox" name="interests[{$interests[f].num_2}][1]" value="1"{if $interests[f].sel_1_2 eq 1} checked{/if}>&nbsp;
							<input type="checkbox" name="interests[{$interests[f].num_2}][2]" value="1"{if $interests[f].sel_2_2 eq 1} checked{/if}>&nbsp;
							<input type="checkbox" name="interests[{$interests[f].num_2}][3]" value="1"{if $interests[f].sel_3_2 eq 1} checked{/if}>{/if}&nbsp;
							</td>
							<td width="15%" align="center" class="main_header_text">{if $interests[f].num_3}<b>{$interests[f].name_3}</b><input type=hidden name="int_spr[{$interests[f].num_3}]" value="{$interests[f].id_3}">{else}&nbsp;{/if}</td>
							<td align="center" class="main_content_text" style="padding:3;">{if $interests[f].num_3}
							<input type="checkbox" name="interests[{$interests[f].num_3}][1]" value="1"{if $interests[f].sel_1_3 eq 1} checked{/if}>&nbsp;
							<input type="checkbox" name="interests[{$interests[f].num_3}][2]" value="1"{if $interests[f].sel_2_3 eq 1} checked{/if}>&nbsp;
							<input type="checkbox" name="interests[{$interests[f].num_3}][3]" value="1"{if $interests[f].sel_3_3 eq 1} checked{/if}>{/if}&nbsp;
							</td>
							
						</tr>
						{/section}
            </table>
			<table><tr height="40">
			{if $data.root  ne 1}<td><input type="button" value="{$button.save}" class="button" onclick="javascript:document.information.submit();"></td>{/if}
			<td><input type="button" value="{$button.close}" class="button" onclick="javascript: window.close();opener.focus();"></td>
			</tr></table>
</form>
{include file="$admingentemplates/admin_bottom.tpl"}