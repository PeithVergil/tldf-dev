{include file="$admingentemplates/admin_top.tpl"}
{strip}
<font class=red_header>{$header.razdel_name}</font>
<font class=red_sub_header>&nbsp;|&nbsp;{$header.category_editform}</font>
<div class="help_text">
	<span class="help_title">{$lang.help}:</span>
	{if $form.par == "edit"}{$help.gs_category_edit}{else}{$help.gs_category_add}{/if}
</div>
<form method="post" action="{$form.action}" enctype="multipart/form-data" name="category">
	<input type="hidden" name="upload" value="0" />
	<input type="hidden" name="e" value="1">
	{$form.hiddens}
	<table border="0" cellspacing="1" cellpadding="5" width="100%">
		<tr>
			<td align="right" width="17%" class="main_header_text">{$header.name}<font class=main_error_text>*</font>:&nbsp;</td>
			<td class="main_content_text" align="left"><input type="text" name="name" value="{$data.name}" size=30  style="width: 300"></td>
			<td class="main_header_text" align="left" width="65%">&nbsp;{$header.status}&nbsp;<input type="checkbox" name="status" value="1" {if $data.status}checked{/if} ></td>
		</tr>
		<tr>
			<td align="right" width="17%" class="main_header_text">{$header.comment}:&nbsp;</td>
			<td colspan="2">
				<textarea name="comment" style="width: 300px; height: 200px">{$data.comment}</textarea>
			</td>
		</tr>
		<tr>
			<td align="right" width="17%" class="main_header_text">{$header.sorter}:&nbsp;</td>
			<td colspan="2">
				<select name="sorter">
				{section name=s loop=$sorter}
					<option value="{$smarty.section.s.index_next}" {if $sorter[s].sel}selected="selected"{/if}>{$smarty.section.s.index_next}</option>
				{/section}
				</select>
			</td>
		</tr>
		<tr>
			<td align="right" width="17%" class="main_header_text">{$header.icon}:&nbsp;</td>
			<td colspan="2">
				<input type="file" name="icon">
				{if $form.par == "edit"}
					<input type="button" class="button" value="{$button.upload}" onclick="javascript: this.form.upload.value='1'; this.form.submit();" />
				{/if}
			</td>
		</tr>
		{if $data.icon_path}
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">
					<table>
						<tr>
							<td><img src="{$data.icon_path}" border="1"></td>
							{if $data.thumb_icon_path}
								<td>&nbsp;&nbsp;<img src="{$data.thumb_icon_path}" border="1">&nbsp;&nbsp;</td>
							{/if}
							<td><input type="button" value="{$button.delete}" class="button" onclick="javascript: this.form.picdel.value = '1'; this.form.submit();"></td>
						</tr>
					</table>
				</td>
			</tr>
		{/if}
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td></td>
			<td>
				{if $form.par == "edit"}
					{if $data.root != 1}
						<input type="button" value="{$button.save}" class="button" onclick="javascript: this.form.submit()" />&nbsp;
						<input type="button" value="{$button.delete}" class="button" onclick="javascript: if(confirm('{$lang.confirm.location_category}')){ldelim}location.href='{$form.delete}'{rdelim}" />&nbsp;
					{/if}
				{else}
					<input type="button" value="{$button.add}" class="button" onclick="javascript: this.form.submit()" />&nbsp;
				{/if}
				<input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$form.back}'" />
			</td>
		</tr>
	</table>
	<br/>
</form>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}