<?php 
 header('Content-type: image/png'); 
 $width = 200;
 $height = 10;
 $handle = imagecreate($width, $height); 
 $background = imagecolorallocate($handle, 200,255,255); 
 $foreground = imagecolorallocate($handle, 0, 0, 200); 
 $red = imagecolorallocate($handle, 255, 0, 0); 
 $green = imagecolorallocate($handle, 0, 255, 0); 
 $blue = imagecolorallocate($handle, 0, 0, 255); 
 $cyan = imagecolorallocate($handle,0,255,255);
 $darkred = imagecolorallocate($handle, 150, 0, 0); 
 $darkblue = imagecolorallocate($handle, 0, 0, 150); 
 $darkgreen = imagecolorallocate($handle, 0, 150, 0); 
 $white = imagecolorallocate($handle,255,255,255);

 imagefilledrectangle($handle,0,0,$width,$height,$background);
 imagefilledrectangle($handle,0,0,$_REQUEST['percentage'] * $width,$height,$foreground);
 imagepng($handle); 
 ?>