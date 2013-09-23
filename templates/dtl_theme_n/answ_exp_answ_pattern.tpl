
<a href="#" onclick="cropExpAnswers({$form.id_user}); return false;">{$lang.answers.crop_answers}</a>
<div class="sep"></div>
<table cellpadding="0" cellspacing="1" width="100%" class="qs_answer_table">
{foreach name=answers from=$exp_answers item=item}
<tr>
	<td>
		<b>{$lang.answers.question}:</b> {$item.question}<br />
		<b>{$lang.answers.best_answer}:</b> <font class="best_answer">{$item.answer}</font><br>
		<b>{$lang.answers.in}:</b> {$item.cat_name}
	</td>
</tr>
{/foreach}
</table>
<table width="100%">
<tr>
	<td>
		{foreach name=links_answ from=$exp_link_arr item=item}
		<a href="#" onclick="goToExpAnswerPage({$item.page},{$form.id_user}); return false;" {if $item.selected} style="font-weight:bold; text-decoration:none;"{/if}>{$item.name}</a>
		{/foreach}
		<input type="hidden" id="exp_page_num{$form.id_user}" value="{$form.page}" />
	</td>
</tr>
</table>