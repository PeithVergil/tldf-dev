{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list_subscriber} : {$form.subscribe_name}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.newsletter_subscribers}</div>
<table width="100%">
	<tr valign=top>
	<!-- user_select -->
	<td width="15%" height="100%">
            <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
			<tr bgcolor="#ffffff"><td class="main_header_text" >{$header.editform_useradd}</td></tr>
			<tr bgcolor="#ffffff"><td class="main_header_text" align="center">
			<table><tr>
			<form name="search_form" action="{$form.action}" method="post">
					{$form.search_hiddens}
					<td class="main_header_text" ><input type="text" style="width:100; height:20" name="search" value="{$search}" ></td>
					<td class="main_content_text" ><select name="s_type" style="width:70; height:40">
					{section name=s loop=$types}
					<option value="{$smarty.section.s.index_next}" {if $types[s].sel}selected{/if}>{$types[s].value}</option>
					{/section}
					</select></td>
					<td><input type="button" value="{$button.search}" class="button" onclick="javascript: document.search_form.submit();" name="search_submit"></td>
			</form>
			</tr></table>
			</td>
		</tr>
		<form name="add_form" action="{$form.action}" method="post">
		{$form.add_hiddens}
		<tr bgcolor="#ffffff">
            <td align="center"><br>
					<select name="userslist"  size="20" multiple style="width: 250px;"  >
					{section name=u loop=$users_arr}
					<option value="{$users_arr[u].value}">{$users_arr[u].name}</option>
					{/section}
					</select><br><br>
			</td>
		</tr>
		</table>
		<table><tr height="40">
			<td><input type="button" value="{$header.add_subscriber}" class="button" onclick="javascript:users_add_click(); document.add_form.submit();"></td>
		</tr></table>
		</form>
	</td>
	<!-- /user select -->
	<!-- main user row -->
	<td width="85%" height="100%">
            <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
							{if $links}
							<tr bgcolor="#ffffff">
								<td height="20"  colspan=4 align="left"  class="main_content_text" >{$links}</td>
							</tr>
							{/if}
							<tr class="table_header">
								<td class="main_header_text" align="center" width="20">{$header.number}</td>
								<td class="main_header_text" align="center">{$header.name_subscriber}</td>
								<td class="main_header_text" align="center">{$header.email_subscriber}</td>
								<td class="main_header_text" align="center" width="100">&nbsp;</td>
							</tr>
							{if $subscribers}
							{section name=spr loop=$subscribers}
							<tr bgcolor="#ffffff">
								<td class="main_content_text" align="center">{$subscribers[spr].number}</td>
								<td class="main_content_text" align="center">{$subscribers[spr].name} ({$subscribers[spr].login})</td>
								<td class="main_content_text" align="center">{$subscribers[spr].email}</td>
								<td class="main_content_text" align="center"><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$subscribers[spr].deletelink}'{literal}}{/literal}"></td>
							</tr>
							{/section}
							{if $links}
							<tr bgcolor="#ffffff">
								<td height="20"  colspan=4 align="left"  class="main_content_text" >{$links}</td>
							</tr>
							{/if}
							{else}
							<tr bgcolor="#ffffff" height="40">
								<td class="main_error_text" align="left" colspan="4" bgcolor="#FFFFFF">{$header.empty_users}</td>
							</tr>
							{/if}
					</table>
			<table><tr height="40">
			<td><input type="button" value="{$header.back_to_subscribe}" class="button" onclick="javascript: location.href='{$form.back}'"></td>
			</tr></table>
<td></tr>
</table>
{literal}
<script>
function users_add_click() {
	 var sel = document.add_form.userslist;
	 var i=0;
	 var res ='';
	 var first = 0;
	 while (i<sel.options.length) {
		if(sel.options[i].selected==true)
		res = res + sel.options[i].value+', ';
	   ++i;
	 }
	 res = res.substring(0, res.length-2)
	 document.add_form.users_str.value=res;
}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}