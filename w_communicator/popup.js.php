<?php
 include "../include/config.php"; 
?>

document.write('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="1" height="1" name="flash_im_popup_obj" id="flash_im_popup_obj" align="middle">');
document.write('<param name="allowScriptAccess" value="sameDomain" />');
document.write('<param name="FlashVars" value="site_mode=1&soundes_enabled=0&orig_site=<?php echo $config["site_root"]?>/w_communicator"/>');
document.write('<param name="movie"   value="<?php echo $config["site_root"]?>/w_communicator/flash_im_popup.swf" />');
document.write('<param name="quality" value="high" />');
document.write('<param name="bgcolor" value="#ffffff" />');
document.write('<param name="wmode" value="transparent" />');
document.write('<embed src="<?php echo $config["site_root"]?>/w_communicator/flash_im_popup.swf" FlashVars="site_mode=1&orig_site=<?php echo $config["site_root"]?>/w_communicator" wmode="transparent" swLiveConnect="true" quality="high" bgcolor="#ffffff" width="1" height="1" name="flash_im_popup_emb" id="flash_im_popup_emb" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />');
document.write('</object>');
