{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.list}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.room}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.chat_rooms_add}</div>
<form name="room" action="room.php" method="post">
	<input type="hidden" name="id" value="{$smarty.request.id}">
	<table border=0 cellspacing=1 cellpadding=3 width="100%">
		<tr bgcolor="#FFFFFF">
			<td class="main_header_text" align="right">{$header.name}:</td>
			<td><input type="text" name="name" value="{$smarty.request.name}"></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td class="main_header_text" align="right">{$header.password}:</td>
			<td><input type="text" name="password" value="{$smarty.request.password}"></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td class="main_header_text" align="right">{$header.public}:</td>
			<td><input type="checkbox" name="ispublic" value="{if $smarty.request.ispublic}{$smarty.request.ispublic}{else}y{/if}" {if $smarty.request.ispublic} checked {/if}></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td class="main_header_text" align="right">{$header.permanent}:</td>
			<td><input type="checkbox" name="ispermanent" value="{if $smarty.request.ispermanent}{$smarty.request.ispermanent}{else}l{/if}" {if $smarty.request.ispublic} checked{/if}></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td colspan="2" align="center">
				<input type="submit" name="add" value="{$header.add_new_room_btn}">
				<input type="submit" name="set" value="{$header.update_room_btn}" {if !$smarty.request.id}disabled{/if}>
				<input type="submit" name="del" value="{$header.remove_room_btn}" {if !$smarty.request.id}disabled{/if}>
			</td>
		</tr>
	</table>
</form>
{include file="$admingentemplates/admin_bottom.tpl"}