{strip}
<div style="width:550px;" class="inline_content">
	<div style="padding-bottom:15px;font-weight:bold;">
		{if $form.vid == 1}
			{$lang.dating_video.title1}
		{elseif $form.vid == 2}
			{$lang.dating_video.title2}
		{elseif $form.vid == 3}
			{$lang.dating_video.title3}
		{/if}
	</div>
	<form action="dating_events.php" method="post" name="comment_form" id="comment_form">
		<div id="ajaxwrap-comment">
			<input type="hidden" name="videoid" value="{$form.vid}" />
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
						{$lang.dating_video_comment.fname}
						{if $err_field.fname}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" name="fname" value="{$data.fname}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $err_field.sname}<span class="error">{/if}
						{$lang.dating_video_comment.sname}
						{if $err_field.sname}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" name="sname" value="{$data.sname}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $err_field.email}<span class="error">{/if}
						{$lang.dating_video_comment.email}
						{if $err_field.email}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" name="email" value="{$data.email}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right" valign="top">
						{if $err_field.comment}<span class="error">{/if}
						{$lang.dating_video_comment.comment}
						{if $err_field.comment}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><textarea name="comment" style="height:250px;width:350px">{$data.comment}</textarea></td>
				</tr>
				<tr>
					<td align="right">&nbsp;</td>
					<td><input type="submit" name="btnSubmit" class="normal-btn" value="{$lang.dating_video_comment.send}" /></td>
				</tr>
			</table>
		</div>
	</form>
</div>
{/strip}
<script>
{literal}
$("#comment_form").submit(function(event) {
	event.preventDefault();
	/* get some values from elements on the page: */
	var $form	= $( this ),
		url		= $form.attr( 'action' ),
		sel		= "sendcomm",
		videoid	= $form.find( 'input[name="videoid"]' ).val(),
		fname	= $form.find( 'input[name="fname"]' ).val(),
		sname	= $form.find( 'input[name="sname"]' ).val(),
		email	= $form.find( 'input[name="email"]' ).val(),
		comment	= $form.find( 'textarea[name="comment"]' ).val();
	
	/* Send the data using post and put the results in a div */
	$.post( url, { 'sel':sel, 'videoid':videoid, 'fname':fname, 'sname':sname, 'email':email, 'comment':comment },
		   function( data ) {
			   //alert("Data Loaded: " + data);
			   var content = $(data).find('#ajaxwrap-comment');
			   $("#ajaxwrap-comment").html( content );
			   $(".inline_content").scrollTop(0);
			}
		);
	});
{/literal}
</script>