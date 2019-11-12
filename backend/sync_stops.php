<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include_once("../lib/MBTAapi.php");
	
	$mbta = new MBTA();
	


	$locations = array();

	$filter = array(
				"filter"=>array("route_type"=>"1,0")
			);
	
	$routes = $mbta->getStops($filter);
	
	file_put_contents("cache/stops.json", "");
	
	file_put_contents("cache/stops.json", json_encode($routes));
?>