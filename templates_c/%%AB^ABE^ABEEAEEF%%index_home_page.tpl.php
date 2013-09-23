<?php /* Smarty version 2.6.18, created on 2013-09-23 03:38:12
         compiled from templates/dtl_theme_n/index_home_page.tpl */ ?>
<!doctype html>
<html lang="<?php echo $this->_tpl_vars['default_lang']; ?>
">
<head>
<meta charset="<?php echo $this->_tpl_vars['charset']; ?>
">
<meta name="google-site-verification" content="c6DPqKAWOPU1EmSXqY4QFrQhaB9SjJvrZw3Kfyr8bjk" />
<meta name="Description" content="<?php echo $this->_tpl_vars['lang']['description']; ?>
" />
<meta name="Keywords" content="<?php echo $this->_tpl_vars['lang']['keywords']; ?>
" />
<title><?php echo $this->_tpl_vars['lang']['main_title']; ?>
</title>
<link rel="shortcut icon" href="<?php echo $this->_tpl_vars['site_root']; ?>
<?php echo $this->_tpl_vars['template_root']; ?>
/images/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['site_root']; ?>
<?php echo $this->_tpl_vars['template_root']; ?>
/css/easySlider.css" media="only screen and (max-width: 1020px)" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['site_root']; ?>
<?php echo $this->_tpl_vars['template_root']; ?>
/css/bootstrap/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['site_root']; ?>
<?php echo $this->_tpl_vars['template_root']; ?>
/css/style.css" />
<?php if ($this->_tpl_vars['tldf_offline']): ?>
    <script type="text/javascript" src="<?php echo $this->_tpl_vars['site_root']; ?>
/javascript/jquery-1.7.2.min.js"></script>
<?php else: ?>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<?php endif; ?>

<script type="text/javascript" src="<?php echo $this->_tpl_vars['site_root']; ?>
/javascript/jquery.tooltip.js?v=0000"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['site_root']; ?>
/javascript/easySlider1.7.js?v=0000"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['site_root']; ?>
/javascript/alertr.js?v=0000"></script>

<?php if ($this->_tpl_vars['script']): ?>
    <script type="text/javascript" src="<?php echo $this->_tpl_vars['site_root']; ?>
<?php echo $this->_tpl_vars['template_root']; ?>
/js/<?php echo $this->_tpl_vars['script']; ?>
.js"></script>
<?php endif; ?>
<!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo $this->_tpl_vars['site_root']; ?>
<?php echo $this->_tpl_vars['template_root']; ?>
/js/media_q.js"></script>
<![endif]-->
<?php echo '
    <script type="text/javascript">
    var sessEnable = navigator.cookieEnabled;
    if (!sessEnable) {
        alert('; ?>
"<?php echo $this->_tpl_vars['lang']['err']['coockie_enabled']; ?>
"<?php echo ');
    }
    </script>
'; ?>

</head>
<?php flush(); ?>
<body>
<?php if (@IS_LIVE_SERVER && @GOOGLE_TAG_MANAGER): ?>
<?php echo '
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-JD9Z"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
\'//www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,\'script\',\'dataLayer\',\'GTM-JD9Z\');</script>
<!-- End Google Tag Manager -->
'; ?>

<?php endif; ?>
<?php if (@IS_LIVE_SERVER && ! $this->_tpl_vars['Version_B']): ?>
<?php echo '
<!-- Google Website Optimizer Control Script -->
<script>
function utmx_section(){}function utmx(){}
(function(){var k=\'1218354454\',d=document,l=d.location,c=d.cookie;function f(n){
if(c){var i=c.indexOf(n+\'=\');if(i>-1){var j=c.indexOf(\';\',i);return c.substring(i+n.
length+1,j<0?c.length:j)}}}var x=f(\'__utmx\'),xx=f(\'__utmxx\'),h=l.hash;
d.write(\'<sc\'+\'ript src="\'+
\'http\'+(l.protocol==\'https:\'?\'s://ssl\':\'://www\')+\'.google-analytics.com\'
+\'/siteopt.js?v=1&utmxkey=\'+k+\'&utmx=\'+(x?x:\'\')+\'&utmxx=\'+(xx?xx:\'\')+\'&utmxtime=\'
+new Date().valueOf()+(h?\'&utmxhash=\'+escape(h.substr(1)):\'\')+
\'" type="text/javascript" charset="utf-8"></sc\'+\'ript>\')})();
</script>
<script>utmx("url",\'A/B\');</script>
<!-- End of Google Website Optimizer Control Script -->
'; ?>

<?php endif; ?>
<?php echo '<div class="container wrap"><div class="page-header"><div id="logo" class="acenter"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/logo.png"></div></div></div><div id="signup"><div class="container wrap"><div class="row"><div class="col-md-5 col-md-offset-7"><form class="form-horizontal" role="form"><h1>Try For Free</h1><a href="#"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/fb_login.png" class="acenter"></a><p style="margin-bottom: 11px;">&mdash;&mdash;&mdash;&nbsp;or create an account&nbsp;&mdash;&mdash;&mdash;</p><div class="form-group"><label for="username" class="col-lg-3 control-label">Username</label><div class="col-lg-9"><input type="text" class="form-control" id="username"></div></div><div class="form-group"><label for="name" class="col-lg-3 control-label">Name</label><div class="col-lg-9"><input type="text" class="form-control" id="name"></div></div><div class="form-group"><label for="name" class="col-lg-3 control-label">Gender</label><div class="col-lg-9"><label class="radio-inline"><input type="radio" id="inlineCheckbox1" value="option1"> Male</label><label class="radio-inline"><input type="radio" id="inlineCheckbox2" value="option2"> Female</label></div></div><div class="form-group"><label for="name" class="col-lg-3 control-label">Date of Birth</label><div class="col-lg-9"><input type="text" class="form-control" id="name"></div></div><div class="form-group"><label for="name" class="col-lg-3 control-label">Looking for</label><div class="col-lg-9"><label class="radio-inline"><input type="radio" id="looking_for" value="option1"> Male</label><label class="radio-inline"><input type="radio" id="looking_for" value="option2"> Female</label></div></div><div class="form-group"><label for="inputEmail1" class="col-lg-3 control-label">Email</label><div class="col-lg-9"><input type="email" class="form-control" id="inputEmail1"></div></div><div class="form-group"><label for="inputEmail1" class="col-lg-3 control-label">Verify Email</label><div class="col-lg-9"><input type="email" class="form-control" id="inputEmail1"></div></div><div class="form-group"><label for="inputPassword1" class="col-lg-3 control-label">Password</label><div class="col-lg-9"><input type="password" class="form-control" id="inputPassword1"></div></div><div class="form-group"><div class="col-lg-12"><p style="font-size: 0.95em;">By clicking “Start Now!” you agree with the<br><a href="#">Terms amd Conditions</a> and <a href="#">Privacy Policy.</a></p></div></div><div class="form-group"><div class="col-lg-12"><button type="submit" id="button_start-now">Sign in</button></div></div></form></div></div></div></div><div class="container wrap"><div id="main"><div class="row"><div class="col-md-12"><div class="gray_box"><div class="inner"><p id="finally">"Finally, a Dating site staffed by real people who are ready to help you find genuine ladies and lasting love."</p><div id="nathamon"><a href="#"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/nathamon.png"></a><div class="name">Nathamon Madison</div><div class="title">Owner - Thai Lady Date Finder</div></div><div id="skype"><a href="#"></a></div></div></div></div></div><div class="row"><div class="col-md-12"><h1>Featured Members</h1><div class="featured"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/yes1.jpg"></div></div></div><div id="discover-title" class="row options"><div class="col-md-12"><h1>Discover How We Can Help You Find Your Perfect Partner</h1></div></div><div class="row options"><div class="opt col-xs-12 col-md-6"><div class="row"><div class="col-md-4"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/get_connected.png"></div><div class="col-md-8"><header><h3>Get Connected</h3></header><p>Invite ladies to connect with you and enjoy email, IM and Webcam free. No more scams where you\'re forced to pay and pay.</p><footer><a href="#">Learn More</a></footer></div></div></div><div class="opt col-xs-12 col-md-6"><div class="row"><div class="col-md-4"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/check_status.png"></div><div class="col-md-8"><header><h3>Profile Insight</h3></header><p>Wouldn\'t it be great to be able to phone a friend and find out all you need to know about someone that\'s caught your eye? Now you can.</p><footer><a href="#">Learn More</a></footer></div></div></div></div><div class="row options"><div class="opt col-xs-12 col-md-6"><div class="row"><div class="col-md-4"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/book_video.png"></div><div class="col-md-8"><header><h3>Video Dating</h3></header><p>Why not see and hear from the ladies you\'re interested in live? See your special lady in our studio soon.</p><footer><a href="#">Learn More</a></footer></div></div></div><div class="opt col-xs-12 col-md-6"><div class="row"><div class="col-md-4"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/coffee_dating.png"></div><div class="col-md-8"><header><h3>Coffee Dating</h3></header><p>Ready to meet in person? No hassle, no obligation coffee dates are a great way to meet a bunch of ladies and find out where your heart leads you.</p><footer><a href="#">Learn More</a></footer></div></div></div></div><div class="row options"><div class="opt col-xs-12 col-md-6"><div class="row"><div class="col-md-4"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/send_gift.png"></div><div class="col-md-8"><header><h3>VIP Gift Service</h3></header><p>Flowers and gifts. Yes, every woman loves to know someone is thinking about her. We\'ll make sure your gift is delivered personally and on time.</p><footer><a href="#">Learn More</a></footer></div></div></div><div class="opt col-xs-12 col-md-6"><div class="row"><div class="col-md-4"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/add_hotlist.png"></div><div class="col-md-8"><header><h3>Platinum Matching</h3></header><p>The lazy guy\'s way to finding the best of the best. Relax and let our team work for you to promote your profile and find your great matches. Your own personal dating concierge - at your service.</p><footer><a href="#">Learn More</a></footer></div></div></div></div><div class="row options"><div class="col-md-12"><div id="case_study" class="gray_box"><div class="inner"><h2>See What Other\'s Are Saying About Thai Land Date Finder</h2><div class="row"><div class="col-md-6"><p>Check out this case study of our friend Tony\'s experience on his Thai Lady Dating Events tour.</p></div><div class="col-md-6"><a href="#"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/home_video1.png" class="img-responsive"></a></div></div><div class="row"><div class="col-md-6"><p>Thai Lady Dating Events are hosted by Nathamon Madison and her team at Meet Me Now Bangkok. But who is Nathamon?</p></div><div class="col-md-6"><a href="#"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/home_video2.png" class="img-responsive"></a></div></div></div></div></div></div><div class="row" id="team"><div class="col-md-12"><h1>Meet Our Team<img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/coffee.png" style="margin-left: 15px;"/></h1></div><div class="col-md-4 blog"><div class="gray_box" style="padding: 23px;"><div class="widget_header"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/home_video_blog.png"><h3>Blog</h3></div><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/home_video_blog1.png" style="margin-bottom: 23px;"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/home_video_blog2.png"></div></div><div class="col-md-4"><div class="gray_box" style="padding: 23px;"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/home_location.png"/></div><div class="gray_box" style="padding: 23px;"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/home_map.png"/></div></div><div class="col-md-4 third"><div class="gray_box"><h4>Contact Us!</h4><p>Meet Me Now Bangkok Co., Ltd.<br>33/7 Soi Pipat 2<br>Silom Road, Bangkok 10500<br>Phone: +66 2 667 0068<br>Fax: +66 2 667 0069<br>Email:<br><a href="#">admin@meetmenow bangkok.com</a></p><p>Office Hours:<br>Mon-Sun: 9am - 6pm<br></p></div><div class="gray_box"><h4>Follow Us!</h4><p style="margin: 0 0px 18px;"><img src="'; ?><?php echo $this->_tpl_vars['site_root']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['template_root']; ?><?php echo '/css/images/social_icons.png" height="48"/></p></div></div></div></div> <!-- main --></div> <!-- container --><footer id="root-footer"><div class="container wrap"><div class="row"><div class="col-md-4"><div class="copy">'; ?><?php echo $this->_tpl_vars['lang']['copyright']; ?><?php echo '</div></div><div class="col-md-8"><div class="links">'; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['gentemplates'])."/index_bottom_popup.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo '</div></div></div></div></footer>'; ?>

<?php echo '
<script type="text/javascript">
$(function(){$(\'label\').tooltip();});
</script>
<script type="text/javascript">
function ZipCodeCheck(zip_value) {
    if (zip_value == \'\') {
        document.getElementById(\'within\').disabled = false;
        document.getElementById(\'search_type\').value = 1;
    } else {
        document.getElementById(\'search_type\').value = 2;
        document.getElementById(\'within\').disabled = true;
    }
    return;
}

var topuser_image = new Array();
var topuser_name = new Array();
var topuser_age = new Array();
var topuser_location = new Array();
var topuser_link = new Array();
'; ?>

<?php $_from = $this->_tpl_vars['top_users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['s'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['s']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
        $this->_foreach['s']['iteration']++;
?>
    topuser_image[<?php echo $this->_tpl_vars['key']; ?>
] = '<?php echo $this->_tpl_vars['item']['icon_path']; ?>
';
    topuser_name[<?php echo $this->_tpl_vars['key']; ?>
] = '<a href=<?php echo $this->_tpl_vars['item']['link']; ?>
><b><?php echo $this->_tpl_vars['item']['name']; ?>
</b></a>';
    topuser_age[<?php echo $this->_tpl_vars['key']; ?>
] = '<span class="text_head"><?php echo $this->_tpl_vars['item']['age']; ?>
 <?php echo $this->_tpl_vars['header']['ans']; ?>
</span>';
    topuser_location[<?php echo $this->_tpl_vars['key']; ?>
] = '<span class="text"><?php if ($this->_tpl_vars['base_lang']['city'][$this->_tpl_vars['item']['id_city']]): ?><?php echo $this->_tpl_vars['base_lang']['city'][$this->_tpl_vars['item']['id_city']]; ?>
, <?php endif; ?><?php if ($this->_tpl_vars['base_lang']['region'][$this->_tpl_vars['item']['id_region']]): ?><?php echo $this->_tpl_vars['base_lang']['region'][$this->_tpl_vars['item']['id_region']]; ?>
, <?php endif; ?><?php echo $this->_tpl_vars['base_lang']['country'][$this->_tpl_vars['item']['id_country']]; ?>
</span>';
    topuser_link[<?php echo $this->_tpl_vars['key']; ?>
] = '<?php echo $this->_tpl_vars['item']['link']; ?>
';
<?php endforeach; endif; unset($_from); ?>
<?php echo '

function ChangeTopUser(direct) {
    user_image = document.getElementById(\'topuser_image\');
    user_name = document.getElementById(\'topuser_name\');
    user_age = document.getElementById(\'topuser_age\');
    user_location = document.getElementById(\'topuser_location\');
    user_link = document.getElementById(\'topuser_link\');
    hid = document.getElementById(\'topuser_hidden\');
    len = topuser_image.length;
    
    if ( (hid.value == \'0\' && direct == \'1\') || (hid.value == (len-1) && direct == \'-1\') ) {
        num = hid.value;
    } else {
        num = eval(len)*1 + eval(hid.value)*1 + eval(direct)*(-1);
        num = num%len;
    }
    
    user_image.src = topuser_image[num];
    user_name.innerHTML = topuser_name[num];
    user_age.innerHTML = topuser_age[num];
    user_location.innerHTML = topuser_location[num];
    user_link.href = topuser_link[num];
    
    hid.value = num;
}
'; ?>

</script>
<script type="text/javascript">
<?php echo '
$("#do-slide").easySlider({
    auto: false,
    pause: 3000,
    speed: 800,
    animateFade: true,
    continuous: true,
    numeric: true
});
$(\'.error_msg\').alertr(0);
'; ?>

</script>
<?php if (@IS_LIVE_SERVER): ?>
    <script type="text/javascript" src="<?php echo $this->_tpl_vars['site_root']; ?>
<?php echo $this->_tpl_vars['template_root']; ?>
/js/google_analytics.js?v=0002"></script>
<?php endif; ?>
</body>
</html>