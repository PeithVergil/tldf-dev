<?php
  global $config;
  /*
  $_GET["act"]="send";
  $_GET["mess_count"]=1;
  $_GET["mess_type0"]=4;
  $_REQUEST["mess_text0"]="First restart chanel POPUP.";
  $_GET["auth_session"]=1;
  ob_start();
  include "wc_config.php";
  include "wc_functions.php";
  include "messages.php"; 
  ob_end_clean(); 
  */
?>
<script type="text/javascript">
var isInternetExplorer = navigator.appName.indexOf("Microsoft") != -1;
// Handle all the FSCommand messages in a Flash movie.

function DoMyFSCommand(command, args) 
{
	if (command=="ShowFlashObject")
	{
		ShowFlashObject(args);
	}
	else if (command=="SetUnreadedMessagesCount")
	{
		SetUnreadedMessagesCount(args);
	}
	else if (command=="ShowImWindow")
	{
		ShowImWindow();
	}
}

function flash_im_popup_obj_DoFSCommand(command, args) 
{
	DoMyFSCommand(command, args);
}
function flash_im_popup_emb_DoFSCommand(command, args) 
{
	DoMyFSCommand(command, args);
}

// Hook for Internet Explorer.
if (navigator.appName && navigator.appName.indexOf("Microsoft") != -1 && navigator.userAgent.indexOf("Windows") != -1 && navigator.userAgent.indexOf("Windows 3.1") == -1) 
{
	document.write('<script language=\"VBScript\"\>\n');
	document.write('On Error Resume Next\n');
	document.write('Sub flash_im_popup_obj_FSCommand(ByVal command, ByVal args)\n');
	document.write('	Call flash_im_popup_obj_DoFSCommand(command, args)\n');
	document.write('End Sub\n');
	document.write('<\/script\>\n');
}

function GetMovieObj() 
{
	if (window.ActiveXObject)
	{
		if (document.getElementById) {   
			return document.getElementById('flash_im_popup_obj');
		}
		if (document.all) {       
			return document.all.flash_im_popup_obj;
		}
		if (document.layers) {   
			return document.name.flash_im_popup_obj;
		}
	}
    else
	{
		if (document.getElementById) {   
			return document.getElementById('flash_im_popup_emb');
		}
		if (document.all) {       
			return document.all.flash_im_popup_emb;
		}
		if (document.layers) {   
			return document.name.flash_im_popup_emb;
		}
    }
}

function ShowFlashObject(show_flag)
{
	var flash_obj = GetMovieObj();
	if (show_flag)
	{
		if (window.opera) {
			flash_obj.style = "width:380; height:80";
		} else {
			flash_obj.width=380;
			flash_obj.height=80;
		}
	}
	else
	{
		if (window.opera) {
			flash_obj.style = "width:1; height:1";
		} else {
			flash_obj.width=1;
			flash_obj.height=1;
		}
    }
}

function SetUnreadedMessagesCount(count)
{
	//alert(count);
	document.getElementById("imessages").style.display = "block";
	document.getElementById("imessages_count").innerHTML = count;
}

function flash_loading_finished()
{
	//ShowFlashObject(1);
};

function ShowImWindow()
{
	// Show Im messenger window
	window.open('<?php echo $config["site_root"]?>/w_communicator/flash_im.php','flash_chat','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');
};

</script>
<div id="div_flash_block" style="position: absolute; left:400px; top:0;"><!-- removed by ralf to produce valid HTML:  name="div_flash_block" -->
<script type="text/javascript" charset="windows-1251" src="<?php echo $config["site_root"]?>/w_communicator/popup.js.php"></script>
</div>
