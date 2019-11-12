<?php

/* 
	Periodically runs on the back end to synchronzie data from the API. 
	Data is cached for access by the UI. This removes API calls form the critical path and avoids excessive calls and lockouts.
*/

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	$stop_id = "North Station";
	

	
	include_once("../lib/MBTAapi.php");
	
	$mbta = new MBTA();
	
	$offset = 0;
	$set_size = 100;

	
	$sync_time = time();
	$current_data = array();
	$current_data["locations"] = array();
	$routes = json_decode(file_get_contents("cache/routes.json"));
	do{
		$filter = array(
						"page"=>array(
									  "offset"=>$offset,
									  "limit"=>$set_size
								)
						
					);
		
		$vehicles = $mbta->getVehicles($filter);
		if(isset($vehicles->errors)){
			if($vehicles->errors[0]->code == "rate_limited"){
				echo $vehicles->errors[0]->code . "<br>";
				sleep(15);
				continue;
			}else{
				echo $vehicles->errors[0]->code;
				exit();
			}
		}
		
		
		
		if(!isset($vehicles->data)){
			break;
		}
		
		foreach($vehicles->data as $v){
			$route_id = $v->relationships->route->data->id;
			$route_info = array_filter($routes,
						   function($e) use (&$route_id){
							   if(is_object($e))
									return $e->id == $route_id;
								else
									return false;
						   }
			);		
			$route_info = array_pop($route_info);

			if($route_info == NULL){
				$route_info = $mbta->getRouteInfo($route_id);
				$routes[] = $route_info;
			}
			$current_data["locations"][] = array(
							  "vehicle_id"=>$v->id, 
							  "latitude"=>$v->attributes->latitude, 
							  "longitude"=>$v->attributes->longitude,
							  "bearing"=>$v->attributes->bearing,
							  "color"=>"#".$route_info->attributes->color,
							  "type"=>$route_info->attributes->description
						);
		}
		$offset = $offset + $set_size;
	}while( sizeof($vehicles->data) > 0 );
	
	$current_data["timestamp"] = $sync_time;

	if(sizeof($current_data["locations"]) == 0){
		exit("No data");	
	}
	$localTime = new DateTimeZone('America/New_York');
	$time = new DateTime();
	$time->setTimestamp($sync_time);
	$time->setTimezone($localTime);
	$current_data["formatted_time"] = $time->format("h:i:s A");
	
	$locations = json_decode(file_get_contents("cache/locations.json"));
	if(sizeof($locations) > 20){
		array_shift($locations);
	}
	$locations[] = $current_data;
	
	file_put_contents("cache/locations.json", "");
	file_put_contents("cache/routes.json", "");
	file_put_contents("cache/locations.json", json_encode($locations));
	file_put_contents("cache/routes.json", json_encode($routes));
?>