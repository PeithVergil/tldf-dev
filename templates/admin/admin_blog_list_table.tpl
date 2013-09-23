{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font><br><br>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.blog_list}</div>
<div id="test_div" class="error_msg" style="padding: 5px 0px;">&nbsp;</div>
<div>
<table border=0 class="table_main" cellspacing=1 cellpadding=3 width="100%">
{if $links}
	<tr bgcolor="#ffffff">
		<td height="20" align="left"  colspan="10" class="main_content_text" >{$links}</td>
	</tr>
{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="20">{$header.number}</td>
		<td align="center"><span style="cursor: pointer;" onclick="document.location.href='admin_blog.php?sorter=1&order={$form.order}';"><font class="main_header_text" style="text-decoration: underline;">{$header.title}</font>&nbsp;{if $form.sorter eq 1}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</span></td>
		<td align="center" width="100"><span style="cursor: pointer;" onclick="document.location.href='admin_blog.php?sorter=2&order={$form.order}';"><font class="main_header_text" style="text-decoration: underline;">{$header.user_login}</font>&nbsp;{if $form.sorter eq 2}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</span></td>
		<td align="center" width="100"><span style="cursor: pointer;" onclick="document.location.href='admin_blog.php?sorter=3&order={$form.order}';"><font class="main_header_text" style="text-decoration: underline;">{$header.creation_date}</font>&nbsp;{if $form.sorter eq 3}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</span></td>
		<td align="center" width="100"><span style="cursor: pointer;" onclick="document.location.href='admin_blog.php?sorter=4&order={$form.order}';"><font class="main_header_text" style="text-decoration: underline;">{$header.type}</font>&nbsp;{if $form.sorter eq 4}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</span></td>
		<td class="main_header_text" align="center" width="50">{$header.posts}</td>
		<td class="main_header_text" align="center" width="50">{$header.comms}</td>
		<td class="main_header_text" align="center">{$header.category}</td>
		<td align="center" width="80"><span style="cursor: pointer;" onclick="document.location.href='admin_blog.php?sorter=5&order={$form.order}';"><font class="main_header_text" style="text-decoration: underline;">{$header.status}</font>&nbsp;{if $form.sorter eq 5}<font class="main_content_text">{if $form.order == 1}&#8595;{else}&#8593;{/if}</font>{/if}</span></td>
		<td class="main_header_text" align="center" width="100">&nbsp;</td>
	</tr>
	{if $blog_profile}
	{section name=spr loop=$blog_profile}
	<tr bgcolor="#FFFFFF">
		<td class="main_content_text" align="center">{$blog_profile[spr].number}</td>
		<td class="main_content_text" align="center">{$blog_profile[spr].title}</td>
		<td class="main_content_text" align="center"><a href="{$blog_profile[spr].user_link}">{$blog_profile[spr].login}</a></td>
		<td class="main_content_text" align="center">{$blog_profile[spr].creation_date}</td>
		<td class="main_content_text" align="center">{if $blog_profile[spr].is_hidden eq 1}{$header.private}{else}{$header.public}{/if}</td>
		<td class="main_content_text" align="center">{$blog_profile[spr].posts_count}</td>
		<td class="main_content_text" align="center">{$blog_profile[spr].comments_count}</td>
		<td class="main_content_text" align="center">{$blog_profile[spr].category_name}</td>
		<td class="main_content_text" align="center"><input type="checkbox" name="active[]" value="{$blog_profile[spr].id}" {if $blog_profile[spr].active eq 1} checked {/if} onclick="ChangeStatus('{$blog_profile[spr].id}', this.checked, document.getElementById('test_div'));"></td>
		<td class="main_content_text" align="center"><input type="button" value="{$lang.users.comunicate}" class="button" onclick="javascript: window.open('{$blog_profile[spr].user_comunicate}','comunicate', 'height=800, resizable=yes, scrollbars=yes,width=600, menubar=no,status=no');"></td>
	</tr>
	{/section}
	{else}
	<tr height="40">
		<td class="main_error_text" align="left" colspan="9" bgcolor="#FFFFFF">{$header.empty_categories}</td>
	</tr>
	{/if}
{if $links}
	<tr bgcolor="#ffffff">
		<td height="20" align="left" colspan="10" class="main_content_text" >{$links}</td>
	</tr>
{/if}
</table>
</div>
{literal}
<script type="text/javascript">
var req = null;
function InitXMLHttpRequest() {
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP");
	}
}

function ChangeStatus(id_blog, status, destination) {
	InitXMLHttpRequest();
	if (req) {
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				destination.innerHTML = req.responseText;
			} else {
				destination.innerHTML = "Changing...";
			}
		}
		req.open("GET", "admin_blog.php?sel=change_status&id_profile=" + id_blog + "&status=" + status, true);
		req.send(null);
	} else {
		destination.innerHTML = 'Browser unable to create XMLHttp Object';
	}
}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}