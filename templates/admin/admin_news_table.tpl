{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
	<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.news}</div>
				<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=5 align="left"  class="main_content_text" >{$links}</td>
					</tr>
					{/if}
							<tr class="table_header">
								<td class="main_header_text" align="center" width="20">{$header.number}</td>
								<td class="main_header_text" align="center">{$header.date}</td>
								<td class="main_header_text" align="center">{$header.title}</td>
								<td class="main_header_text" align="center">{$header.status}</td>
								<td class="main_header_text" align="center" width="100">&nbsp;</td>
							</tr>
							{if $news}
							{section name=spr loop=$news}
							<tr bgcolor="#FFFFFF">
								<td class="main_content_text" align="center">{$news[spr].number}</td>
								<td class="main_content_text" align="center"><a href="{$news[spr].editlink}">{$news[spr].date}</a></td>
								<td class="main_content_text" align="left" style="padding:4"><a href="{$news[spr].editlink}">{$news[spr].title}</a></td>
								<td class="main_content_text" align="center">{$news[spr].status}</td>
								<td class="main_content_text" align="center"><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$news[spr].deletelink}'{literal}}{/literal}"></td>
							</tr>
							{/section}
							{else}
							<tr height="40">
								<td class="main_error_text" align="left" colspan="5" bgcolor="#FFFFFF">{$header.empty}</td>
							</tr>
							{/if}
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=5 align="left"  class="main_content_text" >{$links}</td>
					</tr>
					{/if}
					</table>
			<table><tr height="40">
			<td><input type="button" value="{$header.add}" class="button" onclick="javascript: location.href='{$add_link}'"></td>
			</tr></table>
<!-- feeds -->
		<br><font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.rss_list}</font>
		<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.news_rss}<br><b>{$lang.hint}:</b>&nbsp;&nbsp;{$header.rss_hint}</div>

		<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
			<tr class="table_header">
				<td class="main_header_text" align="center" width="20">{$header.number}</td>
				<td class="main_header_text" align="center">{$header.rss_date}</td>
				<td class="main_header_text" align="center">{$header.rss_link}</td>
				<td class="main_header_text" align="center">{$header.rss_status}</td>
				<td class="main_header_text" align="center" width="100">&nbsp;</td>
			</tr>
			{if $feeds}
			{section name=spr loop=$feeds}
			<tr bgcolor="#FFFFFF">
				<td class="main_content_text" align="center">{$feeds[spr].number}</td>
				<td class="main_content_text" align="center">{$feeds[spr].date}</td>
				<td class="main_content_text" align="left" style="padding:4"><a href="{$feeds[spr].editlink}">{$feeds[spr].link}</a></td>
				<td class="main_content_text" align="center">{$feeds[spr].status}</td>
				<td class="main_content_text" align="center"><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$feeds[spr].deletelink}'{literal}}{/literal}"></td>
			</tr>
			{/section}
			{else}
			<tr height="40">
				<td class="main_error_text" align="left" colspan="5" bgcolor="#FFFFFF">{$header.rss_empty}</td>
			</tr>
			{/if}
		</table>
		<table><tr height="40">
			<td><input type="button" value="{$header.rss_add}" class="button" onclick="javascript: location.href='{$rss_add_link}'"></td>
			<td><input type="button" value="{$header.rss_update}" class="button" onclick="javascript: location.href='{$rss_update_link}'"></td>
		</tr></table>
{include file="$admingentemplates/admin_bottom.tpl"}