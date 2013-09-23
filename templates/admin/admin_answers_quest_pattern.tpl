<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>{if $path}
		<b>{$lang.answers.questions_for}:</b>
		{foreach name=path from=$path item=item}
		{if $smarty.foreach.path.iteration > 1}{$item.name}{if !$smarty.foreach.path.last}->{/if}{/if}
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
{if !$form.view_all}
<tr>
	<td>
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td style="padding-right:30px;"><input type="checkbox" id="opened" {if $form.opened == '1'}checked="checked" {/if} onclick="refreshQuestions(1); return false;" />&nbsp;{$lang.answers.opened_qs}</td>
			<td style="padding-right:30px;"><input type="checkbox" id="closed" {if $form.closed == '1'}checked="checked" {/if} onclick="refreshQuestions(1); return false;" />&nbsp;{$lang.answers.closed_qs}</td>
			<td><input type="checkbox" id="yours" {if $form.yours  > '0'}checked="checked" {/if} onclick="refreshQuestions(1); return false;" />&nbsp;{$lang.answers.yours_qs}</td>
		</tr>
		</table>
	</td>
</tr>
<tr><td class="sep"></td></tr>
{/if}
{if $questions}
<tr>
	<td>
		<table cellpadding="0" cellspacing="1" width="100%" class="qs_table">
		{foreach name=q from=$questions item=item}
		<tr id="tr_q{$item.id}">
			<td width="10%" valign="top">
				<img src="{$item.icon_url}" border="0"/><br />
				{$item.login}<br />
				{$item.age} {$lang.answers.ans}
			</td>
			<td width="80%" valign="top">
				<b>{$lang.answers.asked}:</b> {if !$item.life_time}{$lang.answers.asked_now}{else}{$item.life_time} {$lang.answers.ago}{/if}&nbsp;|&nbsp;<b>{$lang.answers.count_answers}:</b> {$item.answers_count}
				{if $item.is_open == '0'}
					&nbsp;|&nbsp;<b>{$lang.answers.closed}:</b> {if !$item.closed_time}{$lang.answers.asked_now}{else}{$item.closed_time} {$lang.answers.ago}{/if}<br />
				{/if}
				<div id="inform{$item.id}"><div style=" float:left; font-weight:bold;">{$lang.answers.question}:&nbsp;</div><div id="quest{$item.id}" style="float:left;">{$item.text}</div><br />
				<div style=" float:left; font-weight:bold;">{$lang.answers.details}:&nbsp;</div><div id="details{$item.id}" style="float:left;">{$item.details}</div></div>
				<div id="edit_area{$item.id}" style="display:none;">
					<textarea id="text_q{$item.id}" style="width:200px; height:20px;"></textarea><br />
					<textarea id="text_details{$item.id}" style="width:200px; height:40px;"></textarea>
					<input type="button" value="{$lang.button.save}" onclick="saveQ({$item.id}); return false;">
				</div>
				<br />
				{if !$path}<b>{$lang.answers.in}:</b> {$item.path}{/if}
				<br />
				{if $item.best_answer_text}
					<b>{$lang.answers.best_answer}:</b> <font class="best_answer">{$item.best_answer_text}</font>
				{/if}
			</td>
			<td width="10%" valign="top">
				{if $auth.id_user == $item.id_owner}
					<a href="#" onclick=" editQ({$item.id}); return false;" >{$lang.button.edit}</a><br />
				{/if}
				<a href="#" onclick=" delQ({$item.id}); return false;" >{$lang.button.delete}</a><br />
				<a href="#" onclick=" window.open('./admin_comunicate.php?id={$item.id_owner}','comunicate', 'height=800, resizable=yes, scrollbars=yes,width=600, menubar=no,status=no'); return false;" >{$lang.users.comunicate}</a><br />
				{if $auth.id_user != $item.id_owner}
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
<tr><td class="sep"></td></tr>
<tr>
	<td>
		{foreach name=links from=$link_arr item=item}
		<a href="#" onclick="goToPage({$item.page}); return false;" {if $item.selected} style="font-weight:bold; text-decoration:none;"{/if}>{$item.name}</a>
		{/foreach}
		<input type="hidden" id="q_page_num" value="{$form.page}" />
	</td>
</tr>
</table>