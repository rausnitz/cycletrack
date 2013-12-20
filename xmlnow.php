<?php

$xmlDoc = simplexml_load_file($feedURL)
or exit ($bikeshareSystem." did not respond to your request. Please refresh the page."); 

$stationChoice = $xmlDoc->xpath("station/terminalName[.='$s']/parent::*");

$stationName = print_r((string) $stationChoice[0]->name, true);
$stationBikes = print_r((string) $stationChoice[0]->nbBikes, true);
$stationDocks = print_r((string) $stationChoice[0]->nbEmptyDocks, true);

?>