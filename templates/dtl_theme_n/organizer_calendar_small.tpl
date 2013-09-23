{strip}			<div id="tooltip" onclick="hideToolTip()" style="background-color: #FFFFFF; border: 1px dotted; width: 150px; text-align: left; position:absolute; display: none; padding:5px;"></div>
				<div align="center">
				<TABLE cellpadding="0" cellspacing="0" border="0">
				<TR><!-- Calendar menu -->
					<TD colspan="7" align="center" valign="middle" style="padding: 0px 5px 5px 5px;">
					<A onclick="javascript: LoadOrgCalendar(document.getElementById('calendar'), '&show_period=month&month={$selected_month.mon}&year={$selected_month.year}&move=back_month&'); return false;" href="#">&laquo;</A>
					&nbsp;&nbsp;&nbsp;<font class="text_head">{$selected_month.month},&nbsp;{$selected_month.year}</font>&nbsp;&nbsp;&nbsp;
					<A onclick="javascript: LoadOrgCalendar(document.getElementById('calendar'), '&show_period=month&month={$selected_month.mon}&year={$selected_month.year}&move=next_month&'); return false;" href="#">&raquo;</A>
					</TD>
				</TR><!-- /Calendar menu -->
				</table>
				<TABLE cellpadding="0" cellspacing="0" border="0">
				<TR><!-- Calendar header -->
					<TD width="50" align="center" class="calendar_header_top_cell">
						{$lang.organizer.weekday.monday}
					</TD>
					<TD width="50" align="center" class="calendar_header_top_cell">
						{$lang.organizer.weekday.tuesday}
					</TD>
					<TD width="50" align="center" class="calendar_header_top_cell">
						{$lang.organizer.weekday.wednesday}
					</TD>
					<TD width="50" align="center" class="calendar_header_top_cell">
						{$lang.organizer.weekday.thursday}
					</TD>
					<TD width="50" align="center" class="calendar_header_top_cell">
						{$lang.organizer.weekday.friday}
					</TD>
					<TD width="50" align="center" class="calendar_header_top_cell">
						{$lang.organizer.weekday.saturday}
					</TD>
					<TD width="50" align="center" class="calendar_header_right_cell">
						{$lang.organizer.weekday.sunday}
					</TD>
				</TR><!-- /Calendar header -->
				{foreach from=$current_month item=week}
				<TR><!-- Calendar body -->
					<TD align="center" valign="top" {if $week.1 == "false"}class="calendar_top_cell_empty"{else}{if $week.1.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.1 == "false"}
							&nbsp;
						{else}
							<TABLE cellpadding="0" cellspacing="0" border="0" width="100%">
							<TR>
								<TD align="left" style="padding-left:5px;" class="calendar_day_number" valign="top">
									<B>{$week.1.mday}</B>
								</TD>
							</TR>
							{if $week.1.count_org_actions>0}
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="organizer.php?sel=date&year={$week.1.year}&month={$week.1.mon}&day={$week.1.mday}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.1.org_actions name=e}{$smarty.section.e.iteration}. <A href=organizer.php?sel=date&year={$week.1.year}&month={$week.1.mon}&day={$week.1.mday}&id_action={$week.1.org_actions[e].id} class=calendar_url>{$week.1.org_actions[e].name}</A><BR>{/section}');return false">
										{$week.1.count_org_actions}
									</A>
								</TD>
							</TR>
							{else}
							<tr>
								<td><br></td>
							</tr>
							{/if}
							</TABLE>
						{/if}
					</TD>
					<TD align="center" valign="top" {if $week.2 == "false"}class="calendar_top_cell_empty"{else}{if $week.2.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.2 == "false"}
							&nbsp;
						{else}
							<TABLE cellpadding="0" cellspacing="0" width="100%">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.2.mday}</B>
								</TD>
							</TR>
							{if $week.2.count_org_actions>0}
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="organizer.php?sel=date&year={$week.2.year}&month={$week.2.mon}&day={$week.2.mday}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.2.org_actions name=e}{$smarty.section.e.iteration}. <A href=organizer.php?sel=date&year={$week.2.year}&month={$week.2.mon}&day={$week.2.mday}&id_action={$week.2.org_actions[e].id} class=calendar_url>{$week.2.org_actions[e].name}</A><BR>{/section}');return false">
										{$week.2.count_org_actions}
									</A>
								</TD>
							</TR>
							{else}
							<tr>
								<td><br></td>
							</tr>
							{/if}
							</TABLE>
						{/if}
					</TD>
					<TD align="center" valign="top" {if $week.3 == "false"}class="calendar_top_cell_empty"{else}{if $week.3.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.3 == "false"}
							&nbsp;
						{else}
							<TABLE cellpadding="0" cellspacing="0" width="100%">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.3.mday}</B>
								</TD>
							</TR>
							{if $week.3.count_org_actions}
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="organizer.php?sel=date&year={$week.3.year}&month={$week.3.mon}&day={$week.3.mday}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.3.org_actions name=e}{$smarty.section.e.iteration}. <A href=organizer.php?sel=date&year={$week.3.year}&month={$week.3.mon}&day={$week.3.mday}&id_action={$week.3.org_actions[e].id} class=calendar_url>{$week.3.org_actions[e].name}</A><BR>{/section}');return false">
										{$week.3.count_org_actions}
									</A>
								</TD>
							</TR>
							{else}
							<tr>
								<td><br></td>
							</tr>
							{/if}
							</TABLE>
						{/if}
					</TD>
					<TD align="center" valign="top" {if $week.4 == "false"}class="calendar_top_cell_empty"{else}{if $week.4.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.4 == "false"}
							&nbsp;
						{else}
							<TABLE cellpadding="0" cellspacing="0" width="100%">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.4.mday}</B>
								</TD>
							</TR>
							{if $week.4.count_org_actions}
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="organizer.php?sel=date&year={$week.4.year}&month={$week.4.mon}&day={$week.4.mday}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.4.org_actions name=e}{$smarty.section.e.iteration}. <A href=organizer.php?sel=date&year={$week.4.year}&month={$week.4.mon}&day={$week.4.mday}&id_action={$week.4.org_actions[e].id} class=calendar_url>{$week.4.org_actions[e].name}</A><BR>{/section}');return false">
										{$week.4.count_org_actions}
									</A>
								</TD>
							</TR>
							{else}
							<tr>
								<td><br></td>
							</tr>
							{/if}
							</TABLE>
						{/if}
					</TD>
					<TD align="center" valign="top" {if $week.5 == "false"}class="calendar_top_cell_empty"{else}{if $week.5.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.5 == "false"}
							&nbsp;
						{else}
							<TABLE cellpadding="0" cellspacing="0" width="100%">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.5.mday}</B>
								</TD>
							</TR>
							{if $week.5.count_org_actions}
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="organizer.php?sel=date&year={$week.5.year}&month={$week.5.mon}&day={$week.5.mday}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.5.org_actions name=e}{$smarty.section.e.iteration}. <A href=organizer.php?sel=date&year={$week.5.year}&month={$week.5.mon}&day={$week.5.mday}&id_action={$week.5.org_actions[e].id} class=calendar_url>{$week.5.org_actions[e].full_name}</A><BR>{/section}');return false">
										{$week.5.count_org_actions}
									</A>
								</TD>
							</TR>
							{else}
							<tr>
								<td><br></td>
							</tr>
							{/if}
							</TABLE>
						{/if}
					</TD>
					<TD align="center" valign="top" {if $week.6 == "false"}class="calendar_top_cell_empty"{else}{if $week.6.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.6 == "false"}
							&nbsp;
						{else}
							<TABLE cellpadding="0" cellspacing="0" width="100%">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.6.mday}</B>
								</TD>
							</TR>
							{if $week.6.count_org_actions}
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="organizer.php?sel=date&year={$week.6.year}&month={$week.6.mon}&day={$week.6.mday}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.6.org_actions name=e}{$smarty.section.e.iteration}. <A href=organizer.php?sel=date&year={$week.6.year}&month={$week.6.mon}&day={$week.6.mday}&id_action={$week.6.org_actions[e].id} class=calendar_url>{$week.6.org_actions[e].full_name}</A><BR>{/section}');return false">
										{$week.6.count_org_actions}
									</A>
								</TD>
							</TR>
							{else}
							<tr>
								<td><br></td>
							</tr>
							{/if}
							</TABLE>
						{/if}
					</TD>
					<TD align="center" valign="top" {if $week.0 == "false"}class="calendar_right_cell_empty"{else}{if $week.0.current_day == "true"}class="calendar_right_cell_today"{else}class="calendar_right_cell"{/if}{/if}>
						{if $week.0 == "false"}
							&nbsp;
						{else}
							<TABLE cellpadding="0" cellspacing="0" width="100%">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.0.mday}</B>
								</TD>
							</TR>
							{if $week.0.count_org_actions}
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="organizer.php?sel=date&year={$week.0.year}&month={$week.0.mon}&day={$week.0.mday}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.0.org_actions name=e}{$smarty.section.e.iteration}. <A href=organizer.php?sel=date&year={$week.0.year}&month={$week.0.mon}&day={$week.0.mday}&id_action={$week.0.org_actions[e].id} class=calendar_url>{$week.0.org_actions[e].full_name}</A><BR>{/section}');return false">
										{$week.0.count_org_actions}
									</A>
								</TD>
							</TR>
							{else}
							<tr>
								<td><br></td>
							</tr>
							{/if}
							</TABLE>
						{/if}
					</TD>
				</TR><!-- /Calendar body -->
				{/foreach}
				</TABLE>
				</div>
{/strip}