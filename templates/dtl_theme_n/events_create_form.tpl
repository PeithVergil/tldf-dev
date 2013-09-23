{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top" style="padding: 5px 0px 10px 0px;"><div class="header">{$lang.section.events}: {$header.add}</div></td>
	</tr>
	<tr>
		<td valign="top" style="padding: 5px 0px 10px 0px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif" hspace="0" vspace="0" border="0" alt=""></td>
				<td style="padding-left: 2px;"><a href="{$calendar_link}">{$header.back_to_calendar}</a></td>
			</tr>
			</table>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td>
	{/if}
	<tr>
		<td valign="top" class="text">
			<div class="content" style=" margin: 0px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding-left: 12px; padding-top: 10px; padding-bottom: 10px;">
				<form name="event_form" id="event_form" action="events.php?sel=add" method="post" enctype="multipart/form-data">
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td height="30" width="160" class="text_head">{$header.event_name} <font class="error">*</font>:</td>
						<td><input type="text" name="event_name" value="{$data.event_name}" style="width: 150px" maxlength="500"></td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$header.type} <font class="error">*</font>:</td>
						<td height="30">
							<select style="width: 160px" name="type">
								<option value="0">{$lang.home_page.select_default}</option>
								{section name=s loop=$types}
								<option value="{$types[s].id}" {if $types[s].sel}selected{/if}>{$types[s].name}</option>
								{/section}
							</select>
						</td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$header.members_can_invite}:</td>
						<td><input type="checkbox" name="members_can_invite" value="1" checked></td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$header.members_can_post_images}:</td>
						<td><input type="checkbox" name="members_can_post_images" value="1" checked></td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$header.country} <font class=error>*</font>:</td>
						<td height="30">
							<select style="width: 160px" name="country" onchange="SelectRegion('qs', this.value, document.getElementById('region_div'), document.getElementById('city_div'));">
								<option value="0">{$lang.home_page.select_default}</option>
								{section name=s loop=$countries}
								<option value="{$countries[s].id}" {if $countries[s].sel}selected{/if}>{$countries[s].name}</option>
								{/section}
							</select>
						</td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$header.region} <font class=error>*</font>:</td>
						<td height="30">
							<div id="region_div">
							{if $regions}
							<select style="width: 160px" name="region" onchange="SelectCity('qs', this.value, document.getElementById('city_div'));">
								<option value="0">{$lang.home_page.select_default}</option>
								{section name=s loop=$regions}
								<option value="{$regions[s].id}" {if $regions[s].sel}selected{/if}>{$regions[s].name}</option>
								{/section}
							</select>
							{/if}
							</div>
						</td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$header.city} <font class=error>*</font>:</td>
						<td height="30">
							<div id="city_div">
							{if $cities}
							<select style="width: 160px" name="city">
								<option value="0">{$lang.home_page.select_default}</option>
								{section name=s loop=$cities}
								<option value="{$cities[s].id}" {if $cities[s].sel}selected{/if}>{$cities[s].name}</option>
								{/section}
							</select>
							{/if}
							</div>
						</td>
					</tr>
					<tr>
						<td height="30" width="160" class="text_head">{$header.event_place} <font class=error>*</font>:</td>
						<td><input type="text" name="event_place" value="{$data.event_place}" style="width: 150px" maxlength="500"></td>
					</tr>
					<tr>
						<td height="30" width="160" class="text_head">{$header.event_date_begin} <font class=error>*</font>:</td>
						<td>
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
					<tr>
						<td height="30" width="160" class="text_head">{$header.event_date_end} <font class=error>*</font>:</td>
						<td>
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
					<tr>
						<td height="40" width="160" class="text_head">{$header.event_periodicity}:</td>
						<td height="40">
						<input type="radio" name="periodicity" value="none" {if $data.periodicity == "none"}checked{/if} onclick="javascript: document.getElementById('die_date').style.display='none';"> {$header.event_period.none}
						<input type="radio" name="periodicity" value="daily" {if $data.periodicity == "daily"}checked{/if} onclick="javascript: document.getElementById('die_date').style.display='inline';"> {$header.event_period.daily}
						<input type="radio" name="periodicity" value="weekly" {if $data.periodicity == "weekly"}checked{/if} onclick="javascript: document.getElementById('die_date').style.display='inline';"> {$header.event_period.weekly}
						<input type="radio" name="periodicity" value="monthly" {if $data.periodicity == "monthly"}checked{/if} onclick="javascript: document.getElementById('die_date').style.display='inline';"> {$header.event_period.monthly}
						<input type="radio" name="periodicity" value="yearly" {if $data.periodicity == "yearly"}checked{/if} onclick="javascript: document.getElementById('die_date').style.display='inline';"> {$header.event_period.yearly}
						<div id="die_date" style="display:{if $data.periodicity == "none"}none{else}inline{/if}">
						<br>{$header.until}:&nbsp;
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
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr><td class="text_head">{$header.event_descr}:</td></tr>
							<tr><td class="text">{$header.no_html}</td></tr>
						</table>
						</td>
						<td><textarea name="description" style="width: 300px;" rows="7">{$data.description}</textarea></td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr><td class="text_head">{$header.flyer}:</td></tr>
							<tr><td class="text">{$header.flyer_hint}</td></tr>
						</table>
						</td>
						<td><input type="file" name="flyer"></td>
					</tr>
					<tr>
						<td class="text_head">{$header.verification}:</td>
						<td><img src="{$form.kcaptcha}" alt="{$header.verification}"><br><input type="text" style="width: 150px" name="keystring"></td>
					</tr>
					<tr>
						<td height="30" colspan="2"><input type="submit" value="{$lang.button.submit}" class="button"></td>
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
{include file="$gentemplates/index_bottom.tpl"}