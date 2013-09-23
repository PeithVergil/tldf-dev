		<a href="#" onClick="javscript: expandUser({$id_user}, {$iter});">{$lang.voip.renew}</a><br /><br />
		<table class="simple_table" cellpadding="0" cellspacing="1">
		<tr>
			<th>{$lang.voip.call_id}</th>
			<th>{$lang.voip.source_number}</th>
			<th>{$lang.voip.dest_number}</th>
			<th>{$lang.voip.dest_user}</th>
			<th>{$lang.voip.call_time}</th>
			<th>{$lang.voip.call_cost}</th>
			<th>{$lang.voip.percent}</th>
			<th>{$lang.voip.date}</th>
		</tr>
		{foreach name=user_stat from=$user_stat item=item}
		<tr>
			<td>{$item.call_id}</td>
			<td>{$item.from_number}</td>
			<td>{$item.to_number}</td>
			<td>{$item.to_user}</td>
			<td>{$item.duration}</td>
			<td>{$item.cost} {$item.curr_name}</td>
			<td>{$item.percent}%</td>
			<td>{$item.date}</td>
		</tr>
		{/foreach}
		</table>