{strip}
{include file="$gentemplates/index_top_popup.tpl"}
			<td width="100%" valign=top>
				<div class="header">{$lang.cards.select_user}</div>
				<div style="padding-top: 15px;"> <span>
					<input type="text" id="user_name" name="user_name" style="width: 150px;">
					</span>&nbsp; <span>
					<input type="button" value="{$lang.button.check}" onclick="ajaxRequest('ecards.php?sel=check_user&fname='+document.getElementById('user_name').value+'&id_card={$id_card}{if $id_order}&id_order={$id_order}{/if}&', 'null', document.getElementById('result_div'), '{$lang.cards.admin.loading}...&nbsp;<img src=\'{$site_root}{$template_root}/images/ajax-loader.gif\'>', true);">
					</span> </div>
				<div id="result_div" style="padding-top: 15px; padding-right: 10px;"></div>
			</td>
		</tr>
	</table>
</div>
{if $tldf_offline}
	<script type="text/javascript" src="{$site_root}/javascript/jquery-ui-1.8.21.min.js"></script>
{else}
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
{/if}
<script type="text/javascript">
/* old search result list, replaced with jquery-ui autocomplete */
function ChooseUserAct(id_user, login, age, icon_path, message) {ldelim}
	/* $.ajax({ldelim} url: 'ecards.php?sel=save_user&id_user_to='+id_user+'&id_card={$id_card}&id_order={$id_order}' {rdelim}); */
	var result_html = '';
	result_html = result_html + '<div style="padding: 10px; margin: 5px 0px 15px 0px; background-color: #cccccc;">';
	result_html = result_html + '<table cellpadding="0" cellspacing="0"><tr>';
	result_html = result_html + '<td valign="top"><img src="' + icon_path + '" class="icon"></td>';
	result_html = result_html + '<td valign="top" style="padding-left: 15px;"><div><b>' + login + '</b>, ' + age + '{$lang.home_page.ans}</div></td>';
	result_html = result_html + '</tr></table></div>';
	/*
	result_html = result_html + '<div style="padding-top: 5px;">';
	result_html = result_html + '<input type="button" value="{$lang.button.confirm_and_send}" onclick="document.location.href=\'ecards.php?sel=order_form&id_order={$id_order}&id_user_to='+id_user+'\'">';
	result_html = result_html + '</div>';
	result_html = result_html + '<div style="padding-top: 5px;">';
	result_html = result_html + '<input type="button" value="{$lang.button.edit_card}" onclick="document.location.href=\'ecards.php?sel=card&id_order={$id_order}&amp;id_user_to='+id_user+'\'">';
	result_html = result_html + '</div>';
	*/
	parent.document.getElementById('id_user_to').value=id_user;
	parent.document.getElementById('result_div').innerHTML = result_html;
	parent.document.getElementById('message_div').innerHTML = message;
	parent.GB_hide();
	return;
{rdelim}
</script>
<script type="text/javascript">
{literal}
$(function() {
	$("#user_name").autocomplete({
		source: "ecards.php?sel=users_autocomplete",
		minLength: 2,
		select: function(event, ui) {
			if (ui.item) {
				/* UserUpdate(ui.item.id); */
			} else {
				alert("Nothing selected, input was " + this.value);
			}
		}
	});
	$("#user_name").focus();
});
{/literal}
</script>
</body>
</html>
{/strip}