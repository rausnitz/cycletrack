<?php defined('SYSPATH') or die('No direct script access.');

class Controller_List extends Controller {
	
	public function action_index()
	{
		$city = $this->request->param('city');
		$system = Kohana::$config->load('bikeshares')[$city];
		
		$feedAsPHP = $system['type'] == 'xml' ? new SimpleXMLElement($system['feed'], NULL, TRUE) : json_decode(file_get_contents($system['feed'], true), true);
		
		// normalize property names in some feeds
		switch ($system['format_id']) {
			case 1:
				// all the XML feeds use consistent element names
				break;
			case 2:
				// typical JSON property-name format
				$this->stations_array_key = 'stationBeanList';
				$this->station_id_key = 'id';
				$this->station_name_key = 'stationName';
				$this->station_bikes_key = 'availableBikes';
				$this->station_docks_key = 'availableDocks';
				break;
			case 3:
				// alternative JSON property-name format
				$this->stations_array_key = 'stations';
				$this->station_id_key = 'id';
				$this->station_name_key = 's';
				$this->station_bikes_key = 'ba';
				$this->station_docks_key = 'da';
				break;
		}
		
		$stationList = [];
		
		if ($system['type'] == 'xml') {
			foreach ($feedAsPHP->station as $station) {
				$name = $station->name->__toString();
				$ID = $station->terminalName->__toString();
				if ($station->nbBikes + $station->nbEmptyDocks > 0) {
					$stationList[$name] = ['ID' => $ID];
				}
			}
		} elseif ($system['type'] == 'json') {
			foreach ($feedAsPHP[$this->stations_array_key] as $station) {
				$name = $station[$this->station_name_key];
				$ID = $station[$this->station_id_key];
				if ($station[$this->station_bikes_key] + $station[$this->station_docks_key] > 0) {
					$stationList[$name] = ['ID' => $ID];
				}
			}
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