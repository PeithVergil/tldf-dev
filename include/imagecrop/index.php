<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>ReCrop Thumb Image</title>

	<style type="text/css">@import "ImageEditor.css";</style>
	<script type="text/javascript" src="PageInfo.js"></script>
	<script type="text/javascript" src="ImageEditor.js"></script>
	<script type="text/javascript">
	//<![CDATA[
		if (window.opener){
			window.moveTo(0, 0);
			//window.resizeTo(screen.width, screen.height - 28);
			window.focus();
		}
		window.onload = function(){
			ImageEditor.init("<?php echo $_REQUEST["imageName"] ?>");
		};
	//]]>
	</script>
</head>
<body>

	<div id="ImageEditorContainer">
		<div id="ImageEditorNotice">&nbsp;</div>
		<div id="ImageEditorToolbar">
			<button onclick="javascript: window.close();">Close</button>
			<span class="spacer"> || </span>
			<button onclick="ImageEditor.save()">Save As Thumb</button><button onclick="ImageEditor.viewActive()">View Thumb</button><button onclick="ImageEditor.viewOriginal()">View Photo</button>
			<span class="spacer"> || </span>w:<input id="ImageEditorTxtWidth" type="text" size="3" maxlength="4" />&nbsp;h:<input id="ImageEditorTxtHeight" type="text" size="3" maxlength="4" /><input id="ImageEditorChkConstrain" type="checkbox" checked="checked" />Constrain&nbsp;<button onclick="ImageEditor.resize();">Resize</button>
			<span class="spacer"> || </span>
			<button onclick="ImageEditor.crop()">Crop</button>
			<span class="spacer"> || </span>
			<button onclick="ImageEditor.rotate(90)">90&deg;CCW</button><button onclick="ImageEditor.rotate(270)">90&deg;CW</button>
			<span class="spacer"> || </span>
			<span id="ImageEditorCropSize"></span>
		</div>
		<div id="ImageEditorImage">&nbsp;</div>
	</div>

</body>
</html>