{literal}
<script type="text/javascript">
	var SLIDE_text = new Array();
	// Don't change anything under here...
	var SLIDE_pic	= new Array();
	var SLIDE_load	= new Array();
	//var SLIDE_status, SLIDE_timeout;
	var SLIDE_actual= 1;
	//var SLIDE_speed	= 3000;
	var SLIDE_speed	= {/literal}{$users_slider_speed}{literal};
	var SLIDE_fade	= 2;
	
	var user_arr  = new Array();
	
	{/literal}
	{foreach key=key item=item from=$promo_user name=foo}
		{literal}
			str_array = new Array();
			str_array['name']	  	 = "{/literal}{$item.name}{literal}";
			str_array['icon_path']	 = "{/literal}{$item.icon_path}{literal}";
			str_array['profile_link']= "{/literal}{$item.profile_link}{literal}";
			//str_array['id_country']	 = "{/literal}{$item.id_country}{literal}";
			str_array['country']	 = "{/literal}{$base_lang.country[$item.id_country]}{literal}";
			str_array['age']		 = "{/literal}{$item.age}{literal}";
			
			user_arr[{/literal}{$key}{literal}] = str_array;
		{/literal}
	{/foreach}
	{literal}
	
	var SLIDE_count = user_arr.length-1;
	
	for (i = 1; i <= SLIDE_count; i++)
	{
		country_name 	 =  user_arr[i]['country'];
		str_SLIDE_text	 = "<a href='"+user_arr[i]['profile_link']+"'><b>"+user_arr[i]['name']+"<\/b><\/a>";
		str_SLIDE_text	+= "<span>"+country_name+"<\/span>";
		str_SLIDE_text	+= "<span>"+user_arr[i]['age']+" Years<\/span>";
		
		SLIDE_text[i]	= str_SLIDE_text;
		SLIDE_load[i]	= new Image();
		SLIDE_load[i].src = user_arr[i]['icon_path'];
	}
	
	function SLIDE_play()
	{
		SLIDE_actual++;
		SLIDE_slide();
		setTimeout("SLIDE_play()",SLIDE_speed);
	}
	
	function SLIDE_slide()
	{
		if (SLIDE_count < 1) return;
		if (SLIDE_actual > (SLIDE_count)) SLIDE_actual=1;
		if (SLIDE_actual < 1) SLIDE_actual = SLIDE_count;
		if (document.all)
		{
			document.getElementById("SLIDE_textBox").style.background = "transparent";
			document.images.SLIDE_picBox.style.filter="blendTrans(duration=2)";
			document.images.SLIDE_picBox.style.filter="blendTrans(duration=SLIDE_fade)";
			document.images.SLIDE_picBox.filters.blendTrans.Apply();
		}
		document.images.SLIDE_picBox.src = SLIDE_load[SLIDE_actual].src;
		//document.all.item('SLIDE_picAnchor').href = user_arr[SLIDE_actual]['profile_link'];
		
		if (document.getElementById) document.getElementById("SLIDE_textBox").innerHTML= SLIDE_text[SLIDE_actual];
		if (document.all) document.images.SLIDE_picBox.filters.blendTrans.Play();
	}
	
	function goToProfile()
	{
		window.location.href = user_arr[SLIDE_actual]['profile_link'];
	}
</script>
{/literal}
{strip}
<h3 class="hide">Featured Users</h3>
<div class="sld-photo"><a href="javascript:void(0)" onclick="goToProfile(); return false;"><span><img name="SLIDE_picBox" src="./uploades/icons/default_icon_male.gif" class="icon" alt="" /></span><b>&nbsp;</b></a></div>
<div id="SLIDE_textBox">&nbsp;</div>
{/strip}