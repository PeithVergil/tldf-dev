{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.promo_user}</font>
<!--<font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_perm}{$data.groupname}</font>-->
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.promo_users_list}</div>
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
    <tr bgcolor="#ffffff">
        <td height="20" colspan="5" class="main_content_text" >{$links}</td>
		<td align="center">
			<input type="button" value="{$button.close}" class="button" onclick="javascript: window.close();opener.focus();">
		</td>
		<td align="center" colspan="2">
			{if $user}
            	<input type="button" value="{$button.add_selected_users}" class="button" onclick="javascript: add_checked_users();">
            {/if}
		</td>
	</tr>
    <tr class="table_header">
        <td class="main_header_text" align="center" width="10">{$header.number}</td>
		<td class="main_header_text" align="center" width="40">&nbsp;</td>
        <td class="main_header_link" align="center"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=1&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 1}{$form.new_order}{else}{$form.order}{/if}'">{$header.nick}</span>&nbsp;{if $sorter eq 1}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
        <td class="main_header_link" align="center"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=2&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 2}{$form.new_order}{else}{$form.order}{/if}'">{$header.fname}</span>&nbsp;{if $sorter eq 2}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
        <td class="main_header_link" align="center"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=3&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 3}{$form.new_order}{else}{$form.order}{/if}'">{$header.gender}</span>&nbsp;{if $sorter eq 3}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
        <td class="main_header_link" align="center"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=4&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 4}{$form.new_order}{else}{$form.order}{/if}'">{$header.age}</span>&nbsp;{if $sorter eq 4}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
        <td class="main_header_link" align="center" width="50"><span style="cursor: pointer; text-decoration: underline;" onclick="document.location.href='{$form.action}?sel={$sel}&sorter=8&letter={$letter}&search={$search}&s_type={$s_type}&s_stat={$s_stat}&group={$group}&order={if $sorter eq 8}{$form.new_order}{else}{$form.order}{/if}'">{$header.login_count}</span>&nbsp;{if $sorter eq 8}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</td>
		<td class="main_header_text" align="center" width="100">&nbsp;</td>
	</tr>
    {if $user}
		<form action="{$form.action}" name="fusers" method="post">
			{$form.hiddens}
			{section name=u loop=$user}
			<tr>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].number}</td>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF"><a href="{$user[u].profile_link}" target="_blank"><img src="{$user[u].icon_path}" class="icon" alt="{$promo_user[f].name}" width="25"></a></td>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF"><a href="{$user[u].profile_link}" target="_blank">{$user[u].nick}</a></td>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].name}</td>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].gender}</td>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$user[u].age}</td>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF">{if !$user[u].root_user}{$user[u].login_count}{/if}</td>
				<td class="main_content_text" align="center" bgcolor="#FFFFFF">
					<input type="checkbox" name="user[{$user[u].number}]" value="{$user[u].id}" {if $user[u].root_user}disabled{/if}>
				</td>
			</tr>
			{/section}
		</form>
    {else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="9" bgcolor="#FFFFFF">{$header.empty}</td>
		</tr>
    {/if}
    <tr bgcolor="#ffffff">
        <td height="20" colspan="5" class="main_content_text" >{$links}</td>
		<td align="center">
			<input type="button" value="{$button.close}" class="button" onclick="javascript: window.close();opener.focus();">
		</td>
		<td align="center" colspan="2">
			{if $user}
            	<input type="button" value="{$button.add_selected_users}" class="button" onclick="javascript: add_checked_users();">
            {/if}
		</td>
	</tr>
</table>
{literal}
<script type="text/javascript">
//file_name = './{/literal}{$form.action}{literal}';

function add_checked_users()
{
	var file_name = './admin_promotions_mail.php';
	var usr_form = document.fusers;
	var str_userid = '';
	for(i=0;i<usr_form.length;i++)
	{
		if (usr_form[i].name.substr(0,4)== 'user')
		{
			if (usr_form[i].checked == true)
			{
				usr_id = usr_form[i].value;
				//alert(usr_id);
				if(str_userid != '')
				{
					str_userid = str_userid + ',';
				}
				str_userid = str_userid + usr_id;
			}
		}
	}
	
	if(str_userid != '')
	{
		if(window.opener.document.getElementById('pid'))
		{
			var mail_id = window.opener.document.getElementById('pid').value;
			//alert(mail_id);
		}
		else
		{
			var mail_id = 'new_mail';
		}
		//if(mail_id)
		{
			//alert(file_name);
			//alert(mail_id);
			str = 'sel=refresh_user&pid='+mail_id+'&userids='+str_userid;
			result_obj = window.opener.document.getElementById('div_promo_users');
			ajaxRequestAdminProUser(file_name, str, result_obj, '', 1);
			//str_promo_user_list = update_refresh_list(mail_id, str_userid);
			//result_obj.innerHTML = str_promo_user_list;
			//usr_form.submit();
			//window.close();
			//opener.focus();
		}
		/*
		else
		{
			alert('Error: Promo mail id missing.');	
		}
		*/
	}
	else
	{
		alert('Error: Please tick at least one user from list.');
	}
}

function update_refresh_list(p_id, str_users)
{
	//
	
	str_new_list = str_users;
	return str_new_list;
}

</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}