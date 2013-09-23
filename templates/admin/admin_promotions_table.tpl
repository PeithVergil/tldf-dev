{include file="$admingentemplates/admin_top.tpl"}
<script language="javascript" type="text/javascript">
{literal}
function deleteRecord(pRecNo)
{
	//alert('ID = ' + pRecNo);
	var lobjConfirm = confirm("Are you sure to delete the Promo Template?");
	if (lobjConfirm == true)
    {
		//alert("You pressed OK!");
		window.location = "admin_promotions.php?sel=del_temp&id=" + pRecNo;
    }
	else
    {
		//alert("You pressed Cancel!");
    }
}
{/literal}
</script>
{if $sistem}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.template_list}</font>
	<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.promotions}</div>
	<div style="padding-bottom:8px;">
		<input type="button" value="{$header.create_new}" class="button" onclick="javascript: location.href='{$create_link}';">
	</div>
	<form method="post" action="{$form.action}"  enctype="multipart/form-data" name="sactivate">
		{$form.hiddens_sistem_active}
		<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
			<tr class="table_header">
				<td class="main_header_text" align="center" width="20">{$header.number}</td>
				<td class="main_header_text" align="center" >{$header.name_template}</td>
				<td width="100" >&nbsp;</td>
				<td width="100" >&nbsp;</td>
				<td width="100" >&nbsp;</td>
			</tr>
			{section name=spr loop=$sistem}
				<tr bgcolor="#ffffff">
					<td class="main_content_text" align="center" >{$sistem[spr].number}</td>
					<td class="main_content_text" align="center" >{$sistem[spr].title}</td>
					<td class="main_content_text" align="center" >
						<a href="admin_promotions.php?sel=edit_temp&tmpid={$sistem[spr].id}">View/Edit</a>
					</td>
					<td class="main_content_text" align="center" >
						<a href="admin_promotions_mail.php?sel=create_promo&tid={$sistem[spr].id}">Create Promo Mail</a>
					</td>
					<td class="main_content_text" align="center" >
						<a href="javascript:deleteRecord({$sistem[spr].id});">Delete</a>
					</td>
				</tr>
			{/section}
		</table>
	</form>
	<BR><BR>
{/if}
{include file="$admingentemplates/admin_bottom.tpl"}