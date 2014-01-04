<?php

include('../config.php');

$station = $_GET['s'];
$feedDown = $bikeshareSystem . " is unresponsive. Please refresh the page.";

if ($feedType == "xml") {
    $xmlDoc = simplexml_load_file($feedURL) or exit ($feedDown);
    $stationChoice = $xmlDoc->xpath("station/terminalName[.='$station']/parent::*");
    $stationName = print_r((string) $stationChoice[0]->name, true);
    $stationBikes = print_r((string) $stationChoice[0]->nbBikes, true);
    $stationDocks = print_r((string) $stationChoice[0]->nbEmptyDocks, true);
} else {
    $jsonURL = file_get_contents($feedURL, true) or exit ($feedDown);
    $jsonDoc = json_decode($jsonURL, true);
    $stationChoice = $station;
    $stationNumInteger = (int)$station;
    $stationsInfo = $jsonDoc['stationBeanList'];

    foreach ($stationsInfo as $b) {
	  if ($b['id'] == $stationNumInteger ) {
        $stationPosition = array_search($b,$stationsInfo);
      }
    }

$stationName = $stationsInfo[$stationPosition]["stationName"];
$stationBikes = $stationsInfo[$stationPosition]["availableBikes"];
$stationDocks = $stationsInfo[$stationPosition]["availableDocks"];
}

date_default_timezone_set($timeZone);

mysql_connect($mysqlHost, $mysqlUser, $mysqlPass);
mysql_select_db($dbName);
$stationEsc = mysql_real_escape_string($station);
$result1 = mysql_query("SELECT * FROM saved1 WHERE stationnumber='$stationEsc'");
$row1 = mysql_fetch_array($result1);
$result2 = mysql_query("SELECT * FROM saved2 WHERE stationnumber='$stationEsc'");
$row2 = mysql_fetch_array($result2);
$result3 = mysql_query("SELECT * FROM saved3 WHERE stationnumber='$stationEsc'");
$row3 = mysql_fetch_array($result3);
$result4 = mysql_query("SELECT * FROM saved4 WHERE stationnumber='$stationEsc'");
$row4 = mysql_fetch_array($result4);