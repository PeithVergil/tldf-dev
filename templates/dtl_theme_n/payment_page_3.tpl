{include file="$gentemplates/index_top.tpl"}
<div class="toc page-simple">
	<div class="upgrade-member tcxf-ch-la">
		<div>
			<div class="callchat_icons">
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a>
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" ></a>
			</div>
		</div>
		<div>
			<!-- begin main cell -->
			<table width="100%" border="0" cellpadding="0" cellspacing="1">
				<tr valign=top>
					<td valign=top>
						<div class="hdr2e">{$lang.payment.allopass_mobile}</div>
						<table cellpadding="0" cellspacing="0" style="border: 0px; width: 300px;">
							<tr>
								<td style="text-align: center; vertical-align: top; background-color: #FFF; width: 300px;">
									<img src="http://www.allopass.com/imgweb/script/uk/acces_title.jpg" alt="Logo" style="width: 300px; height: 25px;" />
								</td>
							</tr>
							<tr>
								<td><br>{$lang.payment.allopass_mobile_text}<br><br></td>
							</tr>
							<tr>
								<td style="text-align: center; vertical-align: top; background-color: #FFF;">
									<span class="text_head" style="vertical-align: top; height: 29px; border: 0px;">{$lang.payment.allopass_select_country}</span>
									{foreach from=$form.flags item=flag}
										<a href="javascript:;" onclick="javascript:window.open('http://www.allopass.com/show_accessv2.php4?CODE_PAYS={$flag}&SITE_ID={$form.SITE_ID}&DOC_ID={$form.DOC_ID.mobile}&LG=uk','phone','toolbar=0,location=0,directories=0,status=0,scrollbars=0,resizable=0,copyhistory=0,menuBar=0,width=300,height=340');"><img src="http://www.allopass.com/imgweb/common/flag_{$flag}.gif" alt="" style="width: 35px; height: 29px; border: 0px;" /></a>
									{/foreach}
								</td>
							</tr>
						</table>
						<br>
						<form name="APform" action="http://www.allopass.com/check/index.php4" method="post">
							<font face="Arial,Helvetica" color="Black" size="11" Style="font-size: 12px;">
								<b>{$lang.payment.allopass_enter_code}</b>
							</font>
							<input type="hidden" name="SITE_ID" value="{$form.SITE_ID}" />
							<input type="hidden" name="DOC_ID" value="{$form.DOC_ID.mobile}" />
							<input type="hidden" name="DATAS" value="{$form.DATAS.mobile}" />
							<input type="hidden" name="RECALL" value="1">
							<input type="hidden" name="LG_SCRIPT" value="uk" /><input type="text" size="8" maxlength="10" value="" name="CODE0" style="background-color: #E7E7E7; border-bottom: 1px solid #000080; border-left: #000080 1px solid; border-right: #000080 1px solid; border-top: #000080 1px solid; color: #000080; cursor: text; font-family: Arial; font-size: 10pt; font-weight: bold; letter-spacing: normal; width: 70px;">
							<input type="button" name="APsub" value="" onclick=" this.form.submit();this.form.APsub.disabled=true;" style="border:0px;margin:0px;padding:0px;width:48px;height:18px;background:url('http://www.allopass.com/imgweb/common/bt_ok.gif');" />
						</form>
					</td>
					<td valign=top width=15>&nbsp;</td>
					<td valign=top>
						<div class="header">{$lang.payment.allopass_credit}</div>
						<table border="0" cellpadding="0" cellspacing="0" width="149" height="50">
							<tr>
								<td width="149" height="50">
									<form name="cben" action="https://secure.allopass.com/cb/subscribe.php4" method="POST" target="DisplaySub">
										<input type="hidden" name="START" value="1">
										<input type="hidden" name="SDOC_ID" value="{$form.DOC_ID.credit}">
										<input type="hidden" name="SSITE_ID" value="{$form.SITE_ID}">
										<input type="hidden" name="DATAS" value="{$form.DATAS.credit}" />
										<input type="hidden" name="LANG" value="L_UK">
										<input type="image" src="http://www.allopass.com/imgweb/script/uk/cb_subscribe_os.gif" onClick="window.open('','DisplaySub','toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable=1,width=600,height=570');" border = 0>
									</form>
								</td>
							</tr>
						</table>
						<form action="http://www.allopass.com/cb/check.php4" method="POST">
							<input type="hidden" name="CDOC_ID" value="{$form.DOC_ID.credit}">
							<input type="hidden" name="CSITE_ID" value="{$form.SITE_ID}">
							<input type="hidden" name="DATAS" value="{$form.DATAS.credit}" />
							<input type="hidden" name="RECALL" value="1">
							<table border="0" cellpadding="0" cellspacing="0" width="149">
								<tr>
									<td colspan="2"><br>{$lang.payment.allopass_credit_text}<br><br></td>
								</tr>
								<tr>
									<td valign="middle">
										<font face="Arial,Helvetica" color="Black" size="11" Style="font-size: 12px;">
											<b>{$lang.payment.allopass_enter_code}</b>
										</font>
									</td>
									<td bgcolor="White">
										<input type="text" size="8" maxlength="10" name="CODE" value="" style="BACKGROUND-COLOR: #E7E7E7; BORDER-BOTTOM: #000080 1px solid; BORDER-LEFT: #000080 1px solid; BORDER-RIGHT: #000080 1px solid; BORDER-TOP: #000080 1px solid; COLOR: #000080; CURSOR: text; FONT-FAMILY: Arial; FONT-SIZE: 10pt; FONT-WEIGHT:bold; LETTER-SPACING: normal; WIDTH:85; TEXT-ALIGN=center;">
										<input type="button" name="APsub" value="" onClick="this.form.submit(); this.form.APsub.disabled=true;" style="border:0px;margin:0px;padding:0px;width:48px; height:18px; background:url('http://www.allopass.com/img/bt_ok.png');">
									</td>
								</tr>
								<tr>
									<td colspan="2" width="300" height="13"><br><img src="http://www.allopass.com/img/cb_bot.gif" alt=""></td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</table>
			<!-- end main cell -->
		</div>
	</div>
</div>
{include file="$gentemplates/index_bottom.tpl"}
