{include file="$admingentemplates/admin_top.tpl"}
<script language="javascript" type="text/javascript">
{literal}
function deleteRecord(pRecNo)
{
	//alert('ID = ' + pRecNo);
	var lobjConfirm = confirm("Are you sure to delete user from Featured list?");
	if (lobjConfirm == true)
    {
		//alert("You pressed OK!");
		window.location = "admin_featured_users.php?sel=del&id=" + pRecNo;
    }
	else
    {
		//alert("You pressed Cancel!");
    }
}
{/literal}
</script>
<font class=red_header>{$header.featured_users}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.featured_users}</div>
<div style="float:left; width:40%;">
	<div class="red_header" style="padding-bottom:10px;">Featured users list for GUYS</div>
	<table border="0" class="table_main" cellspacing="1" cellpadding="5" width="100%">
		<tr class="table_header">
			<td class="main_header_text" align="center" width="30">{$header.icon}</td>
			<td class="main_header_link" align="center">{$header.name}</td>
			<td class="main_header_link" align="center">{$header.age}</td>
			<td class="main_header_link" align="center">{$header.featured_land}</td>
			<td class="main_header_link" align="center">{$header.featured_home}</td>
			<td class="main_header_link" align="center">{$header.action}</td>
		</tr>
		{if $user}
		<form action="{$form.action}" name="fusers" method="post">
			{$form.hiddens}
			{section name=u loop=$user}
				{if $user[u].gender eq 'Female'}
				<tr style="background-color:{if $user[u].status neq '&radic;'}#EFEFEF{/if}" >
					<td class="main_content_text" align="center"><a href="{$user[u].edit_link}"><img src="{$user[u].icon_path}" class="icon" alt="{$promo_user[f].name}" width="25"></a></td>
					<td class="main_content_text" align="center" >
						<p style="margin:0px; padding-bottom:3px;"><a href="{$user[u].edit_link}">{$user[u].name}</a></p>
						{$user[u].user_group}
					</td>
					<td class="main_content_text" align="center" >{$user[u].age}</td>
					<td class="main_content_text" align="center" >{$user[u].featured_land}</td>
					<td class="main_content_text" align="center" >{$user[u].featured_home}</td>
					<td class="main_content_text" align="center" ><a href="javascript:deleteRecord({$user[u].id});">Delete</a></td>
				</tr>
				{/if}
			{/section}
		</form>
		{else}
			<tr height="40">
				<td class="main_error_text" align="left" colspan="12" >{$header.empty}</td>
			</tr>
		{/if}
	</table>
</div>
<div style="float:left; width:4%;">&nbsp;</div>
<div style="float:left; width:56%;">
	<div class="red_header" style="padding-bottom:10px;">Featured users list for LADIES</div>
	<table border="0" class="table_main" cellspacing="1" cellpadding="5" width="100%">
		<tr class="table_header">
			<td class="main_header_text" align="center" width="30">{$header.icon}</td>
			<td class="main_header_link" align="center">{$header.name}</td>
			<td class="main_header_link" align="center">{$header.age}</td>
			<td class="main_header_link" align="center">{$header.country}</td>
			<td class="main_header_link" align="center">{$header.featured_land}</td>
			<td class="main_header_link" align="center">{$header.featured_home}</td>
			<td class="main_header_link" align="center">{$header.action}</td>
		</tr>
		{if $user}
		<form action="{$form.action}" name="fusers" method="post">
			{$form.hiddens}
			{section name=u loop=$user}
				{if $user[u].gender eq 'Male'}
				<tr style="background-color:{if $user[u].status neq '&radic;'}#EFEFEF{/if}" >
					<td class="main_content_text" align="center"><a href="{$user[u].edit_link}"><img src="{$user[u].icon_path}" class="icon" alt="{$promo_user[f].name}" width="25"></a></td>
					<td class="main_content_text" align="center" >
						<p style="margin:0px; padding-bottom:3px;"><a href="{$user[u].edit_link}">{$user[u].name}</a></p>
						{$user[u].user_group}
					</td>
					<td class="main_content_text" align="center" >{$user[u].age}</td>
					<td class="main_content_text" align="center" >{$user[u].country}</td>
					<td class="main_content_text" align="center" >{$user[u].featured_land}</td>
					<td class="main_content_text" align="center" >{$user[u].featured_home}</td>
					<td class="main_content_text" align="center" ><a href="javascript:deleteRecord({$user[u].id});">Delete</a></td>
				</tr>
				{/if}
			{/section}
		</form>
		{else}
			<tr height="40">
				<td class="main_error_text" align="left" colspan="12" >{$header.empty}</td>
			</tr>
		{/if}
	</table>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}