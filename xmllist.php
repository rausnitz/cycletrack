<?php

$xmlFeed = simplexml_load_file($feedURL); 

$stationNumbersXML = array();
$stationNamesXML = array ();

foreach ($xmlFeed->xpath( '//terminalName') as $numbers) {
  $stationNumbersXML[] = $numbers;
}
foreach ($xmlFeed->xpath( '//name') as $names) {
  $stationNamesXML[] = $names;
}

$stationNumbers = array();
$stationNames = array();

foreach ($stationNumbersXML as $a) {$stationNumbers[] = "$a";}
foreach ($stationNamesXML as $a) {$stationNames[] = "$a";}

include '../stationcontent.php';

?>