{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.addition.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.success_form}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.area_stories_edit}</div>

<table border=0 cellspacing=1 cellpadding=5 width="100%">
	<form method="POST" action="{$form.action}" name="story_form" enctype="multipart/form-data">
	{$form.hiddens}
	<tr bgcolor="#ffffff">
		<td align="left" width="200px" class="main_header_text">{$header.couple_name} <font class=main_error_text>*</font>:&nbsp;</td>
		<td align="left" colspan="2" class="main_content_text"><input type="text" name="couple_name" value="{$story.couple_name}" size=30 style="width: 195"></td>
	</tr>
	<tr bgcolor="#ffffff">
		<td align="left" class="main_header_text">{$header.image} # 1 <font class=main_error_text></font>:&nbsp;</td>
		<td align="left" class="main_content_text" width="110px">{$story.image_path_1}</td>
		<td align="left" class="main_content_text">&nbsp;<input type="file" name="image_path_1">{if $story.image_1_delete_link}<br>
		&nbsp;<input type="checkbox" name="delimage1">&nbsp;{$header.del_image}{/if}</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td align="left" class="main_header_text">{$header.image} # 2 <font class=main_error_text></font>:&nbsp;</td>
		<td align="left" class="main_content_text" width="110px">{$story.image_path_2}</td>
		<td align="left" class="main_content_text">&nbsp;<input type="file" name="image_path_2">{if $story.image_2_delete_link}<br>
		&nbsp;<input type="checkbox" name="delimage2">&nbsp;{$header.del_image}{/if}</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td align="left" class="main_header_text">{$header.image} # 3 <font class=main_error_text></font>:&nbsp;</td>
		<td align="left" class="main_content_text" width="110px">{$story.image_path_3}</td>
		<td align="left" class="main_content_text">&nbsp;<input type="file" name="image_path_3">{if $story.image_3_delete_link}<br>
		&nbsp;<input type="checkbox" name="delimage3">&nbsp;{$header.del_image}{/if}</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td align="left" class="main_header_text">{$header.story_title} <font class=main_error_text>*</font>:&nbsp;</td>
		<td align="left" colspan="2" class="main_content_text"><input type="text" name="story_title" value="{$story.story_title}" size=30 style="width: 195"></td>
	</tr>
	<tr bgcolor="#ffffff">
		<td align="left" class="main_header_text">{$header.description}:&nbsp;</td>
		<td align="left" colspan="2" class="main_content_text"><textarea rows="10" cols="70" name="description">{$story.description}</textarea></td>
	</tr>
	<tr bgcolor="#ffffff">
		<td align="left" class="main_header_text">{$header.date}:&nbsp;</td>
		<td class="main_content_text" align="left" colspan="2">
                        <select name="s_day" >
						{section name=d loop=$s_day}
							<option value="{$s_day[d].value}" {if $s_day[d].sel}selected{/if}>{$s_day[d].value}</option>
						{/section}
						</select>&nbsp;
                        <select name="s_month" >
						{section name=m loop=$s_month}
							<option value="{$s_month[m].value}" {if $s_month[m].sel}selected{/if}>{$s_month[m].name}</option>
						{/section}
						</select>&nbsp;
                        <select name="s_year" >
						{section name=y loop=$s_year}
							<option value="{$s_year[y].value}" {if $s_year[y].sel}selected{/if}>{$s_year[y].value}</option>
						{/section}
						</select>
		</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td colspan="3">
		<table>
			<tr height="40">
				{if $form.par eq 'edit'}
				<td><input type="button" value="{$button.save}" class="button" onclick="javascript:document.story_form.sel.value='save';document.story_form.submit()"></td>
				{else}
				<td><input type="button" value="{$button.add}" class="button" onclick="document.story_form.sel.value='add_story';document.story_form.submit()"></td>
				{/if}
				<td><input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$form.back}'"></td>
			</tr>
		</table>
		</td>
	</tr>
</form>
</table>
{include file="$admingentemplates/admin_bottom.tpl"}