{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.bot}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.chat_bot}</div>
{if $enableBots}
	<form name="bot" action="bot.php" method="post">
		<input type="hidden" name="id" value="{$_REQUEST.id}">
		<table border="0" cellspacing="8">
		<tr><td align="right">{$header.bot_name}</td><td><input type="text" name="login" value="{$_REQUEST.login}"></td></tr>
		<tr>
			<td align="right">{$header.bot_room_list_avatar}</td>
			<td >
				<select name="room_avatar">
					{assign var="selected" value="`$_REQUEST.bot.room_avatar`"}
					<option id="0" {if $selected==""}selected{/if}>{$header.sel_none}</option>
					{foreach from=$_REQUEST.smilies key=key item=ordersel}
					<option id="{$key}" {if $ordersel==$selected}selected{/if}>{$ordersel}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td align="right">{$header.bot_main_chat_avatar}</td>
			<td >
				<select name="chat_avatar">
					{assign var="selected" value="`$_REQUEST.bot.chat_avatar`"}
					<option id="0" {if $selected==""}selected{/if}>{$header.sel_none}</option>
					{foreach from=$_REQUEST.smilies key=key item=ordersel}
					<option id="{$key}" {if $ordersel==$selected}selected{/if}>{$ordersel}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td align="right">{$header.login_into_room}</td>
			<td >
				<select name="roomid">
					{assign var="selected" value="`$_REQUEST.bot.roomid`"}
					{foreach from=$_REQUEST.rooms key=key item=ordersel}
					<option id="{$key}" {if $key==$selected}selected{/if}>{$ordersel}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td align="right">{$header.active_when_1}</td>
			<td >
				<input type="text" name="active_on_min_users" size="3" maxlength="2" value="{$_REQUEST.bot.active_on_min_users}">
			</td>
		</tr>
		<tr>
			<td align="right">{$header.active_when_2}</td>
			<td >
				<input type="text" name="active_on_max_users" size="3" maxlength="2" value="{$_REQUEST.bot.active_on_max_users}">
			</td>
		</tr>
		<tr>
			<td align="right">{$header.active_when_3}</td>
			<td >
				<input type="checkbox" name="active_on_no_moderators" id="active_on_no_moderators"
				{if $_REQUEST.bot.active_on_no_moderators == 1} checked {/if}>
			</td>
		</tr>
		<tr>
			<td align="right">{$header.active_when_4}</td>
			<td >
				<input type="checkbox" name="active_on_no_bots" id="active_on_no_bots"
				{if $_REQUEST.bot.active_on_no_bots == 1} checked {/if}>
			</td>

		</tr>
		<tr>
			<td align="right">{$header.active_when_5}</td>
			<td >
				<select name="active_on_user">
					{assign var="selected" value="`$_REQUEST.bot.active_on_user`"}
					<option id="0" {if $selected=="0"}selected{/if}>{$header.sel_none}</option>
					{foreach from=$_REQUEST.users key=key item=ordersel}
					<option id="{$ordersel.id}" {if $ordersel.id==$selected}selected{/if}>{$ordersel.login}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="{$header.submit_btn}"></td>
		</tr>
		</table>
	</form>
{else}
	<font class="main_error_text">{$header.err_bots_disabled}</font>
{/if}
<br><br>
<p align="right" class="main_header_text">&copy;<a href="http://tufat.com/" target="_blank">TUFaT.com</a> </p>
{include file="$admingentemplates/admin_bottom.tpl"}