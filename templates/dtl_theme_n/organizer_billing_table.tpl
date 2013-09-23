{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px;"><div style="padding: 5px 0px">{$lang.organizer.page_title}</div></div>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td>
	</tr>
	{/if}
	<tr>
		<td style="padding: 10px 0px 3px 0px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif" hspace="0" vspace="0" border="0" alt=""></td>
				<td style="padding-left: 2px;"><a href="organizer.php">{$lang.button.back}</a></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td valign="top" class="text">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top">
					<div class="content" style=" margin: 0px; padding: 7px 12px 12px 7px;">
					<div class="header">{$lang.organizer.send_req_header}</div>
						<table cellpadding="3" cellspacing="0" border="0">
						<tr>
							<td width="80" class="text_head">{$lang.organizer.date_send}</td>
							<td width="80" class="text_head">{$lang.organizer.count}</td>
							<td width="110" class="text_head">{$lang.organizer.paysysytem}</td>
							<td width="150" class="text_head">{$lang.organizer.period}</td>
							<td width="100" class="text_head">{$lang.organizer.status}</td>
						</tr>
						{foreach item=item from=$sendreq}
						<tr>
							<td class="text">{$item.date_send}</td>
							<td class="text">{$item.count_curr}&nbsp;{$item.currency}</td>
							<td class="text">{$item.paysystem_name}</td>
							<td class="text">{$item.group_amount}&nbsp;{$item.group_period}&nbsp;{$lang.organizer.in}&nbsp;{$item.group_name}</td>
							<td class="text">{$item.status}</td>
						</tr>
						{/foreach}
						</table>
					</div>
					<div class="content" style=" margin-top: 10px; padding: 7px 12px 12px 7px;">
					<div class="header">{$lang.organizer.account_income_spending}</div>
						<table cellpadding="3" cellspacing="0" border="0">
						<tr>
							<td width="80" class="text_head">{$lang.organizer.date_send}</td>
							<td width="80" class="text_head">{$lang.organizer.count}</td>
							<td width="110" class="text_head">{$lang.organizer.paysysytem}</td>
							<td class="text_head">{$lang.organizer.income_type}</td>
						</tr>
   						{foreach item=item from=$entry}
						<tr>
							<td class="text">{$item.date_entry}</td>
							<td class="text">{$item.currency}&nbsp;{$data.cur}</td>
							<td class="text">{$item.type}</td>
							<td class="text">{$item.pay_type}</td>
						</tr>
						{/foreach}
					</table>
					</div>
					<div class="content" style=" margin-top: 10px; padding: 7px 12px 12px 7px;">
					<div class="header">{$lang.organizer.my_account_status}</div>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td valign="top">
									<table cellpadding="3" cellspacing="0" border="0">
										<tr>
											<td>
											<table cellpadding="0" cellspacing="0">
												<tr>
													<td width="160" class="text_head">{$lang.organizer.count}:&nbsp;</td>
													<td class="text">{$data.account}&nbsp;{$data.cur}</td>
												</tr>
												<tr>
													<td style="padding-top: 5px;" class="text_head">{$lang.organizer.date_refresh}:&nbsp;</td>
													<td style="padding-top: 5px;" class="text">{$data.date_refresh}</td>
												</tr>
												{if !$data.free_group}
												<tr>
													<td style="padding-top: 5px;" class="text_head">{$lang.organizer.rest}:&nbsp;</td>
													<td style="padding-top: 5px;" class="text">{$data.user_days_left}&nbsp;{$lang.organizer.days}&nbsp;{$lang.organizer.in}&nbsp;{$data.user_group_name}</td>
												</tr>
												{/if}
											</table>
											</td>
										</tr>
									</table>
								</td>
								<td align="right" valign="top" style="padding-top: 5px;">
								<div>
								<form name="calc" id="calc" style="margin: 0px; padding: 0px;">{strip}
<!--
// Title: Tigra Calculator
// URL: http://www.softcomplex.com/products/tigra_calculator/
// Version: 1.0
// Date: 04/14/2003 (mm/dd/yyyy)
// Note: Permission given to use this script in ANY kind of applications if
//    header lines are left unchanged.
-->
									<table cellpadding="0" cellpadding="0" border="0" width="100%" align="center">
									<tr>
										<td align="center">
										<input type="text" name="monitor" value="0" size="25" maxlength="25" dir="rtl" style="border-top:1px solid #4682B4;border-bottom:1px solid #4682B4;border-left:1px solid #4682B4;border-right:1px solid #4682B4;width:170">
										<input type="hidden" name="input1" value="0">
										</td>
									</tr>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="2" border="0">
										<tr align="center">
											<td><span id="calc_link_0" style="cursor: pointer;"><img id="calc_img_0" src="{$site_root}{$template_root}/calc_img/0_1.gif" border="0" alt="7" width="30" height="21"></span></td>
											<td><span id="calc_link_1" style="cursor: pointer;"><img id="calc_img_1" src="{$site_root}{$template_root}/calc_img/1_1.gif" border="0" alt="8" width="30" height="21"></span></td>
											<td><span id="calc_link_2" style="cursor: pointer;"><img id="calc_img_2" src="{$site_root}{$template_root}/calc_img/2_1.gif" border="0" alt="9" width="30" height="21"></span></td>
											<td><span id="calc_link_3" style="cursor: pointer;"><img id="calc_img_3" src="{$site_root}{$template_root}/calc_img/3_1.gif" border="0" alt="divide" width="30" height="21"></span></td>
											<td><span id="calc_link_4" style="cursor: pointer;"><img id="calc_img_4" src="{$site_root}{$template_root}/calc_img/4_1.gif" border="0" alt="clear" width="30" height="21"></span></td>
										</tr>
										<tr align="center">
											<td><span id="calc_link_5" style="cursor: pointer;"><img id="calc_img_5" src="{$site_root}{$template_root}/calc_img/5_1.gif" border="0" alt="4" width="30" height="21"></span></td>
											<td><span id="calc_link_6" style="cursor: pointer;"><img id="calc_img_6" src="{$site_root}{$template_root}/calc_img/6_1.gif" border="0" alt="5" width="30" height="21"></span></td>
											<td><span id="calc_link_7" style="cursor: pointer;"><img id="calc_img_7" src="{$site_root}{$template_root}/calc_img/7_1.gif" border="0" alt="6" width="30" height="21"></span></td>
											<td><span id="calc_link_8" style="cursor: pointer;"><img id="calc_img_8" src="{$site_root}{$template_root}/calc_img/8_1.gif" border="0" alt="multiply" width="30" height="21"></span></td>
											<td><span id="calc_link_9" style="cursor: pointer;"><img id="calc_img_9" src="{$site_root}{$template_root}/calc_img/9_1.gif" border="0" alt="extract square root" width="30" height="21"></span></td>
										</tr>
										<tr align="center">
											<td><span id="calc_link_10" style="cursor: pointer;"><img id="calc_img_10" src="{$site_root}{$template_root}/calc_img/10_1.gif" border="0" alt="1" width="30" height="21"></span></td>
											<td><span id="calc_link_11" style="cursor: pointer;"><img id="calc_img_11" src="{$site_root}{$template_root}/calc_img/11_1.gif" border="0" alt="2" width="30" height="21"></span></td>
											<td><span id="calc_link_12" style="cursor: pointer;"><img id="calc_img_12" src="{$site_root}{$template_root}/calc_img/12_1.gif" border="0" alt="3" width="30" height="21"></span></td>
											<td><span id="calc_link_13" style="cursor: pointer;"><img id="calc_img_13" src="{$site_root}{$template_root}/calc_img/13_1.gif" border="0" alt="substruct" width="30" height="21"></span></td>
											<td><span id="calc_link_14" style="cursor: pointer;"><img id="calc_img_14" src="{$site_root}{$template_root}/calc_img/14_1.gif" border="0" alt="show result" width="30" height="21"></span></td>
										</tr>
										<tr align="center">
											<td><span id="calc_link_15" style="cursor: pointer;"><img id="calc_img_15" src="{$site_root}{$template_root}/calc_img/15_1.gif" border="0" alt="0" width="30" height="21"></span></td>
											<td><span id="calc_link_16" style="cursor: pointer;"><img id="calc_img_16" src="{$site_root}{$template_root}/calc_img/16_1.gif" border="0" alt="change sign" width="30" height="21"></span></td>
											<td><span id="calc_link_17" style="cursor: pointer;"><img id="calc_img_17" src="{$site_root}{$template_root}/calc_img/17_1.gif" border="0" alt="decimal point" width="30" height="21"></span></td>
											<td><span id="calc_link_18" style="cursor: pointer;"><img id="calc_img_18" src="{$site_root}{$template_root}/calc_img/18_1.gif" border="0" alt="add" width="30" height="21"></span></td>
											<td>&nbsp;</td>
										</tr>
										</table>
										</td>
									</tr>
									</table>
									{/strip}</form>
								</div>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
{literal}
<script language="JavaScript">

var arr_zn = ["7","8","9","/","C","4","5","6","*","sqr","1","2","3","-","=","0","z",".","+"];
var T = TCR, a_img = [], i, j, l;

function ch_img(v1, v2) {
	document.getElementById('calc_img_'+v1).src = '{/literal}{$site_root}{$template_root}/calc_img/{literal}'+v1+'_'+v2+'.gif';
}

T.TCRNew(document.forms['calc'].elements['input1']);
T.TCRmntr('C');
T.t_load = true;
if (T.control_obj.value == '') from_p = '0';
else from_p = T.control_obj.value;
document.forms[0].elements[0].value = from_p;

for (i = 0; i < 19; i++) {
	l = document.getElementById('calc_link_'+i);
	l.onmousedown = Function("ch_img(" + i + ",0)")
	l.onmouseout = Function("ch_img(" + i + ",1)")
	l.onmouseup = l.onmouseover = Function("ch_img(" + i + ",2)")
	l.onclick = l.ondblclick = Function("T.TCRmntr('" + arr_zn[i] + "')");
}
</script>
{/literal}
{include file="$gentemplates/index_bottom.tpl"}