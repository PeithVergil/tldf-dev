<?php
	error_reporting(0);
	
	ob_start();
	
	require_once('inc/common.php');

	header("Pragma: public");
	header("Expires: 0");
	header("Content-type: text/xml");
?>
<preloader_settings <?php echo array2attrs($GLOBALS['fc_config']['preloader'])?>>
	<text <?php echo array2attrs($GLOBALS['fc_config']['preloader']['text'])?>/>
</preloader_settings>
<?php 
	ob_end_flush();
?>