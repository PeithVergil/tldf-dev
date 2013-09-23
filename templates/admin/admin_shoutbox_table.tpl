{include file="$admingentemplates/admin_top.tpl"} <font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.shoutbox_list}</div>
<div id="id_error" class="error_msg" {if !$form.error} style="display:none;"{/if}>* {$form.error}</div>
<br />
<form action="{$file_name}" name="limit_form" method="post" enctype="multipart/form-data">
    {$form.hiddens}
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>{$lang.shoutbox.mess_limit}:
                <input type="text" name="limit" id="id_limit" value="{$limit}" size="3" onkeyup="javascript: checkLimit(this);"/>
                <input id="save_button" type="submit" value="{$lang.button.save}" />
            </td>
        </tr>
    </table>
</form>
<br />
<table class="simple_centered_table" cellpadding="0" cellspacing="1">
    <tr id="id_tr_main" {if !$shoutbox} style="display:none;"{/if}>
        <th>{$lang.shoutbox.user}</th>
        <th>{$lang.shoutbox.message}</th>
		<th>{$lang.shoutbox.status}</th>
        <th>{$lang.shoutbox.date}</th>
        <th></th>
    </tr>
    {foreach name=s from=$shoutbox item=item}
    <tr id="tr{$smarty.foreach.s.iteration}">
        <td><a href="{$item.profile_link}">{$item.login}</a></td>
        <td>
			<span id="id_result{$smarty.foreach.s.iteration}"></span>
			<span id="text{$smarty.foreach.s.iteration}">{$item.text}</span>
			<span id="editor{$smarty.foreach.s.iteration}" style="display:none;">
				<form>
					<textarea id="formtext{$smarty.foreach.s.iteration}">{$item.text}</textarea>
					<input type="hidden" id="id_id{$smarty.foreach.s.iteration}" value="{$item.id}" />
					<input type="hidden" id="val_status{$smarty.foreach.s.iteration}" value="{$item.status}" />
				</form>
            </span>
		</td>
        <td>
			<input type="checkbox" id="status{$smarty.foreach.s.iteration}" value="1" {if $item.status}checked{/if} disabled="disabled" />
		</td>
		<td>{$item.date_add}</td>
        <td style="white-space:nowrap;">
			<a id="edit{$smarty.foreach.s.iteration}" href="#" onclick="javascript: ShowEditor('{$smarty.foreach.s.iteration}'); return false;">{$lang.button.edit}</a>
			<a id="save{$smarty.foreach.s.iteration}" onclick="SubmitForm({$smarty.foreach.s.iteration});" style="display:none;">{$lang.button.save}</a>
			&nbsp;&nbsp;&nbsp;
			<a href="#" onclick="deleteMessage({$smarty.foreach.s.iteration});">{$lang.button.delete}</a>
		</td>
    </tr>
    {/foreach}
    <input type="hidden" name="scount" id="id_count" value="{$smarty.foreach.s.iteration}" />
    <tr id="tr_zero" {if $shoutbox} style="display:none;"{/if}>
        <td class="error_msg" colspan="4" width="100%">{$lang.shoutbox.no_messages}</td>
    </tr>
</table>
<br>
<table cellpadding="0" cellspacing="0">
    <tr>
        <td><b>{$lang.shoutbox.statistic}</b></td>
    </tr>
    <tr>
        <td>{$lang.shoutbox.total_mess}: <b>{$stat.total_mess}</b></td>
    </tr>
    <tr>
        <td>{$lang.shoutbox.most_active}: <b><a href="{$stat.ma_profile_link}">{$stat.ma_login}</a></b></td>
    </tr>
    <!--
	<tr>
		<td><br />{$lang.shoutbox.reset_statistic}: <a href="{$stat.reset_link}">{$lang.shoutbox.reset}</a></td>
	</tr>
	-->
</table>
<script type="text/javascript">
file_name = './{$file_name}';
{literal}

function checkLimit(lim_obj)
{
	save_butt = document.getElementById('save_button');
	if (lim_obj.value != '')
		lim_obj.value = parseInt(lim_obj.value);
	if (isNaN(lim_obj.value))
	{
		lim_obj.value = '';
		save_butt.disabled = true;
	}
	else
		save_butt.disabled = false;
	if (lim_obj.value == '')
		save_butt.disabled = true;
}
count = document.getElementById('id_count').value;
function ShowEditor(id_line)
{
	resetFields();
	document.getElementById('text'+id_line).style.display = 'none';
	document.getElementById('edit'+id_line).style.display = 'none';
	document.getElementById('editor'+id_line).style.display = 'inline';
	document.getElementById('save'+id_line).style.display = 'inline';
	document.getElementById('id_result'+id_line).style.display = 'none';
	document.getElementById('status'+id_line).disabled = false;
}

function resetFields()
{
	for(i=1;i<=count;i++)
	{
		document.getElementById('text'+i).style.display = 'inline';
		document.getElementById('edit'+i).style.display = 'inline';
		document.getElementById('editor'+i).style.display = 'none';
		document.getElementById('save'+i).style.display = 'none';
		document.getElementById('status'+i).disabled = true;
	}
}

function SubmitForm(id_line)
{
	resetFields();

	result_obj = document.getElementById('id_result'+id_line);
	result_obj.style.display = 'inline';

	id = document.getElementById('id_id'+id_line).value;

	text = document.getElementById('formtext'+id_line).value;
	is_checked=false;
	is_checked = document.getElementById('status'+id_line).checked;
	document.getElementById('text'+id_line).innerHTML = text;
	
	status="0";
	if(is_checked)
	{
		status="1";
	}
	
	str = 'sel=save_text&id='+id+'&text='+text+'&status='+status;

	ajaxRequest(file_name, str, result_obj, '', 1);
}

function deleteMessage(id_line)
{
	result_obj = document.getElementById('id_error');
	result_obj.style.display = 'inline';
	result_obj.innerHTML = '';

	id = document.getElementById('id_id'+id_line).value;

	tr = document.getElementById('tr'+id_line);

	str = 'sel=delete&id='+id;
	ajaxRequest(file_name, str, result_obj, '', 1);

	tr.style.display='none';

	del_main = true;
	for(i=1;i<=count;i++)
	{
		if (document.getElementById('tr'+i).style.display != 'none')
			del_main=false;
	}

	if (del_main)
	{
		document.getElementById('id_tr_main').style.display='none';
		document.getElementById('tr_zero').style.display='';
	}
}
{/literal}
</script>
{include file="$admingentemplates/admin_bottom.tpl"}