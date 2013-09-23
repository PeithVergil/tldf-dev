{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$section.blog_calendar}</div></div>
<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top" class="text">
			{include file="$gentemplates/blog_menu.tpl"}
			<div class="content_2" style=" margin: 0px;">
	<TABLE width="100%" cellpadding="0" cellspacing="0" border="0">
		<TR><!-- Calendar menu -->
			<TD colspan="7" align="center" valign="middle" style="padding: 10px 10px 10px 10px;">
			<A href="{$page_path}?show_period=month&month={$selected_month.mon}&year={$selected_month.year}&move=back_month">&laquo;</A>
			&nbsp;&nbsp;&nbsp;<font class="text_head">{$selected_month.month},&nbsp;{$selected_month.year}</font>&nbsp;&nbsp;&nbsp;
			<A href="{$page_path}?show_period=month&month={$selected_month.mon}&year={$selected_month.year}&move=next_month">&raquo;</A>
			</TD>
		</TR><!-- /Calendar menu -->
		<TR><!-- Calendar header -->
			<TD width="14%" align="center" class="calendar_header_top_cell">
				{$st.monday}
			</TD>
			<TD width="14%" align="center" class="calendar_header_top_cell">
				{$st.tuesday}
			</TD>
			<TD width="14%" align="center" class="calendar_header_top_cell">
				{$st.wednesday}
			</TD>
			<TD width="14%" align="center" class="calendar_header_top_cell">
				{$st.thursday}
			</TD>
			<TD width="14%" align="center" class="calendar_header_top_cell">
				{$st.friday}
			</TD>
			<TD width="14%" align="center" class="calendar_header_top_cell">
				{$st.saturday}
			</TD>
			<TD width="14%" align="center" class="calendar_header_right_cell">
				{$st.sunday}
			</TD>
		</TR><!-- /Calendar header -->

		{foreach from=$current_month item=week}
				<TR><!-- Calendar body -->
					<TD width="14%" height="80px" align="center" valign="top" {if $week.1 == "false"}class="calendar_top_cell_empty"{else}{if $week.1.current_day == "true"}class="calendar_top_cell_today"{else}class="calendar_top_cell"{/if}{/if}>
						{if $week.1 == "false"}
							&nbsp;
						{else}
							<TABLE width="100%" cellpadding="0" cellspacing="0">
							<TR>
								<TD align="left" style="padding-left:5px;" class="calendar_day_number" valign="top">
									<B>{$week.1.mday}</B>
								</TD>
							</TR>
							{section loop=$week.1.blog name=b}
							<TR>
								<TD align="left" valign="top" style="padding-left:10px; padding-bottom:5px">
									<A href="{$week.1.blog[b].post_link}" class="calendar_url">
										{$week.1.blog[b].title}
									</A><BR>
								</TD>
							</TR>
							{/section}
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
							{section loop=$week.2.blog name=b}
							<TR>
								<TD align="left" valign="top" style="padding-left:10px; padding-bottom:5px">
									<A href="{$week.2.blog[b].post_link}" class="calendar_url">
										{$week.2.blog[b].title}
									</A><BR>
								</TD>
							</TR>
							{/section}
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
							{section loop=$week.3.blog name=b}
							<TR>
								<TD align="left" valign="top" style="padding-left:10px; padding-bottom:5px">
									<A href="{$week.3.blog[b].post_link}" class="calendar_url">
										{$week.3.blog[b].title}
									</A><BR>
								</TD>
							</TR>
							{/section}
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
							{section loop=$week.4.blog name=b}
							<TR>
								<TD align="left" valign="top" style="padding-left:10px; padding-bottom:5px">
									<A href="{$week.4.blog[b].post_link}" class="calendar_url">
										{$week.4.blog[b].title}
									</A><BR>
								</TD>
							</TR>
							{/section}
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
							{section loop=$week.5.blog name=b}
							<TR>
								<TD align="left" valign="top" style="padding-left:10px; padding-bottom:5px">
									<A href="{$week.5.blog[b].post_link}" class="calendar_url">
										{$week.5.blog[b].title}
									</A><BR>
								</TD>
							</TR>
							{/section}
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
							{section loop=$week.6.blog name=b}
							<TR>
								<TD align="left" valign="top" style="padding-left:10px; padding-bottom:5px">
									<A href="{$week.6.blog[b].post_link}" class="calendar_url">
										{$week.6.blog[b].title}
									</A><BR>
								</TD>
							</TR>
							{/section}
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
							{section loop=$week.0.blog name=b}
							<TR>
								<TD align="left" valign="top" style="padding-left:10px; padding-bottom:5px">
									<A href="{$week.0.blog[b].post_link}" class="calendar_url">
										{$week.0.blog[b].title}
									</A><BR>
								</TD>
							</TR>
							{/section}
							</TABLE>
						{/if}
					</TD>
				</TR><!-- /Calendar body -->
				{/foreach}
				<TR><TD colspan="7" style="font-size: 9px;">&nbsp;</TD></TR>
				</TABLE>
			</div>
		</td>
	</tr>
</table>
</td>
{include file="$gentemplates/index_bottom.tpl"}