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

		// get current time and scrape times
		$this->unixtime0 = time();
		$this->time0 = date('g:i:s a', $this->unixtime0); // e.g. 9:01:24 pm
		$this->scrapeTimesUnix = [];
		$this->scrapeTimes = [];
		// scrape times can be found in file names
		foreach (array_reverse($feeds) as $feed) {
			preg_match('/at-(.*)\./', $feed, $match);
			$this->scrapeTimesUnix[] = $match[1];
		}
		foreach ($this->scrapeTimesUnix as $time) {
			$this->scrapeTimes[] = date('g:i a', $time); // e.g. 11:40 pm
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
		$stationInfo = array(
			'name' => '',
			'bikes' => [],
			'docks' => [],
			'times' => [],
		);

		// get bikes and docks info from each feed
		foreach ($this->feeds as $key => $feed) {
			// find the station's array key by its ID value
			$find = array_search($station, array_column($feed[$this->keys['stations_array']], $this->keys['id']));
			$found = $feed[$this->keys['stations_array']][$find];
			// get name, bikes, docks info
			$stationInfo['name'] = $found[$this->keys['name']];
			$stationInfo['bikes'][] = $found[$this->keys['bikes']];
			$stationInfo['docks'][] = $found[$this->keys['docks']];
		}

		// pass along feed time info
		$stationInfo['times'] = [$this->time0];
		foreach ($this->scrapeTimes as $time) {
			$stationInfo['times'][] = $time;
		}

		// prepare view variables
		$view = View::factory('single');
		$view->stationInfo = $stationInfo;
		$view->city = $this->city;
		$view->station = $station;
		$view->systemName = $this->system['name'];

		$this->response->body($view);

	}

	public function action_map()
	{
		// gather JavaScript for making the markers
		$this->markerScript = "";
		$this->markerScript .= $this->initial_markers();
		$this->markerScript .= $this->marker_data();

		// prepare view variables
		$view = View::factory('map');
		$view->city = $this->city;
		$view->systemName = $this->system['name'];
		$view->markerScript = $this->markerScript;
		$view->mapboxToken = Kohana::$config->load('map')['mapboxToken'];
		$view->mapboxTiles = Kohana::$config->load('map')['mapboxTiles'];

		$this->response->body($view);

	}

	public function action_ajax() // for updating the map
	{
		$this->response->headers('Content-Type', 'text/javascript');

		// gather JavaScript for updating the markers
		$this->markerScript = "\n\t";
		$this->markerScript .= $this->marker_data();
		$this->markerScript .= "drawMarkers(); \n\t";
		$this->markerScript .= "map.locate({setView: false});";

		$this->response->body($this->markerScript);
	}

	private function initial_markers()
	{
		$markerScript = $this->markerScript;

		// for the initial markers, just use the live feed
		$statusNow = $this->feeds[0];

		foreach ($statusNow[$this->keys['stations_array']] as $station) {
			// get station names, IDs, and whether or not they're active
			$stationName = addslashes($station[$this->keys['name']]);
			$ID = $station[$this->keys['id']];
			$activeStation = ($station[$this->keys['hide_if']['key']] == $this->keys['hide_if']['value'] ? 'false' : 'true');
			// pass station info to JavaScript objects
			$markerScript .= "var station_$ID = stationData['station_$ID'] = {}; \n\t";
			$markerScript .= "station_$ID.id = '$ID'; \n\t";
			$markerScript .= "station_$ID.name = '$stationName'; \n\t";
			$markerScript .= "station_$ID.show = $activeStation; \n\t";
			// create Leaflet marker with latitude/longitude info
			$markerScript .= "station_$ID.marker = L.circleMarker([{$station[$this->keys['latitude']]}, {$station[$this->keys['longitude']]}]); \n\n\t";
		}

		return $markerScript;
	}

	private function marker_data() // add bike and dock info for map markers
	{
		$markerScript = $this->markerScript;

		foreach ($this->feeds as $key => $feed) {
			foreach ($feed[$this->keys['stations_array']] as $station) {
				$ID = $station[$this->keys['id']];
				// associate bike and dock counts with JavaScript object for each station
				$markerScript .= "station_$ID.bikes$key = {$station[$this->keys['bikes']]}; station_$ID.docks$key = {$station[$this->keys['docks']]}; \n\t";
			}
		}

		$markerScript .= "\n\t";

		// pass feed times to JavaScript
		$markerScript .= "var timeAtUpdate = '$this->time0'; \n\t";
		$markerScript .= "var unixtime0 = '$this->unixtime0'; \n\t";
		foreach ($this->scrapeTimes as $key => $time) {
			$key = $key + 1;
			$markerScript .= "var time$key = '$time'; \n\t";
		}
		foreach ($this->scrapeTimesUnix as $key => $time) {
			$key = $key + 1;
			$markerScript .= "var unixtime$key = $time; \n\t";
		}

		return $markerScript;
	}

}
