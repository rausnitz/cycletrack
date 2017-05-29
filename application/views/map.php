<!DOCTYPE html>

<html>

<head>
<title>Cycletrack for <?php echo $systemName; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="@rausnitz" />
<meta name="twitter:creator" content="@rausnitz" />
<meta property="og:title" content="Cycletrack for <?php echo $systemName; ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.rausnitz.com/track/<?php echo $city; ?>" />
<meta property="og:image" content="https://www.rausnitz.com/track/assets/images/og.jpg" />
<meta property="og:description" content="A web app that provides bikeshare station status information." />

<script type='text/javascript' src='/vendor/jquery/1.11.2/jquery.min.js'></script>
<script type='text/javascript' src='/vendor/mapbox/2.1.9/mapbox.js'></script>
<script type='text/javascript' src='/vendor/d3/scale.min.js'></script>
<link href='/vendor/mapbox/2.1.9/mapbox.css' rel='stylesheet' type='text/css'/>
<link href='https://fonts.googleapis.com/css?family=Inconsolata:400,700' rel='stylesheet' type='text/css'>
<link href='/vendor/font-awesome/4.3.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'/>

<script type='text/javascript' src='/vendor/mapbox/plugins/locatecontrol/L.Control.Locate.min.js'></script>
<link href='/vendor/mapbox/plugins/locatecontrol/L.Control.Locate.mapbox.css' rel='stylesheet' type='text/css'/>
<!--[if lt IE 9]>
<link href='/vendor/mapbox/plugins/locatecontrol/L.Control.Locate.ie.css' rel='stylesheet' type='text/css'/>
<![endif]-->

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

// static data about each station (id, active or not, name, latitude, longitude)
var stationData = <?php echo json_encode($allStationsData); ?>;
// number of bikes and empty docks at each station (separated from stationData so that an ajax call can easily replace with fresh bike/dock data)
var stationDockData = <?php echo json_encode($allStationsDockData); ?>;
// store markers in stationData object so that an ajax call can update each station's markers instead of drawing new ones
for (var prop in stationData) {
	stationData[prop].marker = L.circleMarker([stationData[prop].latitude, stationData[prop].longitude]);
}

// the time at which each feed was scraped
var unixtimes = <?php echo json_encode($unixtimes); ?>;
var times = <?php echo json_encode($times); ?>;

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

L.control.locate({
	drawCircle: false,
	remainActive: true,
	showPopup: false,
	onLocationError: function() {}, // nothing, instead of the default function
	markerStyle: {weight: 0, fillOpacity: 0, opacity: 0, radius: 0}
}).addTo(map);

$(".leaflet-control-locate").hide(); // button is shown later, once location is found

var markers = new L.featureGroup();

function popupText(station) {
	var name = "<div class='station_name'><strong>" + stationData[station].name + "</strong></div>",
		start = "<table class='current_status'><tr id='labels'><td></td><td>Bikes</td><td>Docks</td></tr>",
		now = "<tr class='now'><td>now</td><td>" + stationDockData[station].feed0.bikes + "</td><td>" + stationDockData[station].feed0.docks + "</td></tr>",
		end = "</table><div class='updated_time current_status'>updated at " + times[0] + "</div><div class='popup_buttons'><a href='#' onclick='updateData()'>Refresh</a> &bull; <a href='" + baseURL + city + "/" + stationData[station].id + "' target='_blank'>Pop Out</a></div>";
		then = '';
		for (var z = 1; z < 4; z++ ) {
			if (typeof stationDockData[station]['feed' + z] !== "undefined") {
				then += "<tr class='recorded'><td>" + times[z] + "</td><td>" + stationDockData[station]['feed' + z].bikes + "</td><td>" + stationDockData[station]['feed' + z].docks + "</td></tr>";
			}
		}

	return name + start + now + then + end;
}

function drawMarkers() {
	for (var prop in stationData) {
		var station = stationData[prop],
			name = stationData[prop].name,
			bikes = new Array(),
			emptyDocks = new Array(),
			marker = stationData[prop].marker;

		for (var feed = 0; feed < 4; feed++ ) {
			if (typeof stationDockData[prop]['feed' + feed] !== "undefined") {
				bikes[feed] = stationDockData[prop]['feed' + feed].bikes;
				emptyDocks[feed] = stationDockData[prop]['feed' + feed].docks;
			}
		}
		var	bikes_share_ha_ha = bikes[0] / (bikes[0] + emptyDocks[0]); // how much of the station currently contains bikes
		marker.setStyle({fillColor: getColor(bikes_share_ha_ha), weight: 3, opacity: 1, fillOpacity: 1}).setRadius(14);

		var minutesElapsed = (unixtimes[0] - unixtimes[2]) / 60, // will be between 10 and 20 minutes
				bikes_share_past = bikes[2] / (bikes[2] + emptyDocks[2]),
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

		marker.bindPopup(popupText(prop));
		if (station.show) {
			marker.addTo(markers);
		}
	}
	console.log(minutesElapsed + ' minutes have passed.');
}
drawMarkers();

var userMarker = new L.layerGroup();

map.on('locationfound', function(e) {
	userMarker.clearLayers();

	var locationHelp = "<div class='your_location'>This is your location. Click <a href='#' onclick='map.fitBounds(markers.getBounds());'>here</a> to expand the map and see all the " + systemName + " stations.</div>";

	theMarker = L.circleMarker(e.latlng).setRadius(14).addTo(userMarker).bindPopup(locationHelp).setStyle({fillColor: '#888', stroke: false, fillOpacity: 1, opacity: 1});
	userMarker.addTo(map);

	// only open the user marker's popup automatically if the marker is located outside the bounds of the bikeshare system
	// and only when the user's location has been found for the first time
	if ( !foundYet && !markers.getBounds().contains(e.latlng) ) {
		theMarker.openPopup();
	}
	foundYet = true;

	$(".leaflet-control-locate").show();
});

map.on('locationerror', function(e) {
	if (!foundYet) { // do fitBounds on locationerror only if this is the first location find
		map.fitBounds(markers.getBounds());
	}
});

map.on('popupopen', function(e) {
	// disable autopan once popup has opened (not affecting the intial autopan on popupopen)
	// otherwise when the updateData function is called, the map will autopan back to whichever popup is open, which is not desirable if the user has panned away from an open popup without closing it
	e.popup.options.autoPan = false;
 });

map.on('popupclose', function(e) {
	// re-enable autopan so that when the user opens the popup again, it can once again autopan into view
	e.popup.options.autoPan = true;
 });

markers.addTo(map);

setInterval(updateData, 180000); // update every three minutes

map.attributionControl.addAttribution('<a href="https://github.com/rausnitz" target="_blank">&copy; Zach Rausnitz</a>');

</script>

</body>

</html>
