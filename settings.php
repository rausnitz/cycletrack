<?php

$dcSettings = array(
'bikeshareSystem' => 'Capital Bikeshare',
'feedURL' => 'http://capitalbikeshare.com/data/stations/bikeStations.xml',
'feedType' => 'xml',
'dbName' => $dbDC,
'stationNumLength' => 5,
'timeZone' => 'America/New_York',
'iqURL' => 'http://www.rausnitz.com/iq/dc/'
);

$bostonSettings = array(
'bikeshareSystem' => 'Hubway',
'feedURL' => 'http://thehubway.com/data/stations/bikeStations.xml',
'feedType' => 'xml',
'dbName' => $dbBoston,
'stationNumLength' => 6,
'timeZone' => 'America/New_York',
'iqURL' => 'http://www.rausnitz.com/iq/boston/'
);

$nycSettings = array(
'bikeshareSystem' => 'Citi Bike NYC',
'feedURL' => 'http://citibikenyc.com/stations/json',
'feedType' => 'json',
'dbName' => $dbNYC,
'stationNumLength' => 8,
'timeZone' => 'America/New_York',
'iqURL' => 'http://www.rausnitz.com/iq/nyc/'
);

$chicagoSettings = array(
'bikeshareSystem' => 'Divvy',
'feedURL' => 'http://divvybikes.com/stations/json',
'feedType' => 'json',
'dbName' => $dbChicago,
'stationNumLength' => 8,
'timeZone' => 'America/Chicago',
'iqURL' => 'http://www.rausnitz.com/iq/chicago/'
);

$minnSettings = array(
'bikeshareSystem' => 'Nice Ride Minnesota',
'feedURL' => 'https://secure.niceridemn.org/data2/bikeStations.xml',
'feedType' => 'xml',
'dbName' => $dbMinn,
'stationNumLength' => 5,
'timeZone' => 'America/Chicago',
'iqURL' => 'http://www.rausnitz.com/iq/minn/'
);

$londonSettings = array(
'bikeshareSystem' => 'Barclays Cycle Hire',
'feedURL' => 'http://www.tfl.gov.uk/tfl/syndication/feeds/cycle-hire/livecyclehireupdates.xml',
'feedType' => 'xml',
'dbName' => $dbLondon,
'stationNumLength' => 6,
'timeZone' => 'Europe/London',
'iqURL' => 'http://www.rausnitz.com/iq/london/'
);

$baySettings = array(
'bikeshareSystem' => 'Bay Area Bike Share',
'feedURL' => 'http://bayareabikeshare.com/stations/json',
'feedType' => 'json',
'dbName' => $dbBay,
'stationNumLength' => 8,
'timeZone' => 'America/Los_Angeles',
'iqURL' => 'http://www.rausnitz.com/iq/bay/'
);

$getSettings = array(
'dc' => $dcSettings,
'boston' => $bostonSettings,
'nyc' => $nycSettings,
'chicago' => $chicagoSettings,
'minn' => $minnSettings,
'bay' => $baySettings,
'london' => $londonSettings
);

?>

