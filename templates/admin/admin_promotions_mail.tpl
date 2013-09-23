{include file="$admingentemplates/admin_top.tpl"}
<script type="text/javascript">
{literal}
function deleteRecord(pRecNo)
{
	//alert('ID = ' + pRecNo);
	var lobjConfirm = confirm("Are you sure to delete the Promo Mail?");
	if (lobjConfirm == true) {
		window.location = "admin_promotions_mail.php?sel=del_rec&id=" + pRecNo;
    }
}
{/literal}
</script>
<font class=red_header>{$header.razdel_name}</font>
<font class=red_sub_header>&nbsp;|&nbsp;{$header.promo_mails}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.promotions}</div>
{*<!--
{if $form.err}
	<div class='error_msg' style="padding-bottom:10px;"><b>{$form.err}</b></div>
{/if}
-->*}
<form method="post" action="{$form.action}"  enctype="multipart/form-data">
	{$form.add_hiddens}
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
		<tr class="table_header">
			<td class="main_header_text" align="center" width="20">{$header.number}</td>
			<td class="main_header_text" align="center" >{$header.promo_mails}</td>
			<td width="110" >&nbsp;</td>
			<td width="100" >&nbsp;</td>
			<td width="100" >&nbsp;</td>
		</tr>
		{section name=spr loop=$sistem}
			<tr bgcolor="#ffffff">
				<td class="main_content_text" align="center" >{$sistem[spr].number}</td>
				<td class="main_content_text" align="center" >{$sistem[spr].title}</td>
				<td class="main_content_text" align="center" >
					<a href="{$sistem[spr].viewlink}">{$header.view_mail} {if $sistem[spr].status neq '1'}/ {$header.send_mail}{/if}</a>
				</td>
				<td class="main_content_text" align="center" >
					{if $sistem[spr].status neq '1'}
						<a href="{$sistem[spr].editlink}">{$header.edit_mail}</a>
					{/if}
				</td>
				<td class="main_content_text" align="center" >
					<a href="javascript:deleteRecord({$sistem[spr].id});">Delete</a>
				</td>
			</tr>
		{/section}
	</table>
</form>
<BR><BR>
{include file="$admingentemplates/admin_bottom.tpl"}