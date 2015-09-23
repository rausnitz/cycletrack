<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Status extends Controller {

	public function before()
	{
		// basic info for this bikeshare system/feed
		$this->city = $this->request->param('city');
		$this->system = Kohana::$config->load('bikeshares')[$this->city];
		$this->keys = Kohana::$config->load('keys')[$this->system['format_id']];
		date_default_timezone_set($this->system['timeZone']);

		// find scrapes of this feed that have been stored
		$scrapePattern = "assets/scrape/$this->city/*." . $this->system['type'];
		$feeds = glob($scrapePattern);

		// create arrays of feed times
		$this->unixtimes = [time()]; // record current time first
		// add scrape times
		foreach (array_reverse($feeds) as $feed) {
			preg_match('/at-(.*)\./', $feed, $match); // scrape times can be found in file names
			$this->unixtimes[] = (int) $match[1];
		}

		// create array of feed times formatted nicely
		$this->times = [];
		foreach ($this->unixtimes as $index=>$time) {
			if ($index == 0) { $this->times[] = date('g:i:s a', $time); } // e.g. 9:01:24 pm
			else { $this->times[] = date('g:i a', $time); } // e.g. 11:40 pm
		}

		// retrieve live feed and add to feeds array
		$feeds[] = $this->system['feed'];

		// convert feed files to PHP variables
		$this->feeds = [];
		foreach (array_reverse($feeds) as $feed) {
			if ($this->system['type'] == 'xml') {
				// if XML file, first convert it to JSON, so that all feeds can be parsed the same way
				$xml = simplexml_load_string(file_get_contents($feed));
				$this->feeds[] = json_decode(json_encode($xml),true);
			} else {
				$this->feeds[] = json_decode(file_get_contents($feed), true);
			}
		}

	}

	public function action_single() // for viewing one station's status on its own text-only page
	{
		// which station
		$station = $this->request->param('station');

		// variable for station info
		$stationInfo =[];

		// get bikes and docks info from each feed
		foreach ($this->feeds as $key => $feed) {
			// find the station's array key by its ID value
			$find = array_search($station, array_column($feed[$this->keys['stations_array']], $this->keys['id']));
			if ($find) { // only continue if the station ID is found in the feed...
				$found = $feed[$this->keys['stations_array']][$find]; // ...because when $find == false, this line will treat [$find] like [0] and grab the first station in the array
				// get name, bikes, docks info
				if ($key == 0) { $stationInfo['name'] = $found[$this->keys['name']]; }
				$stationInfo['bikes'][$key] = $found[$this->keys['bikes']];
				$stationInfo['docks'][$key] = $found[$this->keys['docks']];
			} elseif ($key == 0) { // if requested station doesn't exist in live feed, redirect to station list page
				$this->redirect("$this->city/list");
			}
		}

		// prepare view variables
		$view = View::factory('single');
		$view->stationInfo = $stationInfo;
		$view->city = $this->city;
		$view->station = $station;
		$view->systemName = $this->system['name'];
		$view->times = $this->times;

		$this->response->body($view);

	}

	public function action_map()
	{
		// for the initial station data, just use the live feed
		$statusNow = $this->feeds[0];

		$allStationsData = [];

		foreach ($statusNow[$this->keys['stations_array']] as $station) {
			$id = $station[$this->keys['id']];

			$stationData = [];
			$stationData['id'] = $id;
			$stationData['show'] = ($station[$this->keys['hide_if']['key']] == $this->keys['hide_if']['value'] ? false : true);
			$stationData['name'] = addslashes($station[$this->keys['name']]);
			$stationData['latitude'] = $station[$this->keys['latitude']];
			$stationData['longitude'] = $station[$this->keys['longitude']];

			$allStationsData['station_'.$id] = $stationData;
		}

		// prepare view variables
		$view = View::factory('map');
		$view->city = $this->city;
		$view->systemName = $this->system['name'];
		$view->allStationsData = $allStationsData;
		$view->allStationsDockData = $this->dock_data();
		$view->unixtimes = $this->unixtimes;
		$view->times = $this->times;
		$view->mapboxToken = Kohana::$config->load('map')['mapboxToken'];
		$view->mapboxTiles = Kohana::$config->load('map')['mapboxTiles'];

		$this->response->body($view);

	}

	public function action_ajax() // for updating the map
	{
		$view = View::factory('update');
		$view->allStationsDockData = $this->dock_data();
		$view->unixtimes = $this->unixtimes;
		$view->times = $this->times;

		$this->response->headers('Content-Type', 'text/javascript');
		$this->response->body($view);
	}

	private function dock_data() // get bike and dock info for map markers
	{
		$allStationsDockData = [];

		foreach ($this->feeds as $key => $feed) {
			foreach ($feed[$this->keys['stations_array']] as $station) {
				$id = $station[$this->keys['id']];
				$stationDockData = [];
				$stationDockData['bikes'] = (int) $station[$this->keys['bikes']];
				$stationDockData['docks'] = (int) $station[$this->keys['docks']];
				$allStationsDockData['station_'.$id]['feed'.$key] = $stationDockData;
			}
		}

		return $allStationsDockData;
	}

}
