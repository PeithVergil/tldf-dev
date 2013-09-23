				<div id="tooltip" onclick="hideToolTip()" style="background-color: #FFFFFF; border: 1px dotted; width:150px; text-align: left; position:absolute; display:none; padding:5px;"></div>
				
				<TABLE width="100%" cellpadding="0" cellspacing="0" border="0">
				<TR><!-- Calendar menu -->
					<TD colspan="7" align="center" valign="middle" style="padding: 0px 5px 5px 5px;">
					<A onclick="javascript: LoadCalendar(document.getElementById('calendar'), '&show_period=month&month={$selected_month.mon}&year={$selected_month.year}&move=back_month&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}');" href="#">&laquo;</A>
					&nbsp;&nbsp;&nbsp;<font class="text_head">{$selected_month.month},&nbsp;{$selected_month.year}</font>&nbsp;&nbsp;&nbsp;
					<A onclick="javascript: LoadCalendar(document.getElementById('calendar'), '&show_period=month&month={$selected_month.mon}&year={$selected_month.year}&move=next_month&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}');" href="#">&raquo;</A>
					</TD>
				</TR><!-- /Calendar menu -->
				<TR><!-- Calendar header -->
					<TD width="14%" align="center" class="calendar_header_top_cell">
						{$st.short.monday}
					</TD>
					<TD width="14%" align="center" class="calendar_header_top_cell">
						{$st.short.tuesday}
					</TD>
					<TD width="14%" align="center" class="calendar_header_top_cell">
						{$st.short.wednesday}
					</TD>
					<TD width="14%" align="center" class="calendar_header_top_cell">
						{$st.short.thursday}
					</TD>
					<TD width="14%" align="center" class="calendar_header_top_cell">
						{$st.short.friday}
					</TD>
					<TD width="14%" align="center" class="calendar_header_top_cell">
						{$st.short.saturday}
					</TD>
					<TD width="14%" align="center" class="calendar_header_right_cell">
						{$st.short.sunday}
					</TD>
				</TR><!-- /Calendar header -->

				{foreach from=$current_month item=week}
				<TR><!-- Calendar body -->
					<TD width="14%" align="center" valign="top" {if $week.1 == "false"}class="calendar_top_cell_empty"{else}{if $week.1.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.1 == "false"}
							&nbsp;
						{else}
							<TABLE width="100%" cellpadding="0" cellspacing="0">
							<TR>
								<TD align="left" style="padding-left:5px;" class="calendar_day_number" valign="top">
									<B>{$week.1.mday}</B>
								</TD>
							</TR>
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="{$event_path}?sel=date&year={$week.1.year}&month={$week.1.mon}&day={$week.1.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.1.event name=e}{$smarty.section.e.iteration}. <A href={$event_path}?sel=date&year={$week.1.year}&month={$week.1.mon}&day={$week.1.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}#{$week.1.event[e].id_event} class=calendar_url>{$week.1.event[e].full_name}</A><BR>{/section}');return false">
										{if $week.1.event_count}{$week.1.event_count} {$header.events}{/if}
									</A><BR>
								</TD>
							</TR>
							</TABLE>
						{/if}
					</TD>
					<TD width="14%" align="center" valign="top" {if $week.2 == "false"}class="calendar_top_cell_empty"{else}{if $week.2.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.2 == "false"}
							&nbsp;
						{else}
							<TABLE width="100%" cellpadding="0" cellspacing="0">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.2.mday}</B>
								</TD>
							</TR>
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="{$event_path}?sel=date&year={$week.2.year}&month={$week.2.mon}&day={$week.2.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.2.event name=e}{$smarty.section.e.iteration}. <A href={$event_path}?sel=date&year={$week.2.year}&month={$week.2.mon}&day={$week.2.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}#{$week.2.event[e].id_event} class=calendar_url>{$week.2.event[e].full_name}</A><BR>{/section}');return false">
										{if $week.2.event_count}{$week.2.event_count} {$header.events}{/if}
									</A><BR>
								</TD>
							</TR>
							</TABLE>
						{/if}
					</TD>
					<TD width="14%" align="center" valign="top" {if $week.3 == "false"}class="calendar_top_cell_empty"{else}{if $week.3.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.3 == "false"}
							&nbsp;
						{else}
							<TABLE width="100%" cellpadding="0" cellspacing="0">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.3.mday}</B>
								</TD>
							</TR>
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="{$event_path}?sel=date&year={$week.3.year}&month={$week.3.mon}&day={$week.3.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.3.event name=e}{$smarty.section.e.iteration}. <A href={$event_path}?sel=date&year={$week.3.year}&month={$week.3.mon}&day={$week.3.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}#{$week.3.event[e].id_event} class=calendar_url>{$week.3.event[e].full_name}</A><BR>{/section}');return false">
										{if $week.3.event_count}{$week.3.event_count} {$header.events}{/if}
									</A><BR>
								</TD>
							</TR>
							</TABLE>
						{/if}
					</TD>
					<TD width="14%" align="center" valign="top" {if $week.4 == "false"}class="calendar_top_cell_empty"{else}{if $week.4.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.4 == "false"}
							&nbsp;
						{else}
							<TABLE width="100%" cellpadding="0" cellspacing="0">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.4.mday}</B>
								</TD>
							</TR>
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="{$event_path}?sel=date&year={$week.4.year}&month={$week.4.mon}&day={$week.4.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.4.event name=e}{$smarty.section.e.iteration}. <A href={$event_path}?sel=date&year={$week.4.year}&month={$week.4.mon}&day={$week.4.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}#{$week.4.event[e].id_event} class=calendar_url>{$week.4.event[e].full_name}</A><BR>{/section}');return false">
										{if $week.4.event_count}{$week.4.event_count} {$header.events}{/if}
									</A><BR>
								</TD>
							</TR>
							</TABLE>
						{/if}
					</TD>
					<TD width="14%" align="center" valign="top" {if $week.5 == "false"}class="calendar_top_cell_empty"{else}{if $week.5.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.5 == "false"}
							&nbsp;
						{else}
							<TABLE width="100%" cellpadding="0" cellspacing="0">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.5.mday}</B>
								</TD>
							</TR>
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="{$event_path}?sel=date&year={$week.5.year}&month={$week.5.mon}&day={$week.5.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.5.event name=e}{$smarty.section.e.iteration}. <A href={$event_path}?sel=date&year={$week.5.year}&month={$week.5.mon}&day={$week.5.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}#{$week.5.event[e].id_event} class=calendar_url>{$week.5.event[e].full_name}</A><BR>{/section}');return false">
										{if $week.5.event_count}{$week.5.event_count} {$header.events}{/if}
									</A><BR>
								</TD>
							</TR>
							</TABLE>
						{/if}
					</TD>
					<TD width="14%" align="center" valign="top" {if $week.6 == "false"}class="calendar_top_cell_empty"{else}{if $week.6.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.6 == "false"}
							&nbsp;
						{else}
							<TABLE width="100%" cellpadding="0" cellspacing="0">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.6.mday}</B>
								</TD>
							</TR>
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="{$event_path}?sel=date&year={$week.6.year}&month={$week.6.mon}&day={$week.6.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.6.event name=e}{$smarty.section.e.iteration}. <A href={$event_path}?sel=date&year={$week.6.year}&month={$week.6.mon}&day={$week.6.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}#{$week.6.event[e].id_event} class=calendar_url>{$week.6.event[e].full_name}</A><BR>{/section}');return false">
										{if $week.6.event_count}{$week.6.event_count} {$header.events}{/if}
									</A><BR>
								</TD>
							</TR>
							</TABLE>
						{/if}
					</TD>
					<TD width="14%" align="center" valign="top" {if $week.0 == "false"}class="calendar_right_cell_empty"{else}{if $week.0.current_day == "true"}class="calendar_right_cell_today"{else}class="calendar_right_cell"{/if}{/if}>
						{if $week.0 == "false"}
							&nbsp;
						{else}
							<TABLE width="100%" cellpadding="0" cellspacing="0">
							<TR>
								<TD style="padding-left:5px;" align="left" class="calendar_day_number" valign="top">
									<B>{$week.0.mday}</B>
								</TD>
							</TR>
							<TR>
								<TD align="left" valign="top" style="padding-left:5px; padding-bottom:5px">
									<A href="{$event_path}?sel=date&year={$week.0.year}&month={$week.0.mon}&day={$week.0.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}" class="calendar_url" onmouseover="javascript: showToolTip(event,'{section loop=$week.0.event name=e}{$smarty.section.e.iteration}. <A href={$event_path}?sel=date&year={$week.0.year}&month={$week.0.mon}&day={$week.0.mday}&id_country={$id_country}&id_region={$id_region}&id_city={$id_city}&type={$type}#{$week.0.event[e].id_event} class=calendar_url>{$week.0.event[e].full_name}</A><BR>{/section}');return false">
										{if $week.0.event_count}{$week.0.event_count} {$header.events}{/if}
									</A><BR>
								</TD>
							</TR>
							</TABLE>
						{/if}
					</TD>
				</TR><!-- /Calendar body -->
				{/foreach}
				<TR><TD colspan="7" style="font-size: 9px;">&nbsp;</TD></TR>
				</TABLE>