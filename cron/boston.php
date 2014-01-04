<?php

include('../config.php');
$feedURL = 'http://thehubway.com/data/stations/bikeStations.xml';
$dbName = $dbBoston;
$feedType = "xml";

include('functions/record.php');

?>