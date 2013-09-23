{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.list}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.messages}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.chat_messages}</div>
	<form name="msglist" id="msglist" action="msglist.php" method="post">
	<table border=0 cellspacing=1 cellpadding=5 >
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="right">{$header.in_this_room}:</td>
			<td>
				<select name="roomid">
				<option value="0">{$header.any_room_option}
				{html_options options=$rooms selected=$smarty.request.roomid}
				</select>
			</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="right">{$header.between_these_dates}:</td>
			<td><input type="text" name="from" value="{$smarty.request.from}" size="19">&nbsp;{$header.and}&nbsp;<input type="text" name="to" value="{$smarty.request.to}" size="19">&nbsp;{$header.date_format_example}</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right">{$header.days_text}:</td>
			<td><input type="text" name="days" value="{$smarty.request.days}" size="8"></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right">{$header.by_this_user}:</td>
			<td>
				<select name="userid">
				<option value="0">{$header.any_user_option}
				{html_options options=$users selected=$smarty.request.userid}
				</select>
			</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td align="right" width="200">{$header.keyword_text}:</td>
			<td><input type="text" name="keyword" value="{$smarty.request.keyword}" size="32"></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td colspan="2" align="center">
				<input type="submit" name="apply" value="{$header.show_messages_btn}">
				<input type="submit" name="clear" value="{$header.clear_filter_btn}">
				<input type="hidden" id="sort" name="sort" value="none">
			</td>
		</tr>
		</table>
	</form>
{if $messages}
<table border=0 class="table_main" cellspacing=1 cellpadding=5 >
	<tr bgcolor="#FFFFFF">
		<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'id'; my_getbyid('msglist').submit()">{$header.id}</a></th>
		<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'sent'; my_getbyid('msglist').submit()">{$header.sent}</a></th>
		<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'user'; my_getbyid('msglist').submit()">{$header.from_user}</a></th>
		<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'toroom'; my_getbyid('msglist').submit()">{$header.to_room}</a></th>
		<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'touser'; my_getbyid('msglist').submit()">{$header.to_user}</a></th>
		<th class="main_header_text" align="center">{$header.txt}</th>
	</tr>
{foreach from=$messages item=message}
	<tr bgcolor="#FFFFFF">
		<td class="main_content_text" align="center">{$message.id}</td>
		<td class="main_content_text" align="center">{$message.sent}</td>
		<td class="main_content_text" align="center">{$message.user}</td>
		<td class="main_content_text" align="center"><a href="room.php?id={$message.toroomid}">{$message.toroom}</a></td>
		<td class="main_content_text" align="center">{$message.touser}</td>
		<td class="main_content_text" align="center">{$message.txt}</td>
	</tr>
{/foreach}
	</table>
{else}
	<font class="main_error_text">{$header.err_no_messages_found}</font>
{/if}
<br><br>
<br><br>
<p align="right" class="main_header_text">&copy;<a href="http://tufat.com/" target="_blank">TUFaT.com</a> </p>
{include file="$admingentemplates/admin_bottom.tpl"}