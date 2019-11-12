<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include_once("../lib/MBTAapi.php");
	
	$mbta = new MBTA();
	


	$locations = array();

	$filter = array(
				"filter"=>array("date"=>"2019-10-22")
			);
	
	$routes = $mbta->getRoutes($filter);
	

	
	file_put_contents("cache/routes.json", json_encode($routes));
?>