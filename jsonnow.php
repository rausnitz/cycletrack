<?php

$jsonURL = file_get_contents($feedURL, true)
or exit ($bikeshareSystem." did not respond to your request. Please refresh the page.");

$jsonDoc = json_decode($jsonURL, true);

$stationChoice = $s;
$stationNumInteger = (int)$s;
$stationsInfo = $jsonDoc['stationBeanList'];

foreach ($stationsInfo as $b) {
  
  if ($b['id'] == $stationNumInteger ) {
      $stationPosition = array_search($b,$stationsInfo);
  }
  
}

$stationName = $stationsInfo[$stationPosition]["stationName"];
$stationBikes = $stationsInfo[$stationPosition]["availableBikes"];
$stationDocks = $stationsInfo[$stationPosition]["availableDocks"];

?>