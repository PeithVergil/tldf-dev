{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.list}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.rooms}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.chat_rooms}</div>
<!-- transfer vars from php to javascript -->
<script type="text/javascript">
var permanent = '{$room_permanent_post}';
var ispublic = '{$room_public_post}';
var name = '{$room_name_post}';
var password = '{$room_password_post}';
var selectstr = '{$room_order_post}';
var optionstr = '{$room_option}';
var hidden = '{$room_identification_post}';
var option_count = {$rowcount};
var deleteroom = '{$room_delete_post}';
var maxorder = '{$max_order_post}';
</script>
	<a href="room.php">{$header.add_new_room}</a><br>
	<br>
{if $rooms}
	<form id="roomlist" action="" method="post" enctype="multipart/form-data">
		<table border=0 class="table_main" cellspacing=1 cellpadding=3 width="100%">
			<tr height="30" bgcolor="#FFFFFF">
				<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'id'; my_getbyid('roomlist').submit()">{$header.id}</a></th>
				<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'name'; my_getbyid('roomlist').submit()">{$header.name}</a></th>
				<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'password'; my_getbyid('roomlist').submit()">{$header.password}</a></th>
				<th class="main_header_text" align="center">{$header.public}</th>
				<th class="main_header_text" align="center">{$header.permanent}</th>
				<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'row_nr'; my_getbyid('roomlist').submit()">{$header.number}</a></th>
				<th class="main_header_text" align="center">{$header.bump_up}</th>
				<th class="main_header_text" align="center">{$header.delete}</th>
			</tr>
{foreach from=$rooms item=room}
			{* assign javascript variables *}
			<tr bgcolor="#FFFFFF">
				<td class="main_content_text" align="center">
					{$room.id}
				</td>
				<td class="main_content_text" align="center">
					<input type="button" value="{$header.edit_btn}" id="bttn_{$room.row_nr}" onclick="javascript: onbttnclick('bttn_{$room.row_nr}','room_name[{$room.row_nr}]');">
					<input type="text" name="room_name[{$room.row_nr}]" value="{$room.name}" id="room_name[{$room.row_nr}]" onchange="javascript:row_change('{$room.row_nr}');" onfocus="javascript: onnamefocus('bttn_{$room.row_nr}','room_name[{$room.row_nr}]');" style="border: 0px;">
				</td>
				<td class="main_content_text" align="center">
					<input type="button" value="{$header.edit_btn}" id="bttn_pass_{$room.row_nr}" onclick="javascript: onbttnclick('bttn_pass_{$room.row_nr}','room_password[{$room.row_nr}]');">
					<input type="text" name="room_password[{$room.row_nr}]" value="{$room.password}" id="room_password[{$room.row_nr}]" onchange="javascript:row_change('{$room.row_nr}');" onfocus="javascript: onnamefocus('bttn_pass_{$room.row_nr}','room_password[{$room.row_nr}]');" style="border: 0px;">
				</td>
				<td class="main_content_text" align="center">
				<input type="checkbox" name="{$room.public_id}" id="{$room.public_id}" onchange="javascript:row_change('{$room.row_nr}');" {$room.ispublic}>
				</td>
				<td class="main_content_text" align="center">
				<input type="checkbox" name="{$room.permanent_id}" id="{$room.permanent_id}" onchange="javascript:perm_change('{$room.row_nr}');" {$room.ispermanent}>
				<td class="main_content_text" align="center">
				{if $room.ispermanent}
				{assign var=room_order_name value=$room_order_post[`$room.row_nr`]}
				<select name={$room_order_name} onchange="javascript: change({$room.row_nr});" onfocus="javascript:focused('{$room.row_nr}');">
				{assign var=selected value=$room_option[`$room.row_nr`][`$room.row_nr`]}
				{foreach from=$room.ordersel key=key item=ordersel}
					<option id="{$key}"
					{if $key==$selected}selected{/if}
					>{$ordersel}</option>
				{/foreach}
				</select>
				{else}
				&nbsp;
				{/if}
				</td>
				<td class="main_content_text" align="center">
					<a href="javascript: bump_up({$room.row_nr});">
						<img src="bumper.gif" border="0" alt="{$header.bump_up}">
					</a>
				</td>
				<td class="main_content_text" align="center">
					<input type="checkbox" name="{$room.delete_id}" id="{$room.delete_id}" onchange="javascript: row_change('{$room.row_nr}');">
				</td>
				<input type="hidden" name="{$room.hidden_id}" id="{$room.hidden_id}" value={$room.id} disabled >
			</tr>
{/foreach}
		</table>
		<br>
		<br>
		<input type="hidden" value="{$maxnumb}" name="{$max_order_post}">
		<input type="submit" value="{$header.submit_all_btn}" name="submited" onclick="javascript: submit_form();">
		<input type="hidden" id="sort" name="sort" value="none">
		<br>
		<br>{$header.room_bottom_note}
	</form>
{else}
	<font class="main_error_text">{$header.err_no_rooms_found}</font>
{/if}
<br><br>
<br><br>
<p align="right" class="main_header_text">&copy;<a href="http://tufat.com/" target="_blank">TUFaT.com</a> </p>
{include file="$admingentemplates/admin_bottom.tpl"}