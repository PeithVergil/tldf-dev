{include file="$gentemplates/index_top.tpl"}
<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr valign=top><td valign=top>
			<div class="header">{$lang.answers.menu.my_q}</div>
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
            <input type="radio" {if $form.open_sort}checked="checked"{/if} onclick="location.href='{$file_name}?sel=my_q&filter=open&page=1';"/> {$lang.answers.opened_qs}&nbsp;&nbsp;
            <input type="radio" {if $form.closed_sort}checked="checked"{/if} onclick="location.href='{$file_name}?sel=my_q&filter=closed&page=1';"/> {$lang.answers.closed_qs}&nbsp;&nbsp;
        </td>
    </tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="1" width="100%" class="qs_table">
			{foreach name=q from=$questions item=item}
			<tr id="tr_q{$item.id}" style="border:1px solid #000000;">
				<td width="80%" valign="top">
					<b>{$lang.answers.asked}:</b> {if !$item.life_time}{$lang.answers.asked_now}{else}{$item.life_time} {$lang.answers.ago}{/if}&nbsp;|&nbsp;<b>{$lang.answers.count_answers}:</b> {$item.answers_count}
					{if $item.is_open == '0'}
						&nbsp;|&nbsp;
						<b>{$lang.answers.closed}:</b> {if !$item.closed_time}{$lang.answers.asked_now}{else}{$item.closed_time} {$lang.answers.ago}{/if}
					{/if}
					<br />
					<b>{$lang.answers.question}:</b> {$item.text}<br />
					<b>{$lang.answers.details}:</b> {$item.details}<br />
					<b>{$lang.answers.in}:</b> {$item.path}<br />
					{if $item.is_open == '0'}
						<b>{$lang.answers.best_answer}:</b> <font class="best_answer">{$item.best_answer_text}</font>
					{/if}
				</td>
				<td width="10%" valign="top">
					{if $item.answers_count > '0'}<a id="view_answers_link{$item.id}" href="#" onclick=" viewAnswers({$item.id}); return false;">{$lang.answers.view_answ}</a>{/if}
				</td>
			</tr>
			<tr id="tr_answer{$item.id}" style="display:none;">
				<td colspan="2" style="padding-left:50px;">
					<div id="answers_area{$item.id}"></div>
				</td>
			</tr>
			{foreachelse}
			<tr><td colspan="2">{$lang.answers.no_questions}</td></tr>
			{/foreach}
			</table>
		</td>
	</tr>
	<tr><td class="sep"></td></tr>
	<tr>
		<td>
			{foreach name=links from=$link_arr item=item}
			<a href="{$file_name}?sel=my_q&page={$item.page}" {if $item.selected} style="font-weight:bold; text-decoration:none;"{/if}>{$item.name}</a>
			{/foreach}
			<input type="hidden" id="q_page_num" value="{$form.page}" />
		</td>
	</tr>
	</table>
</td>
<div id="tmp" style="display:none;"></div>
<script type="text/javascript">
file_name = '{$file_name}';
tmp_text = '{$lang.ajax.tmp_text}';
{literal}

function viewAnswers(id){
	refreshAnswers(id);
}

function refreshAnswers(id){
	tr_answer_obj = getById('tr_answer'+id);
	tr_answer_obj.style.display = '';
	answers_area_obj = getById('answers_area'+id);
	answers_area_obj.style.display = '';
	
	str_adds = '';
	a_page_obj = getById('answer_page_num'+id);
	if(a_page_obj!=null){
		str_adds +='&ans_page'+id+'='+a_page_obj.value;
	}else{
		str_adds +='&ans_page'+id+'=1';
	}
	
	str = 'sel=get_answers&id_parent='+id+str_adds;
	destination_odj = getById('answers_area'+id);
	anisochronous = true;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
}

function goToAnswerPage(num,id){
	a_page_obj = getById('answer_page_num'+id);
	a_page_obj.value = num;
	refreshAnswers(id);
}

function makeBest(id, id_parent){
	
	str = 'sel=make_best&id='+id+'&id_parent='+id_parent;
	destination_odj = getById('tmp');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	goToAnswerPage('1', id_parent);
}

function cropAnswers(id){
	answers_area_obj = getById('answers_area'+id);
	tr_answer_obj = getById('tr_answer'+id);
	answers_area_obj.style.display = 'none';
}

function getById(id){
	return document.getElementById(id);
}
{/literal}
</script>
{include file="$gentemplates/index_bottom.tpl"}