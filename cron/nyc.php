<?php

include('../config.php');
$feedURL = 'http://citibikenyc.com/stations/json';
$dbName = $dbNYC;
$feedType = "json";

include('functions/record.php');

?>