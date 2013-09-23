<?php
//Errors
define('ERR_FILE_EXCEED_MAX', 'File exceed maximum size');
define('ERR_FILE_INVALID_TYPE', 'Invalid file type');
include_once "uploads.config.php";

class Uploads{
	var $site_path;
	var $site_url;
	var $config;

	function Uploads($site_path, $site_url){
		global $upload_config;
		$this->config=$upload_config;
		$this->site_path=$site_path;
		$this->site_url=$site_url;
		$this->gd_used = extension_loaded('gd')?1:0;
	}
	
	function getUploadConfig($upload_type){
		$upload_cfg['site_path']=$this->site_path.$this->config['upload_cfg'][$upload_type]['upload_dir'];
		$upload_cfg['href']=$this->site_url.$this->config['upload_cfg'][$upload_type]['upload_dir'];
		$upload_cfg['file_max_size']=$this->config['upload_cfg'][$upload_type]['file_max_size'];
		return $upload_cfg;
	}
	
	function checkFile($file_data, $file_type=FT_IMAGE, $upload_type=FT_COMMON, $upload_cfg=''){
		if ($upload_cfg=='') $upload_cfg=$this->config['upload_cfg'][$upload_type];

		//Check file size
		$max_size=intval($upload_cfg['file_max_size']);
		if (($file_data['size']>$max_size) && ($max_size>0)) {
			return array('error'=>ERR_FILE_EXCEED_MAX, 'max_size'=>$max_size);
		}
		
		$f_type=$file_data['type'];

		//Check file type
		$valid_types=$this->config['valid_types'][$file_type];
		if ($file_data['name']!='' && !in_array($f_type, $valid_types)){
			return array('error'=>ERR_FILE_INVALID_TYPE, 'file_type'=>$f_type);
		}
		return true;
	}
	
	function uploadFile($file_data, $file_type=FT_IMAGE, $upload_type=FT_COMMON, $upload_cfg=''){
		if ($upload_cfg=='') $upload_cfg=$this->config['upload_cfg'][$upload_type];

		$upload_dir=$this->site_path.$upload_cfg['upload_dir'].'/';

		preg_match("/(.+)\.(.*?)\Z/", $file_data['name'], $matches);
		$suffix = $matches[2];
				
		$prefix = "mHTTP_temp_";

		do {
			$seed = substr(md5(microtime().getmypid()), 0, 8);
			$path_to_image = $upload_dir.$prefix.$seed.'.'.$suffix;
			$new_file_name = $prefix.$seed.'.'.$suffix;
		} while (file_exists($path_to_image));
		move_uploaded_file($file_data['tmp_name'], $path_to_image);
		@chmod($path_to_image, 0755);
		
		//If any actions is neccessary after load (like creating thumbs, image resizing) specify them in after load function
		$this->afterLoad($new_file_name, $upload_dir, $file_type, $upload_type);
		
		return $new_file_name;
	}
	
	function afterLoad($file_name, $path, $file_type, $upload_type){
		$settings=GetSiteSettings(array('thumb_max_width', 'thumb_max_height', 'big_thumb_max_width', 'big_thumb_max_height'));
		switch ($upload_type){
			case UT_ADO_IMAGES:
				$thumb_width=$settings['thumb_max_width']; $thumb_height=$settings['thumb_max_height'];
				$big_thumb_width=$settings['big_thumb_max_width']; $big_thumb_height=$settings['big_thumb_max_height'];
				break;
			default:
				return;
		}
		if ($thumb_width>0 && $thumb_height>0) $this->createThumb($file_name, $path, $thumb_width, $thumb_height);
		if ($big_thumb_width>0 && $big_thumb_height>0) $this->createThumb($file_name, $path, $big_thumb_width, $big_thumb_height, 'big_thumb_');
		if ($resize_width>0 && $resize_height>0) $this->resizeImage($path.$file_name, $path.$file_name, $resize_width, $resize_height);
	}
	
	function createThumb($file_name, $path, $thumb_width, $thumb_height, $thumb_prefix='thumb_'){
		$this->resizeImage($path.$file_name, $path.$thumb_prefix.$file_name, $thumb_width, $thumb_height);
	}
	
	function resizeImage($file_from, $file_to, $width_to, $height_to){
		$image_info=getimagesize($file_from);
		$image_width=$image_info[0];
		$image_height=$image_info[1];
		switch($image_info[2]){
			case "1" :
				$srcImage = @ImageCreateFromGif($file_from);
				break;	/// GIF
			case "2" :
				$srcImage = @imagecreatefromjpeg($file_from);
				break;	/// JPG
			case "3" :
				$srcImage = @imagecreatefrompng($file_from);
				break;	/// PNG
			case "6" :
				$srcImage = @imagecreatefromwbmp($file_from);
				break;	/// BMP

		}
		if($srcImage){
			$srcWidth  = ImageSX( $srcImage );
			$srcHeight = ImageSY( $srcImage );
			if($image_width>$width_to){
				$image_height = round($image_height*$width_to/$image_width);
				$image_width = $width_to;
			}
			if($image_height>$height_to){
				$image_width = round($image_width*$height_to/$image_height);
				$image_height = $height_to;
			}

			$destImage = @imagecreatetruecolor( $width_to, $height_to);

			if ($image_width<$width_to){
				$x = round(($width_to-$image_width)/2);
			}
			if ($image_height<$height_to){
				$y = round(($height_to-$image_height)/2);
			}

			$r = $g = $b = 255;
			$color = ImageColorAllocate($destImage, $r, $g, $b);
			imagefilledrectangle($destImage, 0, 0, $width_to, $height_to, $color);

			imagecopyresampled( $destImage, $srcImage, $x, $y, 0, 0, $image_width, $image_height, $srcWidth, $srcHeight );

			switch($image_info[2]){
				case "1" :
					if (function_exists("imagegif")) ImageGif( $destImage, $file_to, 100 );
					else return false;
					break;	/// GIF
				case "2" :
					if (function_exists("imagejpeg")) ImageJpeg( $destImage, $file_to, 100 );
					else return false;
					break;	/// JPG
				case "3" :
					if (function_exists("imagepng")) ImagePng( $destImage, $file_to, 100 );
					else return false;
					break;	/// PNG
				case "6" :
					if (function_exists("imagewbmp")) ImageWbmp( $destImage, $file_to, 100 );
					else return false;
					break;	/// BMP
			}

			ImageDestroy( $srcImage  );
			ImageDestroy( $destImage );
			return true;
		}else{
			return false;
		}
	}
}
?>