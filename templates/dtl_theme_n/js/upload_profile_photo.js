/**
 * CUSTOMIZATION
 * - uploadSuccessRedirect
 * - additional input validation
 * - uploadify formData
 **/

function custom_file_upload(max_file_size_bytes, max_file_size_string, file_exts, file_types, file_type_desc, button_text, session_id)
{
	var has_XHR2 = hasXHR2();
	var has_Flash = hasFlash();
	
	//alert(has_XHR2 + '|' + has_Flash);
	
	var httpRequest;
	var selectedFile = null;
	
	var form = document.getElementById('upload_form');
	
	var uploadSuccessRedirect = 'myprofile.php?sel=upload_photo';
	
	if (has_XHR2) {
		$('#file_upload').change(function() {
			$('#file_info').fadeIn();
			$('#file_name').html(this.value);
		});
	}
	
	$('#submit').click(function(e) {
		if (has_XHR2 || has_Flash) {
			e.preventDefault();
		}
		
		if (has_XHR2 || !has_Flash) {
			if ($('#file_upload').val().trim() == '') {
				jAlert('Please select a file.');
				e.preventDefault();
				return;
			}
		} else {
			if (selectedFile === null) {
				jAlert('Please select a file.');
				e.preventDefault();
				return;
			}
		}
		
		if (has_XHR2) {
			var file_upload = document.getElementById('file_upload');
			var file = file_upload.files[0];
			
			var ext = getFileExtension(file.name);
			file_exts_check = file_exts.replace(/\s/g, '') + ';';
			if (file_exts_check.indexOf('.' + ext + ';') < 0) {
				jAlert('Wrong File Extension: <b>' + ext + '</b><br><br>File must have one of the following extensions:<br><br>' + file_exts.replace(/\*\./g, ''));
				e.preventDefault();
				return;
			}
			
			file_types_check = file_types.replace(/\s/g, '') + ';';
			if (file_types_check.indexOf(file.type + ';') < 0) {
				jAlert('Wrong File Type: <b>' + file.type + '</b><br><br>File must be of one of the following types:<br><br>' + file_types);
				e.preventDefault();
				return;
			}
			
			if (file.size > max_file_size_bytes) {
				jAlert('File size must not exceed ' + max_file_size_string + 'B');
				e.preventDefault();
				return;
			}
			
			var progress = document.getElementById('prog');
			var formData = new FormData(form);
			
			httpRequest = new XMLHttpRequest();
			
			var upload = httpRequest.upload;
			upload.onprogress = function(e) {
				progress.max = e.total;
				progress.value = e.loaded;
			}
			upload.onload = function(e) {
				progress.value = 1;
				progress.max = 1;
			}
			
			httpRequest.onreadystatechange = handleResponse;
			httpRequest.open('POST', form.action + '?act=ajax');
			httpRequest.send(formData);
		} else if (has_Flash) {
			$('#file_upload').uploadify('upload', '*');
		} else {
			// form is submitted with normal POST
		}
	});

	function handleResponse() {
		if (httpRequest.readyState == 4 && httpRequest.status == 200) {
			var response = httpRequest.responseText;
			// alert(response);
			if (response.slice(0, 2) == 'OK') {
				if (typeof uploadSuccessRedirect == 'function') {
					uploadSuccessRedirect();
					jAlert(response.slice(3));
				} else {
					window.location.href = uploadSuccessRedirect;
				}
			} else {
				jAlert(response);
			}
		}
	}

	/**
	 * UPLOADIFY
	 **/

	if (!has_XHR2 && has_Flash) {
		$(function() {
			$('#file_upload').uploadify({
				'swf'      			: SITE_ROOT + '/javascript/uploadify-3.2/uploadify.swf',
				'uploader'			: form.action + '?act=flash',
				'fileObjName'		: 'file_upload',
				'fileSizeLimit'		: max_file_size_string + 'B',
				'fileTypeExts'		: file_exts,
				'fileTypeDesc'		: file_type_desc,
				'buttonText'		: button_text,
				/*
				 * don't use limits as they trigger an error when replacing a selected file
				 * 'uploadLimit'	: 1,
				 * 'queueSizeLimit'	: 1,
				 */
				'auto'     			: false,
				'multi'				: false,
				'requeueErrors'		: true,
				'removeCompleted'	: true,
				'onSelect'			: function(file) {
					if (selectedFile !== null) {
						$('#file_upload').uploadify('cancel', selectedFile.id, true);
						$("#" + selectedFile.id).hide();
					}
					selectedFile = file;
				},
				'onUploadStart'		: function(file) {
					$('#file_upload').uploadify('settings', 'formData',
						{
							'sel'			: 'save_photo',
							'session_id'	: session_id,
							'timestamp' 	: form.timestamp.value,
							'token'     	: form.token.value
						}
					);
				},
				'onUploadError'		: function(file, errorCode, errorMsg, errorString) {
					if (errorString != 'Cancelled') {
						jAlert('The file ' + file.name + ' could not be uploaded: ' + errorMsg);
					}
				},
				'onUploadSuccess'	: function(file, data, response) {
					//alert(data);
					if (data.slice(0, 2) == 'OK') {
						if (typeof uploadSuccessRedirect == 'function') {
							uploadSuccessRedirect();
							jAlert(data.slice(3));
						} else {
							window.location.href = uploadSuccessRedirect;
						}
					} else {
						jAlert(data);
					}
				}
			});
		});
	}
}
