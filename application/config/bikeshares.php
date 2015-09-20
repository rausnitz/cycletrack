<?php defined('SYSPATH') OR die('No direct script access.');

return array(

	'dc' => array(
		'name' => 'Capital Bikeshare',
		'feed' => 'http://capitalbikeshare.com/data/stations/bikeStations.xml',
		'type' => 'xml',
		'format_id' => 1, // format_id refers to config/keys.php
		'place' => 'Washington, D.C.',
		'timeZone' => 'America/New_York',
	),

	'boston' => array(
		'name' => 'Hubway',
		'feed' => 'http://thehubway.com/data/stations/bikeStations.xml',
		'type' => 'xml',
		'format_id' => 1,
		'place' => 'Boston',
		'timeZone' => 'America/New_York',
	),

	'nyc' => array(
		'name' => 'Citi Bike',
		'feed' => 'http://citibikenyc.com/stations/json',
		'type' => 'json',
		'format_id' => 2,
		'place' => 'New York',
		'timeZone' => 'America/New_York',
	),

	'chicago' => array(
		'name' => 'Divvy',
		'feed' => 'http://divvybikes.com/stations/json',
		'type' => 'json',
		'format_id' => 2,
		'place' => 'Chicago',
		'timeZone' => 'America/Chicago',
	),

	'seattle' => array(
		'name' => 'Pronto Cycle Share',
		'feed' => 'https://secure.prontocycleshare.com/data/stations.json',
		'type' => 'json',
		'format_id' => 3,
		'place' => 'Seattle',
		'timeZone' => 'America/Los_Angeles',
	),

	'minn' => array(
		'name' => 'Nice Ride',
		'feed' => 'https://secure.niceridemn.org/data2/bikeStations.xml',
		'type' => 'xml',
		'format_id' => 1,
		'place' => 'Twin Cities',
		'timeZone' => 'America/Chicago',
	),

	'bay' => array(
		'name' => 'Bay Area Bike Share',
		'feed' => 'http://bayareabikeshare.com/stations/json',
		'type' => 'json',
		'format_id' => 2,
		'place' => 'S.F. Bay Area',
		'timeZone' => 'America/Los_Angeles',
	),

	'toronto' => array(
		'name' => 'Bike Share Toronto',
		'feed' => 'https://www.bikesharetoronto.com/data/stations/bikeStations.xml',
		'type' => 'xml',
		'format_id' => 1,
		'place' => 'Toronto',
		'timeZone' => 'America/Toronto',
	),

	'columbus' => array(
		'name' => 'CoGo',
		'feed' => 'http://www.cogobikeshare.com/stations/json',
		'type' => 'json',
		'format_id' => 2,
		'place' => 'Columbus',
		'timeZone' => 'America/New_York',
	),

	'chattanooga' => array(
		'name' => 'Bike Chattanooga',
		'feed' => 'http://www.bikechattanooga.com/stations/json',
		'type' => 'json',
		'format_id' => 2,
		'place' => 'Chattanooga',
		'timeZone' => 'America/New_York',
	),

	'london' => array(
		'name' => 'Barclays Cycle Hire',
		'feed' => 'http://www.tfl.gov.uk/tfl/syndication/feeds/cycle-hire/livecyclehireupdates.xml',
		'type' => 'xml',
		'format_id' => 1,
		'place' => 'London',
		'timeZone' => 'Europe/London',
	),

);
