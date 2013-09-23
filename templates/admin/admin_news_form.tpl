{include file="$admingentemplates/admin_top.tpl"}
<div>
	<span class="red_header">{$header.razdel_name}</span><span class="red_sub_header">&nbsp;|&nbsp;{$header.editform}</span>
</div>
<div class="help_text">
	<span class="help_title">{$lang.help}:</span>{$help.news_editor}
</div>
<div>
	<form method="post" action="{$form.action}" enctype="multipart/form-data">
		{$form.hiddens}
		<table border="0" cellspacing="1" cellpadding="5" width="100%">
			<tr bgcolor="#ffffff">
				<td align="right" width="120" class="main_header_text">
					{$header.date}:&nbsp;
				</td>
				<td class="main_content_text" align="left">
					<select name="n_day">
						{section name=d loop=$day}
							<option value="{$day[d].value}" {if $day[d].sel}selected{/if}>{$day[d].value}</option>
						{/section}
					</select>&nbsp;
					<select name="n_month">
						{section name=m loop=$month}
							<option value="{$month[m].value}" {if $month[m].sel}selected{/if}>{$month[m].name}</option>
						{/section}
					</select>&nbsp;
					<select name="n_year">
						{section name=y loop=$year}
							<option value="{$year[y].value}" {if $year[y].sel}selected{/if}>{$year[y].value}</option>
						{/section}
					</select>&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="checkbox" name="status" value="1" {if $data.status}checked="checked"{/if}>&nbsp;-&nbsp;{$header.status}&nbsp;
				</td>
			</tr>
			<tr bgcolor="#ffffff" valign="top">
				<td align="right" class="main_header_text">
					{$header.title}:&nbsp;
				</td>
				<td class="main_content_text" align="left">
					<input type="text" value="{$data.title}" name="title" style="width: 420px">
				</td>
			</tr>
			<tr bgcolor="#ffffff" valign="top">
				<td align="right" class="main_header_text">
					{$header.text}:&nbsp;
				</td>
				<td class="main_content_text" align="left">
					{if RICH_TEXT_EDITOR == 'SPAW-1' || RICH_TEXT_EDITOR == 'SPAW-2'}
						{$editor}
					{elseif RICH_TEXT_EDITOR == 'TINYMCE'}
						<script type="text/javascript" src="{$site_root}/javascript/tiny_mce-3.5.8/tiny_mce.js"></script>
						<script type="text/javascript">
						tinyMCE.init({ldelim}
							mode : "textareas",
							oninit: myInit,
							{include file="$admingentemplates/admin_tiny_mce.tpl"}
						{rdelim});
						function myInit() {ldelim}
							tinyMCE.get('tinymce_text').setContent('{$data.text|escape:javascript}');
						{rdelim}
						</script>
						<textarea name="text" id="tinymce_text" rows="20" cols="60" style="width:700px;height:500px;"></textarea>
					{else}
						<textarea name="text" rows="20" cols="60" style="width:700px;height:500px;">{$data.text}</textarea>
					{/if}
				</td>
			</tr>
		</table>
		<table cellpadding="5" cellspacing="0">
			<tr>
				<td width="120">&nbsp;</td>
				{if $form.par eq "edit"}
					<td>
						<input type="submit" value="{$button.save}" class="button">
					</td>
					<td>
						<input type="button" value="{$button.delete}" class="button" onclick="{literal}if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$form.delete}'{literal}}{/literal}">
					</td>
				{else}
					<td>
						<input type="submit" value="{$button.add}" class="button">
					</td>
				{/if}
				<td>
					<input type="button" value="{$button.back}" class="button" onclick="location.href='{$form.back}'">
				</td>
			</tr>
		</table>
	</form>
<div>
{include file="$admingentemplates/admin_bottom.tpl"}