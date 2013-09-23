{include file="$gentemplates/index_top.tpl"}
<td>
	<!-- begin main cell -->
	<table cellpadding="0" cellspacing="0" width="100%">	
	<tr valign=top><td valign=top>
			<div class="header">{$lang.answers.menu.my_a}</div>
			<div class="sep"></div>
	</td></tr>
	<tr><td>{include file="$gentemplates/answ_menu.tpl"}</td></tr>
	<tr><td height="10"></td></tr>
	<tr><td>
		<a href="{$file_name}?sel=my_q">{$lang.answers.menu.my_q}</a>&nbsp;&nbsp;&nbsp;
		<a href="{$file_name}?sel=my_a">{$lang.answers.menu.my_a}</a>
	</td></tr>
	<tr><td height="10"></td></tr>
	<tr>
    	<td>
        	<input type="radio" {if !$form.best_filter}checked="checked"{/if} onclick="location.href='{$file_name}?sel=my_a&filter=all&page=1';"/> {$lang.answers.all_yours_answers}&nbsp;&nbsp;
            <input type="radio" {if $form.best_filter == '1'}checked="checked"{/if} onclick="location.href='{$file_name}?sel=my_a&filter=best_answ&page=1';"/> {$lang.answers.yours_best_answers}&nbsp;&nbsp;
        </td>
    </tr>
	<tr><td height="10"></td></tr>
    <tr><td>
		<table cellpadding="0" cellspacing="1" width="100%" class="qs_table">
		{foreach name=answers from=$answers item=item}
		<tr>
			<td width="10%" valign="top">
				{if !$item.root_user}<a href="viewprofile.php?id={$item.id_user}"><img src="{$item.icon_url}" border="0"/></a>
				{else}<img src="{$item.icon_url}" border="0"/>{/if}
				<br />
				{if !$item.root_user}<a href="viewprofile.php?id={$item.id_user}">{$item.login}</a>
				{else}{$item.login}{/if}
				<br />
				{$item.age} {$lang.answers.ans}
			</td>
			<td width="90%" valign="top">
					<b>{$lang.answers.asked}:</b> {if !$item.item_life_time}{$lang.answers.asked_now}{else}{$item.item_life_time} {$lang.answers.ago}{/if}&nbsp;|&nbsp;<b>{$lang.answers.answered}:</b> {$item.comment_life_time}					
					{if $item.is_open == '0'}
						&nbsp;|&nbsp;<b>{$lang.answers.closed}:</b> {if !$item.item_closed_time}{$lang.answers.asked_now}{else}{$item.item_closed_time} {$lang.answers.ago}{/if}<br />
					{/if}
					<br />
					<b>{$lang.answers.question}:</b> {$item.item_text}<br />
					{if $item.item_details}<b>{$lang.answers.details}:</b> {$item.item_details}<br />{/if}
					<b>{$lang.answers.in}:</b> {$item.path}<br />
					{if $item.is_best == '1'}<font class="best_answer"><b>{$lang.answers.best_answer_header}</b></font><br />{/if}
					<b>{$lang.answers.your_answer}:</b> {$item.comment_text}<br />
			</td>
		</tr>
		{foreachelse}
		<tr><td>{$lang.answers.no_answers}</td></tr>
		{/foreach}
		</table>
    </td></tr>
	<tr>
		<td>
			{foreach name=links from=$link_arr item=item}
			<a href="{$file_name}?sel=my_a&page={$item.page}" {if $item.selected} style="font-weight:bold; text-decoration:none;"{/if}>{$item.name}</a>
			{/foreach}
		</td>
	</tr>
    </table>
</td>
{include file="$gentemplates/index_bottom.tpl"}