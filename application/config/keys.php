<?php defined('SYSPATH') OR die('No direct script access.');

// different bikeshare systems use different terms in their feeds
// this config file allows the app to use the same code to parse every feed
return array(

	1 => array(
		'stations_array' => 'station',
		'id' => 'terminalName',
		'name' => 'name',
		'bikes' => 'nbBikes',
		'docks' => 'nbEmptyDocks',
		'latitude' => 'lat',
		'longitude' => 'long',
		'hide_if' => array( // key-value pair for stations that aren't in service and thus shouldn't appear in the app
			'key' => 'installed', 'value' => 'false'
		),
	),

	2 => array(
		'stations_array' => 'stationBeanList',
		'id' => 'id',
		'name' => 'stationName',
		'bikes' => 'availableBikes',
		'docks' => 'availableDocks',
		'latitude' => 'latitude',
		'longitude' => 'longitude',
		'hide_if' => array(
			'key' => 'statusValue', 'value' => 'Not In Service'
		),
	),

	3 => array(
		'stations_array' => 'stations',
		'id' => 'id',
		'name' => 's',
		'bikes' => 'ba',
		'docks' => 'da',
		'latitude' => 'la',
		'longitude' => 'lo',
		'hide_if' => array(
			'key' => 'st', 'value' => '2'
		),
	),

);
