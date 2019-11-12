<?php

$stops = json_decode( file_get_contents("cache/stops.json"));

$western_lon = 0.0;
$eastern_lon = -180.0;
$northern_lat = 0.0;
$southern_lat = 90.0;

foreach($stops as $s){
	
	if($s->attributes->longitude < $western_lon){
		$western_lon = $s->attributes->longitude;
	}
	
	if($s->attributes->longitude > $eastern_lon){
		$eastern_lon = $s->attributes->longitude;
	}
	
	if($s->attributes->latitude > $northern_lat){
		$northern_lat = $s->attributes->latitude;
	}
	
	if($s->attributes->latitude < $southern_lat){
		$southern_lat = $s->attributes->latitude;
	}	
}

echo "Northern Limit: {$northern_lat}<br>";
echo "Southern Limit: {$southern_lat}<br>";
echo "Eastern Limit: {$eastern_lon}<br>";
echo "Western Limit: {$western_lon}<br>";

?>