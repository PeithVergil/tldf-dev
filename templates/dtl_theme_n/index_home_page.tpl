<!doctype html>
<html lang="{$default_lang}">
<head>
<meta charset="{$charset}">
<meta name="google-site-verification" content="c6DPqKAWOPU1EmSXqY4QFrQhaB9SjJvrZw3Kfyr8bjk" />
<meta name="Description" content="{$lang.description}" />
<meta name="Keywords" content="{$lang.keywords}" />
<title>{$lang.main_title}</title>
<link rel="shortcut icon" href="{$site_root}{$template_root}/images/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/easySlider.css" media="only screen and (max-width: 1020px)" />
<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/bootstrap/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/style.css" />
{if $tldf_offline}
    <script type="text/javascript" src="{$site_root}/javascript/jquery-1.7.2.min.js"></script>
{else}
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
{/if}

<script type="text/javascript" src="{$site_root}/javascript/jquery.tooltip.js?v=0000"></script>
<script type="text/javascript" src="{$site_root}/javascript/easySlider1.7.js?v=0000"></script>
<script type="text/javascript" src="{$site_root}/javascript/alertr.js?v=0000"></script>

{if $script}
    <script type="text/javascript" src="{$site_root}{$template_root}/js/{$script}.js"></script>
{/if}
<!--[if lt IE 9]>
    <script type="text/javascript" src="{$site_root}{$template_root}/js/media_q.js"></script>
<![endif]-->
{literal}
    <script type="text/javascript">
    var sessEnable = navigator.cookieEnabled;
    if (!sessEnable) {
        alert({/literal}"{$lang.err.coockie_enabled}"{literal});
    }
    </script>
{/literal}
{* GOOGLE ANALYTICS TAG REPLACED WITH GOOGLE TAG MANAGER
{if $smarty.const.IS_LIVE_SERVER}
    {literal}
        <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-15603421-2']);
        _gaq.push(['_trackPageview']);
        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        }) ();
        </script>
    {/literal}
{/if}
*}
</head>
{php}flush();{/php}
<body>
{if $smarty.const.IS_LIVE_SERVER && $smarty.const.GOOGLE_TAG_MANAGER}
{literal}
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-JD9Z"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-JD9Z');</script>
<!-- End Google Tag Manager -->
{/literal}
{/if}
{if $smarty.const.IS_LIVE_SERVER && !$Version_B}
{literal}
<!-- Google Website Optimizer Control Script -->
<script>
function utmx_section(){}function utmx(){}
(function(){var k='1218354454',d=document,l=d.location,c=d.cookie;function f(n){
if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.indexOf(';',i);return c.substring(i+n.
length+1,j<0?c.length:j)}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;
d.write('<sc'+'ript src="'+
'http'+(l.protocol=='https:'?'s://ssl':'://www')+'.google-analytics.com'
+'/siteopt.js?v=1&utmxkey='+k+'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='
+new Date().valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
'" type="text/javascript" charset="utf-8"></sc'+'ript>')})();
</script>
<script>utmx("url",'A/B');</script>
<!-- End of Google Website Optimizer Control Script -->
{/literal}
{/if}
{strip}

<div class="container wrap">
    <div class="page-header">
        <div id="logo" class="acenter"><img src="{$site_root}{$template_root}/css/images/logo.png"></div>
        
    </div>
</div>

<div id="signup">
    <div class="container wrap">
        <div class="row">
          <div class="col-md-5 col-md-offset-7">
            <form class="form-horizontal" role="form">
                <h1>Try For Free</h1>
                <a href="#"><img src="{$site_root}{$template_root}/css/images/fb_login.png" class="acenter"></a>
                <p style="margin-bottom: 11px;">&mdash;&mdash;&mdash;&nbsp;or create an account&nbsp;&mdash;&mdash;&mdash;</p>

                <div class="form-group">
                    <label for="username" class="col-lg-3 control-label">Username</label>
                    <div class="col-lg-9">
                      <input type="text" class="form-control" id="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="name" class="col-lg-3 control-label">Name</label>
                    <div class="col-lg-9">
                      <input type="text" class="form-control" id="name">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-lg-3 control-label">Gender</label>
                    <div class="col-lg-9">
                        <label class="radio-inline">
                          <input type="radio" id="inlineCheckbox1" value="option1"> Male
                        </label>
                        <label class="radio-inline">
                          <input type="radio" id="inlineCheckbox2" value="option2"> Female
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-lg-3 control-label">Date of Birth</label>
                    <div class="col-lg-9">
                      <input type="text" class="form-control" id="name">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-lg-3 control-label">Looking for</label>
                    <div class="col-lg-9">
                        <label class="radio-inline">
                          <input type="radio" id="looking_for" value="option1"> Male
                        </label>
                        <label class="radio-inline">
                          <input type="radio" id="looking_for" value="option2"> Female
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail1" class="col-lg-3 control-label">Email</label>
                    <div class="col-lg-9">
                      <input type="email" class="form-control" id="inputEmail1">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="inputEmail1" class="col-lg-3 control-label">Verify Email</label>
                    <div class="col-lg-9">
                      <input type="email" class="form-control" id="inputEmail1">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="inputPassword1" class="col-lg-3 control-label">Password</label>
                    <div class="col-lg-9">
                      <input type="password" class="form-control" id="inputPassword1">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-12">
                      <p style="font-size: 0.95em;">By clicking “Start Now!” you agree with the<br><a href="#">Terms amd Conditions</a> and <a href="#">Privacy Policy.</a></p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-12">
                      <button type="submit" id="button_start-now">Sign in</button>
                    </div>
                </div>
            </form>
          </div>
        </div>
    </div>
</div>

<div class="container wrap">
    <div id="main">

        <div class="row">
          <div class="col-md-12">
          
            <div class="gray_box">
                <div class="inner">
                    <p id="finally">
                        "Finally, a Dating site staffed by real people who are ready to help you find genuine ladies and lasting love."
                    </p>

                    <div id="nathamon">
                        <a href="#">
                            <img src="{$site_root}{$template_root}/css/images/nathamon.png">
                        </a>
                        <div class="name">
                            Nathamon Madison
                        </div>
                        <div class="title">
                            Owner - Thai Lady Date Finder
                        </div>
                    </div>

                    <div id="skype">
                        <a href="#">
                        </a>
                    </div>
                </div>
            </div>
            
          </div>
        </div>
        
        <div class="row">
            
          <div class="col-md-12">
            <h1>Featured Members</h1>
            
            <div class="featured">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">

                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">

                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
                <img src="{$site_root}{$template_root}/css/images/yes1.jpg">
            </div>
           </div>
        </div>
    
        
        <div id="discover-title" class="row options">
            <div class="col-md-12">
                <h1>Discover How We Can Help You Find Your Perfect Partner</h1>
            </div>
        </div>
        
        <div class="row options">
            <div class="opt col-xs-12 col-md-6">
                <div class="row">
                  <div class="col-md-4">
                    <img src="{$site_root}{$template_root}/css/images/get_connected.png">
                  </div>
                  <div class="col-md-8">
                    <header>
                        <h3>Get Connected</h3>
                    </header>
                    <p>
                        Invite ladies to connect with you and enjoy email, IM and Webcam free. No more scams where you're forced to pay and pay.
                    </p>
                    <footer>
                        <a href="#">Learn More</a>
                    </footer>
                  </div>
                </div>
            </div>
            <div class="opt col-xs-12 col-md-6">
                <div class="row">
                  <div class="col-md-4">
                    <img src="{$site_root}{$template_root}/css/images/check_status.png">
                  </div>
                  <div class="col-md-8">
                    <header>
                        <h3>Profile Insight</h3>
                    </header>
                    <p>
                        Wouldn't it be great to be able to phone a friend and find out all you need to know about someone that's caught your eye? Now you can.
                    </p>
                    <footer>
                        <a href="#">Learn More</a>
                    </footer>
                  </div>
                </div>
            </div>
        </div>

        <div class="row options">
            <div class="opt col-xs-12 col-md-6">
                <div class="row">
                  <div class="col-md-4">
                    <img src="{$site_root}{$template_root}/css/images/book_video.png">
                  </div>
                  <div class="col-md-8">
                    <header>
                        <h3>Video Dating</h3>
                    </header>
                    <p>
                        Why not see and hear from the ladies you're interested in live? See your special lady in our studio soon.
                    </p>
                    <footer>
                        <a href="#">Learn More</a>
                    </footer>
                  </div>
                </div>
            </div>
            <div class="opt col-xs-12 col-md-6">
                <div class="row">
                  <div class="col-md-4">
                    <img src="{$site_root}{$template_root}/css/images/coffee_dating.png">
                  </div>
                  <div class="col-md-8">
                    <header>
                        <h3>Coffee Dating</h3>
                    </header>
                    <p>
                        Ready to meet in person? No hassle, no obligation coffee dates are a great way to meet a bunch of ladies and find out where your heart leads you.
                    </p>
                    <footer>
                        <a href="#">Learn More</a>
                    </footer>
                  </div>
                </div>
            </div>
        </div>
        <div class="row options">
            <div class="opt col-xs-12 col-md-6">
                <div class="row">
                  <div class="col-md-4">
                    <img src="{$site_root}{$template_root}/css/images/send_gift.png">
                  </div>
                  <div class="col-md-8">
                    <header>
                        <h3>VIP Gift Service</h3>
                    </header>
                    <p>
                        Flowers and gifts. Yes, every woman loves to know someone is thinking about her. We'll make sure your gift is delivered personally and on time.
                    </p>
                    <footer>
                        <a href="#">Learn More</a>
                    </footer>
                  </div>
                </div>
            </div>
            <div class="opt col-xs-12 col-md-6">
                <div class="row">
                  <div class="col-md-4">
                    <img src="{$site_root}{$template_root}/css/images/add_hotlist.png">
                  </div>
                  <div class="col-md-8">
                    <header>
                        <h3>Platinum Matching</h3>
                    </header>
                    <p>
                        The lazy guy's way to finding the best of the best. Relax and let our team work for you to promote your profile and find your great matches. Your own personal dating concierge - at your service.
                    </p>
                    <footer>
                        <a href="#">Learn More</a>
                    </footer>
                  </div>
                </div>
            </div>
        </div>
    
        <div class="row options">
          <div class="col-md-12">
          
            <div id="case_study" class="gray_box">
                <div class="inner">
                    <h2>See What Other's Are Saying About Thai Land Date Finder</h2>

                    <div class="row">
                        <div class="col-md-6">
                            <p>Check out this case study of our friend Tony's experience on his Thai Lady Dating Events tour.</p>
                        </div>
                        <div class="col-md-6">
                            <a href="#">
                                <img src="{$site_root}{$template_root}/css/images/home_video1.png" class="img-responsive">
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p>Thai Lady Dating Events are hosted by Nathamon Madison and her team at Meet Me Now Bangkok. But who is Nathamon?</p>
                        </div>
                        <div class="col-md-6">
                            <a href="#">
                                <img src="{$site_root}{$template_root}/css/images/home_video2.png" class="img-responsive">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
                
          </div>
        </div>
        
        <div class="row" id="team">
          <div class="col-md-12">

                <h1>Meet Our Team<img src="{$site_root}{$template_root}/css/images/coffee.png" style="margin-left: 15px;"/></h1>
                
          </div>
            <div class="col-md-4 blog">
                <div class="gray_box" style="padding: 23px;">
                    <div class="widget_header">
                        <img src="{$site_root}{$template_root}/css/images/home_video_blog.png">
                        <h3>Blog</h3>
                    </div>
                    <img src="{$site_root}{$template_root}/css/images/home_video_blog1.png" style="margin-bottom: 23px;">
                    <img src="{$site_root}{$template_root}/css/images/home_video_blog2.png">
                </div>          
                                
            </div>
            <div class="col-md-4">
            
                <div class="gray_box" style="padding: 23px;"><img src="{$site_root}{$template_root}/css/images/home_location.png"/></div>
                <div class="gray_box" style="padding: 23px;"><img src="{$site_root}{$template_root}/css/images/home_map.png"/></div>
            
            </div>
            <div class="col-md-4 third">
                <div class="gray_box">
                <h4>Contact Us!</h4>
                <p>
                Meet Me Now Bangkok Co., Ltd.<br>
                33/7 Soi Pipat 2<br>
                Silom Road, Bangkok 10500<br>
                Phone: +66 2 667 0068<br>
                Fax: +66 2 667 0069<br>
                Email:<br>
                <a href="#">admin@meetmenow bangkok.com</a>
                </p>

                <p>Office Hours:<br>
                Mon-Sun: 9am - 6pm<br>
                </p>
                </div>
                
                <div class="gray_box">
                    <h4>Follow Us!</h4>
                    <p style="margin: 0 0px 18px;">
                        <img src="{$site_root}{$template_root}/css/images/social_icons.png" height="48"/>
                    </p>
                </div>
            </div>
        </div>
    </div> <!-- main -->
    

</div> <!-- container -->

<footer id="root-footer">
    <div class="container wrap">
        <div class="row">
            <div class="col-md-4">
                <div class="copy">
                    {$lang.copyright}
                </div>
            </div>
            <div class="col-md-8">
                <div class="links">
                    {include file="$gentemplates/index_bottom_popup.tpl"}
                </div>
            </div>
        </div>
        

        
    </div>
</footer>

{/strip}
{literal}
<script type="text/javascript">
$(function(){$('label').tooltip();});
</script>
<script type="text/javascript">
function ZipCodeCheck(zip_value) {
    if (zip_value == '') {
        document.getElementById('within').disabled = false;
        document.getElementById('search_type').value = 1;
    } else {
        document.getElementById('search_type').value = 2;
        document.getElementById('within').disabled = true;
    }
    return;
}

var topuser_image = new Array();
var topuser_name = new Array();
var topuser_age = new Array();
var topuser_location = new Array();
var topuser_link = new Array();
{/literal}
{foreach name=s key=key item=item from=$top_users}
    topuser_image[{$key}] = '{$item.icon_path}';
    topuser_name[{$key}] = '<a href={$item.link}><b>{$item.name}</b></a>';
    topuser_age[{$key}] = '<span class="text_head">{$item.age} {$header.ans}</span>';
    topuser_location[{$key}] = '<span class="text">{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}</span>';
    topuser_link[{$key}] = '{$item.link}';
{/foreach}
{literal}

function ChangeTopUser(direct) {
    user_image = document.getElementById('topuser_image');
    user_name = document.getElementById('topuser_name');
    user_age = document.getElementById('topuser_age');
    user_location = document.getElementById('topuser_location');
    user_link = document.getElementById('topuser_link');
    hid = document.getElementById('topuser_hidden');
    len = topuser_image.length;
    
    if ( (hid.value == '0' && direct == '1') || (hid.value == (len-1) && direct == '-1') ) {
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
{/literal}
</script>
<script type="text/javascript">
{literal}
$("#do-slide").easySlider({
    auto: false,
    pause: 3000,
    speed: 800,
    animateFade: true,
    continuous: true,
    numeric: true
});
$('.error_msg').alertr(0);
{/literal}
</script>
{if $smarty.const.IS_LIVE_SERVER}
    <script type="text/javascript" src="{$site_root}{$template_root}/js/google_analytics.js?v=0002"></script>
{/if}
</body>
</html>