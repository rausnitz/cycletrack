<?php

include('../config.php');
$feedURL = 'https://secure.niceridemn.org/data2/bikeStations.xml';
$dbName = $dbMinn;
$feedType = "xml";

include('functions/record.php');

?>