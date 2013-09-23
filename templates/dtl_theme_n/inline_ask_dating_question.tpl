{strip}
<div style="width:550px;" class="inline_content">
	<div style="padding-bottom:15px;">
		You Can Ask Any Dating Events Related Question Here:
	</div>
	<form action="dating_events.php" method="post" name="question_form" id="question_form">
		<div id="ajaxwrap-question">
			<table width="500" cellpadding="2" cellspacing="2">
				{if $form.err}
					<tr>
						<td colspan="2" style="padding:0px 11px 12px 0px;">
							<div class="error_ajax">{$form.err}</div>
						</td>
					</tr>
				{/if}
				<tr>
					<td align="right">
						{if $err_field.fname}<span class="error">{/if}
						First Name
						{if $err_field.fname}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" name="fname" value="{$data.fname}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $err_field.sname}<span class="error">{/if}
						Last Name
						{if $err_field.sname}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" name="sname" value="{$data.sname}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $err_field.email}<span class="error">{/if}
						Email
						{if $err_field.email}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" name="email" value="{$data.email}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right" valign="top">
						{if $err_field.question}<span class="error">{/if}
						My Question
						{if $err_field.question}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><textarea name="question" style="height:250px;width:350px">{$data.question}</textarea></td>
				</tr>
				<tr>
					<td align="right">&nbsp;</td>
					<td><input type="submit" name="btnSubmit" class="normal-btn" value="Send" /></td>
				</tr>
			</table>
		</div>
	</form>
</div>
{/strip}
<script>
{literal}
$("#question_form").submit(function(event) {
	event.preventDefault();
	/* get some values from elements on the page: */
	var $form	= $( this ),
		url		= $form.attr( 'action' ),
		sel		= "quesend",
		fname	= $form.find( 'input[name="fname"]' ).val(),
		sname	= $form.find( 'input[name="sname"]' ).val(),
		email	= $form.find( 'input[name="email"]' ).val(),
		question= $form.find( 'textarea[name="question"]' ).val();
	
	/* Send the data using post and put the results in a div */
	$.post( url, { 'sel':sel, 'fname':fname, 'sname':sname, 'email':email, 'question':question },
		   function(data) {
			   //alert("Data Loaded: " + data);
			   var content = $(data).find('#ajaxwrap-question');
			   $("#ajaxwrap-question").html(content);
			   $(".inline_content").scrollTop(0);
			}
		);
	});
{/literal}
</script>