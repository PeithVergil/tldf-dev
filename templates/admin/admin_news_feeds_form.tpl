{include file="$admingentemplates/admin_top.tpl"}
 	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.rss_editform}</font>
	<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.news_rss_edit}</div>

				<table border=0 cellspacing=1 cellpadding=5 width="100%">
                <form method="post" action="{$form.action}"  enctype="multipart/form-data" name="news">
                {$form.hiddens}
                    <tr bgcolor="#ffffff" valign="top">
                        <td align="right" width="17%" class="main_header_text">{$header.rss_link}:&nbsp;</td>
                        <td class="main_content_text" align="left"><input type="text" name=link value="{$data.link}" style="width: 400px"></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td align="right" width="17%" class="main_header_text">{$header.max_items}:&nbsp;</td>
                        <td class="main_content_text" align="left"><input type="text" name=max_news id=max_news value="{$data.max_news}" style="width: 50px">&nbsp;&nbsp;<input type="checkbox" name="all" value="1" onclick="UpdateMaxNews(this);" {if $data.all eq 1}checked{/if}> All</td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td align="right" width="17%" class="main_header_text">{$header.rss_status}:&nbsp;</td>
                        <td class="main_content_text" align="left"><input type="checkbox" name="status" value="1" {if $data.status}checked{/if} ></td>
                    </tr>
                    </form>
            </table>
			<table><tr height="40">
			{if $form.par eq "edit"}
			<td><input type="button" value="{$button.save}" class="button" onclick="javascript:document.news.submit();"></td>
			<td><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$form.delete}'{literal}}{/literal}"></td>
			{else}
			<td><input type="button" value="{$button.add}" class="button" onclick="javascript:document.news.submit();"></td>
			{/if}
			<td><input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$form.back}'"></td>
			</tr></table>
	{literal}
	<script>
		function UpdateMaxNews(obj){
			var action = obj.checked;
			var mn_field = document.getElementById('max_news');
			if(action == true){
				mn_field.value=0;
			}
			mn_field.disabled=action;
			return;
		}
	</script>
	{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}