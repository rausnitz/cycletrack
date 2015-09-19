<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Status extends Controller {

	public function before()
	{
		$this->city = $this->request->param('city');
		$this->config = Kohana::$config->load('bikeshares')[$this->city];
		$this->feedType = $this->config['type'];
		$this->keys = Kohana::$config->load('keys')[$this->config['format_id']];
		$this->systemName = $this->config['name'];
		date_default_timezone_set($this->config['timeZone']);

		$scrapePattern = "assets/scrape/$this->city/*." . $this->feedType;
		$recentFeeds = glob($scrapePattern);

		$allFeeds = $recentFeeds;
		$allFeeds[] = $this->config['feed'];

		$this->feedsAsPHP = [];
		$this->unixtime0 = time();
		$this->time0 = date('g:i:s a', $this->unixtime0);

		foreach (array_reverse($allFeeds) as $feed) {
			if ($this->feedType == 'xml') {
				$xml = simplexml_load_string(file_get_contents($feed));
				$this->feedsAsPHP[] = json_decode(json_encode($xml),TRUE);
			} else {
				$this->feedsAsPHP[] = json_decode(file_get_contents($feed), true);
			}
		}

		foreach (array_reverse($recentFeeds) as $feed) {
			preg_match('/at-(.*)\./', $feed, $match);
			$this->scrapeTimesUnix[] = $match[1];
		}

		foreach ($this->scrapeTimesUnix as $time) {
			$this->scrapeTimes[] = date('g:i a', $time);
		}

	}

	public function action_single()
	{
		$station = $this->request->param('station');
		$stationInfo = array(
			'name' => '',
			'bikes' => [],
			'docks' => [],
			'times' => [],
		);

		foreach ($this->feedsAsPHP as $key => $feed) {
			$grab = array_search($station, array_column($feed[$this->keys['stations_array']], $this->keys['id']));
			$stationInfo['name'] = $feed[$this->keys['stations_array']][$grab][$this->keys['name']];
			$stationInfo['bikes'][] = $feed[$this->keys['stations_array']][$grab][$this->keys['bikes']];
			$stationInfo['docks'][] = $feed[$this->keys['stations_array']][$grab][$this->keys['docks']];
		}

		$stationInfo['times'] = [$this->time0];
		foreach ($this->scrapeTimes as $time) {
			$stationInfo['times'][] = $time;
		}

		$view = View::factory('single');
		$view->stationInfo = $stationInfo;
		$view->city = $this->city;
		$view->station = $station;
		$view->systemName = $this->systemName;

		$this->response->body($view);

	}

	public function action_map()
	{
		$this->markerScript = "";
		$this->markerScript .= $this->initial_markers();
		$this->markerScript .= $this->marker_data();

		$view = View::factory('map');
		$view->city = $this->city;
		$view->systemName = $this->systemName;
		$view->markerScript = $this->markerScript;
		$view->mapboxToken = Kohana::$config->load('map')['mapboxToken'];
		$view->mapboxTiles = Kohana::$config->load('map')['mapboxTiles'];

		$this->response->body($view);

	}

	public function action_ajax()
	{
		$this->response->headers('Content-Type', 'text/javascript');

		$this->markerScript = "\n\t";
		$this->markerScript .= $this->marker_data();
		$this->markerScript .= "drawMarkers(); \n\t";
		$this->markerScript .= "map.locate({setView: false});";

		$this->response->body($this->markerScript);
	}

	private function initial_markers()
	{
		$markerScript = $this->markerScript;

		$statusNow = $this->feedsAsPHP[0];

		foreach ($statusNow[$this->keys['stations_array']] as $station) {
			$stationName = addslashes($station[$this->keys['name']]);
			$ID = $station[$this->keys['id']];
			$markerScript .= "var station_$ID = stationData['station_$ID'] = {}; \n\t";
			$markerScript .= "station_$ID.id = '$ID'; \n\t";
			$markerScript .= "station_$ID.name = '$stationName'; \n\t";
			$markerScript .= "station_$ID.marker = L.circleMarker([{$station[$this->keys['latitude']]}, {$station[$this->keys['longitude']]}]); \n\n\t";
		}

		return $markerScript;
	}

	private function marker_data()
	{
		$markerScript = $this->markerScript;

		foreach ($this->feedsAsPHP as $key => $feed) {
			foreach ($feed[$this->keys['stations_array']] as $station) {
				$ID = $station[$this->keys['id']];
				$markerScript .= "station_$ID.bikes$key = {$station[$this->keys['bikes']]}; station_$ID.docks$key = {$station[$this->keys['docks']]}; \n\t";
			}
		}

		$markerScript .= "\n\t";
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
