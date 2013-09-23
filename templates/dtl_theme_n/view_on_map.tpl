<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Language" content="en-us">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="pragma" content="no-cache">
	<meta name="revisit-after" content="3 days">
	<meta name="robot" content="All">
	<meta name="Description" content="{$lang.description}">
	<meta name="Keywords" content="{$lang.keywords}">
	<title>{$lang.main_title}</title>
	<link href="{$site_root}/css.php" rel="stylesheet" type="text/css">
</head>
<body {if $settings.map_type eq 'google'}onload="initialize()" onunload="GUnload()"{/if}>
<div style="padding: 5px 10px">
<table width="100%" cellspacing=0 cellpadding=0>
<tr valign=top>
{if $settings.map_type eq 'yahoo'}
<script language="JavaScript" type="text/javascript"
        src="http://api.maps.yahoo.com/v3.0/fl/javascript/apiloader.js?appid={$settings.map_app_id}">
</script>
{elseif $settings.map_type eq 'google'}
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$settings.google_app_id}" type="text/javascript"></script>
    <script type="text/javascript">
    {literal}
    function initialize() {
		var map = new GMap2(document.getElementById("mapContainer"));
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.addControl(new GOverviewMapControl());
		map.setCenter(new GLatLng({/literal}{$lat}, {$lon}{literal}), 10);
    }
    {/literal}
    </script>
{/if}
	<td colspan="2" align="left">
<div id="mapContainer" style="height: 450px; width: 800px;"></div>
{if $settings.map_type eq 'yahoo'}
<script language="JavaScript" type="text/javascript">
var latlon = new LatLon('{$lat}', '{$lon}');
var mymap = new Map("mapContainer",'{$settings.map_app_id}', latlon, 8,MapViews.MAP);
mymap.addTool(new PanTool(), true);
mymap.addWidget(new NavigatorWidget());
</script>
{/if}
	</td>
</tr>
<tr><td height="30%">&nbsp;</td></tr>
</table>
</div>
</body>
</html>