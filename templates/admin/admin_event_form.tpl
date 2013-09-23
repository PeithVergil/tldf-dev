{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform}</font>
	<div class="help_text"><span class="help_title">{$lang.help}:</span>{if $form.par eq "edit"}{$help.events_edit}{else}{$help.events_add}{/if}</div>
	{if $form.err}{else}
	{if $err}<font class=main_error_text>*{$err}</font><br><br>{/if}
	<form method="POST" action="{$form.action}" name="eventform" enctype="multipart/form-data">
    {$form.hiddens}
	<table border=0 cellspacing=1 cellpadding=5 width="100%">
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.event_name}<font class=main_error_text>*</font>:&nbsp;</td>
					<td class="main_content_text" align="left"><input type="text" name="name" value="{$data.name}" size=30 style="width: 195"></td>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.type}<font class=main_error_text>*</font>:&nbsp;</td>
					<td class="main_content_text" align="left">
						<select style="width: 195px" name="type">
							<option value="0">{$lang.home_page.select_default}</option>
							{section name=s loop=$types}
							<option value="{$types[s].id}" {if $types[s].sel}selected{/if}>{$types[s].name}</option>
							{/section}
						</select>
					</td>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.members_can_invite}:&nbsp;</td>
					<td class="main_content_text" align="left"><input type="checkbox" name="members_can_invite" value="1" {if $data.members_can_invite}checked{/if}></td>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.members_can_post_images}:&nbsp;</td>
					<td class="main_content_text" align="left"><input type="checkbox" name="members_can_post_images" value="1" {if $data.members_can_post_images}checked{/if}></td>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.country}<font class=main_error_text>*</font>:&nbsp;</td>
					<td class="main_content_text" align="left">
						<select style="width: 195px" name="country" onchange="SelectRegionAdmin(this.value, document.getElementById('region_div'), document.getElementById('city_div'));">
							<option value="0">{$lang.home_page.select_default}</option>
							{section name=s loop=$countries}
							<option value="{$countries[s].id}" {if $countries[s].sel}selected{/if}>{$countries[s].name}</option>
							{/section}
						</select>
					</td>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.region}<font class=main_error_text>*</font>:&nbsp;</td>
					<td class="main_content_text" align="left">
						<div id="region_div">
						{if $regions}
						<select style="width: 195px" name="region" onchange="SelectCity(this.value, document.getElementById('city_div'));">
							<option value="0">{$lang.home_page.select_default}</option>
							{section name=s loop=$regions}
							<option value="{$regions[s].id}" {if $regions[s].sel}selected{/if}>{$regions[s].name}</option>
							{/section}
						</select>
						{/if}
						</div>
					</td>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.city}<font class=main_error_text>*</font>:&nbsp;</td>
					<td class="main_content_text" align="left">
						<div id="city_div">
						{if $cities}
						<select style="width: 195px" name="city">
							<option value="0">{$lang.home_page.select_default}</option>
							{section name=s loop=$cities}
							<option value="{$cities[s].id}" {if $cities[s].sel}selected{/if}>{$cities[s].name}</option>
							{/section}
						</select>
						{/if}
						</div>
					</td>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.event_place}<font class=main_error_text>*</font>:&nbsp;</td>
					<td class="main_content_text" align="left"><input type="text" name="place" value="{$data.place}" size=30 style="width: 195"></td>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.event_date_begin}<font class=main_error_text>*</font>:&nbsp;</td>
					<td class="main_content_text" align="left">
                        <select name="s_day" >
						{section name=d loop=$s_day}
							<option value="{$s_day[d].value}" {if $s_day[d].sel}selected{/if}>{$s_day[d].value}</option>
						{/section}
						</select>&nbsp;
                        <select name="s_month" >
						{section name=m loop=$s_month}
							<option value="{$s_month[m].value}" {if $s_month[m].sel}selected{/if}>{$s_month[m].name}</option>
						{/section}
						</select>&nbsp;
                        <select name="s_year" >
						{section name=y loop=$s_year}
							<option value="{$s_year[y].value}" {if $s_year[y].sel}selected{/if}>{$s_year[y].value}</option>
						{/section}
						</select>&nbsp;
						<select name="s_hour" >
						{section name=h loop=$s_hour}
							<option value="{$s_hour[h].value}" {if $s_hour[h].sel}selected{/if}>{$s_hour[h].value}</option>
						{/section}
						</select>&nbsp;
						<select name="s_min" >
						{section name=i loop=$s_min}
							<option value="{$s_min[i].value}" {if $s_min[i].sel}selected{/if}>{$s_min[i].value}</option>
						{/section}
						</select>
					</td>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.event_date_end}<font class=main_error_text>*</font>:&nbsp;</td>
					<td class="main_content_text" align="left">
                        <select name="f_day" >
						{section name=d loop=$f_day}
							<option value="{$f_day[d].value}" {if $f_day[d].sel}selected{/if}>{$f_day[d].value}</option>
						{/section}
						</select>&nbsp;
                        <select name="f_month" >
						{section name=m loop=$f_month}
							<option value="{$f_month[m].value}" {if $f_month[m].sel}selected{/if}>{$f_month[m].name}</option>
						{/section}
						</select>&nbsp;
                        <select name="f_year" >
						{section name=y loop=$f_year}
							<option value="{$f_year[y].value}" {if $f_year[y].sel}selected{/if}>{$f_year[y].value}</option>
						{/section}
						</select>&nbsp;
						<select name="f_hour" >
						{section name=h loop=$f_hour}
							<option value="{$f_hour[h].value}" {if $f_hour[h].sel}selected{/if}>{$f_hour[h].value}</option>
						{/section}
						</select>&nbsp;
						<select name="f_min" >
						{section name=i loop=$f_min}
							<option value="{$f_min[i].value}" {if $f_min[i].sel}selected{/if}>{$f_min[i].value}</option>
						{/section}
						</select>
					</td>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.event_periodicity}:&nbsp;</td>
					<td class="main_content_text" align="left">
						<input type="radio" name="periodicity" value="none" {if $data.periodicity == "none"}checked{/if} onclick="javascript: document.getElementById('die_date').style.display='none';"> {$header.event_period.none}
						<input type="radio" name="periodicity" value="daily" {if $data.periodicity == "daily"}checked{/if} onclick="javascript: document.getElementById('die_date').style.display='inline';"> {$header.event_period.daily}
						<input type="radio" name="periodicity" value="weekly" {if $data.periodicity == "weekly"}checked{/if} onclick="javascript: document.getElementById('die_date').style.display='inline';"> {$header.event_period.weekly}
						<input type="radio" name="periodicity" value="monthly" {if $data.periodicity == "monthly"}checked{/if} onclick="javascript: document.getElementById('die_date').style.display='inline';"> {$header.event_period.monthly}
						<input type="radio" name="periodicity" value="yearly" {if $data.periodicity == "yearly"}checked{/if} onclick="javascript: document.getElementById('die_date').style.display='inline';"> {$header.event_period.yearly}
						<div id="die_date" style="display:{if $data.periodicity == "none"}none{else}inline{/if}">
						&nbsp;&nbsp;&nbsp;{$header.until}:&nbsp;
                        <select name="d_day" >
						<option value="0" ></option>
						{section name=d loop=$d_day}
							<option value="{$d_day[d].value}" {if $d_day[d].sel}selected{/if}>{$d_day[d].value}</option>
						{/section}
						</select>&nbsp;
                        <select name="d_month" >
						<option value="0" ></option>
						{section name=m loop=$d_month}
							<option value="{$d_month[m].value}" {if $d_month[m].sel}selected{/if}>{$d_month[m].name}</option>
						{/section}
						</select>&nbsp;
                        <select name="d_year" >
						<option value="0" ></option>
						{section name=y loop=$d_year}
							<option value="{$d_year[y].value}" {if $d_year[y].sel}selected{/if}>{$d_year[y].value}</option>
						{/section}
						</select>&nbsp;
						</div>
					</td>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.event_descr}:&nbsp;</td>
					<td class="main_content_text" align="left"><textarea cols="50" rows="5" name="contain">{$data.contain}</textarea>
				</tr>
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.flyer}:&nbsp;</td>
					<td>{if $data.flyer}<img src="{$data.flyer}"><br><br>{/if}<input type="file" name="flyer"></td>
				</tr>
				<tr bgcolor="#ffffff"><td colspan="2">
				<table>
					<tr height="40">
					{if $form.par eq "edit"}
					<td><input type="button" value="{$button.save}" class="button" onclick="javascript:document.eventform.sel.value='save';document.eventform.submit()"></td>
					<td><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm('Delete this event?')){location.href={/literal}'{$form.delete_link}'{literal}}{/literal}"></td>
					{else}
					<td><input type="button" value="{$button.add}" class="button" onclick="javascript:document.eventform.sel.value='add';document.eventform.submit()"></td>
					{/if}
					<td><input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$form.back}'"></td>
					</tr>
				</table>
				</td></tr>
    </table>
    </form>
    {/if}
{include file="$admingentemplates/admin_bottom.tpl"}