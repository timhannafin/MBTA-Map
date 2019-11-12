<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  
	
	<!--


https://maps.googleapis.com/maps/api/staticmap?key=YOUR_API_KEY&center=42.34267778290006,-71.1105487205086&zoom=12&format=png&maptype=roadmap&style=element:labels%7Cvisibility:off&style=feature:administrative%7Celement:geometry%7Cvisibility:off&style=feature:landscape%7Cvisibility:off&style=feature:landscape%7Celement:geometry.fill%7Ccolor:0xffffff%7Cvisibility:on&style=feature:landscape%7Celement:geometry.stroke%7Ccolor:0x000000&style=feature:poi%7Cvisibility:off&style=feature:road%7Cvisibility:off&style=feature:road%7Celement:labels.icon%7Cvisibility:off&style=feature:transit%7Cvisibility:off&size=480x360
-->
	

<style type="text/css">
.form-check-label{
	font-size:12px;
}
</style>

</head>

<body>
    <div class="container-fluid">
    	<div class="row">
        	<div class="col-1 align-left">
            	<p id="timestamp">00:00:00 AM</p>
            	<form id="display-types-form">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Rapid Transit" id="rapid_transit" checked/>
                        <label class="form-check-label" for="rapid_transit">Rapid Transit</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Commuter Rail" id="commuter_rail"/>
                        <label class="form-check-label" for="comutter_rail">Commuter Rail</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Local Bus" id="local_bus"/>
                        <label class="form-check-label" for="local_bus">Local Bus</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Key Bus" id="key_bus"/>
                        <label class="form-check-label" for="key_bus">Key Bus</label>
                    </div>
                </form>
            </div>
        	<div class="col-11">
    			<canvas id="mapCanvas"></canvas>
            </div>
        </div>
    </div>
<script>
var canvas_size = 1000;
var canvas;
var canvas_context;
var display_types = [];

var north_lat_limit = 42.50283138;
var south_lat_limit = 42.21306895;

var east_lon_limit = -70.8682396;
var west_lon_limit = -71.2589284;	
	
function build_display_type_array(){
	display_types = [];
	$("#display-types-form")
	.find(".form-check-input")
		.each(function(){						 
				if( $(this).prop("checked")){
					display_types.push( $(this).attr("value") );
				}
		});
	console.log(display_types);
}

function hexToRgb(hex) {
  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? {
    red: parseInt(result[1], 16),
    green: parseInt(result[2], 16),
    blue: parseInt(result[3], 16)
  } : null;
}

function gps_to_coord(latitude,longitude){
	lat_factor = canvas_size/(north_lat_limit - south_lat_limit);
	lon_factor = canvas_size/(Math.abs(west_lon_limit) - Math.abs(east_lon_limit));
	//console.log({lat_factor,lon_factor});
	x = ( Math.abs(west_lon_limit) - Math.abs(longitude))*(lon_factor);
	y = (north_lat_limit - latitude)*(lat_factor);
	return {"x":x,"y":y};
}	
	
function draw_arrow(location, bearing, head_size, length, color) {
	var from = {};

	var angle = 90-bearing;
	if(angle<0){
		angle = angle+360;
	}
	angle = angle * (Math.PI / 180);
	
	var from = {};
	from.x = Math.round(location.x-(length*Math.cos(angle)));
	from.y = Math.round(location.y+(length*Math.sin(angle)));
	
	canvas_context.strokeStyle = 'rgba(' + color.red + ',' + color.green + ',' + color.blue + ',' + 255 + ')';
	canvas_context.beginPath();
	canvas_context.moveTo(location.x, location.y);
	canvas_context.lineTo(from.x, from.y);
	
	canvas_context.moveTo(location.x, location.y);
	var head_angle = Math.atan2(location.y - from.y, location.x - from.x);
	head_angle += (1.0/3.0) * (2 * Math.PI);

	var arrow_head = {};
	arrow_head.x = Math.round((head_size * Math.cos(head_angle)) + location.x);
	arrow_head.y = Math.round((head_size * Math.sin(head_angle)) + location.y);	
	canvas_context.lineTo(arrow_head.x, arrow_head.y);
	
	head_angle += (1.0/3.0) * (2 * Math.PI);
	canvas_context.moveTo(location.x, location.y);
	arrow_head.x = Math.round((head_size * Math.cos(head_angle)) + location.x);
	arrow_head.y = Math.round((head_size * Math.sin(head_angle)) + location.y);	
	canvas_context.lineTo(arrow_head.x, arrow_head.y);
	
	canvas_context.stroke();
}
	
function sync_location_data(){
	$.ajax("backend/cache/locations.json",{
		method:"GET",
		dataType:"json",
		success:function(data){
			location_data = data;
		}
	});	
}


var stops_locations;
function draw_stops(){
	//var px = canvas_context.createImageData(5, 5);
	for (var i in stops_locations) {
		canvas_context.beginPath();

		var coords = gps_to_coord(stops_locations[i].attributes.latitude,stops_locations[i].attributes.longitude);
		canvas_context.arc(coords.x, coords.y, 5, 0,  2 * Math.PI, false);
		canvas_context.strokeStyle = '#e5e5e5';
		canvas_context.lineWidth = 1;
		canvas_context.stroke();
	}	
}

var location_data;
var frame_counter=0;	
function draw_data_frame(){
	$("#timestamp").html(location_data[frame_counter].formatted_time);
	
	canvas_context.clearRect(0, 0, canvas_size, canvas_size);
	draw_stops();
	for(vehicle in location_data[frame_counter].locations){
		
		if( display_types.find( function(element){ return element == location_data[frame_counter].locations[vehicle].type } ) ){
			var color = hexToRgb(location_data[frame_counter].locations[vehicle].color);
			var coords = gps_to_coord(location_data[frame_counter].locations[vehicle].latitude,location_data[frame_counter].locations[vehicle].longitude);
			draw_arrow({x,y},location_data[frame_counter].locations[vehicle].bearing, 3, 10, color);
		}
	}
	frame_counter++;
	if(frame_counter > location_data.length-1){
		frame_counter = 0;
		sync_location_data();
	}
}

build_display_type_array();
$(document).ready(function(){
	canvas = $("#mapCanvas")[0];
	canvas_context = canvas.getContext('2d');
	canvas_context.imageSmoothingEnabled = false;
	canvas_context.clearRect(0, 0, canvas_size, canvas_size);
	
	$("#display-types-form .form-check-input").change(function(){
		build_display_type_array();
	});
	
	$("#mapCanvas").css({"height":canvas_size+"px","width":canvas_size+"px"}).attr("height",canvas_size).attr("width",canvas_size);
	$.ajax("backend/cache/stops.json",{
		method:"GET",
		dataType:"json",
		success:function(data){
			stops_locations = data;
			draw_stops();			
		}
		
	});
	
	sync_location_data();
	setInterval(draw_data_frame, 1000);
/*	
	$.ajax("backend/cache/locations.json",{
		method:"GET",
		dataType:"json",
		success:function(data){
			location_data = data;
			frame_counter = 0;
			draw_data_frame();
			setInterval(draw_data_frame, 1000);
		}
	});
*/
 
});

</script>    
</body>
</html>