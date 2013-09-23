<?php
	// 뢠  ⥪騥 ᮥ 
	$_GET["part"]="message";
	$_GET["act"]="send";
	$_GET["mess_count"]=1;
	$_GET["mess_type0"]=4;
	$_REQUEST["mess_text0"]="First restart chanel.";
	$_GET["auth_session"]=1;
	ob_start();
	include "communicator.php"; 
	
	global $g_user_name;
	$id_user = get_user_id($g_user_name);
	
	$invite_and_send_user_name="";    //    । 짮⥫
	if ((isset($_GET["send_user"]))&&($_GET["send_user"]!=""))
	{	//   quick search - 뢠   । 짮⥫
		$send_user_id = (int)$_GET["send_user"];
		$invite_and_send_user_name = get_user_login($send_user_id);
		if ($invite_and_send_user_name!="")
		{
			$_GET["invite_user_name"]=$invite_and_send_user_name;
			//VP checking user in Connections list
			$strSQL2 = 'SELECT COUNT(*) FROM '.CONNECTIONS_TABLE.'
						WHERE id_friend = "'.$send_user_id.'" AND id_user = "'.$id_user.'" AND status = "1"
						OR id_user = "'.$send_user_id.'" AND id_friend = "'.$id_user.'" AND status = "1"';
			$is_connected = $dbconn->getOne($strSQL2);
			if (!empty($is_connected))
			{
				invite_user();
			}
		}
	};
	
	$login_status = test_login();
	ob_end_clean(); 
	
	$file_name = substr(__FILE__, strlen($config["site_path"]));
	$file_name = str_replace("\\", "/", $file_name);
	if(substr($file_name, 0, 1) != "/") $file_name = "/".$file_name;
	
	$strSQL = "select id_module from ".MODULE_FILE_TABLE." where file='".$file_name."' ";
	$rs = $dbconn->Execute($strSQL);
	$id_module = $rs->fields[0];
	
	if (!$login_status)
	{
		$strSQL = "select status from ".USERS_TABLE." where id='".$id_user."'";
		$rs = $dbconn->Execute($strSQL);
		if (!$rs->fields[0])
		{
			echo "<script>if(opener){ opener.location.href='".$config["server"].$config["site_root"]."/alert.php?id_module=".$id_module."&err=1'; window.close(); opener.focus();}</script>";
			exit;
		}
		$strSQL = "select distinct a.id_module from ".GROUP_MODULE_TABLE." a, ".USER_GROUP_TABLE." b
					where b.id_user='".$id_user."' and b.id_group=a.id_group and a.id_module=".$id_module;
		$rs = $dbconn->Execute($strSQL);
		if (!$rs->RowCount())
		{
			echo "<script>if(opener){ opener.location.href='".$config["server"].$config["site_root"]."/alert.php?id_module=".$id_module."&err=1'; window.close(); opener.focus();}</script>";
			exit;
		}
	}
	else
	{
		echo "<script>if(opener){ opener.location.href='".$config["server"].$config["site_root"]."/alert.php?id_module=".$id_module."&err=1'; window.close(); opener.focus();}</script>";
		exit;
	}
?>
<html>
<head>
<style>
.body
{
	PADDING-RIGHT: 0px;
	MARGIN-TOP:    0px;
	PADDING-LEFT:  0px;
	FONT-SIZE:     11px;
	MARGIN-LEFT:   0px;
	MARGIN-RIGHT:  0px;
	PADDING-TOP:   0px;
	BACKGROUND-COLOR: #ffffff
}
</style>
<TITLE>Dating instant messenger</TITLE>
</head>
<body class="body" onLoad="PageLoaded()" onbeforeunload="beforeunload()" onUnload="dounload()">
<script language="VBScript" type="text/vbscript" src="<?php echo $config["site_root"]?>/w_communicator/test_flash_ver.vb"></script>
<script language="JavaScript" src="<?php echo $config["site_root"]?>/w_communicator/test_flash_ver.js"></script>
<script type="text/javascript" src="<?php echo $config["site_root"]?>/w_communicator/mybic.js"></script>
<script language="JavaScript" src="<?php echo $config["site_root"]?>/w_communicator/cookies.js"></script>
<script type="text/javascript">
var isInternetExplorer = navigator.appName.indexOf("Microsoft") != -1;
// Handle all the FSCommand messages in a Flash movie.

function DoMyFSCommand(command, args) 
{
  if (command=="resize_form") 
     {
       resize_form(args);  
     }
     else
  if (command=="OpenInParent") 
     {
       OpenInParent(args);
     }
}

function flash_im_obj_DoFSCommand(command, args) 
{
  DoMyFSCommand(command, args);
}
function flash_im_emb_DoFSCommand(command, args) 
{
  DoMyFSCommand(command, args);
}

// Hook for Internet Explorer.
if (navigator.appName && navigator.appName.indexOf("Microsoft") != -1 && navigator.userAgent.indexOf("Windows") != -1 && navigator.userAgent.indexOf("Windows 3.1") == -1) 
{
        document.write('<script language=\"VBScript\"\>\n');
        document.write('On Error Resume Next\n');
        document.write('Sub flash_im_obj_FSCommand(ByVal command, ByVal args)\n');
        document.write('        Call flash_im_obj_DoFSCommand(command, args)\n');
        document.write('End Sub\n');
        document.write('</script\>\n');
}

function GetMovieObj() 
{
 if (window.ActiveXObject)
    {
      if (document.getElementById) {   
                  return document.getElementById('flash_im_obj');
          } else
      if (document.all) {       
                  return document.all.flash_im_obj;
          } else if (document.layers) {   
                  return document.name.flash_im_obj;
          }
    }
    else
    {
      if (document.getElementById) {   
                  return document.getElementById('flash_im_emb');
          } else
      if (document.all) {       
                  return document.all.flash_im_emb;
          } else if (document.layers) {   
                  return document.name.flash_im_emb;
          }
    };
 return 0;
} 

var cur_right_pos=1;

var default_width_right=200;
var min_width_right=190;
var default_width=600;
var min_width=560;
var default_height=700;
var min_height=410;
var diff_val_x = 0;
var diff_val_y = 0;

var diff_val_start_x = 0;
var diff_val_start_y = 0;


var old_w=0, old_h=0;
var post_window_resize_timer=0;
var save_window_position_timer=0;


function on_window_resize()
{
  if (post_window_resize_timer) 
    {
      clearInterval(post_window_resize_timer); post_window_resize_timer=0;
    }
  post_window_resize_timer = setInterval('post_window_resize()', 300);
};
function post_window_resize()
{
  if (post_window_resize_timer) 
    {
      clearInterval(post_window_resize_timer); post_window_resize_timer=0;
    }
  var expdate = new Date ();
  FixCookieDate (expdate);
  expdate.setTime (expdate.getTime() + (365 * 24 * 60 * 60 * 1000)); // 24 hrs from now 
  var w = window.document.body.scrollWidth;
  var h = window.document.body.clientHeight;
  //alert("Resize alert oldW: "+old_w+" W: "+w+" oldH: "+old_h+" H: "+h); 
  if ((old_w!=w)||(old_h!=h))
     { 
       old_w=w; old_h=h;
       SetCookie ("flash_im_width"+cur_right_pos, w, expdate,"/");
       SetCookie ("flash_im_height", h, expdate,"/");
       //alert("Resize RP: "+cur_right_pos+" W: "+w+" H: "+h); 
     }
};

function resize_form(right_pos)
{
  if (cur_right_pos==1) save_current_window_position();
  cur_right_pos=parseInt(""+right_pos);
  var  saved_width = GetCookie("flash_im_width"+right_pos);
  var  saved_height;
  if (window.outerHeight) saved_height = window.outerHeight;
                     else saved_height = window.document.body.clientHeight;
  if (saved_width==null) if (right_pos) saved_width=default_width_right;
                                   else saved_width=default_width;
  saved_width=parseInt(""+saved_width);
  if (right_pos==1) 
     { 
       //alert("SW1: "+saved_width+" MW1: "+min_width);
       if (saved_width<min_width_right) saved_width=min_width_right;
     }
     else
     {
       //alert("SW: "+saved_width+" MW: "+min_width);
       if (saved_width<min_width) saved_width=min_width;
     } 
  saved_width+=diff_val_x; saved_height+=diff_val_y;
  var orig_w = window.document.body.scrollWidth;
  window.resizeTo(700, 600);//saved_width, saved_height); 
  //alert("RP: "+right_pos+" W: "+saved_width+" H: "+saved_height+" DH: "+diff_val_y);
  //moveBy(orig_w-window.document.body.scrollWidth, 0); 
  //on_window_resize();
  //if (cur_right_pos==1) save_current_window_position();
};

function PageLoaded()
{
 if ((top.opener)&&(window.ActiveXObject))
    { // Only for IE
      var w_left=0, w_top=0;
      if (window.screenLeft) w_left=window.screenLeft;
      if (window.screenTop) w_top=window.screenTop;
      var cookie_left = GetCookie("flash_im_pos_x");
      var cookie_top = GetCookie("flash_im_pos_y");
      if ((cookie_left!=null)&&(cookie_top!=null))
         {
           diff_val_start_x=cookie_left-w_left;
           diff_val_start_y=cookie_top-w_top;
           if ((diff_val_start_x>0)||(diff_val_start_x<-20)) diff_val_start_x=0;
           if ((diff_val_start_y>0)||(diff_val_start_y<-50)) diff_val_start_y=0;
         }; 
    };
 var orig_w = window.document.body.scrollWidth;
 var orig_h;
 if (window.outerHeight) orig_h = window.outerHeight;
                    else orig_h = window.document.body.clientHeight;
 self.resizeTo(orig_w,orig_h); 
 diff_val_x = orig_w-window.document.body.scrollWidth; 
 if (window.outerHeight) diff_val_y = orig_h-window.outerHeight;
                    else diff_val_y = orig_h-window.document.body.clientHeight;
 self.resizeTo(orig_w+diff_val_x,orig_h+diff_val_y); 
 window.onresize=on_window_resize;
 if (window.opera)
    { // Special save position timer for opera
      save_window_position_timer = setInterval('save_current_window_position_timed()', 1500);
    }
}


function flash_loading_finished()
{
 //alert("Flash loading finished.");
}

function save_current_window_position_timed()
{
 if (cur_right_pos==1) save_current_window_position();
};

function save_current_window_position()
{
  var w_left=0, w_top=0;
  if (window.screenLeft) w_left=window.screenLeft;
  if (window.screenX) w_left=window.screenX;
  if (window.screenTop) w_top=window.screenTop;
  if (window.screenY) w_top=window.screenY;
  w_left+=diff_val_start_x; w_top+=diff_val_start_y; 
  var expdate = new Date ();
  FixCookieDate (expdate);
  expdate.setTime (expdate.getTime() + (365 * 24 * 60 * 60 * 1000)); // 24 hrs from now 
  SetCookie ("flash_im_pos_x", w_left, expdate, "/");
  SetCookie ("flash_im_pos_y", w_top, expdate, "/");
  //alert("Left: "+w_left+" Top: "+w_top+" DX: "+diff_val_start_x+" DY: "+diff_val_start_y); 
};

var logouted=0;
function flash_doLogout()
{
  //alert("close");
  if (cur_right_pos==1) save_current_window_position();
  logouted=1;
  if ((window.ActiveXObject)||(window.opera))
     {
       window.opener = "_";
     }
     else
     {
       window.open('','_parent','');
     };
  window.close();

};

function OpenInParent(url)
{
  //alert(url);
  if ((top.opener)&&(!top.opener.closed))
     {
       top.opener.document.location=url;
     }
     else
     {
        window.open(url,'_blank'); 
     };
};

var ajaxObj = new XMLHTTP("<?php echo $config["site_root"]?>/w_communicator/communicator.php");
function my_close_browser()
{
 if (logouted!=0) return 0;
 logouted=1;
 if (cur_right_pos==1) save_current_window_position();
 /*
 if (logouted==0)
    {
      alert('Close sess');
      window.open('<?php echo $config["site_root"]?>/w_communicator/close_im.php','flash_chat_close','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=100, height=100');
    };
 */
 //if ((logouted==0)&&(window.ActiveXObject))
    { // Ie Browser
      var flash_obj = GetMovieObj();
      flash_obj.parentNode.removeChild(flash_obj);
      ajaxObj.format=0;
      ajaxObj.async=0;
      ajaxObj.method="GET";
      ajaxObj.call("part=message&act=send&mess_count=1&mess_type0=6&mess_text0=ie_exit&auth_session=1&unchache_session_id="+Math.random(), ComResp); 
    }
};

function beforeunload()
{
  //alert("Before");
  my_close_browser();

};

function dounload() 
{
  //alert("Now unload");
  //my_close_browser();
};


function ComResp(resp) 
{
 if(resp) 
   {
     //alert("OK"+resp);
     for(i=0; i<2000; i++) setTimeout('return;', 5000);
     //alert("2");
     //alert("OK"+resp);
   }
   else
   {
     //alert("FAILED");
   }
} 
</script>
<?php
//$is_connected = true;
if ((isset($_GET["send_user"]))&&($_GET["send_user"]!=""))
{
	if (empty($is_connected))
	{
		//VP echo $strSQL2;
		//print("<br>\nInvite_is: failed");
		?>
		<div style="font-size:13px;color:#ff0000;padding:15px;">
			<p align="center"><b>* User not in your Connections list.</b></p>
			<p align="center"><a href="flash_im.php">Continue with IMessage</a></p>
			<p align="center"><a href="#" onclick="javascript:window.close(); return false;">Close</a></p>
		</div>
		<?php
		return;
	}
}
?>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="100%" height="100%" id="flash_im_obj" name="flash_im_obj"align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
    <param name="FlashVars" value="site_mode=1<?php echo "&invite_and_send_user_name=$invite_and_send_user_name"; echo "&orig_site=".$config["site_root"]."/w_communicator"; ?>"/>
	<param name="movie" value="flash_im.swf" />
    <param name="quality" value="high" />
    <param name="bgcolor" value="#ffffff" />
    <embed src="flash_im.swf" FlashVars="site_mode=1<?php echo "&invite_and_send_user_name=$invite_and_send_user_name"; echo "&orig_site=".$config["site_root"]."/w_communicator"; ?>" swLiveConnect="true" quality="high" bgcolor="#ffffff" width="100%" height="100%" name="flash_im_emb" id="flash_im_emb"  align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
</body>
</html>