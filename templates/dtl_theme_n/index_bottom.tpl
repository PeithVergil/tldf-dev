{strip}
			</div>
			<!-- content end -->
			{*
				<!-- OLD FOOTER BUTTONS -->
				<div id="link_button_wrap">
					<div id="link_button">
						<ul class="purple">
							<li><a href="{$site_root}/info.php?sel=5">{$lang.bottom.meet_me_story}</a></li>
							<li><a href="{$site_root}/info.php?sel=6" >{$lang.bottom.who_relies}</a></li>
							<li><a href="{$site_root}/info.php?sel=7" >{$lang.bottom.what_to_expect}</a></li>
							<li><a href="{$site_root}/info.php?sel=8" >{$lang.bottom.dating_truth}</a></li>
						</ul>
						<ul class="blue">
							<li><a href="{$site_root}/help.php" >{$lang.bottom.faq}</a></li>
							<li><a href="{$site_root}/free_report.php" >{$lang.bottom.free_report}</a></li>
							<li><a href="{$site_root}/request_call_back.php" >{$lang.bottom.request_call_back}</a></li>
							<li><a href="{$site_root}/contact.php" >{$lang.bottom.contact}</a></li>
						</ul>
					</div>
					<div class="orange_btn"><a href="{$site_root}/express_interest.php">{$lang.bottom.express_interest}</a></div>
					<div class="clear"></div>
				</div>
			*}
		</div>
		<!-- Footer Start -->
		<div id="footer_wrap">
			<div class="footer-inside">
				<div id="footer">{include file="$gentemplates/index_bottom_popup.tpl"}</div>
				<p id="copy">{$lang.copyright}</p>
			</div>
		</div>
		<!-- Footer End -->
		{if $banner.bottom}
			<div align="center">
				<table height="60" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td width="480">
							{$banner.bottom}
						</td>
					</tr>
				</table>
			</div>
		{/if}
	</div>
	<!-- main-section end -->
</div>
<!-- wrapper end -->
<!-- chat invite: start -->
<div id="new_chat_request" style="display:none; position:fixed; bottom:10px; right:10px; border:2px solid red; border-radius:10px; width:300px; height:100px; padding:10px; background-color:yellow; text-align: center;">
	<b><span id="new_chat_request_name"></span><br /><br /><span id="new_chat_request_msg"></span></b><br />
	<div id="new_chat_request_cancelled" style="color:red; font-weight:bold;">Cancelled</div><br />
	<input type="button" value="Accept" style="cursor:pointer;" onclick="accept_chat();" /> &nbsp; <input type="button" value="Deny" style="cursor:pointer;" onclick="deny_chat();" />
</div>
<div id="chat_invite_dialog" align="center" style="padding:20px;">
	{*<!-- <img src="{$site_root}{$template_root}/images/ajax-loader.gif" border="0" alt="" /><br /><br /><br /> -->*}
	<h2>&nbsp;</h2>
</div>
<!-- chat invite: end -->
{/strip}
{if $tldf_offline}
	<script type="text/javascript" src="{$site_root}/javascript/jquery-ui-1.8.21.min.js"></script>
{else}
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
{/if}
{literal}
<script type="text/javascript">
function ChangeTopMenu(menu_num) {
	/*
	document.getElementById("sub_menu_div_1").style.display = 'none';
	document.getElementById("sub_menu_div_2").style.display = 'none';
	document.getElementById("sub_menu_div_3").style.display = 'none';
	if (document.getElementById("sub_menu_div_4")) document.getElementById("sub_menu_div_4").style.display = 'none';
	if (document.getElementById("sub_menu_div_5")) document.getElementById("sub_menu_div_5").style.display = 'none';
	if (document.getElementById("sub_menu_div_6")) document.getElementById("sub_menu_div_6").style.display = 'none';
	document.getElementById("sub_menu_div_"+menu_num).style.display = 'block';
	*/
}

//var file_name = './user_slider/user_slider4.php';
//var userid = '{/literal}{$auth.id_user}{literal}';
//alert(userid);

$(".placeholder").click(function() {
	// .position() uses position relative to the offset parent, so it supports position: relative parent elements
	var pos = $(this).position();
	
	// .outerHeight() takes into account border and padding.
	var height = $(this).outerHeight();
	
	var top_pos = pos.top + height;
	var lft_pos = pos.left + 2;
	
	//show the menu directly over the placeholder
    $("#popup-menu").css({
        position: "absolute",
        top:  top_pos + "px",
        left: lft_pos + "px"
    }).show();
});

$(".close-popup").click(function() {
	$("#popup-menu").hide();
});

$(document).ready(function() {
	/*
	BookMark = null;
	BookMark = checkBookMark();
	if (BookMark) {
		lObj = document.getElementById(BookMark);
		if (lObj) {
			lObj.className="disp_block";
		}
	}
	*/
	objUserSlider = document.getElementById('user_slider');
	if (objUserSlider) {
		//SlideFeaturedUsers(file_name, userid);
		//setTimeout('SlideFeaturedUsers("./user_slider/user_slider4.php", 372);',5000);
		SLIDE_play();
	}
});

/* VP functionality changes
function SlideFeaturedUsers(file_name, userid)
{
	//alert(userid);
	if (userid != '') {
		//alert(file_name);
		file_name = file_name+'?uid='+userid;
		//str = '?uid='+userid;
		result_obj = document.getElementById('user_slider');
		ajaxRequestPage(file_name, result_obj);
	} else {
		//alert('Error: unable to pass userid.');
	}
}
*/
</script>
{/literal}
{*
{include_php file="$site_path/w_communicator/flash_im_popup.php"}
*}
{literal}
<script type="text/javascript">
$(document).ready(function()
{
	// How it works popup
	$(".popup-list").msAccordion({defaultid:'', vertical:true});
	
	// attach tooltips to labels
	$('label').tooltip();
	$('.tooltip').tooltip();
	
	// attach datepicker
	$(".txtJQDate").datepicker();
	
	// create thumbnails
	$('.nailthumb-item').nailthumb({width:60,height:90});
	
	// Sliding thumbnails
	$('.colorbox_list').bxSlider();
	$('.video-slider').bxSlider({displaySlideQty:3});
	// bxSlider adds a li element before the first item, probably because of better easing
	// when infinite loop is used. But this disturbs colorbox, so we need to remove it.
	$('.photo_slider_colorbox:first').remove();
	$('.video_slider_colorbox:first').remove();
	
	// ColorBox
	$(".photo_slider_colorbox").colorbox({rel:'photo_colorbox'});
	$(".video_slider_colorbox").colorbox({iframe:true, innerWidth:700, innerHeight:450});
	$(".video_colorbox").colorbox({iframe:true, innerWidth:700, innerHeight:450});
	$(".ajax_colorbox").colorbox();
	$(".express_interest_colorbox").colorbox({width:600, height:"100%"});
	$(".inline_colorbox").colorbox({inline:true, width:"50%", height:"100%"});
	$(".iframe_colorbox").colorbox({iframe:true, width:"80%", height:"80%"});
	$(".video_dating_events").colorbox({iframe:true, innerWidth:750, innerHeight:420});
	
	//$('.error_msg').alertr(500000);
	//$('.error_msg').centernew();
});

// Alert
// RS: for some strange reason it does not work on the live server when we put the alert inside the $(document).ready
msg = $.trim($('.error_msg').html());
if (msg != '') {
	jAlert(msg);
}
</script>
{/literal}
{if $registered}
	<script type="text/javascript" src="{$site_root}{$template_root}/js/chatinvite.js?v=0002"></script>
{/if}
{if $smarty.const.IS_LIVE_SERVER}
	<script type="text/javascript" src="{$site_root}{$template_root}/js/google_analytics.js?v=0002"></script>
{/if}
</body>
</html>