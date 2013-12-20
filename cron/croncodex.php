<?php
$xmlDoc = simplexml_load_file($feedURL); 

$stationNumbersXML = array();
$stationNamesXML = array ();
$stationBikesXML = array ();
$stationDocksXML = array ();

foreach( $xmlDoc->xpath( '//terminalName') as $numbers) {
    $stationNumbersXML[] = $numbers;
}
foreach( $xmlDoc->xpath( "//name") as $names) {
    $stationNamesXML[] = $names;
}
foreach( $xmlDoc->xpath( '//nbBikes') as $bikes) {
    $stationBikesXML[] = $bikes;
}
foreach( $xmlDoc->xpath( '//nbEmptyDocks') as $docks) {
    $stationDocksXML[] = $docks;
}

$stationNumbers = array();
$stationNames = array();
$stationBikes = array();
$stationDocks = array();


foreach ($stationNumbersXML as $a) {$stationNumbers[] = "$a";}
foreach ($stationNamesXML as $a) {$stationNames[] = "$a";}
foreach ($stationBikesXML as $a) {$stationBikes[] = "$a";}
foreach ($stationDocksXML as $a) {$stationDocks[] = "$a";}


	?>