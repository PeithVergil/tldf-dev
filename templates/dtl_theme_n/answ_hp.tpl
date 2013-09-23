{include file="$gentemplates/index_top.tpl"}
<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr valign=top><td valign=top>
			<div class="header">{$lang.answers.menu.hp}</div>
			<div class="sep"></div>
	</td></tr>
	<tr><td>{include file="$gentemplates/answ_menu.tpl"}</td></tr>
	<tr><td class="sep"></td></tr>
	<tr>
		<td style="padding:10px 10px 10px 0px;">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td valign="top" style="padding-right:30px;">
					<div class="qa_h_ask_q">{$lang.answers.ask_your_question}</div>
					<div class="sep"></div>
					<form action="{$file_name}" method="post" style="margin:0px;">
					{$form.hiddens}
						<textarea name="question" style="width:300px; height:100px;">{$lang.answers.default_q}</textarea>
						<div style="height:8px;"></div>
						<input type="button" value="{$lang.button.next}" onclick="this.form.submit();" />
					</form>
				</td>
				<td valign="top" style="padding-right:30px;">
					<div class="qa_h_answer_q">{$lang.answers.answer_questions}</div>
					<div class="sep"></div>
					<font style="color:#7f7f7f; font-weight:bold;">{$lang.answers.answer_questions_comment}</font>
					<div class="sep"></div>
					<div align="right">
					<input type="button" value="{$lang.answers.answer_questions_button}" onclick="location.href='{$file_name}?sel=answer'" />
					</div>
				</td>
				<td valign="top">
					<div style="position:relative; top:-13px; display:inline;">
					<div class="content">
						{include file="$gentemplates/answ_info.tpl"}
					</div>
					</div>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr><td height="15"></td></tr>
	<tr><td>
		<div class="header">{$lang.answers.hp_q_header}</div><div class="sep"></div>
	</td></tr>
	<tr><td>
		<table cellpadding="0" cellspacing="1" width="100%" class="qs_table">
		{foreach name=q from=$questions item=item}
			<tr>
				<td width="10%" valign="top">
					{if !$item.root_user}<a href="viewprofile.php?id={$item.id_user}"><img src="{$item.icon_url}" border="0"/></a>
					{else}<img src="{$item.icon_url}" border="0"/>{/if}
					<br />
					{if !$item.root_user}<a href="viewprofile.php?id={$item.id_user}">{$item.login}</a>{else}
					{$item.login}{/if}
					<br />
					{$item.age} {$lang.answers.ans}
				</td>
				<td width="70%" valign="top">
					<b>{$lang.answers.asked}:</b> <font class="hidden_text">{if !$item.life_time}{$lang.answers.asked_now}{else}{$item.life_time} {$lang.answers.ago}{/if}</font>&nbsp;|&nbsp;<b>{$lang.answers.count_answers}:</b> <font class="hidden_text">{$item.answers_count}</font>
					{if $item.is_open == '0'}
						&nbsp;|&nbsp;<b>{$lang.answers.closed}:</b> <font class="hidden_text">{if !$item.closed_time}{$lang.answers.asked_now}{else}{$item.closed_time} {$lang.answers.ago}{/if}</font>
					{/if}
					<br />
					<b>{$lang.answers.question}:</b> {$item.text}<br />
					{if $item.details}<b>{$lang.answers.details}:</b> {$item.details}<br />{/if}
					<b>{$lang.answers.in}:</b> <font class="hidden_text">{$item.path}</font>
					<br />
					{if $item.is_open == '0'}
						<b>{$lang.answers.best_answer}:</b> <font class="best_answer">{$item.best_answer_text}</font>
					{/if}
				</td>
				<td width="10%" valign="top">
					<a id="view_answers_link{$item.id}" href="#" onclick=" viewAnswers({$item.id}); return false;" {if $item.answers_count < '1'}style="display:none"{/if}>{$lang.answers.view_answ}</a>
				</td>
			</tr>
			<tr id="tr_answer{$item.id}" style="display:none;">
				<td colspan="3" class="answers">
					<div id="answers_area{$item.id}"></div>
				</td>
			</tr>
			{foreachelse}
			<tr><td>{$lang.answers.no_questions}</td></tr>
			{/foreach}
		</table>
	</td></tr>
	<tr><td height="20"></td></tr>
	<tr><td>
		<div class="header">{$lang.answers.top_experts}</div>
	</td></tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="1" width="100%" class="qs_table">
			{foreach name=exp from=$experts item=item}
			<tr>
				<td width="2%" valign="top">{$smarty.foreach.exp.iteration}</td>
				<td width="10%" valign="top">
					{if !$item.root_user}<a href="viewprofile.php?id={$item.id_user}"><img src="{$item.icon_url}" border="0"/></a>
					{else}<img src="{$item.icon_url}" border="0"/>{/if}
					<br />
					{if !$item.root_user}<a href="viewprofile.php?id={$item.id_user}">{$item.login}</a>{else}
					{$item.login}{/if}
					<br />
					{$item.age} {$lang.answers.ans}
				</td>
				<td valign="top" width="77%">
					<b>{$lang.answers.best_answ_count}:</b> {$item.answers_count}<br />
					<b>{$lang.answers.best_answ_in}:</b>
					{foreach name=cats from=$item.count_answ_in_cats item=subitem}
					{$subitem.name} ({$subitem.answ_count}){if !$smarty.foreach.cats.last},{/if}
					{/foreach}
				</td>
				<td width="11%">
					<div align="right" style="vertical-align: bottom; height:80px;"><img src="{$site_root}{$template_root}/images/expert.gif" /></div>
					<a id="view_exp_answers_link{$item.id_user}" href="#" onclick=" viewExpAnswers({$item.id_user}); return false;">{$lang.answers.view_exp_answ}</a>
				</td>
			</tr>
			<tr id="tr_exp_answer{$item.id_user}" style="display:none;">
				<td colspan="4" class="answers">
					<div id="exp_answers_area{$item.id_user}"></div>
				</td>
			</tr>
			{foreachelse}
			<tr><td>{$lang.answers.no_experts}</td></tr>
			{/foreach}
			</table>
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
	tr_answer_obj.style.display = 'none';
}

//////experts

function viewExpAnswers(id_user){
	refreshExpAnswers(id_user);
}

function refreshExpAnswers(id_user){
	tr_answer_obj = getById('tr_exp_answer'+id_user);
	tr_answer_obj.style.display = '';
	answers_area_obj = getById('exp_answers_area'+id_user);
	answers_area_obj.style.display = '';
	
	str_adds = '';
	a_page_obj = getById('exp_page_num'+id_user);
	if(a_page_obj!=null){
		str_adds +='&exp_ans_page'+id_user+'='+a_page_obj.value;
	}else{
		str_adds +='&exp_ans_page'+id_user+'=1';
	}
	
	str = 'sel=get_exp_answers&id_user='+id_user+str_adds;
	destination_odj = getById('exp_answers_area'+id_user);
	anisochronous = true;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
}

function goToExpAnswerPage(num,id_user){////////////////////////////////
	a_page_obj = getById('exp_page_num'+id_user);
	a_page_obj.value = num;
	refreshExpAnswers(id_user);
}

function cropExpAnswers(id){
	answers_area_obj = getById('exp_answers_area'+id);
	tr_answer_obj = getById('tr_exp_answer'+id);
	tr_answer_obj.style.display = 'none';
}

function getById(id){
	return document.getElementById(id);
}
{/literal}
</script>
{include file="$gentemplates/index_bottom.tpl"}