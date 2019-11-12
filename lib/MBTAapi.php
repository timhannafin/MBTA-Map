<?php

	Class MBTA{
		private $api_key = "d889a9d1b2b646fbbd4ed7a54962fb4a";
		private $base_api_url = "https://api-v3.mbta.com";
		
		function __construct(){
			
		}


		private function executeCurl($target_url){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $target_url); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$json = curl_exec($ch);
			$result = json_decode($json);
			return $result;
		}
	

		function getTrips($filter, $include = NULL){
			$filter_query = http_build_query($filter);
			if($include){
				$include = urlencode($include);
				$target_url = "{$this->base_api_url}/trips?include={$include}&{$filter_query}";
			}else{
				$target_url = "{$this->base_api_url}/trips?{$filter_query}";
			}
			$result = $this->executeCurl($target_url);
			if(isset($result->data))
				return $result->data;
			else
				return NULL;
		}	

		function getStops($filter, $include = NULL){
			$filter_query = http_build_query($filter);
			if($include){
				$include = urlencode($include);
				$target_url = "{$this->base_api_url}/stops?include={$include}&{$filter_query}";
			}else{
				$target_url = "{$this->base_api_url}/stops?{$filter_query}";
			}
			$result = $this->executeCurl($target_url);
			if(isset($result->data))
				return $result->data;
			else
				return NULL;
		}
		
		
		function getVehicles($filter, $include = NULL){
			$filter_query = http_build_query($filter);
			if($include){
				$include = urlencode($include);
				$target_url = "{$this->base_api_url}/vehicles?include={$include}&{$filter_query}";
			}else{
				$target_url = "{$this->base_api_url}/vehicles?{$filter_query}";
			}
			$result = $this->executeCurl($target_url);
			return $result;
			/*
			if(isset($result->data))
				return $result->data;
			else{
				var_dump($result);
				exit();
				$return = new stdClass();
				$return->error = $result->code;
				return $return;
			}
			*/
		}	
		
		function getRoutes($filter, $include = NULL){
			$filter_query = http_build_query($filter);
			if($include){
				$include = urlencode($include);
				$target_url = "{$this->base_api_url}/routes?include={$include}&{$filter_query}";
			}else{
				$target_url = "{$this->base_api_url}/routes?{$filter_query}";
			}

			$result = $this->executeCurl($target_url);
			return $result->data;		
		}
	
		function getPredictions($filter, $include = NULL){
			$filter_query = http_build_query($filter);
			if($include){
				$include = urlencode($include);
				$target_url = "{$this->base_api_url}/predictions?include={$include}&{$filter_query}";
			}else{
				$target_url = "{$this->base_api_url}/predictions?{$filter_query}";
			}

			$result = $this->executeCurl($target_url);
			return $result->data;		
		}
		
		function getSchedule($filter){
			$url_query = http_build_query($filter);
			$target_url = "{$this->base_api_url}/schedules?{$url_query}";
			$result = $this->executeCurl($target_url);
			return $result->data;		
		}
		
		function getTripInfo($trip_id){
			$trip_id = urlencode($trip_id);
			$target_url = "{$this->base_api_url}/trips/{$trip_id}";
			$result = $this->executeCurl($target_url);
			if(isset($result->data))
				return $result->data;
			else
				return NULL;
		}	

		function getRoutePatternInfo($route_pattern_id){
			$trip_id = urlencode($route_pattern_id);
			$target_url = "{$this->base_api_url}/route_patterns/{$route_pattern_id}";
			$result = $this->executeCurl($target_url);
			return $result->data;		
		}
		
		function getShapeInfo($shape_id){
			$shape_id = urlencode($shape_id);
			$target_url = "{$this->base_api_url}/shapes/{$shape_id}";
			$result = $this->executeCurl($target_url);
			return $result->data;		
		}
		
		function getStopInfo($stop_id){
			$stop_id = urlencode($stop_id);
			$target_url = "{$this->base_api_url}/stops/{$stop_id}";
			$result = $this->executeCurl($target_url);
			return $result->data;		
		}	
		
		function getRouteInfo($route_id){
			$route_id = urlencode($route_id);
			$target_url = "{$this->base_api_url}/routes/{$route_id}";
			$result = $this->executeCurl($target_url);
			return $result->data;		
		}			
	}
?>