<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);





$img = array();

$width = 1000;
$height = 1000;

$color = "#000000";
$background_color = "#FFFFFF";

$background = explode(",",hex2rgb($background_color));


$image = imagecreate($width, $height);

$background_color = imagecolorallocate($image, $background[0], $background[1], $background[2]);

imagefill ( $image, 0, 0, $background_color );

$locations = json_decode(file_get_contents("cache/locations.json"));

$min_lat = (71.817916870117);	
$min_lon = (41.893211364746);

$i = 0;
foreach($locations as $l){
	$i++;
	if($i == 300)
		break;
	
	if($l->color != "#FFC72C"){
		$y = (int)round(($l->longitude-$min_lon)*1000);
		$x = (int)round(($l->latitude-$min_lat)*1000);
		$color = $l->color;

		$color = explode(",",hex2rgb($color));
		$color = imagecolorallocate($image, $color[0], $color[1], $color[2]);
		//echo $x . "," . $y  . "," . ($x+1) . "," . ($y+1) . "<br>";
		imagefilledrectangle ( $image , $x , $y , $x+1 , $y+1 , $color );
	}
	
}
//exit();

header("Content-type: image/png");	
imagepng($image);
imagedestroy($image);


function hex2rgb($hex) {
    // Copied
   $hex = str_replace("#", "", $hex);

   switch (strlen($hex)) {
       case 1:
           $hex = $hex.$hex;
       case 2:
          $r = hexdec($hex);
          $g = hexdec($hex);
          $b = hexdec($hex);
           break;

       case 3:
          $r = hexdec(substr($hex,0,1).substr($hex,0,1));
          $g = hexdec(substr($hex,1,1).substr($hex,1,1));
          $b = hexdec(substr($hex,2,1).substr($hex,2,1));
           break;

       default:
          $r = hexdec(substr($hex,0,2));
          $g = hexdec(substr($hex,2,2));
          $b = hexdec(substr($hex,4,2));
           break;
   }

   $rgb = array($r, $g, $b);
   return implode(",", $rgb);
}
?>