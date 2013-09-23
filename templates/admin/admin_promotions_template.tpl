{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font>
<font class=red_sub_header>&nbsp;|&nbsp;{if $form.mode == 'edit'}{$header.edit_temp}{else}{$header.create_new}{/if}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.promotions}</div>
<table width="100%">
	<tr valign=top>
		<!-- user_select -->
		<td>
			<form name="add_form" action="{$form.action}" method="post">
				{$form.add_hiddens}
				<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
					<tr>
						<td class="main_header_text" >
							Title:<br />
							<input type="text" name="title" style="width:325px;" value="{$form.title}" >
						</td>
					</tr>
					<tr>
						<td class="main_header_text" >
							Header:<br />
							<select name="head" style="width:150px;">
								<option value="1">Default Header</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="main_header_text" >
							Body Text:<br />
							{if RICH_TEXT_EDITOR == 'SPAW-1' || RICH_TEXT_EDITOR == 'SPAW-2'}
								{$editor}
							{elseif RICH_TEXT_EDITOR == 'TINYMCE'}
								<script type="text/javascript" src="{$site_root}/javascript/tiny_mce-3.5.8/tiny_mce.js"></script>
								<script type="text/javascript">
								tinyMCE.init({ldelim}
									mode: "exact",
									elements: "tinymce_body_text",
									oninit: myInit,
									{include file="$admingentemplates/admin_tiny_mce_slim.tpl"}
								{rdelim});
								function myInit() {ldelim}
									tinyMCE.get('tinymce_body_text').setContent('{$form.body_text|escape:javascript}');
								{rdelim}
								</script>
								<textarea name="body_text" id="tinymce_body_text" rows="20" cols="60" style="width:445px;height:350px;"></textarea>
							{else}
								<textarea name="body_text" rows="20" cols="60" style="width:445px;height:350px;">{$form.body_text}</textarea>
							{/if}
						</td>
					</tr>
					<tr>
						<td class="main_header_text" >
							Footer text:<br />
							<textarea name="footer_text" id="footer_text" style="width:445px; height:150px;" >{$form.footer_text}</textarea>
						</td>
					</tr>
					<tr>
						<td align="center" style="padding:5px;">
							<input type="button" value="{$header.preview}" class="button" onclick="javascript:preview();">
							&nbsp;
							<input type="submit" value="{$header.save_template}" class="button">
							&nbsp;
							<input type="button" value="{$header.cancel}" class="button" onclick="javascript: location.href='{$form.back}'">
						</td>
					</tr>
				</table>
			</form>
		</td>
		<!-- /user select -->
		<!-- main user row -->
		<td width="650">
			<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
				<tr>
					<td><b>SUBJECT:</b> {$form.title}</td>
				</tr>
				<tr>
					<td bgcolor="#eeeeee">
						<div><img src="../promo_images/header_1.jpg" alt=""></div>
						<div id="promo_template_wrap">
							<table cellpadding="5" cellspacing="0">
								<tr>
									<td>
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td valign="top" style="padding-right:25px;">
													<img src="../promo_images/nathamon.png" alt="Nathamon">
												</td>
												<td valign="top">
													<div>Hello <b>User Name</b>,</div>
													<div id="spn_body_text">{$form.body_text}</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td><img src="../promo_images/sample_user_match.jpg" alt=""></td>
								</tr>
								<tr>
									<td>
										<div id="spn_footer_text">{$form.footer_text}</div>
										<div>Kind Regards,<br /> The <b>ThaiLadyDateFinder&#8482;</b> Team</div>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
			</table>
		<td>
	</tr>
</table>
{literal}
<script>
function preview()
{
	//document.getElementById('body_text').style.display= 'block';
	
	var myIFrame = document.getElementById('body_text_rEdit');
	var strBdyText = myIFrame.contentWindow.document.body.innerHTML;
	//alert('content: ' + strBdyText);
	
	document.getElementById('spn_body_text').innerHTML = "";
	document.getElementById('spn_body_text').innerHTML = strBdyText;
	
	var strFtrText = document.getElementById('footer_text').value;
	document.getElementById('spn_footer_text').innerHTML = strFtrText;
}

/*
function resetText()
{
	alert('ads');
	var myIFrame = document.getElementById('body_text_rEdit');
	var strBText = myIFrame.contentWindow.document.body.innerHTML;
	
	document.getElementById('body_text').value = strBText;
}
*/
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}