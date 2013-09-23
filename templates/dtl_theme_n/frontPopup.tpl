{include file="$gentemplates/index_top_popup.tpl"}

			<td align="left">
				<div id="popup_front">
					<div class= "register_pane" style="float: left; margin: 0; width: 365px;">
						<div class="regis-home-title" style="padding:0;">
							<span style="color:#E13E8D">MEET</span> {$data.fname} Now. <br>
							{*<span style="padding-left:95px;">Thailadydatefinder.com</span>*}
						</div>
						<br>
						<div id="register-form-sidebar">
							{include file="$gentemplates/registration_form.tpl"}
						</div>
						
						<div id="user-box1">
							<strong class="login-text tcxf-ch-la"><span>{$lang.already_member}</span><a id="login" user_id={$form.user_id}>[LOGIN]</a></strong>
						</div>
						
					</div>
					<div class="sel_profile" style="width:350px; margin-left: 350px; padding-top:20px; text-align: center;">
						<img src="{$icon_path}" width="250" height="250px" />
					</div>
					
					<div class="box-frame-popup" style="width:270px;">
						<div class="hdr2">Quick Stat</div>
						<p class="text_head">
							<label>Name:</label>&nbsp;{$data1.fname}&nbsp;{$data1.sname}
						</p>
						<p class="text_head">
							<label>Gender:</label>&nbsp;{if $data1.gender ==1}{$lang.gender.1}{else}{$lang.gender.2}{/if}
						</p>
						<p class="text_head">
							<label>Status:</label>&nbsp;{if $data_1.couple}{$lang.users.couple}{else}{$lang.users.single}{/if}
						</p>
						<p class="text_head">
							<label>Nationality:</label>&nbsp;{$data1.nationality_match}
						</p>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
{literal}
	$('#login').live('click',function(){
			var id = $(this).attr('user_id');
				window.top.location.href = "index.php?sel=login&id="+id;
	});

$.alerts.horizontalOffset = 0;
$.alerts.verticalOffset = -100;
msg = $.trim($('.error_msg').html());
if (msg != '') {
	jAlert(msg);
}
{/literal}
</script>
</body>
</html>
