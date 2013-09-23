{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font>
<font class=red_sub_header>&nbsp;|&nbsp;{$header.mails_list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.activity_mails}</div>
<form method="post" action="admin_site_activity_mail_list.php" enctype="multipart/form-data">
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
		<tr class="table_header">
			<td class="main_header_text" align="center" width="20">{$header.id}</td>
			<td class="main_header_text" align="center" width="20">{$header.sequence}</td>
			<td class="main_header_text" align="center">{$header.mail_to}</td>
			<td class="main_header_text" align="center">{$header.mail_action}</td>
			<td class="main_header_text" align="center">{$header.mail_subject}</td>
			{if $debug}
				<td class="main_header_text" align="center">{$header.mail_tpl_file}</td>
			{/if}
			<td class="main_header_text" align="center" width="80">{$header.mail_english}</td>
			<td class="main_header_text" align="center" width="80">{$header.mail_thai}</td>
		</tr>
		{foreach item=item from=$sistem}
			<tr bgcolor="#ffffff">
				<td class="main_content_text" align="center">{$item.id}</td>
				<td class="main_content_text" align="center">{$item.sequence}</td>
				<td class="main_content_text" align="center">{$item.mail_to}</td>
				<td class="main_content_text">{$item.title}</td>
				<td class="main_content_text">{$item.subject}</td>
				{if $debug}
					<td class="main_content_text" >{$item.template_file}</td>
				{/if}
				<td class="main_content_text" align="center" >
					<a href="{$item.viewlink}">{$header.view_mail}</a>
				</td>
				<td class="main_content_text" align="center" >
					{if $item.multi_lang}
						<a href="{$item.viewlink}&langid=2">{$header.view_mail}</a>
					{else}
						&nbsp;
					{/if}
				</td>
			</tr>
		{/foreach}
	</table>
</form>
<br><br>
{include file="$admingentemplates/admin_bottom.tpl"}