<script type="text/javascript">
	var fv = "file='+song_file+'&autostart="+false+"&title='+song_name+'&lightcolor=0xD12627&repeat=true";
	var FO = {
		movie:"{$site_root}/include/mp3player/mp3player.swf",width:"300",height:"20",majorversion:"7",build:"0",bgcolor:"#FFFFFF", flashvars:fv
	};
	UFO.create(FO, "player'+id_song+'");
</script>