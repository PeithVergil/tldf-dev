<div>
	<h2 class="pop-title">{$lang.section.contact_us}</h2>
	<form action="contact.php?sel=ajaxsend" method="post" name="contact_form" id="contact_form">
		<div id="ajaxwrap-contact">
			<table width="430" cellpadding="3" cellspacing="0">
				{if $form.err}
					<tr>
						<td colspan="2" style="padding:0px 11px 12px 0px;">
							<div class="error_ajax">{$form.err}</div>
						</td>
					</tr>
				{/if}
				<tr>
					<td width="110">{$lang.contact_us.name}: <font class="error">*</font></td>
					<td>
						<input type="text" name="fname" maxlength="50" value="{$data.fname}" style="width:300px">
					</td>
				</tr>
				<tr>
					<td>{$lang.contact_us.email}: <font class="error">*</font></td>
					<td>
						<input type="text" name="email" maxlength="50" value="{$data.email}" style="width:300px">
					</td>
				</tr>
				<tr>
					<td>{$lang.contact_us.subject}: <font class="error">*</font></td>
					<td>
						<input type="text" name="subject" maxlength="70" value="{$data.subject}" style="width:300px">
					</td>
				</tr>
				<tr>
					<td>{$lang.contact_us.message}: <font class="error">*</font></td>
					<td>
						<textarea name="message" class="blackborder" style="width:305px; height:150px;">{$data.message}</textarea>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<p class="basic-btn_here">
							<b></b><span>
							<input type="submit" value="{$button.send}" />
							</span>
						</p>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>
<script>
{literal}
	/* attach a submit handler to the form */
	$("#contact_form").submit(function(event) {
		/* stop form from submitting normally */
		event.preventDefault();
		/* get some values from elements on the page: */
		var $form	= $( this ),
			url		= $form.attr( 'action' ),
			fnam	= $form.find( 'input[name="fname"]' ).val(),
			emal 	= $form.find( 'input[name="email"]' ).val(),
			subj	= $form.find( 'input[name="subject"]' ).val(),
			mesg	= $form.find( 'textarea[name="message"]' ).val();
		
		/* Send the data using post and put the results in a div */
		// $.post(url, { name: "John", email: "sdg sdg" },   function(data) { alert("Data Loaded: " + data); });
		// $.post("test.php", $("#testform").serialize());
		$.post( url, { fname:fnam, email:emal, subject:subj, message:mesg },
			function( data ) {
				//alert("Data Loaded: " + data);
				var content = $( data ).find( '#ajaxwrap-contact' );
				$( "#ajaxwrap-contact" ).empty().append( content );
			}
		);
	});
{/literal}
</script>