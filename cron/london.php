<?php

include('../config.php');
$feedURL = 'http://www.tfl.gov.uk/tfl/syndication/feeds/cycle-hire/livecyclehireupdates.xml';
$dbName = $dbLondon;
$feedType = "xml";

include('functions/record.php');

?>