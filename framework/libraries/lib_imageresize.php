<?php

function imageresize_temp($image_url, $extension, $image_width = "260") {
	

	switch ($extension) {
		case 'jpeg':
			$simg = imagecreatefromjpeg($image_url);
		break;
		case 'jpg':
			$simg = imagecreatefromjpeg($image_url);
		break;
		case 'png':
			$simg = imagecreatefrompng($image_url);
		break;
		case 'bmp':
			$simg = imagecreatefrombmp($image_url);
		break;
		case 'gif':
			$simg = imagecreatefromgif($image_url);
		break;
		default:
			return false;
	}
	$currwidth = imagesx($simg);
	$currheight = imagesy($simg);

	$zoom = $image_width / $currwidth;
	$newwidth = $image_width;
	$newheight = $currheight * $zoom;

	$dimg = imagecreatetruecolor($newwidth, $newheight);
	imagecopyresampled($dimg, $simg, 0, 0, 0, 0, $newwidth, $newheight, $currwidth, $currheight);
	
	$filename = '/tmp/'.time().'.'.$extension;
	
	imagejpeg($dimg, $filename);
	imagedestroy($simg);
	imagedestroy($dimg);
	
	return $filename;
	
}

?>
