{include file="$admingentemplates/admin_top.tpl" script="qforms"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_users} {$form.groupname}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.groups_users}</div>
{if !$form.err}
<font class=error_msg>{$header.remember}</font><br><br>
{/if}
				<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
				<tr class="table_header">
					<td class="main_header_text" align="center"><b>&nbsp;</b></td>
					<td class="main_header_text" align="center" width="300"><b>{$header.all_users}</b></td>
					<td class="main_header_text" align="center" width="50"><b>&nbsp;</b></td>
					<td class="main_header_text" align="center" width="300"><b>{$header.users_in_list}</b></td>
					<td class="main_header_text" align="center"><b>&nbsp;</b></td>
				</tr>
				<tr bgcolor="#ffffff">
					<td class="main_header_text" align="center"><b>&nbsp;</b></td>
					<td  class="main_header_text" align="center">
						<table><tr>
						<form name="search_form" action="{$form.action}" method="post">
						{$search_hiddens}
						<td class="main_header_text" ><input type="text" style="width:100; height:20" name="search" value="{$search}" {if $root}disabled{/if}></td>
						<td class="main_content_text" ><select name="s_type" style="width:70; height:40">
						{section name=s loop=$types}
						<option value="{$smarty.section.s.index_next}" {if $types[s].sel}selected{/if}>{$types[s].value}</option>
						{/section}
						</select></td>
						<td>{if !$root}<input type="button" value="{$button.search}" class="button" onclick="javascript:users_search_click();document.search_form.submit();">{/if}</td>
						</form>
					</tr></table>
					</td>
					<td class="main_header_text" align="center"><b>&nbsp;</b></td>
					<td class="main_header_text" align="center"><b>&nbsp;</b></td>
					<td class="main_header_text" align="center"><b>&nbsp;</b></td>
				</tr>
				<form name="addUsers" action="{$form.action}" method="post"enctype="multipart/form-data">
 				{$form.hiddens}
                 <tr bgcolor="#ffffff">
					<td class="main_content_text" align="center"><b>&nbsp;</b></td>
                    <td align="center" class="main_content_text"><br>
						<select name="AllUsers"  size="20" multiple style="width: 250px;" onDblClick="objForm.AllUsers.transferTo('IncUsers');"  {if $root}disabled{/if}>
						{section name=u loop=$allusers_arr}
						<option value="{$allusers_arr[u].value}" {if $allusers_arr[u].sel}selected{/if}>{$allusers_arr[u].name}</option>
						{/section}
						</select><br><br>
					</td>
                    
					<td align="center" class="main_header_text"> 
					{if !$root}
					<input type="button" style="width:40px" value="&gt;&gt;" class="button" onClick="objForm.AllUsers.transferTo('IncUsers', true, 'all');"><br><br>
					<input type="button" style="width:40px" value="&gt;" class="button" onClick="objForm.AllUsers.transferTo('IncUsers');"><br><br><br>
					<input type="button" style="width:40px" value="&lt;" class="button" onClick="objForm.AllUsers.transferFrom('IncUsers');"><br><br>
					<input type="button" style="width:40px" value="&lt;&lt;" class="button" onClick="objForm.AllUsers.transferFrom('IncUsers', true, 'all');"><br><br>
					{/if}
					</td>
                    
					<td align="center" class="main_content_text"><br>
						<select name="IncUsers" size="20" multiple style="width: 250px;" onDblClick="objForm.AllUsers.transferFrom('IncUsers');"  {if $root}disabled{/if}>
						{section name=u loop=$gusers_arr}
						<option value="{$gusers_arr[u].value}" {if $gusers_arr[u].sel}selected{/if}>{$gusers_arr[u].name}</option>
						{/section}
						</select><br><br>
					</td>
					<td class="main_content_text" align="center"><b>&nbsp;</b></td>
                  </tr>
				<!-- user hidden list  -->
				{section name=u loop=$goldusers_arr}
				<input type=hidden value="{$goldusers_arr[u].value}" name="prevusers[{$smarty.section.u.index}]">
				{/section}
            </table>
			<table><tr height="40">
			{if !$root}
			<td>	<input type=submit value="{$button.save}"  class="button"  {if $root}disabled{/if}></td>
			{/if}
			<td><input type=button value="{$button.back}" onclick="javascript: location.href='{$form.back}'" class="button"></td>
			</tr></table>
                </form>
				{literal}
 				<SCRIPT LANGUAGE="JavaScript">
				<!--//
				// initialize the qForm object
				objForm = new qForm("addUsers");
				// make the User field a container, this will ensure that the "reset()"
				// method will restore the values in the select box, even if they've
				// been removed from the select box
				objForm.AllUsers.makeContainer();
				// setting the "dummyContainer" property to false will ensure that no values
				// from this container are included with the value
				objForm.AllUsers.dummyContainer = true;

				// make the "Members" field a container--every item in the "Members" select box
				// will be part of the container, even if the item isn't selected.
				objForm.IncUsers.makeContainer();
				//-->
			
				function users_search_click() {
				 var sel = document.addUsers.IncUsers;
				 var i=0;
				 var res ='';
				 var first = 0;
				 while (i<sel.options.length) {
					res = res + sel.options(i).value+', ';
				   ++i;
				 }
				 document.search_form.IncSUsers.value=res;
				}
					
				</script>
				{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}