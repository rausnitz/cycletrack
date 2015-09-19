<?php defined('SYSPATH') or die('No direct script access.');

class Controller_List extends Controller {

	public function action_index()
	{
		$city = $this->request->param('city');
		$system = Kohana::$config->load('bikeshares')[$city];
		$keys = Kohana::$config->load('keys')[$system['format_id']];

		if ($system['type'] == 'xml') {
			$xml = simplexml_load_string(file_get_contents($system['feed']));
			$feedAsPHP = json_decode(json_encode($xml),TRUE);
		} else {
			$feedAsPHP = json_decode(file_get_contents($system['feed']), true);
		}

		$stationList = [];

		foreach ($feedAsPHP[$keys['stations_array']] as $station) {
			$name = $station[$keys['name']];
			$ID = $station[$keys['id']];
			$activeStation = ($station[$keys['hide_if']['key']] == $keys['hide_if']['value'] ? false : true);
			if ($activeStation) { $stationList[$name] = ['ID' => $ID]; }
		}

		array_multisort(array_keys($stationList), SORT_NATURAL, $stationList);

		$alphaNav = [];

		foreach ($stationList as $name=>$data) {
			$character = substr($name, 0, 1);
			$stationList[$name]['character'] = $character;
			$stationList[$name]['alphaAnchor'] = false;
		}
		foreach ($stationList as $name=>$data) {
			if ( !in_array($data['character'],$alphaNav) ) {
				$alphaNav[] = $data['character'];
				$stationList[$name]['alphaAnchor'] = true;
			}
		}

		$view = View::factory('list');
		$view->city = $city;
		$view->system = $system;
		$view->stationList = $stationList;
		$view->alphaNav = $alphaNav;

		$this->response->body($view);
	}

}
