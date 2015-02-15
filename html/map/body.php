<body>

<div id="map"></div>

<?php
if ($defaultLong < -30) {
  $tile = 'http://{s}.tile.stamen.com/toner-lite/{z}/{x}/{y}.png';
 } else {
  $tile = 'http://{s}.tile.stamen.com/toner-lite/{z}/{x}/{y}.png';
}
?>

<script>

		map = new L.Map('map', {
	attributionControl: true
});
	
	L.tileLayer('<?php echo $tile;?>', {
			minZoom: 4,
			maxZoom: 18,
			subdomains: 'abcd',
			attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a> |&nbsp;<span style="background-color:; color:; font-weight:bold;">you are using <a style=""href="http://rausnitz.com/iq" target="_blank">BIKESHARE IQ</a> &#8226; <a style="" href="<?php echo $iqURL;?>list.php" target="_blank">text-only version</a> &#8226; by <a href="http://rausnitz.com" target="_blank">zach rausnitz</a></span>'
		}).addTo(map);
		
				var blueIcon = L.icon({
    iconUrl: 'http://www.rausnitz.com/bikesharetimer/img/bluemarker.png',
    iconRetinaUrl: 'http://www.rausnitz.com/bikesharetimer/img/bluemarker.png',
    iconSize: [20, 20],
    popupAnchor: [0, -3],
});
				var greenIcon = L.icon({
    iconUrl: 'http://www.rausnitz.com/bikesharetimer/img/greenmarker.png',
    iconRetinaUrl: 'http://www.rausnitz.com/bikesharetimer/img/greenmarker.png',
    iconSize: [20, 20],
    popupAnchor: [0, 0],
});

	
<?php 
error_reporting(0);
$stationNumbersObjects = array();
$stationNamesObjects = array();
$stationBikesObjects = array();
$stationDocksObjects = array();
$stationLatsObjects = array();
$stationLongsObjects = array();
$stationNumbers = array();
$stationNames = array();
$stationBikes = array();
$stationDocks = array();
$stationLats = array();
$stationLongs = array();

if ($feedType == "xml") {
    $xmlFeed = simplexml_load_file($feedURL); 
    
	foreach ($xmlFeed->xpath( '//terminalName') as $numbers) {
      $stationNumbersObjects[] = $numbers;
    }

	foreach ($xmlFeed->xpath( '//name') as $names) {
      $stationNamesObjects[] = $names;
    }
	
	foreach( $xmlFeed->xpath( '//nbBikes') as $bikes) {
      $stationBikesObjects[] = $bikes;
    }

foreach( $xmlFeed->xpath( '//nbEmptyDocks') as $docks) {
      $stationDocksObjects[] = $docks;
    }
 
foreach( $xmlFeed->xpath( '//lat') as $lats) {
      $stationLatsObjects[] = $lats;
    }

foreach( $xmlFeed->xpath( '//long') as $longs) {
      $stationLongsObjects[] = $longs;
    }

    foreach ($stationNumbersObjects as $a) {
	  $stationNumbers[] = "$a";
	}

	foreach ($stationNamesObjects as $a) {
	  $stationNames[] = "$a";
	}
	
foreach ($stationBikesObjects as $a) {$stationBikes[] = "$a";}
foreach ($stationDocksObjects as $a) {$stationDocks[] = "$a";}
foreach ($stationLatsObjects as $a) {$stationLats[] = "$a";}
foreach ($stationLongsObjects as $a) {$stationLongs[] = "$a";}

} else {
    $jsonURL = file_get_contents($feedURL, true);
    $jsonDoc = json_decode($jsonURL, true);
    $stationInfo = $jsonDoc['stationBeanList'];

    foreach ($stationInfo as $station) {
      $stationNames[] = $station['stationName'];
      $stationNumbersRaw[] = $station['id'];
	  $stationBikes[] = $station['availableBikes'];
	  $stationDocks[] = $station['availableDocks'];
	  $stationLats[] = $station['latitude'];
	  $stationLongs[] = $station['longitude'];
    }

    foreach ($stationNumbersRaw as $a) {
      $stationNumbers[] = str_pad($a,8,"0",STR_PAD_LEFT);
    }
}

date_default_timezone_set($timeZone);

mysql_connect($mysqlHost, $mysqlUser, $mysqlPass);
mysql_select_db($dbName);

function ifDate($time) {
if ($time) {$show = date('g:i a', $time);}
else {$show = "(error)";}
return $show;
}

for ($i = 0; $i < count($stationNumbers); $i++) {
    $stationEsc = mysql_real_escape_string($stationNumbers[$i]);
$result1 = mysql_query("SELECT * FROM saved1 WHERE stationnumber='$stationEsc'");
$row1 = mysql_fetch_array($result1);
$result2 = mysql_query("SELECT * FROM saved2 WHERE stationnumber='$stationEsc'");
$row2 = mysql_fetch_array($result2);
$result3 = mysql_query("SELECT * FROM saved3 WHERE stationnumber='$stationEsc'");
$row3 = mysql_fetch_array($result3);
    echo 'L.marker([' . $stationLats[$i] . ', ' . $stationLongs[$i] . '],{icon: blueIcon}).addTo(map)'
	. '.bindPopup("<span class=\'iq-popup\'><strong>' . $stationNames[$i] . '</strong><br /><hr />bikes & open docks:<br /><strong><span id=\'status' . $stationNumbers[$i] . '\'>'
	. $stationBikes[$i] . ' & ' . $stationDocks[$i] . ' at ' . date('g:i:s a', time()) . '&mdash;</span>' 
	. '<span id=\'loading' . $stationNumbers[$i] . '\'><a href=\'#\' onclick=\'updateCurrent(&quot;' . $stationNumbers[$i]
	. '&quot;)\'>update</a></span></strong><br /><hr />' . $row1[2] . ' & ' . $row1[3] . ' at ' . ifDate($row1[4])
	. '<br />' . $row2[2] . ' & ' . $row2[3] . ' at ' . ifDate($row2[4])
	. '<br />' . $row3[2] . ' & ' . $row3[3] . ' at ' . ifDate($row3[4])
	. '<hr /><a href=\'' . $iqURL . 'now.php?s='
	. $stationNumbers[$i] . '\' target=\'_blank\'' . '>POP OUT</a></span>", {maxWidth: 185});' . "\n\n"
	;
}


?>
		
			function onLocationFound(e) {
				L.marker(e.latlng, {icon: greenIcon}).addTo(map)
				.bindPopup("<span class='iq-popup'>This is you. <a href='javascript:map.setView(new L.LatLng(<?php echo $defaultLat; ?>, <?php echo $defaultLong; ?>), <?php echo $defaultZoom; ?>);'>See all the stations</a>.</span>", {maxWidth: 200});
			
		}

		function onLocationError(e) {
			map.setView(new L.LatLng(<?php echo $defaultLat; ?>, <?php echo $defaultLong; ?>), <?php echo $defaultZoom; ?>)
		}

		map.on('locationfound', onLocationFound);
		map.on('locationerror', onLocationError);
		
		map.locate({setView: true, maxZoom: 14});	


	</script>




</body></html>