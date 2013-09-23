{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;
{if $sel eq "terminated_guy"}{$header.list_terminated_guy}{/if}
{if $sel eq "terminated_lady"}{$header.list_terminated_lady}{/if}
</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.users_terminated}</div>
<table width="100%">
	<tr>
        <td height="30"  align="left" bgcolor="#FFFFFF" class="main_content_text" > {$letter_links} </td>
        <td height="30"  align="right" bgcolor="#FFFFFF" class="main_content_text" >
            <form name="search_form" action="{$form.action}" method="post" style="margin:0px">
                {$form.hiddens}
                <table>
                    <tr>
                        <td class="main_content_text" >
                            <input type="text" name="search" value="{$search}" style="width:150px">
                        </td>
                        <td class="main_content_text" >
                            <select name="s_type">
								{section name=s loop=$types}
									<option value="{$smarty.section.s.index_next}" {if $types[s].sel}selected{/if}>{$types[s].value}</option>
								{/section}
							</select>
                        </td>
                        <td class="main_content_text">
                            <input type="button" value="{$button.search}" class="button" onclick="javascript: document.search_form.submit();" name="search_submit">
                        </td>
                    </tr>
                </table>
            </form>
            {if $sel eq ""}
			<form name="groups_form" action="{$form.action}" method="post" style="margin:0px">
                {$form.hiddens}
                <table>
                    <tr>
                        <td class="main_content_text" >
                            <select name="group">
                                <option value="">{$header.groups}</option>
								{section name=s loop=$groups}
									<option value="{$groups[s].id}" {if $groups[s].id == $group}selected{/if}>{$groups[s].name} ({$groups[s].count})</option>
								{/section}
                            </select>
                        </td>
                        <td class="main_content_text">
                            <input type="button" value="{$button.select}" class="button" onclick="javascript: document.groups_form.submit();" name="group_submit">
                        </td>
                    </tr>
                </table>
            </form>
			{/if}
        </td>
    </tr>
</table>
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
    {if $links}
    <tr bgcolor="#ffffff">
        <td height="20"  colspan="10" align="left"  class="main_content_text" >{$links}</td>
	</tr>
    {/if}
    <tr class="table_header">
        <td class="main_header_text" align="center" width="10">{$header.number}</td>
        <td class="main_header_link" align="center" width="160"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=1&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 1}{$form.new_order}{else}{$form.order}{/if}'">{$header.name}</span>&nbsp;{if $sorter eq 1}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center" width="150"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=2&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 2}{$form.new_order}{else}{$form.order}{/if}'">{$header.user_group}</span>&nbsp;{if $sorter eq 2}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center" width="30" ><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=3&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 3}{$form.new_order}{else}{$form.order}{/if}'">{$header.age}</span>&nbsp;{if $sorter eq 3}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center" width=""   ><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=4&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 4}{$form.new_order}{else}{$form.order}{/if}'">{$header.email}</span>&nbsp;{if $sorter eq 4}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center" width="90" ><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=5&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 5}{$form.new_order}{else}{$form.order}{/if}'">{$header.phone}</span>&nbsp;{if $sorter eq 5}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center" width="90" ><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=6&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 6}{$form.new_order}{else}{$form.order}{/if}'">{$header.mm_contact_mobile_number}</span>&nbsp;{if $sorter eq 6}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_link" align="center" width="80" ><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=7&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 7}{$form.new_order}{else}{$form.order}{/if}'">{$header.date_registration}</span>&nbsp;{if $sorter eq 7}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
        <td class="main_header_link" align="center" width="80" ><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=8&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 8}{$form.new_order}{else}{$form.order}{/if}'">{$header.date_termination}</span>&nbsp;{if $sorter eq 8}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
        <td class="main_header_link" align="center" width="40" ><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=9&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 9}{$form.new_order}{else}{$form.order}{/if}'">{$header.login_count}</span>&nbsp;{if $sorter eq 9}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
    </tr>
    {if $user}
		{section name=u loop=$user}
        <tr>
            <td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].number}</td>
            <td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].name}</td>
            <td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].group_name}</td>
			<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].age}</td>
			<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].email}</td>
			<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].phone_number}</td>
			<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].mobile_number}</td>
            <td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].date_rigistration}</td>
            <td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].date_termination}</td>
            <td class="main_content_text" align="center" bgcolor="#FFFFFF">{if !$user[u].root_user}{$user[u].login_count}{/if}</td>
         </tr>
        {/section}
    {else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="10" bgcolor="#FFFFFF">{$header.empty}</td>
		</tr>
    {/if}
    {if $links}
    <tr bgcolor="#ffffff">
        <td height="20" colspan="10" align="left"  class="main_content_text" >{$links}</td>
	</tr>
    {/if}
</table>
{include file="$admingentemplates/admin_bottom.tpl"}