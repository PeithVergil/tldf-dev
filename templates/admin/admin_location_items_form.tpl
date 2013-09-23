{include file="$admingentemplates/admin_top.tpl"}
{strip}
<font class="red_header">{$header.razdel_name}</font>
<font class="red_sub_header">&nbsp;|&nbsp;{$header.item_editform}</font>
<div class="help_text">
	<span class="help_title">{$lang.help}:</span>
	{if $form.par == "edit"}{$help.gs_item_edit}{else}{$help.gs_item_add}{/if}
</div>
<form method="post" action="{$form.action}" enctype="multipart/form-data" name="items_form">
	<input type="hidden" name="upload" value="0">
	<input type="hidden" name="e" value="1">
	<input type="hidden" name="cat" value="0">
	{$form.hiddens}
	<table border="0" cellspacing="1" cellpadding="5" width="100%">
		<tr>
			<td align="right" width="17%" class="main_header_text">{$header.name}<font class=main_error_text>*</font>:&nbsp;</td>
			<td class="main_content_text" align="left"><input type="text" name="name" value="{$data.name}" size="30" style="width:300px"></td>
			<td class="main_header_text" align="left" width="65%">&nbsp;{$header.status}&nbsp;<input type="checkbox" name="status" value="1" {if $data.status}checked{/if} ></td>
		</tr>
		<tr>
			<td align="right" width="17%" class="main_header_text">{$header.comment}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="left"><textarea name="comment" style="width:300px; height:200px">{$data.comment}</textarea></td>
		</tr>
		<tr>
			<td align="right" width="17%" class="main_header_text">{$header.price}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="left"><input type="text" name="price" value="{$data.price}" size="20" style="width:100px">&nbsp;{$form.curency}</td>
		</tr>
		<tr>
			<td align="right" width="17%" class="main_header_text">{$header.category}:&nbsp;</td>
			<td colspan="2" class="main_content_text">
				<select name="id_category" onchange="javascript: this.form.cat.value='1'; this.form.submit()">
				{section name=s loop=$category}
					<option value="{$category[s].id}" {if $data.id_category == $category[s].id}selected="selected"{/if}>{$category[s].name}</option>
				{/section}
				</select>
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
			<td align="right" width="17%" class="main_header_text">{$lang.users.head_upload_foto}:&nbsp;</td>
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
							<td><input type="button" class="button" value="{$button.delete}" class="button" onclick="javascript: this.form.picdel.value='1'; this.form.submit();" /></td>
						</tr>
					</table>
				</td>
			</tr>
		{/if}
		<tr bgcolor="#ffffff">
			<td align="right" width="17%" class="main_header_text">
				{$lang.users.head_upload_video}:&nbsp;<br/>&nbsp;
			</td>
			<td colspan="2">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<div>
								<input type="file" name="video" />
								{if $form.par == "edit"}
									<input type="button" class="button" value="{$button.upload}" onclick="javascript: this.form.upload.value='1'; this.form.submit();" />
								{/if}
							</div>
							{$data.video_comment}
						</td>
						{if $data.video_path}
							<td>
								&nbsp;&nbsp;
								<a href="#" onclick="javascript: window.open('./admin_citylocation.php?sel=upload_view&amp;id_file={$data.video_name}&amp;type_upload=v','video_view','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;"><img src="{$data.video_image_path}" class="icon" alt="" border="0"></a>
								&nbsp;&nbsp;
							</td>
							<td>
								<input type="button" class="button" value="{$button.delete}" onclick="javascript: this.form.viddel.value = '1'; this.form.submit();" />
								<br/>&nbsp;
							</td>
						{/if}
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height="1" colspan="2"><div style="height: 1px; margin: 5px 0px" class="delimiter"></div></td></tr>
		{if $form.par == "edit"}
			<tr>
				<td align="right" width="17%" class="main_header_text">{$header.images}:&nbsp;</td>
				<td colspan="2">
					<input type="file" name="images">
					<input type="button" class="button" value="{$button.upload}" onclick="this.form.imgupload.value='1'; this.form.submit();" />
				</td>
			</tr>
			{if $data.gallery}
				<tr>
					<td>&nbsp;</td>
					<td colspan="2">
						<table>
							<tr>
								{foreach item=item key=key from=$data.gallery}
									<td align=center>
										<img src="{$item.thumb_image_path}" border="1"><br>
										<a href="#" onclick="javascript: var f=document.items_form; f.picdel.value='1'; f.picdelid.value='{$item.id}'; f.submit();" class="main_content_text">[{$button.delete}]</a>
									</td>
								{/foreach}
							</tr>
						</table>
					</td>
				</tr>
			{/if}
		{/if}
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td></td>
			<td>
				{if $form.par == "edit"}
					{if $data.root != 1}
						<input type="button" class="button" value="{$button.save}" onclick="javascript: this.form.submit()" />&nbsp;
						<input type="button" class="button" value="{$button.delete}" onclick="javascript: if(confirm('{$form.confirm}')){ldelim}location.href='{$form.delete}'{rdelim}">&nbsp;
					{/if}
				{else}
					<input type="button" class="button" value="{$button.add}" onclick="javascript: this.form.submit()" />&nbsp;
				{/if}
				<input type="button" class="button" value="{$button.back}" onclick="javascript: location.href='{$form.back}'" />
			</td>
		</tr>
	</table>
	<br/>
</form>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}