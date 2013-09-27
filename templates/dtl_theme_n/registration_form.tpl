{strip}

{literal}
<div id="fb-root"></div>
<script>
/*
* SH 2. Login for FB.
*/


  window.fbAsyncInit = function() {
  FB.init({
    appId      : '298096403662644', // App ID
    channelUrl : 'b8d008257aee496c9e2352c36f51a43c', // Channel File
    status     : true, // check login status
    cookie     : true, // enable cookies to allow the server to access the session
    xfbml      : true  // parse XFBML
  });

  // Here we subscribe to the auth.authResponseChange JavaScript event. This event is fired
  // for any authentication related change, such as login, logout or session refresh. This means that
  // whenever someone who was previously logged out tries to log in again, the correct case below 
  // will be handled. 
  FB.Event.subscribe('auth.authResponseChange', function(response) {
    // Here we specify what we do with the response anytime this event occurs. 
    if (response.status === 'connected') {
        //fbLogin();
        var c = confirm("You are logged into facebook Do you want to register with us using facebook?");
        if (c){ window.location.href = 'index.php?sel=fb_login'; }
        else{ FB.logout();}
        
        
        /*if(response.status === 'not_authorized'){
            fbLogin();
            window.location.href = 'index.php?sel=fb_login';
            }else{ window.location.href = 'index.php?sel=fb_login';}*/
  }
  else if (response.status === 'not_authorized') {

      // In this case, the person is logged into Facebook, but not into the app, so we call
      // FB.login() to prompt them to do so. 
      // In real-life usage, you wouldn't want to immediately prompt someone to login 
      // like this, for two reasons:
      // (1) JavaScript created popup windows are blocked by most browsers unless they 
      // result from direct interaction from people using the app (such as a mouse click)
      // (2) it is a bad experience to be continually prompted to login upon page load.
      //FB.login();
    } else {
      // In this case, the person is not logged into Facebook, so we call the login() 
      // function to prompt them to do so. Note that at this stage there is no indication
      // of whether they are logged into the app. If they aren't then they'll see the Login
      // dialog right after they log in to Facebook. 
      // The same caveats as above apply to the FB.login() call here.
     // FB.login();
    }
  });
  };

  // Load the SDK asynchronously
  (function(d){
   var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
   if (d.getElementById(id)) {return;}
   js = d.createElement('script'); js.id = id; js.async = true;
   js.src = "//connect.facebook.net/en_US/all.js";
   ref.parentNode.insertBefore(js, ref);
  }(document));


  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      console.log('Good to see you, ' + response.name + '.');
    });
  }
  
    function fbLogin(){
     FB.login(function(response) {
   
 }, {scope: 'email,user_birthday'});
  }
  
</script>

{/literal}

<form class='loginForm form-horizontal' role="form" name="profile" method="post" action="{$form.action}" onsubmit="return CheckForm(this);">
    <input type="hidden" name="signup" value="{$smarty.request.signup}" />
    {$form.hiddens}
    {if $form.err}
        <div class="error_msg">{$form.err}</div>
    {/if}
    
        <h1>Try For Free</h1>
         <a href = 'javascript:void(0)' onclick="fbLogin();"><img src="{$site_root}{$template_root}/css/images/fb_login.png" class="acenter"></a>
          <p style="margin-bottom: 11px;">&mdash;&mdash;&mdash;&nbsp;or create an account&nbsp;&mdash;&mdash;&mdash;</p>
                 <div class="form-group">
                <label class='title col-lg-3 control-label' title="{$lang.username_thai}" >
                    {if $err_field.login}<span class="error">{/if}
                    {$lang.users.login}
                    {if $err_field.login}</span>{/if}
                    <span class="mandatory">*</span>:
                </label>
            <div class="col-lg-9">
                <input type="text" name="login" class="form-control" id="login" maxlength="40" value="{$data.login}" {if $data.root == 1}disabled="disabled"{/if} onblur="if (CheckValue(this)) CheckLogin('mp', this.value, error_div);" />
                
                {*<!-- error message for login already in use -->*}
                <div id="error_div" class="error"></div>
            </div>
            </div>
            <!--    password-->
        

        
        {if $use_field.fname & SB_REGISTRATION}
            <div class="form-group">
                <label for="name" class="col-lg-3 control-label">
                    {if $err_field.fname}<span class="error">{/if}
                        {*$lang.first_name_thai*}Name
                        {if $err_field.fname}</span>{/if}
                        {if $mandatory.fname & SB_REGISTRATION}
                    {/if}
                </label>
                <div class="col-lg-9">
                  <input type="text" name="name" class="form-control" value="{$data.name}" {if $data.root == 1}disabled="disabled"{/if} onblur="CheckValue(this);" />
                </div>
            </div>
        {/if}
        
        {if $use_field.mm_nickname & SB_REGISTRATION}
            <div class="form-group">
                <label for="{$lang.nickname_thai}" class="col-lg-3 control-label">
                    {if $err_field.mm_nickname}<span class="error">{/if}
                        {$lang.users.mm_nickname}
                        {if $err_field.mm_nickname}</span>{/if}
                        {if $mandatory.mm_nickname & SB_REGISTRATION}{/if}
                    </label>
                <div class="col-lg-9">
                    <input type="text" name="mm_nickname" class="form-control" value="{$data.mm_nickname}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                 </div>
            </div>  
        {/if}
        
        {if $use_field.gender & SB_REGISTRATION}
            <div class="form-group">
                <label for="name" class="col-lg-3 control-label">
                    {if $err_field.gender}<span class="error">{/if}
                    {$lang.users.gender}
                    {if $err_field.gender}</span>{/if}
                    {if $mandatory.gender & SB_REGISTRATION}{/if}
                </label>
                <div class="col-lg-9">
                    {foreach item=item from=$gender}
                        <label class="radio-inline">
                            <input type="radio" name="gender" value="{$item.id}" onclick="CheckGender(this)" {if $item.sel}checked="checked"{/if} />
                            {$item.name}
                        </label>
                    {/foreach}
                </div>
            </div>
        {/if}  
        {if $use_field.couple & SB_REGISTRATION}
            <div class="form-group">
                <label for="name" class="col-lg-3 control-label">
                    {if $err_field.couple}<span class="error">{/if}
                    {$lang.users.single_couple}
                    {if $err_field.couple}</span>{/if}
                    {if $mandatory.couple & SB_REGISTRATION}{/if}
                </label>
                    
                 <div class="col-lg-9">
                     <label class="radio-inline">
                          <input type="radio" onclick=showcouple(); name="couple" value="0" {if ! $data.couple}checked="checked"{/if} />
                          {$lang.users.single}
                    </label>
                     <label class="radio-inline">       
                        <input type="radio" onclick = showcouple(); name="couple" value="1" {if $data.couple}checked="checked"{/if} />S
                        {$lang.users.couple}
                    </label>    
                </div>  
            </div>

            <div id="couple_user_form" style="display:none; position:relative; top:0px">
                {if $data.couple_user}
                    <input type="hidden" value="{$data.couple_user}" name="couple_user" />
                    {$lang.users.couple_link}:<br>
                    <a href="{$data.couple_link}" target="_blank"><b>{$data.couple_login}</b></a>
                    {$data.couple_gender} {$data.couple_age} {$lang.home_page.ans}<br>
                    <input type="checkbox" value="1" name="couple_delete" />
                    {$lang.users.couple_delete}<br>
                    {if ! $data.couple_accept}{$lang.users.couple_accept}{/if}
                {else}
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td class="txtblack">{$lang.users.couple_login}:</td>
                            <td>
                                <input type="text" name="couple_login" maxlength="25" value="{$data.couple_login}" style="width: 150px" />
                            </td>
                            <td>
                                <a href="quick_search.php">{$lang.button.search}</a>
                            </td>
                        </tr>
                    </table>
                {/if}
            </div>
        {/if}
        {if $use_field.mm_marital_status & SB_REGISTRATION}
            <div class="form-group">
                <label class='col-lg-3 control-label'title="{$lang.marital_status_thai}">
                    {if $err_field.mm_marital_status}<span class="error">{/if}
                    {$lang.users.mm_marital_status}
                    {if $err_field.mm_marital_status}</span>{/if}
                    {if $mandatory.mm_marital_status & SB_REGISTRATION}{/if}
                </label>
             <div class="col-lg-9">
                {foreach item=item from=$mm_marital_status}
                    <label class="radio-inline" for="marital_status_{$item.id}" title="{$lang.mm_marital_status[$item.id]}">
                      input type="radio" name="mm_marital_status" id="marital_status_{$item.id}" value="{$item.id}" {if $item.sel}checked="checked"{/if} />
                      {$item.value}
                    </label>
                {/foreach}  
                   
                </div>
            </div>  
        {/if}
        {if $use_field.date_birthday & SB_REGISTRATION}
            <div class="form-group">
                    <label class='title col-lg-3 control-label' title="{$lang.birthday_thai}" >
                        {if $err_field.date_birthday}<span class="error">{/if}
                        {$lang.users.date_birthday}
                        {if $err_field.date_birthday}</span>{/if}
                        {if $mandatory.date_birthday & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
                    </label>
                <div class="col-lg-9">
                    <select name="b_{$date_part1_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:auto !important; margin-right:5px;">
                        <option value="">{$date_part1_default}</option>
                        {foreach item=item from=$date_part1}
                            <option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
                        {/foreach}
                    </select>
                    <select name="b_{$date_part2_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:auto !important; margin-right:5px;">
                        <option value="">{$date_part2_default}</option>
                        {foreach item=item from=$date_part2}
                            <option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
                        {/foreach}
                    </select>
                    <select name="b_{$date_part3_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:auto !important;">
                        <option value="">{$date_part3_default}</option>
                        {foreach item=item from=$date_part3}
                            <option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
                        {/foreach}
                    </select>
                    </div>
                    </div>
            
        {/if}

        <div class="form-group">
            <label class='col-lg-3 control-label'>
                Looking for
            </label>
             <div class="col-lg-9">
                {foreach item=item from=$gender}
                    <label class="radio-inline" title="{$lang.mm_gender[$item.id]}">
                    <input type="radio" name="gender_looking" value="{$item.id}" {if !$item.sel}checked="checked"{/if} />{$item.name}
                    </label>
                {/foreach}
            </div>
        </div>

        {if $use_field.mm_place_of_birth & SB_REGISTRATION}
            <div class="form-group">
                <label class='title'title="{$lang.place_of_birth_thai}">
                    {if $err_field.mm_place_of_birth}<span class="error">{/if}
                    {$lang.users.mm_place_of_birth}
                    {if $err_field.mm_place_of_birth}</span>{/if}
                    {if $mandatory.mm_place_of_birth & SB_REGISTRATION}{/if}
                </label>
                <div class="col-lg-9">
                    <input type="text" name="mm_place_of_birth" maxlength="25" value="{$data.mm_place_of_birth}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                </div>
            </div>
        {/if}
        {if $use_field.id_nationality & SB_REGISTRATION}
            <div class="form-group">
                <label class='title'title="{$lang.nationality_thai}">
                    {if $err_field.id_nationality}<span class="error">{/if}
                    {$lang.users.nationality}
                    {if $err_field.id_nationality}</span>{/if}
                    {if $mandatory.id_nationality & SB_REGISTRATION}{/if}
                </label>
                <div class="col-lg-9">
                    <select name="id_nationality" {if $data.root == 1}disabled="disabled"{/if} style="width:200px">
                        <option value="0">{$lang.home_page.select_default}</option>
                        {foreach item=item from=$nation}
                            <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        {/if}
        {if ($use_field.mm_id_number & SB_REGISTRATION) && $data.gender == 2}
            <div class="form-group">
                    <label class='title'title="ID Number">
                        {if $err_field.mm_id_number}<span class="error">{/if}
                        {$lang.users.mm_id_number}
                        {if $err_field.mm_id_number}</span>{/if}
                        {if $mandatory.mm_id_number & SB_REGISTRATION}{/if}
                    </label>
                <div class="form-group">
                    <input type="text" name="mm_id_number" maxlength="25" value="{$data.mm_id_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                    <label class='title'title="{$lang.application.confidential}">
                        {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                    </label>
                </div>
            </div>
        {else}
            <div class="form-group">
                <input type="hidden" name="mm_id_number" value="{$data.mm_id_number}" />
            </div>
        {/if}
        {if $use_field.email & SB_REGISTRATION}
            <div class="form-group">
                    <label for="inputEmail1" class="col-lg-3"  title="Email{*$lang.email_thai*}">
                        {if $err_field.email}<span class="error">{/if}
                        {$lang.users.email}
                        {if $err_field.email}</span>{/if}
                        {if $mandatory.email & SB_REGISTRATION}{/if}
                    </label>
                <div class="col-lg-9">
                    <input type="text" name="email" class="form-control" value="{$data.email}" {if $data.root == 1}disabled="disabled"{/if} onblur="CheckValue(this);" oncopy="return false" />
                </div>
            </div>

            <div class="form-group">    
                    <label class='title col-lg-3 control-label' title="Email{*$lang.email_thai*}" >
                        {if $err_field.email}<span class="error">{/if}
                        {$lang.users.email}
                        {if $err_field.email}</span>{/if}
                        {if $mandatory.email & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
                    </label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="email" maxlength="50" value="{$data.email}" {if $data.root == 1}disabled="disabled"{/if} onblur="CheckValue(this);" oncopy="return false" />
                    {*<label class='title'title="{$lang.application.confidential}">
                        {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                    </label>*}
                    </div></div>
                
        {/if}
        <div class="form-group">    
                <label class='title col-lg-3 control-label' title="{$lang.confirm_email_thai}" >
                    {if $err_field.reemail}<span class="error">{/if}
                    {$lang.users.reemail}
                    {if $err_field.reemail}</span>{/if}
                    <span class="mandatory">*</span>:
                </label>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="reemail" maxlength="40" value="{$data.reemail}" {if $data.root == 1}disabled="disabled"{/if}  onblur="CheckValue(this);" oncopy="return false" ondrag="return false" ondrop="return false" onpaste="return false" autocomplete="off" />
                {*<label class='title'title="{$lang.application.confidential}">
                    {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                </label>*}
            </div></div>
        <!--  Password  SH 2   -->
        <div class="form-group">    
            <label class='title col-lg-3 control-label' title="{$lang.password_thai}" >
            {if $err_field.pass}<span class="error">{/if}
            {$lang.users.pass}
            {if $err_field.pass}</span>{/if}
            <span class="mandatory">*</span>:
            </label>
            <div class="col-lg-9">
            <input type="password" class="form-control" name="pass" maxlength="20" value="{$data.pass}" {if $data.root == 1}disabled="disabled"{/if} onblur="CheckValue(this);" />
            </div></div>
        {if $use_field.mm_contact_phone_number & SB_REGISTRATION}
            <div class="form-group">
                    <label class='col-lg-3 control-label'title="{$lang.contact_phone_thai}">
                        {if $err_field.mm_contact_phone_number}<span class="error">{/if}
                        {$lang.users.mm_contact_phone_number}
                        {if $err_field.mm_contact_phone_number}</span>{/if}
                        {if $mandatory.mm_contact_phone_number & SB_REGISTRATION}{/if}
                    </label>
                <div class="col-lg-9">
                    <input type="text" name="mm_contact_phone_number" class="form-control" value="{$data.mm_contact_phone_number}" {if $data.root == 1}disabled="disabled"{/if} />
                    <label class='title'title="{$lang.application.confidential}">
                        {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                    </label>
                </div>
            </div>
        {/if}
        {if $use_field.mm_contact_mobile_number & SB_REGISTRATION}
            <div class="form-group">
                    <label class='col-lg-3 control-label'title="{$lang.contact_mobile_thai}">
                        {if $err_field.mm_contact_mobile_number}<span class="error">{/if}
                        {$lang.users.mm_contact_mobile_number}
                        {if $err_field.mm_contact_mobile_number}</span>{/if}
                        {if $mandatory.mm_contact_mobile_number & SB_REGISTRATION}{/if}
                    </label>
                <div class="col-lg-9">
                    <input type="text" name="mm_contact_mobile_number" class="form-control" value="{$data.mm_contact_mobile_number}" {if $data.root == 1}disabled="disabled"{/if} />
                    <label class='title'title="{$lang.application.confidential}">
                        {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                    </label>
                </div>
            </div>
        {/if}
        {if $voipcall_feature == 1}
            {if $use_field.phone & SB_REGISTRATION}
                <tr>
                    <td>
                        {$lang.users.phone}:
                    </td>
                    <td>
                        <input type="text" name="phone" maxlength="25" value="{$data.phone}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" onblur="CheckValue(this);" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">{$lang.users.phone_notice}</td>
                </tr>
            {/if}
        {/if}
        {if ($use_field.gender_search & SB_REGISTRATION)
        || ($use_field.age_min & SB_REGISTRATION)
        || ($use_field.age_max & SB_REGISTRATION)
        || ($use_field.couple_search & SB_REGISTRATION)
        || ($use_field.relationship & SB_REGISTRATION)}
            {if $use_field.gender_search & SB_REGISTRATION}
                <div class="form-group">
                        {if $err_field.gender_search}<span class="error">{/if}
                        {$lang.users.gender}
                        {if $err_field.gender_search}</span>{/if}
                    <div class="col-lg-9">
                        {foreach item=item from=$gender}
                            <input type="radio" name="gender_search" value="{$item.id}" {if $item.sel_search}checked="checked"{/if} />
                            <span style="padding-right:15px;" class="txtblack">{$item.name_search}</span>
                        {/foreach}
                    </div>
                </div>
            {/if}
            {if $use_field.age_min & SB_REGISTRATION || $use_field.age_max & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.age_range_thai}">
                        {if $err_field.age_min || $err_field.age_max}<span class="error">{/if}
                        {$lang.users.age_range}
                        {if $err_field.age_min || $err_field.age_max}</span>{/if}
                        </label>
                    <div class="col-lg-9">
                        {if $use_field.age_min & SB_REGISTRATION}
                            <span class="txtblack">{$lang.users.from_big}</span>
                            <span style="padding-left:10px;">
                                <select name="age_min" {if $data.root == 1}disabled="disabled"{/if}>
                                    {foreach item=item from=$age_min}
                                    <option value="{$item}" {if $min_age_sel == $item}selected="selected"{/if}>{$item}</option>
                                    {/foreach}
                                </select>
                            </span>
                        {/if}
                        {if $use_field.age_max & SB_REGISTRATION}
                            <span class="txtblack" style="padding-left:10px;">{$lang.users.to_big}</span>
                            <span style="padding-left:10px;">
                                <select name="age_max" {if $data.root == 1}disabled="disabled"{/if}>
                                    {foreach item=item from=$age_max}
                                    <option value="{$item}" {if $max_age_sel == $item}selected="selected"{/if}>{$item}</option>
                                    {/foreach}
                                </select>
                            </span>
                        {/if}
                    </div>
                </div>
            {/if}
            {if $use_field.couple_search & SB_REGISTRATION}
                <div class="form-group">
                        {if $err_field.couple_search}<span class="error">{/if}
                        {$lang.users.single_couple}
                        {if $err_field.couple_search}</span>{/if}
                    <div class="col-lg-9">
                        <input type="radio" name="couple_search" value="0" {if !$data.couple_search}checked="checked"{/if} />
                        <label class='title'>
                            {$lang.users.single}
                        </label>
                        <input type="radio" name="couple_search" value="1" {if $data.couple_search}checked="checked"{/if} />
                        <label class='title'>
                            {$lang.users.couple}
                        </label>
                        </div>
                </div>
            {/if}
            {if $use_field.id_relationship & SB_REGISTRATION}
                <div class="form-group">
                        {if $err_field.id_relationship}<span class="error">{/if}
                        {$lang.users.relationship}
                        {if $err_field.id_relationship}</span>{/if}
                    <div class="col-lg-9">
                        {if $relation_input_type == "select"}
                            <select name="relation[]" {if $data.root == 1}disabled="disabled"{/if} multiple style="width:150px; height:80px">
                                <option value="0" {if $relation.sel_all}selected="selected"{/if}>{$button.all}</option>
                                {html_options values=$relation.opt_value selected=$relation.opt_sel output=$relation.opt_name}
                            </select>
                        {else}
                            <div class="col-lg-9">
                                        <input type="checkbox" name="relation[]" value="0" id="all" {if $relation.sel_all}checked="checked"{/if} />
                                    
                                        <label class='title'for="all">{$button.all}</label>
                                </div>
                                {section name=r loop=$relation.opt_value}
                                    {if $smarty.section.r.index is div by 5 && !$smarty.section.r.last}
                                        <tr>
                                    {/if}
                                    <div class="col-lg-9">
                                        <input type="checkbox" id="relation_{$smarty.section.r.index}" name="relation[]" value="{$relation.opt_value[r]}" {if $relation.opt_sel[r] == $relation.opt_value[r]}checked="checked"{/if} />
                                    
                                        <label class='title'for="relation_{$smarty.section.r.index}" style="margin-bottom:5px;">{$relation.opt_name[r]}</label>
                                    </div>
                                    {if $smarty.section.r.index_next is div by 5 || $smarty.section.r.last}
                                        
                                    {/if}
                                {/section}
                            
                        {/if}
                    </div>
                </div>
            {/if}
        {/if}
        {if $use_field.id_country & SB_REGISTRATION}
            {if $use_field.id_country & SB_REGISTRATION}
                <div class="form-group">
                    <label class='title'title="{$lang.country_thai}">
                        {if $err_field.id_country}<span class="error">{/if}
                        {$lang.users.country}
                        {if $err_field.id_country}</span>{/if}
                        {if $mandatory.id_country & SB_REGISTRATION}{/if}
                    </label>
                    <div class="col-lg-9">
                        <select name="id_country" {if $data.root == 1}disabled="disabled"{/if} onchange="SelectRegion('rp', this.value, document.getElementById('region_div'), document.getElementById('city_div'));">
                            <option value="0">{$lang.home_page.select_default}</option>
                            {foreach item=item from=$country}
                            <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            {/if}
            {if $use_field.id_region & SB_REGISTRATION}
                <div class="form-group">
                        {if $err_field.id_region}<span class="error">{/if}
                        {$lang.users.region}
                        {if $err_field.id_region}</span>{/if}
                        {if $mandatory.id_region & SB_REGISTRATION}{/if}
                    <div class="col-lg-9">
                            {if isset($region)}
                                <select name="id_region" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" onchange="SelectCity('rp', this.value, document.getElementById('city_div'));">
                                    <option value="0">{$lang.home_page.select_default}</option>
                                    {foreach item=item from=$region}
                                    <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
                                    {/foreach}
                                </select>
                            {else}
                        {/if}
                    </div>
                </div>
            {/if}
            {if $use_field.id_city & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.city}">
                            {if $err_field.id_city}<span class="error">{/if}
                            {$lang.users.city}
                            {if $err_field.id_city}</span>{/if}
                            {if $mandatory.id_city & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                            {if isset($city)}
                                <select name="id_city" {if $data.root == 1}disabled="disabled"{/if} style="width: 150px">
                                    <option value="0">{$lang.home_page.select_default}</option>
                                    {foreach item=item from=$city}
                                    <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
                                    {/foreach}
                                </select>
                            {else}
                            {/if}
                    </div>
                </div>
            {/if}
            {if $use_field.mm_city & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.city}">
                            {if $err_field.mm_city}<span class="error">{/if}
                            {$lang.users.city}
                            {if $err_field.mm_city}</span>{/if}
                            {if $mandatory.mm_city & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_city" maxlength="25" value="{$data.mm_city}" size="30" {if $data.root == 1}disabled="disabled"{/if} style="width: 150px" />
                    </div>
                </div>
            {/if}
            {if $use_field.zipcode & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.zipcode_thai}">
                            {if $err_field.zipcode}<span class="error">{/if}
                            {$lang.users.zipcode}
                            {if $err_field.zipcode}</span>{/if}
                            {if $mandatory.zipcode & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="zipcode" maxlength="25" value="{$data.zipcode}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" maxlength="{$form.zip_count}" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                        {*<!-- <span class="text_hidden">{$lang.users.us_only}</span> -->*}
                    </div>
                </div>
            {/if}
            {if $use_field.mm_address_1 & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.address_line_1_thai}">
                            {if $err_field.mm_address_1}<span class="error">{/if}
                            {$lang.users.mm_address_1}
                            {if $err_field.mm_address_1}</span>{/if}
                            {if $mandatory.mm_address_1 & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_address_1" maxlength="40" value="{$data.mm_address_1}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
            {if $use_field.mm_address_2 & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.address_line_2_thai}">
                            {if $err_field.mm_address_2}<span class="error">{/if}
                            {$lang.users.mm_address_2}
                            {if $err_field.mm_address_2}</span>{/if}
                            {if $mandatory.mm_address_2 & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_address_2" maxlength="40" value="{$data.mm_address_2}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
        {/if}
        {if $use_field.id_language_1 & SB_REGISTRATION}
            {if $use_field.id_language_1 & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.language_thai}">
                            {if $err_field.id_language_1 || $err_field.id_language_2 || $err_field.id_language_3}<span class="error">{/if}
                            {$lang.users.language}
                            {if $err_field.id_language_1 || $err_field.id_language_2 || $err_field.id_language_3}</span>{/if}
                            {if $mandatory.id_language_1 & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <select name="id_language_1" {if $data.root == 1}disabled="disabled"{/if} style="width:160px; margin-right:5px;">
                            <option value="0">{$lang.home_page.select_default}</option>
                            {foreach item=item from=$lang_sel}
                            <option value="{$item.id}" {if $item.sel1}selected="selected"{/if}>{$item.value}</option>
                            {/foreach}
                        </select>
                        <select name="id_language_2" {if $data.root == 1}disabled="disabled"{/if} style="width:160px; margin-right:5px;">
                            <option value="0">{$lang.home_page.select_default}</option>
                            {foreach item=item from=$lang_sel}
                            <option value="{$item.id}" {if $item.sel2}selected="selected"{/if}>{$item.value}</option>
                            {/foreach}
                        </select>
                        <select name="id_language_3" {if $data.root == 1}disabled="disabled"{/if} style="width:160px; margin-right:5px;">
                            <option value="0">{$lang.home_page.select_default}</option>
                            {foreach item=item from=$lang_sel}
                            <option value="{$item.id}" {if $item.sel3}selected="selected"{/if}>{$item.value}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            {/if}
            {if $use_field.mm_level_of_english & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.level_of_english_thai}">
                        {if $err_field.mm_level_of_english}<span class="error">{/if}
                        {$lang.users.mm_level_of_english}
                        {if $err_field.mm_level_of_english}</span>{/if}
                        {if $mandatory.mm_level_of_english & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        {foreach item=item from=$mm_level_of_english}
                                    <input type="radio" name="mm_level_of_english" id="level_english_{$item.id}" value="{$item.id}" {if $item.sel}checked="checked"{/if} style="vertical-align: top;" />
                            <span style="margin-right:15px;" class="txtblack">
                                        <label class='title'for="level_english_{$item.id}" title="{$lang.mm_level_english[$item.value]}">{$item.value}</label>
                            </span>
                        {/foreach}
                    </div>
                </div>
            {/if}
            {if $use_field.site_language & SB_REGISTRATION}
                <div class="form-group">
                        {if $err_field.site_language}<span class="error">{/if}
                        {$lang.users.site_language}
                        {if $err_field.site_language}</span>{/if}
                        {if $mandatory.site_language & SB_REGISTRATION}{/if}
                    <div class="col-lg-9">
                        <select name="site_language" style="width:200px">
                            {foreach from=$site_langs item=item}
                            <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            {else}
                <div class="form-group">
                    <div class="col-lg-9">
                        <input type="hidden" name="site_language" value="{$data.site_language}">
                    </div>
                </div>
            {/if}
        {/if}
        {if $use_field.mm_employment_status & SB_REGISTRATION}
            {if $use_field.mm_employment_status & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="Employment Status">
                            {if $err_field.mm_employment_status || $err_field.mm_business_name || $err_field.mm_employer_name}<span class="error">{/if}
                            {$lang.users.mm_employment_status}
                            {if $err_field.mm_employment_status || $err_field.mm_business_name || $err_field.mm_employer_name}</span>{/if}
                            {if $mandatory.mm_employment_status & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <table border="0" cellpadding="0" cellspacing="0">
                            {foreach item=item from=$mm_employment_status}
                                <tr>
                                    <td>
                                        <input type="radio" name="mm_employment_status" value="{$item.id}" {if $item.sel}checked="checked"{/if} />
                                    </td>
                                    <td style="padding-right:15px;" class="txtblack">
                                        <label class='title'title="{$lang.mm_employment_status[$item.value]}">{$item.value}</label>
                                    </td>
                                    {if $item.id == 1}
                                        <td></td>
                                        <td></td>
                                    {elseif $item.id == 2}
                                        {if $use_field.mm_business_name & SB_REGISTRATION}
                                            <td>
                                                <label class='title'title="{$lang.business_name_thai}">
                                                    {if $err_field.mm_business_name}<span class="error">{/if}
                                                    {$lang.users.mm_business_name}
                                                    {if $err_field.mm_business_name}</span>{/if}
                                                </label>
                                            </td>
                                            <td>
                                                <input type="text" name="mm_business_name" maxlength="40" value="{$data.mm_business_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                                            </td>
                                        {else}
                                            <td></td>
                                            <td></td>
                                        {/if}
                                    {elseif $item.id == 3}
                                        {if $use_field.mm_employer_name & SB_REGISTRATION}
                                            <td>
                                                <label class='title'title="{$lang.employer_name_thai}">
                                                    {if $err_field.mm_employer_name}<span class="error">{/if}
                                                    {$lang.users.mm_employer_name}
                                                    {if $err_field.mm_employer_name}</span>{/if}
                                                </label>
                                            </td>
                                            <td>
                                                <input type="text" name="mm_employer_name" maxlength="25" value="{$data.mm_employer_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                                            </td>
                                        {else}
                                            <td></td>
                                            <td></td>
                                        {/if}
                                    {/if}
                                </tr>
                            {/foreach}
                        </table>
                    </div>
                </div>
            {/if}
            {if $use_field.mm_job_position & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.job_position_thai}">
                            {if $err_field.mm_job_position}<span class="error">{/if}
                            {$lang.users.mm_job_position}
                            {if $err_field.mm_job_position}</span>{/if}
                            {if $mandatory.mm_job_position & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_job_position" maxlength="25" value="{$data.mm_job_position}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                    </div>
                </div>
            {/if}
            {if $use_field.mm_work_address & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.work_address_thai}">
                            {if $err_field.mm_work_address}<span class="error">{/if}
                            {$lang.users.mm_work_address}
                            {if $err_field.mm_work_address}</span>{/if}
                            {if $mandatory.mm_work_address & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_work_address" maxlength="40" value="{$data.mm_work_address}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
            {if $use_field.mm_work_phone_number & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.work_phone_thai}">
                            {if $err_field.mm_work_phone_number}<span class="error">{/if}
                            {$lang.users.mm_work_phone_number}
                            {if $err_field.mm_work_phone_number}</span>{/if}
                            {if $mandatory.mm_work_phone_number & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_work_phone_number" maxlength="25" value="{$data.mm_work_phone_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
        {/if}
        {if $use_field.mm_ref_1_first_name & SB_REGISTRATION}
            {if $use_field.mm_ref_1_first_name & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="Name">{*{$lang.first_name_thai}*}
                            {if $err_field.mm_ref_1_first_name}<span class="error">{/if}
                            {$lang.users.fname}
                            {if $err_field.mm_ref_1_first_name}</span>{/if}
                            {if $mandatory.mm_ref_1_first_name & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_ref_1_first_name" maxlength="25" value="{$data.mm_ref_1_first_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
            {if $use_field.mm_ref_1_last_name & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.last_name_thai}">
                            {if $err_field.mm_ref_1_last_name}<span class="error">{/if}
                            {$lang.users.sname}
                            {if $err_field.mm_ref_1_last_name}</span>{/if}
                            {if $mandatory.mm_ref_1_last_name & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_ref_1_last_name" maxlength="25" value="{$data.mm_ref_1_last_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
            {if $use_field.mm_ref_1_relationship & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.reference_relationship_thai}">
                            {if $err_field.mm_ref_1_relationship}<span class="error">{/if}
                            {$lang.users.mm_reference_relationship}
                            {if $err_field.mm_ref_1_relationship}</span>{/if}
                            {if $mandatory.mm_ref_1_relationship & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_ref_1_relationship" maxlength="25" value="{$data.mm_ref_1_relationship}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
            {if $use_field.mm_ref_1_phone_number & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.reference_phone_thai}">
                            {if $err_field.mm_ref_1_phone_number}<span class="error">{/if}
                            {$lang.users.mm_reference_phone_number}
                            {if $err_field.mm_ref_1_phone_number}</span>{/if}
                            {if $mandatory.mm_ref_1_phone_number & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_ref_1_phone_number" maxlength="25" value="{$data.mm_ref_1_phone_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
        {/if}
        {if $use_field.mm_ref_1_first_name & SB_REGISTRATION}
            {if $use_field.mm_ref_2_first_name & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.first_name_thai}">
                            {if $err_field.mm_ref_2_first_name}<span class="error">{/if}
                            Name{*$lang.first_name_thai*}
                            {if $err_field.mm_ref_2_first_name}</span>{/if}
                            {if $mandatory.mm_ref_2_first_name & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_ref_2_first_name" maxlength="25" value="{$data.mm_ref_2_first_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
            {if $use_field.mm_ref_2_last_name & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.last_name_thai}">
                            {if $err_field.mm_ref_2_last_name}<span class="error">{/if}
                            {$lang.users.sname}
                            {if $err_field.mm_ref_2_last_name}</span>{/if}
                            {if $mandatory.mm_ref_2_last_name & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_ref_2_last_name" maxlength="25" value="{$data.mm_ref_2_last_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
            {if $use_field.mm_ref_2_relationship & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.reference_relationship_thai}">
                            {if $err_field.mm_ref_2_relationship}<span class="error">{/if}
                            {$lang.users.mm_reference_relationship}
                            {if $err_field.mm_ref_2_relationship}</span>{/if}
                            {if $mandatory.mm_ref_2_relationship & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_ref_2_relationship" maxlength="25" value="{$data.mm_ref_2_relationship}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
            {if $use_field.mm_ref_2_phone_number & SB_REGISTRATION}
                <div class="form-group">
                        <label class='title'title="{$lang.reference_phone_thai}">
                            {if $err_field.mm_ref_2_phone_number}<span class="error">{/if}
                            {$lang.users.mm_reference_phone_number}
                            {if $err_field.mm_ref_2_phone_number}</span>{/if}
                            {if $mandatory.mm_ref_2_phone_number & SB_REGISTRATION}{/if}
                        </label>
                    <div class="col-lg-9">
                        <input type="text" name="mm_ref_2_phone_number" maxlength="25" value="{$data.mm_ref_2_phone_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
                        <label class='title'title="{$lang.application.confidential}">
                            {$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
                        </label>
                    </div>
                </div>
            {/if}
        {/if}
        {if $use_field.headline & SB_REGISTRATION}
            <div class="form-group">
                <label>
                    {if $err_field.headline}<span class="error">{/if}
                    {$lang.users.headline}
                    {if $err_field.headline}</span>{/if}
                    {if $mandatory.headline & SB_REGISTRATION}{/if}
                </label>
                <div class="col-lg-9">
                    <textarea name="headline" rows="5" cols="80" style="width:400px; height:50px;" {if $data.root == 1}disabled="disabled"{/if}>{$data.headline}</textarea>
                </div>
            </div>
            
           
        {/if}
        {*
        <tr>
            <td colspan="2">
                <input type="checkbox" name="agreed" value="1" {if $data.agreed}checked="checked"{/if} /> 
                <span class="txtblack">
                    <label class='title'title="{$lang.terms_of_service_agree_thai}">
                        {if $err_field.agreed}<span class="error">{/if}
                        {$lang.registration.page_1_agreed_1}
                        {if $err_field.agreed}</span>{/if}
                        &nbsp;<a href="{$site_root}/info.php?sel=3" target=_blank>{$lang.registration.page_1_agreed_2}</a>
                    </label>
                </span>
            </td>
        </tr>
        {if $use_field.subscribes & SB_REGISTRATION}
            <tr>
                <td valign="top">{$lang.account.subheader_subscribe}:</td>
                <td>
                    {foreach item=item key=key from=$s_subscr}
                        <div>
                            <input type="checkbox" name="s_subscr[{$key}]" value="{$item.id}" {if $item.sel}checked="checked"{/if} />
                            {$item.name}
                        </div>
                    {/foreach}
                    {if $adm_subscr}
                        <div style="height:10px"></div>
                        {foreach item=item key=key from=$adm_subscr}
                            <div>
                                <input type="checkbox" name="a_subscr[{$key}]" value="{$item.id}" {if $item.sel}checked="checked"{/if} />
                                {$item.name}
                            </div>
                        {/foreach}
                    {/if}
                </td>
            </tr>
        {/if}*}
    </table>
    <div class="clear"></div>
    {*<table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                {if $err_field.captcha}<span class="error">{/if}
                {$lang.contact_us.security_code}:
                {if $err_field.captcha}</span>{/if}
            </td>
            <td><img src="{$form.kcaptcha}" alt="{$lang.contact_us.security_code}"></td>
            <td>&nbsp;<input type="text" class="txt_spam_code" name="keystring" /></td>
        </tr>
    </table>*}
    <div class="form-group">
                    <div class="col-lg-12">
                        By clicking “Start Now!” you agree with the
                        <a href="{$site_root}/info.php?sel=3" target="_blank">Terms amd Conditions</a> and <a href="{$site_root}/info.php?sel=3" target="_blank">Privacy Policy.</a>
                    </p>
                    </div>
                    </div>
                  <div class="form-group">
                    <div class="col-lg-12">
                      <button type="submit" class="startnowBtn" id="button_start-now">Sign in</button>
                    </div>
                </div>
<!--    <p class="basic-btn_here _mleft30"> -->
<!--        <b>&nbsp;</b><span><input type="submit" value="{$button.create_account}" title="{$button.create_account}" /></span> -->
<!--    </p> -->
</form>
{/strip}
<script type="text/javascript">
{literal}
$(function(){
    $('.startnowBtn a').click(function(){
        $('.loginForm').submit();
        });
    
});
function CharCountChecker(obj)
{
    if (obj.value.length >= 165) {
        return false;
    }
    return true;
}
function showcouple()
{
    couple = document.forms.profile.elements.couple;
    couple_show = true;
    if (couple) {
        for (i=0; i<couple.length; i++) {
            if ((couple[i].checked)&&(couple[i].value=="0")) {
                couple_show=false;
                break;
            }
        }
        if (couple_show) {
            document.getElementById('couple_user_form').style.display="block";
        } else {
            document.getElementById('couple_user_form').style.display="none";
        }
    }
    return false;
}
{/literal}
{if $form.num == 1 && $use_field.couple & SB_REGISTRATION}
showcouple();
{/if}
function CheckValue(obj)
{ldelim}
    return true;
    f = obj.form;
    
    if (obj.name=='login' && obj.value == '') {ldelim}
        alert("{$lang.err.invalid_login}");
        return;
    {rdelim}
    if (obj.name=='login' && (obj.value.length < 5 || obj.value.length > 20)) {ldelim}
        alert("{$lang.err.login_length}");
        return;
    {rdelim}
    if (obj.name=='pass' && obj.value == '') {ldelim}
        alert("{$lang.err.invalid_passw}");
        return;
    {rdelim}
    if (obj.name=='pass' && obj.value.length < 6) {ldelim}
        alert("{$lang.err.pass_length}");
        return;
    {rdelim}
    if (obj.name=='login' && obj.value == f.pass.value) {ldelim}
        alert("{$lang.err.pass_eq_log}");
        return;
    {rdelim}
    if (obj.name=='pass' && obj.value == f.login.value) {ldelim}
        alert("{$lang.err.pass_eq_log}");
        return;
    {rdelim}
    if (obj.name=='repass' && (bp.pass.value != obj.value)) {ldelim}
        alert("{$lang.err.pass_eq_repass}");
        return;
    {rdelim}
    if (obj.name=='sname' && obj.value == '') {ldelim}
        alert("{$lang.err.invalid_sname}");
        return;
    {rdelim}
    if (obj.name=='fname' && obj.value == '') {ldelim}
        alert("{$lang.err.invalid_name}");
        return;
    {rdelim}
    if (obj.name=='mm_nickname' && obj.value == '') {ldelim}
        alert("{$lang.err.invalid_mm_nickname}");
        return;
    {rdelim}
    if (obj.name=='email' && (obj.value == '' || (obj.value != '' && obj.value.search('^.+@.+\\..+$') == -1))) {ldelim}
        alert("{$lang.err.email_bad}");
        return;
    {rdelim}
{if $voipcall_feature}
    if (obj.name=='phone' && (obj.value != '' && obj.value.search(/^\d{ldelim}10,15{rdelim}(x\d{ldelim}1,5{rdelim})?$/) == -1)) {ldelim}
        alert("{$lang.err.phone_bad}");
        return;
    {rdelim}
{/if}
{rdelim}

function CheckForm(f)
{ldelim}
    return true;
    /*
    if (f.login.value == '') {ldelim}
        alert("{$lang.err.invalid_login}");
        f.login.focus()
        return false;
    {rdelim}
    if (f.login.value.length < 5 || f.login.value.length > 20) {ldelim}
        alert("{$lang.err.login_length}");
        f.login.focus();
        return false;
    {rdelim}
    if (f.pass.value == '') {ldelim}
        alert("{$lang.err.invalid_passw}");
        f.pass.focus()
        return false;
    {rdelim}
    if (f.pass.value.length < 6) {ldelim}
        alert("{$lang.err.pass_length}");
        f.pass.focus();
        return false;
    {rdelim}
    if (f.login.value == f.pass.value) {ldelim}
        alert("{$lang.err.pass_eq_log}");
        f.pass.focus();
        return false;
    {rdelim}
    if (f.pass.value != f.repass.value) {ldelim}
        alert("{$lang.err.pass_eq_repass}");
        f.repass.focus();
        return false;
    {rdelim}
    if (f.fname.value == '') {ldelim}
        alert("{$lang.err.invalid_name}");
        f.name.focus();
        return false;
    {rdelim}
    if (f.sname.value == '') {ldelim}
        alert("{$lang.err.invalid_sname}");
        f.sname.focus();
        return false;
    {rdelim}
    if (f.mm_nickname.value == '') {ldelim}
        alert("{$lang.err.invalid_mm_nickname}");
        f.mm_nickname.focus();
        return false;
    {rdelim}
    if (f.email.value == '' || f.email.value.search('^.+@.+\\..+$') == -1) {ldelim}
        alert("{$lang.err.email_bad}");
        f.email.focus();
        return false;
    {rdelim}
    {if $voipcall_feature}
        if (f.phone.value != '' && f.phone.value.search(/^\d{ldelim}10,15{rdelim}(x\d{ldelim}1,5{rdelim})?$/) == -1) {ldelim}
            alert("{$lang.err.phone_bad}");
            f.phone.focus()
            return false;
        {rdelim}
    {/if}
    if (bp.agreed.checked == false) {ldelim}
        alert("{$lang.err.term_agreed_err}");
        return false;
    }
    return true;
    */
{rdelim}
function CheckGender(obj)
{ldelim}
    if (obj.value == "1") {ldelim}
        alert("{$lang.confirm.register_as_male}"+"\n"+"{$lang.confirm.unable_to_change}");
    {rdelim}
    else if (obj.value == "2") {ldelim}
        alert("{$lang.confirm.register_as_female}"+"\n"+"{$lang.confirm.unable_to_change}");
    {rdelim}
{rdelim}
    
</script>