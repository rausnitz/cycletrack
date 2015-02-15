<?php

include('../config.php');
$feedURL = 'https://toronto.bixi.com/data/bikeStations.xml';
$dbName = $dbToronto;
$feedType = "xml";

include('functions/record.php');

?>