{include file="$gentemplates/index_top.tpl"}
<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr valign=top><td valign=top>
			<div class="header">{$lang.answers.menu.experts}</div>
			<div class="sep"></div>
	</td></tr>
	<tr><td>{include file="$gentemplates/answ_menu.tpl"}</td></tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td>
			<div id='experts'>
				{include file="$gentemplates/answ_experts_pattern.tpl"}
			</div>
		</td>
	</tr>
	</table>
</td>
<div id="tmp" style="display:none;"></div>
<script type="text/javascript">
file_name = '{$file_name}';
tmp_text = '{$lang.ajax.tmp_text}';
{literal}

function goToExpPage(page){
	page_num_obj = getById('page_num');
	page_num_obj.value = page;
	refreshExperts();
}

function refreshExperts(){
	page_num_obj = getById('page_num');
	if(page_num_obj != null)
		page = page_num_obj.value
	else
		page = 1;

	str = 'sel=get_experts&page='+page;
	destination_odj = getById('experts');
	anisochronous = true;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
}

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
	answers_area_obj.style.display = 'none';
}

function getById(id){
	return document.getElementById(id);
}
{/literal}
</script>

{include file="$gentemplates/index_bottom.tpl"}