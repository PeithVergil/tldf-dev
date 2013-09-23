{include file="$gentemplates/index_top.tpl"}
{strip}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$lang.organizer.page_title}</div></div>
		</td>
	</tr>
	{if $form.back_link}
	<tr>
		<td style="padding-top: 3px; padding-bottom: 5px;">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif" border="0" alt="" vspace="0" hspace="0"></td>
					<td>&nbsp;<a href="{$form.back_link}">{$lang.button.back_to_organizer}</a></td>
				</tr>
			</table>
		</td>
	</tr>
	{/if}
	{if $form.err && $form.section eq ''}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td>
	</tr>
	{/if}
	<tr>
		<td valign="top" class="text">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" width="50%" height="280">
					<div class="content" style=" margin: 0px; padding: 10px 15px 15px 10px;">
					<div class="header">{$lang.organizer.general_options}</div>
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<td valign="top" style="padding-top: 15px;"><a href="organizer.php?sel=bookmarks"><b>{$lang.organizer.my_bookmarks}</b></a></td>
					</tr>
					{if ($use_pilot_module_events)}
					<tr>
						<td style="padding-top: 5px;"><a href="events_calendar.php?from=organizer"><b>{$lang.organizer.my_events_stats}</b></a></td>
					</tr>
					{/if}
					<tr>
						<td style="padding-top: 5px;"><a href="organizer.php?sel=billing"><b>{$lang.organizer.billing_history}</b></a></td>
					</tr>
					<tr>
						<td style="padding-top: 5px;"><a href="organizer.php?sel=homepage_management"><b>{$lang.organizer.homepage_management}</b></a></td>
					</tr>
					<tr>
						<td style="padding-top: 5px; padding-left: 15px;"><a href="organizer.php?sel=homepage_management&type=layout">{$lang.organizer.my_page_layout}</a></td>
					</tr>
					<tr>
						<td style="padding-top: 5px; padding-left: 15px;"><a href="organizer.php?sel=homepage_management&type=page_styles">{$lang.organizer.my_page_styles}</a></td>
					</tr>
					</table>
					</div>
					{if $form.section ne 'calendar_action'}
					<div style=" margin-top: 10px; padding: 10px 15px 15px 10px;" class="content">
					<div class="header">{$lang.organizer.my_stats}</div>
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td valign="top" style="padding-top: 8px;" width="100%">
							<table cellpadding="0" cellspacing="0" width="100%" height="100%">
							{foreach item=item from=$user_stat}
								<tr>
									<td style="padding-top: 5px;" width="90%">{$lang.organizer[$item.name]}:&nbsp;</td>
									<td style="padding-top: 5px;" width="10%" class="text_head"><a href="{$item.link}">{$item.value}</a></td>
								</tr>
							{/foreach}
							</table>
							</td>
						</tr>
					</table>
					</div>
					{/if}
				</td>
				<td width="1%">&nbsp;</td>
				<td width="49%" valign="top">
					<div class="content" style="margin: 0px; padding: 10px 15px 10px 10px;">
					<div class="header">{$lang.organizer.calendar}</div>
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td id="calendar" height="225" valign="middle" align="center">{$calendar_out_put}</td>
						</tr>
						<tr>
							<td style="padding-top: 3px;"><a href="organizer.php?sel=date">{$lang.organizer.calendar_management}</a>
							<div id="tool_tip_5"><label title="{$lang.organizer.help_tip.calendar}"><img src="{$site_root}{$template_root}/images/question_icon.gif"></label></div>
				{literal}
				<script type="text/javascript">
				$(function() {
				$('#tool_tip_5 *').tooltip();
				});
				</script>
				{/literal}
							</td>
						</tr>
					</table>
					</div>
					{if $form.section ne 'calendar_action'}
					<div style="margin-top: 10px; padding: 10px 15px 15px 10px;" class="content">
					<div>
						<div class="header">{$lang.organizer.my_other_site_profiles}</div>
					<table cellpadding="0" cellspacing="0" width="100%" border="0" height="170">
						{if $site_profiles}
						{foreach item=item from=$site_profiles}
						<tr>
							<td valign="top"  style="padding-top: 5px;"><a href="http://{$item.link}" target="_blank">{$item.descr}</a></td>
							<td align="right" style="padding-top: 5px;" width="11"><a href="organizer.php?sel=del_profile&id={$item.id}"><img src="{$site_root}{$template_root}/images/org_delete_icon.gif" border="0" alt="{$lang.organizer.del_profile}"></a></td>
						</tr>
						{/foreach}
						{else}
						<tr>
							<td colspan="2" style="padding: 10px 0px;" class="text">{$lang.organizer.no_site_profiles}</td>
						</tr>
						{/if}
						<tr>
							<td colspan="2" style="padding-top: 45px;" align="left"><input type="button" value="{$lang.organizer.add_profile}" onclick="ShowAddProfileForm();"></td>
						</tr>
					</table>
					</div>
					<div id="add_profile_form" {if $form.err && $form.section eq '1'} style="display: inline;"{else} style="display: none;"{/if}>
					<form method="post" action="organizer.php" style="margin: 0px; padding: 0px;">
					<input type="hidden" name="sel" value="save_profile">
					<table cellpadding="0" cellspacing="0" width="100%" border="0">
						{if $form.err && $form.section eq '1'}
						<tr>
							<td colspan="2"><div class="error_msg">{$form.err}</div></td>
						</tr>
						{/if}
						<tr>
							<td style="padding-top: 10px;" class="text">{$lang.organizer.url}:&nbsp;<font class="error">*</font>&nbsp;</td>
							<td style="padding-top: 10px;" align="right">
								<table cellpadding="0" cellspacing="0">
								<tr>
									<td class="text_hidden">http://&nbsp;</td>
									<td><input type="text" name="profile_url" style="width: 170px;" value="{$data.profile_url}" maxlength="255"></td>
								</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="padding-top: 5px;" class="text">{$lang.organizer.description}:&nbsp;<font class="error">*</font>&nbsp;</td>
							<td align="right" style="padding-top: 5px;"><input type="text" name="profile_descr" style="width: 170px;" value="{$data.profile_descr}" maxlength="255"></td>
						</tr>
						<tr>
							<td style="padding-top: 5px;" colspan="2"><input type="submit" value="{$lang.organizer.save_profile}"></td>
						</tr>
					</table>
					</form>
					</div>
					</div>
					{/if}
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			{if $form.section eq 'calendar_action'}
			<tr>
				<td colspan="3" valign="top">
				<div class="content" style="margin: 0px; padding: 10px 15px 15px 10px;">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td>
								<div><input type="button" onclick="ShowCalendarAddForm(); return false;" value="{$lang.organizer.create_action}"></div>
								<div id="calendar_add_form" {if !($form.err && $form.section eq 'calendar_action')} style="display: none;"{/if}>
								<form method="post" action="organizer.php" style="margin: 0px; padding: 0px;">
								<input type="hidden" name="sel" value="save_action">
								<table cellpadding="0" cellspacing="0" width="100%" border="0">
									{if $form.err && $form.section eq 'calendar_action'}
									<tr>
										<td colspan="2"><div class="error_msg">{$form.err}</div></td>
									</tr>
									{/if}
									<tr>
										<td style="padding-top: 10px;" class="text" width="120">{$lang.organizer.action_date}:&nbsp;<font class="error">*</font>&nbsp;</td>
										<td style="padding-top: 10px;">
				                        <select name="day" >
										{section name=d loop=$day}
											<option value="{$day[d].value}" {if $day[d].sel}selected{/if}>{$day[d].value}</option>
										{/section}
										</select>&nbsp;
				                        <select name="month" >
										{section name=m loop=$month}
											<option value="{$month[m].value}" {if $month[m].sel}selected{/if}>{$month[m].name}</option>
										{/section}
										</select>&nbsp;
				                        <select name="year" >
										{section name=y loop=$year}
											<option value="{$year[y].value}" {if $year[y].sel}selected{/if}>{$year[y].value}</option>
										{/section}
										</select>&nbsp;&nbsp;
										<select name="hour" >
										{section name=h loop=$hour}
											<option value="{$hour[h].value}" {if $hour[h].sel}selected{/if}>{$hour[h].value}</option>
										{/section}
										</select>&nbsp;
										<select name="min" >
										{section name=i loop=$min}
											<option value="{$min[i].value}" {if $min[i].sel}selected{/if}>{$min[i].value}</option>
										{/section}
										</select>
										</td>
									</tr>
									<tr>
										<td style="padding-top: 10px;" class="text">{$lang.organizer.action_name}:&nbsp;<font class="error">*</font>&nbsp;</td>
										<td style="padding-top: 10px;"><input type="text" name="action_name" style="width: 170px;" value="{$data.action_name}" maxlength="255"></td>
									</tr>
									<tr>
										<td style="padding-top: 5px;" class="text">{$lang.organizer.action_descr}:&nbsp;<font class="error">*</font>&nbsp;</td>
										<td style="padding-top: 5px;"><textarea name="action_descr" style="width: 250px;" rows="5">{$data.action_descr}</textarea>
									</tr>
									<tr>
										<td style="padding-top: 5px;" colspan="2"><input type="submit" value="{$lang.organizer.save_action}"></td>
									</tr>
								</table>
								</form>
								</div>
							</td>
						</tr>
						{if $user_actions ne 'empty'}
						<tr>
							<td style="padding-top: 10px;">
							<form style="margin: 0px;" action="organizer.php" method="post">
							<input type="hidden" name="sel" value="del_action">
							<input type="hidden" name="day" value="{$form.day}">
							<input type="hidden" name="month" value="{$form.month}">
							<input type="hidden" name="year" value="{$form.year}">
							<table cellpadding="0" cellspacing="0" width="100%">
								{foreach item=item from=$user_actions}
								<tr>
									<td bgcolor="#{if $item.sel eq 1}{$css_color.home_search_2}{else}{$css_color.home_search}{/if}" style="padding: 10px 10px;">
										<table cellpadding="0" cellspacing="0" width="100%" border="0">
										<tr>
											<td valign="top" width="15" style="padding-bottom: 10px;"><input type="checkbox" value="{$item.id}" name="del_id[]"></td>
											<td style="padding-bottom: 10px; padding-left: 3px;"><b>{$item.name}</b></td>
											<td style="padding-bottom: 10px;" align="right"><b>{$item.date}</b></td>
										</tr>
										</table>
										<table cellpadding="0" cellspacing="0" width="100%" border="0">
										<tr>
											<td colspan="3">
												<div style="background-color:  #FFFFFF; padding: 10px;">{$item.contain}</div>
											</td>
										</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td bgcolor="White" height="5"></td>
								</tr>
								{/foreach}
								<tr>
									<td style="padding-top: 5px;"><input type="submit" value="{$lang.organizer.del_selected}"></td>
								</tr>
							</table>
							</form>
							</td>
						</tr>
						{/if}
					</table>
				</div>
				</td>
			</tr>
			{/if}
			</table>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>{/strip}
{literal}
<script type="text/javascript">

function ShowCalendarAddForm() {
	if (document.getElementById('calendar_add_form').style.display == 'none') {
		document.getElementById('calendar_add_form').style.display = 'inline';
	} else {
		document.getElementById('calendar_add_form').style.display = 'none';
	}
	return;
}

function ShowAddProfileForm() {
	if (document.getElementById('add_profile_form').style.display == 'none') {
		document.getElementById('add_profile_form').style.display = 'inline';
	} else {
		document.getElementById('add_profile_form').style.display = 'none';
	}
	return;
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

var req = null;

function InitXMLHttpRequest() {
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP");
	}
}

function LoadOrgCalendar(destination, url) {
	InitXMLHttpRequest();
	// Load the result from the response page
	if (req) {
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				destination.innerHTML = req.responseText;
			} else {
				destination.innerHTML = "<div align='center' style='width: 330px;'>Loading data...</div>";
			}
		}
		req.open("GET", "organizer.php?sel=ajax_calendar&act=1"+url, true);
		req.send(null);
	} else {
		destination.innerHTML = 'Browser unable to create XMLHttp Object';
	}
}
</script>
{/literal}
{include file="$gentemplates/index_bottom.tpl"}