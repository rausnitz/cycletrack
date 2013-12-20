<?php

$stationNumbersRaw = array();
$stationNumbers = array();
$stationNames = array();
$stationBikes = array();
$stationDocks = array();
 
$jsonURL = file_get_contents($feedURL, true);
$jsonDoc = json_decode($jsonURL, true);

$stationInfo = $jsonDoc['stationBeanList'];

foreach ($stationInfo as $station){
$stationNames[] = $station['stationName'];
$stationNumbersRaw[] = $station['id'];
$stationBikes[] = $station['availableBikes'];
$stationDocks[] = $station['availableDocks'];
}

foreach ($stationNumbersRaw as $a){
$stationNumbers[] = str_pad($a,8,"0",STR_PAD_LEFT);
}

$stationNumsNames = array_combine($stationNumbers, $stationNames);
$stationNumsBikes = array_combine($stationNumbers, $stationBikes);
$stationNumsDocks = array_combine($stationNumbers, $stationDocks);
	?>
