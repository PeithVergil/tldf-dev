{strip}
{include file="$admingentemplates/admin_top.tpl"}
<span class="red_header">{$lang.giftshop.razdel_name}</span><span class="red_sub_header">&nbsp;|&nbsp;{$lang.giftshop.item_editform}</span>
<div class="help_text">
	<div class="help_title">{$lang.help}:</div>
	{if $form.par == "edit"}
		{$help.gs_item_edit}
	{else}
		{$help.gs_item_add}
	{/if}
</div>
{$par}
<form method="post" action="admin_giftshop.php" enctype="multipart/form-data" id="items_form">
	{if $form.par == 'add'}
		<input type="hidden" name="sel" value="itemsinsert">
		<input type="hidden" name="e" value="1">
		<input type="hidden" name="cat" value="{$data.id_category}">
		<input type="hidden" name="page" value="{$form.page}">
	{else}
		<input type="hidden" name="sel" value="itemsupdate">
		<input type="hidden" name="e" value="1">
		<input type="hidden" name="cat" value="{$data.id_category}">
		<input type="hidden" name="page" value="{$form.page}">
		<input type="hidden" name="id" value="{$data.id}">
		<input type="hidden" name="picdel" value="0">
		<input type="hidden" name="picdelid" value="0">
		<input type="hidden" name="imgupload" value="0">
	{/if}
	<table border="0" cellspacing="1" cellpadding="5" width="100%">
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">{$lang.giftshop.name}<font class=main_error_text>*</font>:&nbsp;</td>
			<td class="main_content_text" align="left"><input type="text" name="name" value="{$data.name}" size="30" style="width:300px;"></td>
			<td class="main_header_text" align="left" width="65%">
				&nbsp;{$lang.giftshop.status}&nbsp;<input type="checkbox" name="status" value="1" style="vertical-align:middle;" {if $data.status}checked="checked"{/if} />
			</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">{$lang.giftshop.comment}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="left"><textarea name="comment" style="width:300px; height:200px;">{$data.comment}</textarea></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">{$lang.giftshop.price}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="left">
				<input type="text" name="price" value="{$data.price}" size="20" style="width:100px;">&nbsp;{$form.curency}
			</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">{$lang.giftshop.category}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="left">
				<select name="id_category" onchange="this.form.submit();">
					{foreach item=item from=$categories}
						<option value="{$item.id}" {if $data.id_category == $item.id}selected="selected"{/if}>{$item.name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">{$lang.giftshop.sorter}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="left">
				<select name="sorter">
					{foreach item=item key=key from=$sorter}
						<option value="{$key}" {if $item.sel}selected="selected"{/if}>{$key}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">{$lang.giftshop.promote}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="left">
				<input type="checkbox" name="promote" value="1" style="vertical-align:middle;" {if $data.promote}checked="checked"{/if} />
			</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right" width="17%" class="main_header_text">{$lang.giftshop.icon}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="left"><input type="file" name="icon"></td>
		</tr>
		{if $data.icon_path}
			<tr bgcolor="#FFFFFF">
				<td>&nbsp;{$data.icon_path}</td>
				<td colspan="2" class="main_content_text" align="left">
					<img src="{$data.icon_path}" border="1" vspace="5" style="vertical-align:middle;">&nbsp;&nbsp;
					<input type="button" value="{$lang.button.delete}" class="button" style="vertical-align:middle;" onclick="if (confirm('{$lang.confirm.giftshop_item_picture}')) {ldelim} this.form.picdel.value=1; this.form.submit(); {rdelim}">
				</td>
			</tr>
		{/if}
		{if $form.par == "edit"}
			<tr bgcolor="#FFFFFF">
				<td align="right" width="17%" class="main_header_text">{$lang.giftshop.images}:&nbsp;</td>
				<td colspan="2" class="main_content_text" align="left">
					<table>
						<tr>
							<td><input type="file" name="images"></td>
							<td><input type="button" value="{$lang.button.upload}" class="button" onclick="this.form.imgupload.value=1; this.form.submit();"></td>
						</tr>
					</table>
				</td>
			</tr>
			{if $data.gallery}
				<tr bgcolor="#FFFFFF">
					<td>&nbsp;</td>
					<td colspan="2" class="main_content_text" align="left">
						<table>
							<tr>
								{foreach item=item key=key from=$data.gallery}
									<td align="center">
										<img src="{$item.thumb_image_path}" border="1"><br>
										<a href="#" onclick="if (confirm('{$lang.confirm.giftshop_item_picture}')) {ldelim} f=document.getElementById('items_form'); f.picdel.value=1; f.picdelid.value='{$item.id}'; f.submit(); {rdelim} return false;" class="main_content_text">[{$lang.button.delete}]</a>
									</td>
								{/foreach}
							</tr>
						</table>
					</td>
				</tr>
			{/if}
		{/if}
	</table>
	<br>
	{if $form.par == "edit"}
		{if $data.root != 1}
			<input type="button" value="{$lang.button.save}" class="button" onclick="this.form.submit();">&nbsp;&nbsp;
			<input type="button" value="{$lang.button.delete}" class="button" onclick="if (confirm('{$lang.confirm.giftshop_item}')) location.href='admin_giftshop.php?sel=itemsdel&amp;id={$data.id}&amp;page{$form.page}';">&nbsp;&nbsp;
		{/if}
	{else}
		<input type="button" value="{$lang.button.add}" class="button" onclick="this.form.submit();">&nbsp;&nbsp;
	{/if}
	<input type="button" value="{$lang.button.back}" class="button" onclick="window.location.href='admin_giftshop.php?sel=items&amp;id_category={$data.id_category}&amp;page={$form.page}';">
</form>
{include file="$admingentemplates/admin_bottom.tpl"}
{/strip}