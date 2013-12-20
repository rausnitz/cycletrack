<?php

$jsonURL = file_get_contents($feedURL, true);
$jsonDoc = json_decode($jsonURL, true);

$stationNumbersRaw = array();
$stationNumbers = array();
$stationNames = array();
$stationInfo = $jsonDoc['stationBeanList'];

foreach ($stationInfo as $station) {
  $stationNames[] = $station['stationName'];
  $stationNumbersRaw[] = $station['id'];
}

foreach ($stationNumbersRaw as $a) {
  $stationNumbers[] = str_pad($a,8,"0",STR_PAD_LEFT);
}

include '../stationcontent.php';

?>