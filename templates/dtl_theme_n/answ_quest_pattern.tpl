<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>{if $path}
		{$lang.answers.questions_for}:
		{foreach name=path from=$path item=item}
		{if $smarty.foreach.path.iteration > 1}
			{$item.name}{if !$smarty.foreach.path.last}->{/if}
		{/if}
		{/foreach}
		{else}
		{$filter_type}
		{/if}
	</td>
</tr>
<tr><td class="sep"></td></tr>
<tr>
	<td>
		<b>{$lang.answers.total_count}:</b> {$count_records}
	</td>
</tr>
<tr><td class="sep"></td></tr>
{if $questions}
<tr>
	<td>
		<table cellpadding="0" cellspacing="1" width="100%" class="qs_table">
		{foreach name=q from=$questions item=item}
		<tr id="tr_q{$item.id}">
			<td width="10%" valign="top">
				{if !$item.root_user}<a href="viewprofile.php?id={$item.id_user}"><img src="{$item.icon_url}" border="0"/></a>
				{else}<img src="{$item.icon_url}" border="0"/>{/if}
				<br />
				{if !$item.root_user}<a href="viewprofile.php?id={$item.id_user}">{$item.login}</a>{else}
				{$item.login}{/if}
				<br />
				{$item.age} {$lang.answers.ans}
			</td>
			<td width="80%" valign="top">
				<b>{$lang.answers.asked}:</b> <font class="hidden_text">{if !$item.life_time}{$lang.answers.asked_now}{else}{$item.life_time} {$lang.answers.ago}{/if}</font>&nbsp;|&nbsp;<b>{$lang.answers.count_answers}:</b> <font class="hidden_text">{$item.answers_count}</font>
				{if $item.is_open == '0'}
					&nbsp;|&nbsp;<b>{$lang.answers.closed}:</b> <font class="hidden_text">{if !$item.closed_time}{$lang.answers.asked_now}{else}{$item.closed_time} {$lang.answers.ago}{/if}</font>
				{/if}
				<br />
				<b>{$lang.answers.question}:</b> {$item.text}<br />
				{if $item.details}<b>{$lang.answers.details}:</b> {$item.details}<br />{/if}
				{if !$path}<b>{$lang.answers.in}:</b> <font class="hidden_text">{$item.path}</font>{/if}
				<br />
				{if $item.is_open == '0'}
					<b>{$lang.answers.best_answer}:</b> <font class="best_answer">{$item.best_answer_text}</font>
				{/if}
			</td>
			<td width="10%" valign="top">
				{if $user.0 != $item.id_owner && $item.is_open == 1}
				<a href="#" onclick=" showAnswerField({$item.id}); return false;" >{$lang.answers.answer_question}</a><br />
				{/if}
				<a id="view_answers_link{$item.id}" href="#" onclick=" viewAnswers({$item.id}); return false;" {if $item.answers_count < '1'}style="display:none"{/if}>{$lang.answers.view_answ}</a>
			</td>
		</tr>
		<tr id="tr_answer{$item.id}" style="display:none;">
			<td colspan="3" class="answers">
				<div id="add_answer_area{$item.id}" style="display:none;">
					<textarea id="text_answer{$item.id}" style="width:200px; height:30px;"></textarea><input type="button" value="{$lang.answers.button_answer}" onclick="answer({$item.id}); return false;" />
				</div>
				<div id="answers_area{$item.id}"></div>
			</td>
		</tr>
		{/foreach}
		</table>
	</td>
</tr>
{else}
<tr><td>{$lang.answers.no_questions}</td></tr>
{/if}
<tr><td height="10"></td></tr>
<tr>
	<td>
		{foreach name=links from=$link_arr item=item}
		<a href="#" onclick="goToPage({$item.page}); return false;" {if $item.selected} style="font-weight:bold; text-decoration:none;"{/if}>{$item.name}</a>
		{/foreach}
		<input type="hidden" id="q_page_num" value="{$form.page}" />
	</td>
</tr>
</table>