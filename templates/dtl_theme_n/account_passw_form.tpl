{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-profile _extra">
    <p class="_extra-back"><a href="account.php">{$lang.back_to_my_account_page}</a></p>
    <div class="hdr2e" style="padding-top: 12px;">{$lang.account.subheader_changepass}</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
		{*
		<tr><td class="text">{$header.toptext}<br>&nbsp;</td></tr>
		*}
		{if $form.err}
			<tr><td><div class="error_msg">{$form.err}</div></td></tr>
		{/if}
			<tr>
			<td>
				<form name="change_pass" action="account.php" method="post" style="padding: 0px; margin: 0px;">
					<input type="hidden" name="sel" value="passw_change">
					<input type="hidden" name="from" value="{$form.from}">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td height="30" width="150" class="text_head">{$header.info_oldpassword}:</td>
							<td height="30"><input type="password" name="oldpassw" style="width: 150px"></td>
						</tr>
						<tr>
							<td height="30" width="150" class="text_head">{$header.info_new_password}:</td>
							<td height="30"><input type="password" name="passw" style="width: 150px"></td>
						</tr>
						<tr>
							<td height="30" width="150" class="text_head">{$header.info_repassword}:</td>
							<td height="30"><input type="password" name="repassw" style="width: 150px"></td>
						</tr>
						<tr>
							<td height="30" width="150" class="text_head">&nbsp;</td>
							<td height="30">
								<p class="basic-btn_here">
									<b></b><span>
									<input type="submit" class="btn_org" style="width:130px;" value="{$button.save}">
									</span>
								</p>
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>
<div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}