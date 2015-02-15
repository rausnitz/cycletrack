<?php

$dcSettings = array(
'bikeshareSystem' => 'Capital Bikeshare',
'feedURL' => 'http://capitalbikeshare.com/data/stations/bikeStations.xml',
'feedType' => 'xml',
'dbName' => $dbDC,
'stationNumLength' => 5,
'timeZone' => 'America/New_York',
'iqURL' => 'http://www.rausnitz.com/iq/dc/',
'defaultLat' => '38.8951',
'defaultLong' => '-77.0367',
'defaultZoom' => '10'
);

$bostonSettings = array(
'bikeshareSystem' => 'Hubway',
'feedURL' => 'http://thehubway.com/data/stations/bikeStations.xml',
'feedType' => 'xml',
'dbName' => $dbBoston,
'stationNumLength' => 6,
'timeZone' => 'America/New_York',
'iqURL' => 'http://www.rausnitz.com/iq/boston/',
'defaultLat' => '42.366981',
'defaultLong' => '-71.076472',
'defaultZoom' => '12'
);

$nycSettings = array(
'bikeshareSystem' => 'Citi Bike NYC',
'feedURL' => 'http://citibikenyc.com/stations/json',
'feedType' => 'json',
'dbName' => $dbNYC,
'stationNumLength' => 8,
'timeZone' => 'America/New_York',
'iqURL' => 'http://www.rausnitz.com/iq/nyc/',
'defaultLat' => '40.72',
'defaultLong' => '-73.98672378',
'defaultZoom' => '12'
);

$chicagoSettings = array(
'bikeshareSystem' => 'Divvy',
'feedURL' => 'http://divvybikes.com/stations/json',
'feedType' => 'json',
'dbName' => $dbChicago,
'stationNumLength' => 8,
'timeZone' => 'America/Chicago',
'iqURL' => 'http://www.rausnitz.com/iq/chicago/',
'defaultLat' => '41.876065599',
'defaultLong' => '-87.6244333636',
'defaultZoom' => '11'
);

$minnSettings = array(
'bikeshareSystem' => 'Nice Ride Minnesota',
'feedURL' => 'https://secure.niceridemn.org/data2/bikeStations.xml',
'feedType' => 'xml',
'dbName' => $dbMinn,
'stationNumLength' => 5,
'timeZone' => 'America/Chicago',
'iqURL' => 'http://www.rausnitz.com/iq/minn/',
'defaultLat' => '44.96',
'defaultLong' => '-93.2',
'defaultZoom' => '11'
);

$londonSettings = array(
'bikeshareSystem' => 'Barclays Cycle Hire',
'feedURL' => 'http://www.tfl.gov.uk/tfl/syndication/feeds/cycle-hire/livecyclehireupdates.xml',
'feedType' => 'xml',
'dbName' => $dbLondon,
'stationNumLength' => 6,
'timeZone' => 'Europe/London',
'iqURL' => 'http://www.rausnitz.com/iq/london/',
'defaultLat' => '51.50',
'defaultLong' => '-0.1275',
'defaultZoom' => '11'
);

$baySettings = array(
'bikeshareSystem' => 'Bay Area Bike Share',
'feedURL' => 'http://bayareabikeshare.com/stations/json',
'feedType' => 'json',
'dbName' => $dbBay,
'stationNumLength' => 8,
'timeZone' => 'America/Los_Angeles',
'iqURL' => 'http://www.rausnitz.com/iq/bay/',
'defaultLat' => '37.589625',
'defaultLong' => '-122.200811',
'defaultZoom' => '9'
);

$torontoSettings = array(
'bikeshareSystem' => 'Bixi Toronto',
'feedURL' => 'https://toronto.bixi.com/data/bikeStations.xml',
'feedType' => 'xml',
'dbName' => $dbToronto,
'stationNumLength' => 4,
'timeZone' => 'America/Toronto',
'iqURL' => 'http://www.rausnitz.com/iq/toronto/',
'defaultLat' => '43.653264',
'defaultLong' => '-79.382458',
'defaultZoom' => '13'
);

$columbusSettings = array(
'bikeshareSystem' => 'CoGo',
'feedURL' => 'http://www.cogobikeshare.com/stations/json',
'feedType' => 'json',
'dbName' => $dbColumbus,
'stationNumLength' => 8,
'timeZone' => 'America/New_York',
'iqURL' => 'http://www.rausnitz.com/iq/columbus/',
'defaultLat' => '39.962002',
'defaultLong' => '-83.001081',
'defaultZoom' => '13'
);

$chattSettings = array(
'bikeshareSystem' => 'Bike Chattanooga',
'feedURL' => 'http://www.bikechattanooga.com/stations/json',
'feedType' => 'json',
'dbName' => $dbChatt,
'stationNumLength' => 8,
'timeZone' => 'America/New_York',
'iqURL' => 'http://www.rausnitz.com/iq/chattanooga/',
'defaultLat' => '35.05',
'defaultLong' => '-85.309207',
'defaultZoom' => '14'
);

$getSettings = array(
'dc' => $dcSettings,
'boston' => $bostonSettings,
'nyc' => $nycSettings,
'chicago' => $chicagoSettings,
'minn' => $minnSettings,
'bay' => $baySettings,
'london' => $londonSettings,
'toronto' => $torontoSettings,
'columbus' => $columbusSettings,
'chattanooga' => $chattSettings
);

?>

