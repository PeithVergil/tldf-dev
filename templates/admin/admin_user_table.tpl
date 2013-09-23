{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;
{if $sel eq ""}{$header.list}{/if}
{if $sel eq "signup_guy"}{$header.list_signup_guy}{/if}
{if $sel eq "signup_lady"}{$header.list_signup_lady}{/if}
{if $sel eq "pen_trial_guy"}{$header.list_pen_trial_guy}{/if}
{if $sel eq "pen_trial_lady"}{$header.list_pen_trial_lady}{/if}
{if $sel eq "trial_guy"}{$header.list_trial_guy}{/if}
{if $sel eq "trial_lady"}{$header.list_trial_lady}{/if}
{if $sel eq "pen_reg_guy"}{$header.list_pen_reg_guy}{/if}
{if $sel eq "pen_reg_lady"}{$header.list_pen_reg_lady}{/if}
{if $sel eq "reg_guy"}{$header.list_reg_guy}{/if}
{if $sel eq "reg_lady"}{$header.list_reg_lady}{/if}
{if $sel eq "pen_plat_guy"}{$header.list_pen_plat_guy}{/if}
{if $sel eq "pen_plat_lady"}{$header.list_pen_plat_lady}{/if}
{if $sel eq "plat_guy"}{$header.list_plat_guy}{/if}
{if $sel eq "plat_lady"}{$header.list_plat_lady}{/if}
{if $sel eq "elite_guy"}{$header.list_elite_guy}{/if}
{if $sel eq "inact_trial_guy"}{$header.list_inact_trial_guy}{/if}
{if $sel eq "inact_trial_lady"}{$header.list_inact_trial_lady}{/if}
{if $sel eq "inact_reg_guy"}{$header.list_inact_reg_guy}{/if}
{if $sel eq "inact_reg_lady"}{$header.list_inact_reg_lady}{/if}
{if $sel eq "inact_plat_guy"}{$header.list_inact_plat_guy}{/if}
{if $sel eq "inact_plat_lady"}{$header.list_inact_plat_lady}{/if}
{if $sel eq "inact_elite_guy"}{$header.list_inact_elite_guy}{/if}
</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.users_list}</div>
<table width="100%">
	<tr>
		<td height="30" align="left" bgcolor="#FFFFFF" class="main_content_text">
			{$letter_links}
		</td>
		<td height="30" align="right" bgcolor="#FFFFFF" class="main_content_text">
			<form name="search_form" action="{$form.action}" method="get" style="margin:0px">
				{$form.hiddens}
				<table>
					<tr>
						<td class="main_content_text">
							<input type="text" name="search" value="{$search}" style="width:150px">
						</td>
						<td class="main_content_text">
							<select name="s_type">
								{section name=s loop=$types}
									<option value="{$smarty.section.s.index_next}" {if $types[s].sel}selected{/if}>{$types[s].value}</option>
								{/section}
							</select>
						</td>
						<td class="main_content_text">
							<input type="button" value="{$button.search}" class="button" onclick="document.search_form.submit();" name="search_submit">
						</td>
					</tr>
				</table>
			</form>
			{if $sel == ""}
				<form name="groups_form" action="{$form.action}" method="get" style="margin:0px">
					{$form.hiddens}
					<table>
						<tr>
							<td class="main_content_text">
								<select name="group">
									<option value="">{$header.groups}</option>
									{section name=s loop=$groups}
										<option value="{$groups[s].id}" {if $groups[s].id == $group}selected{/if}>{$groups[s].name} ({$groups[s].count})</option>
									{/section}
								</select>
							</td>
							<td class="main_content_text">
								<input type="button" value="{$button.select}" class="button" onclick="document.groups_form.submit();" name="group_submit">
							</td>
						</tr>
					</table>
				</form>
			{/if}
		</td>
	</tr>
</table>
{*
<table>
	<tr valign="top" height=30>
		<td>
			<input type="button" value="{$header.add}" class="button" onclick="location.href='{$add_link}'">
		</td>
		<td>
			<input type="button" value="{$header.topten}" class="button" onclick="document.location.href='admin_users.php?sel=top'">
		</td>
	</tr>
</table>
*}
<table border="0" class="table_main" cellspacing="1" cellpadding="5" width="100%">
	{if $links}
		<tr bgcolor="#ffffff">
			<td height="20" colspan={if !$invite_users}8{else}10{/if} align="left" class="main_content_text">
				{$links}
			</td>
			{if !$invite_users}
				<td align="center">
					{if $user}
						<input type="button" value="{$header.refresh}" class="button" onclick="document.fusers.sel.value='active'; document.fusers.submit();">
					{/if}
				</td>
				<td class="main_content_text" align="left">&nbsp;</td>
				<td align="center">
					{if $user}
						<input type="button" value="{$header.delete}" class="button" {literal}onclick="if (window.confirm('{/literal}{$lang.users.del_confirm}{literal}')) { document.fusers.sel.value='delete'; document.fusers.submit(); } else return false;"{/literal}>
					{/if}
				</td>
				<td class="main_content_text" align="left">&nbsp;</td>
			{/if}
		</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="10">{$header.number}</td>
		<td class="main_header_link" align="center"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=1&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 1}{$form.new_order}{else}{$form.order}{/if}'">{$header.nick}</span>&nbsp;{if $sorter eq 1}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=2&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 2}{$form.new_order}{else}{$form.order}{/if}'">{$header.fname}</span>&nbsp;{if $sorter eq 2}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=3&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 3}{$form.new_order}{else}{$form.order}{/if}'">{$header.gender}</span>&nbsp;{if $sorter eq 3}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=4&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 4}{$form.new_order}{else}{$form.order}{/if}'">{$header.age}</span>&nbsp;{if $sorter eq 4}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center" width="80"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=5&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 5}{$form.new_order}{else}{$form.order}{/if}'">{$header.date_registration}</span>&nbsp;{if $sorter eq 5}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center" width="80"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=6&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 6}{$form.new_order}{else}{$form.order}{/if}'">{$header.date_last_login}</span>&nbsp;{if $sorter eq 6}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center" width="50"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=8&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 8}{$form.new_order}{else}{$form.order}{/if}'">{$header.login_count}</span>&nbsp;{if $sorter eq 8}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		{if !$invite_users}
			<td class="main_header_link" align="center" width="80"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=7&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 7}{$form.new_order}{else}{$form.order}{/if}'">{$header.status}</span>&nbsp;{if $sorter eq 7}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		{/if}
		<td class="main_header_text" align="center" width="120">&nbsp;</td>
		{if !$invite_users}
			<td class="main_header_text" align="center" width="80">&nbsp;</td>
			<td class="main_header_text" align="center" width="100">&nbsp;</td>
		{else}
			<td class="main_header_text" align="center" width="80">&nbsp;</td>
		{/if}
	</tr>
	{if $user}
		<form action="{$form.action}" name="fusers" method="post">
			{$form.hiddens}
			{section name=u loop=$user}
				<tr>
					<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].number}</td>
					<td class="main_content_text" align="center" bgcolor="#FFFFFF">{if $user[u].root_user}{$user[u].nick}{else}<a href="{$user[u].edit_link}">{$user[u].nick}</a>{/if}</td>
					<td class="main_content_text" align="center" bgcolor="#FFFFFF">{if $user[u].root_user}{$user[u].name}{else}<a href="{$user[u].edit_link}">{$user[u].name}</a>{/if}</td>
					<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].gender}</td>
					<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].age}</td>
					<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].date_rigistration}</td>
					<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].last_login}</td>
					<td class="main_content_text" align="center" bgcolor="#FFFFFF">{if !$user[u].root_user}{$user[u].login_count}{/if}</td>
					{if !$invite_users}
						<td class="main_content_text" align="center" bgcolor="#FFFFFF">
							<!--{$user[u].id_group}-->
							<input type="checkbox" name="active[{$user[u].number}]" value="1" onchange="alertMessage(this);" {if $user[u].status}checked{/if} {if $user[u].root_user || !$user[u].use_active}disabled{/if}>
							<input type="hidden" name="hactive[{$user[u].number}]" value="{$user[u].id}">
						</td>
					{/if}
					<td class="main_content_text" align="left" bgcolor="#FFFFFF"> {if !$user[u].root_user}
						&nbsp;&nbsp;[<a href="#" onclick="window.open('{$user[u].descr_link}','description', 'height=750, resizable=yes, scrollbars=yes,width=750, menubar=no,status=no'); return false;">{$header.descr_link}</a>]<br>
						&nbsp;&nbsp;[<a href="#" onclick="window.open('{$user[u].personal_link}','personal', 'height=750, resizable=yes, scrollbars=yes,width=750, menubar=no,status=no'); return false;">{$header.personal_link}</a>]<br>
						&nbsp;&nbsp;[<a href="#" onclick="window.open('{$user[u].upload_link}','upload', 'height=750, resizable=yes, scrollbars=yes,width=750, menubar=no,status=no'); return false;">{$header.upload_link}</a>]<br>
						&nbsp;&nbsp;[<a href="#" onclick="window.open('{$user[u].perfect_link}','perfect_match', 'height=750, resizable=yes, scrollbars=yes,width=750, menubar=no,status=no'); return false;">{$header.perfect_link}</a>]<br>
						{if $user[u].invited_link}&nbsp;&nbsp;[<a href="{$user[u].invited_link}">{$user[u].invited_link_name}</a>]<br>
						{/if}{/if}
					</td>
					{if !$invite_users}
						<td class="main_content_text" align="center" bgcolor="#FFFFFF">
							<input type="checkbox" name="delete[{$user[u].number}]" value="{$user[u].id}" {if $user[u].root_user}disabled{/if}>
						</td>
						<td class="main_content_text" align="center" bgcolor="#FFFFFF" width="100">
							<table cellspacing=1 cellpadding=0>
								<tr>
									<td width=80>
										<input type="button" value="{$header.comunicate}" class="button" onclick="window.open('{$user[u].comunicate}','comunicate', 'height=800, resizable=yes, scrollbars=yes,width=600, menubar=no,status=no');">
									</td>
								</tr>
							</table>
						</td>
					{else}
						<td class="main_content_text" align="center" bgcolor="#FFFFFF" width="100">
							{if $user[u].invite_link}
								<input type="button" value="{$lang.club.admin.invite_butt}" onclick="location.href='{$user[u].invite_link}'" />
							{/if}
						</td>
					{/if}
				</tr>
			{/section}
		</form>
	{else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="12" bgcolor="#FFFFFF">{$header.empty}</td>
		</tr>
	{/if}
	{if $links}
		<tr bgcolor="#ffffff">
			<td height="20" colspan={if !$invite_users}8{else}10{/if} align="left" class="main_content_text">
				{$links}
			</td>
			{if !$invite_users}
				<td align="center">
					{if $user}
						<input type="button" value="{$header.refresh}" class="button" onclick="document.fusers.sel.value='active'; document.fusers.submit();">
					{/if}
				</td>
				<td class="main_content_text" align="left">&nbsp;</td>
				<td align="center">
					{if $user}
						<input type="button" value="{$header.delete}" class="button" {literal}onclick="if (window.confirm('{/literal}{$lang.users.del_confirm}{literal}')) { document.fusers.sel.value='delete'; document.fusers.submit(); } else return false;"{/literal}>
					{/if}
				</td>
				<td class="main_content_text" align="left">&nbsp;</td>
			{/if}
		</tr>
	{/if}
</table>
{*
<table>
	<tr height="40">
		<td>
			<input type="button" value="{$header.add}" class="button" onclick="location.href='{$add_link}'">
		</td>
		<td>
			<input type="button" value="{$header.topten}" class="button" onclick="document.location.href='admin_users.php?sel=top'">
		</td>
	</tr>
</table>
*}
<script type="text/javascript">
{literal}
function alertMessage(pObj) {
	if (!pObj.checked) {
		{/literal}
		var alertMsg = '{$alerts.user_disable}';
		{literal}
		var lobjConfirm = confirm(alertMsg);
		if (lobjConfirm != true) {
			pObj.checked = true;
		}
	}
}
{/literal}
</script>
{include file="$admingentemplates/admin_bottom.tpl"}