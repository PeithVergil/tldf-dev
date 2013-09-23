{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<div style="margin: 0px; height: 25px;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
			<div class="header">{$lang.section.events}:&nbsp;{$date}</div>
		</td>
		<td valign="top" align="right"><a href="{$calendar_link}"><b>{$lang.section.calendar}</b></a>
		<div id="tool_tip_4"><label title="{$lang.events.help_tip.my_calendar}"><img src="{$site_root}{$template_root}/images/question_icon.gif"></label></div>
		{literal}
		<script type="text/javascript">
		$(function() {
		$('#tool_tip_4 *').tooltip();
		});
		</script>
		{/literal}
		</td>
	</tr>
	</table>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding: 5px 0px;">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td valign="middle">
						<span style="display: inline;" id="hide_image"><img src="{$site_root}{$template_root}/images/btn_up.gif" alt="" vspace="0" hspace="0" align="middle"></span>
						<span style="display: none;" id="show_image"><img src="{$site_root}{$template_root}/images/btn_down.gif" alt="" vspace="0" hspace="0" align="middle"></span>
					</td>
					<td valign="middle" style="padding-left: 5px;">
						<span style="display: inline;" id="hide_link"><a href="#" onclick="ShowSearchForm('1'); return false;">{$header.search_form_hide}</a></span>
						<span style="display: none;" id="show_link"><a href="#" onclick="ShowSearchForm('2'); return false;">{$header.search_form}</a></span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<div id="index_quick_search" class="index_quick_search" style="display: inline;">
				<table cellpadding="0" cellspacing="0" width=100% height=280>
				<tr>
					<td valign="top" style="padding: 12px 25px;" width=150>
						<form action="events.php?sel=search" method="post" style="margin: 0px;" id="s_form">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td class="text_head" valign="top">{$header.location}</td>
							</tr>
							<tr>
								<td valign="top" style="padding-top: 5px;">
									<select style="width: 150px" name="id_country" class="index_select" onchange="SelectRegion('mp', this.value, document.getElementById('region_div'), document.getElementById('city_div'));">
										<option value="0">{$lang.home_page.select_default}</option>
										{section name=s loop=$form.countries}
										<option value="{$form.countries[s].id}" {if $form.countries[s].sel}selected{/if}>{$form.countries[s].name}</option>
										{/section}
									</select>
									<div style="margin-top: 5px" id="region_div">
									<select style="width: 150px" name="id_region" class="index_select" onchange="SelectCity('mp', this.value, document.getElementById('city_div'));">
										<option value="0">{$lang.home_page.select_default}</option>
										{section name=s loop=$form.regions}
										<option value="{$form.regions[s].id}" {if $form.regions[s].sel}selected{/if}>{$form.regions[s].name}</option>
										{/section}
									</select>
									</div>
									<div style="margin-top: 5px" id="city_div">
									<select style="width: 150px" class="index_select" name="id_city">
										<option value="0">{$lang.home_page.select_default}</option>
										{section name=s loop=$form.cities}
										<option value="{$form.cities[s].id}" {if $form.cities[s].sel}selected{/if}>{$form.cities[s].name}</option>
										{/section}
									</select>
									</div>
								</td>
							</tr>
							<tr>
								<td valign="top" style="padding-top: 25px;" class="text_head">{$header.type}</td>
							</tr>
							<tr>
								<td valign="top" style="padding-top: 5px;">
									<select style="width: 150px" class="index_select" name="type">
										<option value="0">{$lang.home_page.select_default}</option>
										{section name=s loop=$form.types}
										<option value="{$form.types[s].id}" {if $form.types[s].sel}selected{/if}>{$form.types[s].name}</option>
										{/section}
									</select>&nbsp;
								</td>
							</tr>
							<tr>
								<td align="left" style="padding-top: 25px;"><input type="submit" class="big_button" value="{$button.search}"></td>
							</tr>
						</table>
						</form>
					</td>
					<td valign="top" align="right" style="padding: 12px 25px;">
						<div id="calendar">{$calendar_out_put}</div>
					</td>
				</tr>
				</table>
			</div>
		</td>
	</tr>
	</table>
	{if $events}
	<div style="padding: 20px 0px 10px 0px">
		{section name=e loop=$events}
			<div style="padding: 0px 0px 5px 0px" class="link">&raquo;&nbsp;&nbsp;<a href="#{$events[e].id_event}">{$events[e].name}</a></div>
		{/section}
	</div>
	{section name=e loop=$events}
	<div style="padding: 5px 0px 0px 0px" class="text_head"><a name="{$events[e].id_event}" href="{$event_path}?sel=event&id_event={$events[e].id_event}"><b>{$events[e].name}</b></a>&nbsp;&nbsp;<span class="text_hidden">{$events[e].type}</span></div>
	<div style="padding: 5px 0px 0px 0px" class="text_hidden">{$header.event_date_begin}:&nbsp;{$events[e].date_begin};&nbsp;&nbsp;{$header.event_date_end}:&nbsp;{$events[e].date_end};&nbsp;&nbsp;{$header.event_periodicity}:&nbsp;{$events[e].periodicity} {if $events[e].date_die}{$header.until} {$events[e].date_die}{/if}</div>
	<div style="padding: 5px 0px 0px 0px" class="text_hidden">{$header.location}:&nbsp;{$events[e].location};&nbsp;&nbsp;{$header.event_place}:&nbsp;{$events[e].place}</div>
	{if $events[e].joined}
	<div style="padding: 5px 0px 0px 0px" class="text"><b>{$events[e].num_users}&nbsp;{$header.joined}.&nbsp;&nbsp;</b><a href="{$events[e].leave_link}">{$header.leave_event}</a></div>
	{else}
	<div style="padding: 5px 0px 0px 0px" class="text"><b>{$events[e].num_users}&nbsp;{$header.joined}.&nbsp;&nbsp;</b><a href="{$events[e].join_link}">{$header.join_event}</a></div>
	{/if}
	<br>
	{/section}
	{else}
    	<div class="error_msg">{$header.no_events}</div>
	{/if}
	<!-- end main cell -->
	{literal}
	<script type="text/javascript">
		function ShowSearchForm(par) {
			if (par == '1') {
				document.getElementById('hide_image').style.display = 'none';
				document.getElementById('hide_link').style.display = 'none';
				document.getElementById('show_image').style.display = 'inline';
				document.getElementById('show_link').style.display = 'inline';
				document.getElementById('index_quick_search').style.display = 'none';
			} else {
				document.getElementById('hide_image').style.display = 'inline';
				document.getElementById('hide_link').style.display = 'inline';
				document.getElementById('show_image').style.display = 'none';
				document.getElementById('show_link').style.display = 'none';
				document.getElementById('index_quick_search').style.display = '';
				//Nifty("div#index_quick_search");
				{/literal}
				LoadCalendar(document.getElementById('calendar'), '&id_country={$form.id_country}&id_region={$form.id_region}&id_city={$form.id_city}&type={$form.type}');
				{literal}
			}
		}

		function showToolTip(e,text){
			if(document.all)e = event;
			var obj = document.getElementById('tooltip');
			obj.innerHTML = text;
			obj.style.display = 'block';
			var st = Math.max(document.body.scrollTop,document.documentElement.scrollTop);
			if(navigator.userAgent.toLowerCase().indexOf('safari')>=0)st=0;
			var leftPos = e.clientX - 100;
			if(leftPos<0)leftPos = 0;
			obj.style.left = leftPos + 'px';
			obj.style.top = e.clientY - obj.offsetHeight -5 + st + 'px';
		}
		function hideToolTip(){
			document.getElementById('tooltip').style.display = 'none';
		}

		function LoadCalendar(destination, param) {
			InitXMLHttpRequest();
			// Load the result from the response page
			if (req) {
				req.onreadystatechange = function() {
					if (req.readyState == 4) {
						destination.innerHTML = req.responseText;
					} else {
						destination.innerHTML = "Loading data...";
					}
				}
				req.open("GET", "events_calendar.php?act=ajax" + param, true);
				req.send(null);
			} else {
				destination.innerHTML = 'Browser unable to create XMLHttp Object';
			}
		}
	</script>
	{/literal}
</td>
{include file="$gentemplates/index_bottom.tpl"}