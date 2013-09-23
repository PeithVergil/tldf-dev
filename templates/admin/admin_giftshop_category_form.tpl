{strip}
{include file="$admingentemplates/admin_top.tpl"}
<span class="red_header">{$lang.giftshop.razdel_name}</span><span class="red_sub_header">&nbsp;|&nbsp;{$lang.giftshop.category_editform}</span>
<div class="help_text">
	<div class="help_title">{$lang.help}:</div>
	{if $form.par == 'add'}
		{$help.gs_category_add}
	{else}
		{$help.gs_category_edit}
	{/if}
</div>
<form method="post" action="admin_giftshop.php" enctype="multipart/form-data">
	{if $form.par == 'add'}
		<input type="hidden" name="sel" value="catinsert">
		<input type="hidden" name="e" value="1">
		<input type="hidden" name="page" value="{$form.page}">
	{else}
		<input type="hidden" name="sel" value="catupdate">
		<input type="hidden" name="e" value="1">
		<input type="hidden" name="page" value="{$form.page}">
		<input type="hidden" name="picdel" value="0">
		<input type="hidden" name="id" value="{$data.id}">
	{/if}
	<table border="0" cellspacing="1" cellpadding="5" width="100%">
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">{$lang.giftshop.name}<span class=main_error_text>*</span>:&nbsp;</td>
			<td class="main_content_text" align="left"><input type="text" name="name" value="{$data.name}" size="30" style="width:300px;"></td>
			<td class="main_header_text" align="left" width="65%">
				&nbsp;{$lang.giftshop.status}&nbsp;<input type="checkbox" name="status" value="1" style="vertical-align:middle;" {if $data.status}checked="checked"{/if}>
			</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">{$lang.giftshop.comment}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="left"><textarea name="comment" style="width:300px; height:200px;">{$data.comment}</textarea></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">{$lang.giftshop.sorter}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="left">
				<select name="sorter">
				{foreach key=key item=item from=$sorter}
					<option value="{$key}" {if $item.sel}selected="selected"{/if}>{$key}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">{$lang.giftshop.icon}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="left"><input type="file" name="icon"></td>
		</tr>
		{if $data.icon_path}
			<tr bgcolor="#FFFFFF">
				<td>&nbsp;</td>
				<td colspan="2" class="main_content_text" align="left">
					<img src="{$data.icon_path}" border="1" vspace="5" style="vertical-align:middle;">&nbsp;
					<input type="button" value="{$lang.button.delete}" class="button" style="vertical-align:middle;" onclick="if (confirm('{$lang.confirm.giftshop_category_picture}')) {ldelim} this.form.picdel.value=1; this.form.submit(); {rdelim}">
				</td>
			</tr>
		{/if}
	</table>
	<br>
	{if $form.par == 'add'}
		<input type="button" value="{$lang.button.add}" class="button" onclick="this.form.submit();">&nbsp;
	{else}
		<input type="button" value="{$lang.button.save}" class="button" onclick="this.form.submit();">&nbsp;
		<input type="button" value="{$lang.button.delete}" class="button" onclick="if (confirm('{$lang.confirm.giftshop_category}')) window.location.href='admin_giftshop.php?sel=catdel&amp;id={$data.id}&amp;page={$form.page}';">&nbsp;
	{/if}
	<input type="button" value="{$lang.button.back}" class="button" onclick="window.location.href='admin_giftshop.php?page={$form.page}';">
</form>
{include file="$admingentemplates/admin_bottom.tpl"}
{/strip}