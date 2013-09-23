		<table class="simple_centered_table" cellpadding="0" cellspacing="1">
		<tr>
			<th>{$lang.voip.dest_user}</th>
			<th>{$lang.voip.call_time}</th>
			<th>{$lang.voip.call_cost}</th>
			<th>{$lang.voip.date}</th>
		</tr>
		{foreach name=user_stat from=$user_stat item=item}
		<tr>
			<td><a href="{$item.call_link}" title="{$lang.voip.call_to_user}">{$item.to_user}</a></td>
			<td>{$item.duration}</td>
			<td>{$item.cost_for_user} {$item.curr_name}</td>
			<td>{$item.date}</td>
		</tr>
		{/foreach}
		</table>