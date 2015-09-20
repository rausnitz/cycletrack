<?php defined('SYSPATH') or die('No direct script access.');

class Controller_List extends Controller {

	public function action_index()
	{
		// basic info for this bikeshare system/feed
		$city = $this->request->param('city');
		$system = Kohana::$config->load('bikeshares')[$city];
		$keys = Kohana::$config->load('keys')[$system['format_id']];

		// convert feed to PHP variable
		if ($system['type'] == 'xml') {
			// if XML file, first convert it to JSON, so that all feeds can be parsed the same way
			$xml = simplexml_load_string(file_get_contents($system['feed']));
			$feed = json_decode(json_encode($xml), true);
		} else {
			$feed = json_decode(file_get_contents($system['feed']), true);
		}

		// make a list of all stations (names and ID numbers)
		$stationList = [];
		foreach ($feed[$keys['stations_array']] as $station) {
			$name = $station[$keys['name']];
			$ID = $station[$keys['id']];
			// ignore stations that are in the feed but not active
			// otherwise add to list of stations
			$activeStation = ($station[$keys['hide_if']['key']] == $keys['hide_if']['value'] ? false : true);
			if ($activeStation) { $stationList[$name] = ['ID' => $ID]; }
		}

		// sort stations alphabetically
		// SORT_NATURAL places "E 10 St" after "E 9 St" instead of before "E 2 St"
		array_multisort(array_keys($stationList), SORT_NATURAL, $stationList);

		// prepare alphabetical navigation for station list
		$alphaNav = [];
		foreach ($stationList as $name=>$data) {
			// find first character in station name
			$character = substr($name, 0, 1);
			$stationList[$name]['character'] = $character;
			$stationList[$name]['alphaAnchor'] = false;
		}
		foreach ($stationList as $name=>$data) {
			// add character to alphabetical navigation no more than once
			// note that station should get anchor tag if it's the first one that starts with a certain character
			if ( !in_array($data['character'],$alphaNav) ) {
				$alphaNav[] = $data['character'];
				$stationList[$name]['alphaAnchor'] = true;
			}
		}

		// prepare view variables
		$view = View::factory('list');
		$view->city = $city;
		$view->system = $system;
		$view->stationList = $stationList;
		$view->alphaNav = $alphaNav;

		$this->response->body($view);
	}

}
