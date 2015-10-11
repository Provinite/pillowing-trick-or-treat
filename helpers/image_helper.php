<?php
function applyWatermark($image, $watermark = "/home/cloverco/public_html/apps/assets/images/ccdwatermark.png")
{
	$maxwidth = 800;
	
	$info = pathinfo($image);
	if ($info['extension'] !== 'png')
		return;
	$im = imagecreatefrompng($image);
	$stamp = imagecreatefrompng($watermark);
	
	$stampwidth = imagesx($stamp);
	$stampheight = imagesy($stamp);
	
	$imagewidth = imagesx($im);
	$imageheight = imagesy($im);
	
	$ratio = $imageheight/$imagewidth;
	
	$out_height = $imageheight;
	$out_width = $imagewidth;
	
	if ($imagewidth > $maxwidth)
	{
		$out_width = $maxwidth;
		$out_height = floor($maxwidth*$ratio);
	}
	$top = $out_height - 10 - $stampheight;
	$left = 10;
	
	$final = imagecreatetruecolor($out_width, $out_height);
	//resize image to final size
	imagecopyresampled($final, $im, 0, 0, 0, 0, $out_width, $out_height, $imagewidth, $imageheight);
	
	//blend stamp onto image
	$cut = imagecreatetruecolor($out_width, $out_height);
	imagecopy($cut, $final, 0, 0, 0, 0, $imagewidth, $imageheight);
	imagecopy($cut, $stamp, $left, $top, 0, 0, $stampwidth, $stampheight);
	
	//save it
	imagepng($cut, $image);
	}
	function createThumbs( $pathToImages, $pathToThumbs, $thumbWidth ) 
	{
	  // open the directory
	  $dir = opendir( $pathToImages );
	
	  // loop through it, looking for any/all JPG files:
	  while (false !== ($fname = readdir( $dir ))) {
		// parse path for the extension
		if (file_exists($pathToThumbs . $fname))
			continue;
		$info = pathinfo($pathToImages . $fname);
		// continue only if this is a JPEG image
		if ( strtolower($info['extension']) == 'png' ) 
		{
		  echo "Creating thumbnail for {$fname} <br />";
	
		  // load image and get image size
		  $img = imagecreatefrompng( "{$pathToImages}{$fname}" );
		  imagealphablending($img, true);
		  $width = imagesx( $img );
		  $height = imagesy( $img );
	
		  // calculate thumbnail size
		  $new_width = $thumbWidth;
		  $new_height = floor( $height * ( $thumbWidth / $width ) );
	
		  // create a new temporary image
		  $tmp_img = imagecreatetruecolor( $new_width, $new_height );
		  imagealphablending($tmp_img,false);
		  imagesavealpha($tmp_img, true);
	
		  // copy and resize old image into new image 
		  imagecopyresampled( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
	
		  // save thumbnail into a file
		  imagepng( $tmp_img, "{$pathToThumbs}{$fname}" );
		}
	  }
	  // close the directory
	  closedir( $dir );
	}
?>