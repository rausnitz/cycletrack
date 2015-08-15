<?php defined('SYSPATH') or die('No direct script access.');

class Controller_List extends Controller {
	
	public function action_index()
	{
		$city = $this->request->param('city');
		$system = Kohana::$config->load('bikeshares')[$city];
		
		$feedAsPHP = $system['type'] == 'xml' ? new SimpleXMLElement($system['feed'], NULL, TRUE) : json_decode(file_get_contents($system['feed'], true), true);
		
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
			foreach ($feedAsPHP['stationBeanList'] as $station) {
				$name = $station['stationName'];
				$ID = $station['id'];
				if ($station['availableBikes'] + $station['availableDocks'] > 0) {
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