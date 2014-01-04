<?php

include('../config.php');
$feedURL = 'http://capitalbikeshare.com/data/stations/bikeStations.xml';
$dbName = $dbDC;
$feedType = "xml";

include('functions/record.php');

?>