<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Status extends Controller {
	
	public function before()
	{
		$this->city = $this->request->param('city');
		$this->config = Kohana::$config->load('bikeshares')[$this->city];
		$this->feedType = $this->config['type'];
		$this->format_id = $this->config['format_id'];
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
			$this->feedsAsPHP[] = ($this->feedType == 'xml') ? new SimpleXMLElement($feed, NULL, TRUE) : json_decode(file_get_contents($feed, true), true);
		}
		
		foreach (array_reverse($recentFeeds) as $feed) {
			preg_match('/at-(.*)\./', $feed, $match);
			$this->scrapeTimesUnix[] = $match[1];
		}
		
		foreach ($this->scrapeTimesUnix as $time) {
			$this->scrapeTimes[] = date('g:i a', $time);
		}
		
		// normalize property names in some feeds
		switch ($this->format_id) {
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
				$this->station_latitude_key = 'latitude';
				$this->station_longitude_key = 'longitude';
				break;
			case 3:
				// alternative JSON property-name format
				$this->stations_array_key = 'stations';
				$this->station_id_key = 'id';
				$this->station_name_key = 's';
				$this->station_bikes_key = 'ba';
				$this->station_docks_key = 'da';
				$this->station_latitude_key = 'la';
				$this->station_longitude_key = 'lo';
				break;
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

		if ($this->feedType == "xml") {
			foreach ($this->feedsAsPHP as $key => $feed) {
				$grab = $feed->xpath("station/terminalName[.='$station']/parent::*");
				$stationInfo['name'] = $grab[0]->name->__toString();
				$stationInfo['bikes'][] = $grab[0]->nbBikes->__toString();
				$stationInfo['docks'][] = $grab[0]->nbEmptyDocks->__toString();		
			}
		} elseif ($this->feedType == 'json') {
			foreach ($this->feedsAsPHP as $key => $feed) {
				$grab = array_search($station, array_column($feed[$this->stations_array_key], $this->station_id_key));
				$stationInfo['name'] = $feed[$this->stations_array_key][$grab][$this->station_name_key];
				$stationInfo['bikes'][] = $feed[$this->stations_array_key][$grab][$this->station_bikes_key];
				$stationInfo['docks'][] = $feed[$this->stations_array_key][$grab][$this->station_docks_key];
			}
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

		if ($this->feedType == 'xml') {
			foreach ($statusNow->station as $station) {
				$stationName = addslashes($station->name);
				$ID = $station->terminalName;
				$markerScript .= "var station_$ID = stationData.station_$ID = {}; \n\t";
				$markerScript .= "station_$ID.id = '$ID'; \n\t";
				$markerScript .= "station_$ID.name = '$stationName'; \n\t";
				$markerScript .= "station_$ID.marker = L.circleMarker([$station->lat, $station->long]); \n\n\t";
			}
		} elseif ($this->feedType == 'json') {
			foreach ($statusNow[$this->stations_array_key] as $station) {
				$stationName = addslashes($station[$this->station_name_key]);
				$ID = $station[$this->station_id_key];
				$markerScript .= "var station_$ID = stationData['station_$ID'] = {}; \n\t";
				$markerScript .= "station_$ID.id = '$ID'; \n\t";
				$markerScript .= "station_$ID.name = '$stationName'; \n\t";
				$markerScript .= "station_$ID.marker = L.circleMarker([{$station[$this->station_latitude_key]}, {$station[$this->station_longitude_key]}]); \n\n\t";
			}
		}
		
		return $markerScript;
	}
	
	private function marker_data()
	{	
		$markerScript = $this->markerScript;
		
		foreach ($this->feedsAsPHP as $key => $feed) {
			if ($this->feedType == 'xml') {
				foreach ($feed->station as $station) {
					$ID = $station->terminalName;
					$markerScript .= "station_$ID.bikes$key = $station->nbBikes; station_$ID.docks$key = $station->nbEmptyDocks; \n\t";
				}
			} elseif ($this->feedType == 'json') {
				foreach ($feed[$this->stations_array_key] as $station) {
					$ID = $station[$this->station_id_key];
					$markerScript .= "station_$ID.bikes$key = {$station[$this->station_bikes_key]}; station_$ID.docks$key = {$station[$this->station_docks_key]}; \n\t";
				}
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