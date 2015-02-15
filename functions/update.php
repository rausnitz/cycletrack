<?php
date_default_timezone_set($timeZone);
$s = $_GET['s'];

if ($feedType == "xml") {
    $xmlDoc = simplexml_load_file($feedURL);
    $stationChoice = $xmlDoc->xpath("station/terminalName[.='$s']/parent::*");
    $stationBikes = print_r((string) $stationChoice[0]->nbBikes, true);
    $stationDocks = print_r((string) $stationChoice[0]->nbEmptyDocks, true);
} else {
    $jsonURL = file_get_contents($feedURL, true);
    $jsonDoc = json_decode($jsonURL, true);
    $stationChoice = $s;
    $stationNumInteger = (int)$s;
    $stationsInfo = $jsonDoc['stationBeanList'];

    foreach ($stationsInfo as $b) {
	  if ($b['id'] == $stationNumInteger ) {
        $stationPosition = array_search($b,$stationsInfo);
      }
    }

$stationBikes = $stationsInfo[$stationPosition]["availableBikes"];
$stationDocks = $stationsInfo[$stationPosition]["availableDocks"];
}
echo '<span style=\'color:#4671d5;\'>'
	. $stationBikes . ' & ' . $stationDocks . ' at '
    . date('g:i:s a', time())
	. '&mdash;</span>';
?>