{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	<div class="error_msg" id="err">{$form.err}</div>
	<div class="upgrade-member tcxf-ch-la">
		<div>
			<div class="callchat_icons">
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a> 
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" ></a>
			</div>
		</div>
		<div>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" width="458">
						<div id="index_login_page">
							{if $header.toptext}
								<p>{$header.toptext}</p>
							{/if}
							<form action="index.php" method="post" name="login_form" style="margin:0px">
								<div class="hdr1">{$lang.top.login}</div>
								<table align="center" width="100%" cellpadding="0" cellspacing="3" border="0">
									<tr>
										<td class="txtwhite"><b>{$lang.home_page.username}:&nbsp;</b></td>
										<td align="left"><input type="text" name="login_lg" id="login_lg" style="width:150px;"></td>
									</tr>
									<tr>
										<td class="txtwhite"><b>{$lang.home_page.login_password}:&nbsp;</b></td>
										<td align="left"><input type="password" name="pass_lg" id="pass_lg" style="width:150px;" onkeypress="if(event.keyCode == 13) CheckValid();"></td>
									</tr>
									<tr>
										<td><input type="hidden" style="width:130px;" name="pid" id="pid" value="{$form.pid}"></td>
										<td align="left" class="txtwhite" style="padding-top:5px;">
											<table cellpadding="0" cellspacing="0">
												<tr>
													<td valign="top" style="padding-right:5px;"><input type="checkbox" name="remember_me" value="1" {* checked="checked" *}></td>
													<td>{$lang.home_page.keep_me_signed_in}</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
								<p class="basic-btn_here ml35"><b>&nbsp;</b><span><input type="button" class="btn_org" style="width:70px;" onclick="CheckValid();" value="{$header.login}" /></span></p>
								<div class="a_center"><strong><a href="#" onclick="window.open('lost_pass.php', 'lost_passw', 'height=350,width=450,resizable=no,scrollbars=no,menubar=no,status=no,left=100,top=20'); return false;" class="txtwhite uline txtsmall" >{$lang.home_page.forgot_your_password}</a></strong></div>
							</form>
						</div>
						<div>&nbsp;</div>
						<div id="text_and_button" class="content_2">
							<div class="hdr2e a_center">{$lang.home_page.register}</div>
							<div>
								<div class="hdr2x a_center g_text">{$header.profit_text}</div>
								<div>
									<p class="basic-btn_here ml35"><b>&nbsp;</b><span><input type="button" class="btn_org" style="width:70px;" onclick="location.href='index.php';" value="{$lang.home_page.sign_in}"></span></p>
								</div>
							</div>
						</div>
					</td>
					<td valign="top" align="center" width="360">
						<img src="{$site_root}{$template_root}/images/happy_couples.png" alt="" />
					</td>
				</tr>
				<tr><td height="30">&nbsp;</td></tr>
			</table>
		</div>
	</div>
</div>
{literal}
<script type="text/javascript">
function CheckValid()
{
	f = document.login_form;
	if (f.login_lg.value == '' && f.pass_lg.value == '') {
		msg = '{/literal}{$err.invalid_login_passw}{literal}';
		// $('#err').html(msg).show();
		jAlert(msg, 'Alert', function() { f.login_lg.focus(); });
		return;
	}
	if (f.login_lg.value == '') {
		msg = '{/literal}{$err.invalid_login}{literal}';
		// $('#err').html(msg).show();
		jAlert(msg, 'Alert', function() { f.login_lg.focus(); });
		return;
	}
	if (f.pass_lg.value == '') {
		msg = '{/literal}{$err.invalid_passw}{literal}';
		// $('#err').html(msg).show();
		jAlert(msg, 'Alert', function() { f.pass_lg.focus(); });
		return;
	}
	f.submit();
}
function HideEmptyPopup()
{
	obj = document.getElementById('err');
	if (obj.innerHTML == "") {
		obj.style.display = "none";
	}
}
$(document).ready(function() {
	HideEmptyPopup();
	document.getElementById('login_lg').focus();
});
</script>
{/literal}
{/strip}
{include file="$gentemplates/index_bottom.tpl"}