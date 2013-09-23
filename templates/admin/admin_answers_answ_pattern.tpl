{if $answers}
<a href="#" onclick="cropAnswers({$form.id_parent}); return false;">{$lang.answers.crop_answers}</a>
<div class="sep"></div>
<table cellpadding="0" cellspacing="1" width="100%" class="simple_table">
{foreach name=answers from=$answers item=item}
<tr>
	<td width="10%" valign="top">
		<img src="{$item.icon_url}" border="0"/><br />
		{$item.login}<br />
		{$item.age} {$lang.answers.ans}
	</td>
	<td width="80%" valign="top">
		<b>{$lang.answers.answered}:</b> {if !$item.life_time}{$lang.answers.asked_now}{else}{$item.life_time} {$lang.answers.ago}{/if}<br />
		{if $item.id == $form.id_best}<font class="best_answer" style="font-weight:bold;">{$lang.answers.best_answer_header}</font><br />{/if}
		{$item.text}
	</td>
	<td width="10%" valign="top">
		{if $form.owner && !$form.id_best}
			<a href="#" onClick="makeBest({$item.id},{$form.id_parent}); return false;">{$lang.answers.make_best}</a>
		{/if}
		<a href="#" onClick="delAnswer({$item.id},{$form.id_parent}); return false;">{$lang.button.delete}</a>
	</td>
</tr>
{/foreach}
</table>
<table width="100%">
<tr>
	<td>
		{foreach name=links_answ from=$link_arr_answ item=item}
		<a href="#" onclick="goToAnswerPage({$item.page},{$form.id_parent}); return false;" {if $item.selected} style="font-weight:bold; text-decoration:none;"{/if}>{$item.name}</a>
		{/foreach}
		<input type="hidden" id="answer_page_num{$form.id_parent}" value="{$form.page}" />
	</td>
</tr>
</table>
{/if}