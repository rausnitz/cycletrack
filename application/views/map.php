<!DOCTYPE html>

<html>

<head>
<title>Cycletrack for <?php echo $systemName; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script type="text/javascript" src='/vendor/jquery/1.11.2/jquery.min.js'></script>
<script type="text/javascript" src='/vendor/mapbox/2.1.9/mapbox.js'></script>
<script type="text/javascript" src='/vendor/d3/scale.min.js'></script>
<link href='/vendor/mapbox/2.1.9/mapbox.css' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Inconsolata:400,700' rel='stylesheet' type='text/css'>

<?php
echo HTML::style('assets/css/map.css');
?>

</head>

<body>

<div id="map"></div>

<script>
var baseURL = "<?php echo URL::base(); ?>",
	city = "<?php echo $city; ?>",
	systemName = "<?php echo $systemName; ?>";

var getColor = scale.linear()
	.domain([0, 0.01, 0.5, 0.99, 1])
	.range(["red", "orange", "yellow", "green", "blue"]); // color scale for markers
	
var foundYet = false; // haven't tried to find location yet

function updateData(){
	foundYet = true;
	$("#refresh_button").hide();
	$("#refresh_bar").show(); // show loading gif
	$(".current_status").css("color","rgba(187, 187, 187, 1)"); // popup text turns gray during update
	map.on('popupopen', function(e) { // popup text turns gray if popup opened during update
		$(".current_status").css("color","rgba(187, 187, 187, 1)");
	});
	function reset(){
		// replace loading gif with refresh button
		$("#refresh_button").show();
		$("#refresh_bar").hide();
		$(".current_status").css("color","rgba(0, 0, 0, 1)"); // back to original color
		map.on('popupopen', function(e) {
			$(".current_status").css("color","rgba(0, 0, 0, 1)");
		});
	};
	$.ajax({
	  method: "GET",
	  url: baseURL + city + '/ajax',
	  dataType: "script",
	  complete: reset
	});
}

L.mapbox.accessToken = '<?php echo $mapboxToken; ?>';
var map = L.mapbox.map('map', '<?php echo $mapboxTiles; ?>').locate({setView: true, maxZoom: 14});

var legend = L.control({position: 'topright'});
legend.onAdd = function () {
	var div = L.DomUtil.create('div', 'legend'),
		grades = [1, 0.99, 0.5, 0.01, 0],
		labels = ["full", "almost full", "balanced", "almost empty", "empty"];
	div.innerHTML += '<div class="legend-item" id="app_title"><a href="' + baseURL + '" target="_blank">Cycletrack</a></div>';
	for (var i = 0; i < grades.length; i++) {
		div.innerHTML +=
			'<div class="legend-item"><span style="background:' + getColor(grades[i]) + '"></span> ' + labels[i] + '</div>';
	}
	div.innerHTML += '<hr><div class="legend-item"><span style="border: 2px solid blue;"></span> filling fast</div><div class="legend-item"><span style="border: 2px solid red;"></span> emptying fast</div>';
	div.innerHTML += '<div class="legend-item" id="refresh_button">refresh data</div><div class="legend-item" id="refresh_bar"><img src="' + baseURL + 'assets/images/updating-bar.gif" width="90" style="max-width: 100%;"></div>';
	return div;
};
legend.addTo(map);

$( "#refresh_button" ).click(function( event ) {
	event.stopPropagation(); // otherwise, a popup that is open will close when the control box is clicked
	updateData();
});
	
var markers = new L.featureGroup();

var stationData = {};

<?php echo $markerScript; ?>

function popupText(station) {
	var name = "<div class='station_name'><strong>" + station.name + "</strong></div>",
		start = "<table class='current_status'><tr id='labels'><td></td><td>Bikes</td><td>Docks</td></tr>",
		now = "<tr class='now'><td>now</td><td>" + station.bikes0 + "</td><td>" + station.docks0 + "</td></tr>",
		end = "</table><div class='updated_time current_status'>updated at " + timeAtUpdate + "</div><div class='popup_buttons'><a href='#' onclick='updateData()'>Refresh</a> &bull; <a href='" + baseURL + city + "/" + station.id + "' target='_blank'>Pop Out</a></div>";
		then = '';
		for (var z = 1; z < 4; z++ ) {
			then += "<tr class='recorded'><td>" + window['time' + z] + "</td><td>" + station['bikes' + z] + "</td><td>" + station['docks' + z] + "</td></tr>";
		}
		
	return name + start + now + then + end;
}

function drawMarkers() {
	for (var prop in stationData) {
		var station = stationData[prop],
			name = station.name,
			bikes0 = station.bikes0, emptyDocks0 = station.docks0,
			bikes1 = station.bikes1, emptyDocks1 = station.docks1,
			bikes2 = station.bikes2, emptyDocks2 = station.docks2,
			bikes3 = station.bikes3, emptyDocks3 = station.docks3,
			bikes_share_ha_ha = bikes0 / (bikes0 + emptyDocks0), // how much of the station currently contains bikes
			marker = station.marker;
		marker.setStyle({fillColor: getColor(bikes_share_ha_ha), weight: 3, opacity: 1, fillOpacity: 1}).setRadius(14);
		
		var minutesElapsed = (unixtime0 - unixtime2) / 60, // will be between 10 and 20 minutes
				bikes_share_past = bikes2 / (bikes2 + emptyDocks2),
				bikes_share_change = bikes_share_ha_ha - bikes_share_past,
				pcntChangePerMin = (bikes_share_change * 100) / minutesElapsed;
		
		if (bikes_share_ha_ha == 0 || bikes_share_ha_ha == 1) {
			marker.setStyle({stroke: false});
		} else if (pcntChangePerMin > 2) { // filling at more than 2 percentage points per minute
			marker.setStyle({stroke: true, color: 'blue'});
			console.log(name + ' has changed ' + (bikes_share_change * 100) + ' percentage points.');
		} else if (pcntChangePerMin < -2) { // emptying at more than 2 percentage points per minute
			marker.setStyle({stroke: true, color: 'red'});
			console.log(name + ' has changed ' + (bikes_share_change * 100) + ' percentage points.');
		} else {
			marker.setStyle({stroke: false});
		}
		
		station.marker.bindPopup(popupText(station));
		station.marker.addTo(markers);
	}
	console.log(minutesElapsed + ' minutes have passed.');
}
drawMarkers();

var userMarker = new L.layerGroup();

map.on('locationfound', function(e) {
	userMarker.clearLayers();
	
	var locationHelp = "<div class='your_location'>This is your location. Click <a href='javascript: map.fitBounds(markers.getBounds());'>here</a> to expand the map and see all the " + systemName + " stations.</div>";
	
	theMarker = L.circleMarker(e.latlng).setRadius(14).addTo(userMarker).bindPopup(locationHelp).setStyle({fillColor: '#888', stroke: false, fillOpacity: 1, opacity: 1});
	userMarker.addTo(map);
	
	// only open the user marker's popup automatically if the marker is located outside the bounds of the bikeshare system
	// and only when the user's location has been found for the first time
	if ( !foundYet && !markers.getBounds().contains(e.latlng) ) {
		theMarker.openPopup();
	}
	foundYet = true;
});

map.on('locationerror', function(e) {
	if (!foundYet) { // do fitBounds on locationerror only if this is the first location find
		map.fitBounds(markers.getBounds());
	}
});

markers.addTo(map);

setInterval(updateData, 180000); // update every three minutes

map.attributionControl.addAttribution('<a href="http://github.com/rausnitz" target="_blank">&copy; Zach Rausnitz</a>');
	
</script>
	
</body>

</html>