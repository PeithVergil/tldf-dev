// TLDF chat invitation module
// Author: Ralf Strehle

// control
var chat_container = 'popup';				// popup, colorbox
var chat_inviter_open_automatic = false;	// only works with colorbox. when true, the screen of the invited user reloads after 5 seconds.
											// until the reason for this has been found, it's more stable to set it to false

// globals
var chat_id = -1;
var chat_type = '';

function invite_chat(to_id, type)
{
	$.post('videochat_ajax.php', { 'cmd' : 'invite', 'type' : type, 'to_id' : to_id }, function(data) {
		if (data == 'occupied') {
			chat_id = -1;
			chat_type = ''
			alert('The other member is occupied. Please try again later.');
		} else {
			chat_id = data;
			chat_type = type
		}
	});
	$('#chat_invite_dialog h2').html('Please wait while your friend answers your request.').attr('style', 'color: black');
	$('#chat_invite_dialog').dialog('option', 'buttons', { 'Cancel Invitation': function(event, ui) { $(this).dialog('close'); } });
	$('#chat_invite_dialog').dialog('open');
	return false;
}

function cancel_chat()
{
	$.post('videochat_ajax.php', { 'cmd' : 'cancel', 'id' : chat_id });
	chat_id = -1;
}

function accept_chat()
{
	if (chat_container == 'colorbox') {
		$.post('videochat_ajax.php', { 'cmd' : 'accept', 'id' : chat_id }, function(data) {
			// RS 2012-11-23 three lines moved to callback function to avoid racing problem
			open_chat_window();
			$('#new_chat_request').fadeOut(2000);
			setTimeout(check_chat_invite, 5000);
		});
	} else {
		// RS 2012-11-23 the popup blocker intervenes when we use a callback function, so we wait 1000ms
		// to assure that the database update is performed before we call open_chat_window()
		/*
		$.post('videochat_ajax.php', { 'cmd' : 'accept', 'id' : chat_id });
		ms = 1000;
		ms += new Date().getTime();
		while (new Date() < ms){}
		//alert('stop');
		*/
		// synchronous post
		$.ajax({type: 'POST', url: 'videochat_ajax.php', data: { 'cmd' : 'accept', 'id' : chat_id }, async: false});
		open_chat_window();
		$('#new_chat_request').fadeOut(2000);
		setTimeout(check_chat_invite, 5000);
	}
}

function deny_chat()
{
	$.post('videochat_ajax.php', { 'cmd' : 'deny', 'id' : chat_id });
	chat_id = -1;
	chat_type = '';
	$('#new_chat_request').fadeOut(2000);
	setTimeout(check_chat_invite, 5000);
}

function open_chat_window()
{
	if (chat_type == 'video') {
		if (chat_container == 'colorbox') {
			$.colorbox({ href:'videochat_start.php?id='+chat_id+'&banner=0', iframe:true, height:'100%', innerWidth:'1000', overlayClose:false, escKey:false });
		} else {
			window.open('videochat_start.php?id='+chat_id+'&banner=1', 'videochat', 'width=972,height=543,resizable=no,scrollbar=no,status=yes,toolbar=no');
		}
	} else if (chat_type == 'text') {
		if (chat_container == 'colorbox') {
			$.colorbox({ href:'textchat_start.php?id='+chat_id+'&banner=0', iframe:true, height:'100%', innerWidth:'700', overlayClose:false, escKey:false });
		} else {
			window.open('textchat_start.php?id='+chat_id+'&banner=1', 'textchat', 'width=672,height=543,resizable=no,scrollbar=no,status=yes,toolbar=no');
		}
	}
}

function check_chat_invite()
{
	if (chat_id != -1) {
		$.get('videochat_ajax.php?cmd=check_status&id='+chat_id, function(data) {
			if (data == 'Denied') {
				$('#chat_invite_dialog h2').html('The other member denied your chat invitation.').attr('style', 'color: red');
				$('#chat_invite_dialog').dialog('option', 'buttons', { 'Close': function(event, ui) { $(this).dialog('close'); } });
			} else if (data == 'Accepted') {
				if (chat_container == 'colorbox' && chat_inviter_open_automatic) {
					open_chat_window();
					$('#chat_invite_dialog').dialog('close');
				} else {
					$('#chat_invite_dialog h2').html('The other member accepted your chat invitation.').attr('style', 'color: green');
					$('#chat_invite_dialog').dialog('option', 'buttons', { 'Start Chat': function(event, ui) { open_chat_window(); $(this).dialog('close'); } });
				}
			}
		});
	}
	$.getJSON('videochat_ajax.php?cmd=check_invite', function(data) {
		if (data.id != -1) {
			chat_id = data.id;
			chat_type = data.request_type;
			$('#new_chat_request_name').html(data.fname);
			if (chat_type == 'video') {
				$('#new_chat_request_msg').html('has invited you to open webcam.');
			} else {
				$('#new_chat_request_msg').html('has invited you for a text chat.');
			}
			$('#new_chat_request_cancelled').hide();
			$('#new_chat_request').fadeIn(2000);
			setTimeout(check_chat_cancel, 5000);
		} else {
			setTimeout(check_chat_invite, 5000);
		}
	});
}

function check_chat_cancel()
{
	$.get('videochat_ajax.php?cmd=check_status&id='+chat_id, function(data) {
		if (data == 'Cancelled' || data == 'no_request') {
			$('#new_chat_request_cancelled').show();
			$('#new_chat_request').fadeOut(4000);
			setTimeout(check_chat_invite, 5000);
		} else {
			setTimeout(check_chat_cancel, 5000);
		}
	});
}

$(document).ready(function () {
	$('#chat_invite_dialog').dialog({
		autoOpen: false,
		modal: true,
		width: 600,
		close: function(event, ui) { cancel_chat(); }
	});
	setTimeout(check_chat_invite, 1000);
});
