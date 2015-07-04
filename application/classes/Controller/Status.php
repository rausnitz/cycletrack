<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Status extends Controller {
	
	public function before()
	{
		$this->city = $this->request->param('city');
		$this->config = Kohana::$config->load('bikeshares')[$this->city];
		$this->feedType = $this->config['type'];
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
				$grab = array_search($station, array_column($feed['stationBeanList'], 'id'));
				$stationInfo['name'] = $feed['stationBeanList'][$grab]['stationName'];
				$stationInfo['bikes'][] = $feed['stationBeanList'][$grab]['availableBikes'];
				$stationInfo['docks'][] = $feed['stationBeanList'][$grab]['availableDocks'];
			}
		}

			
		$stationInfo['times'] = [$this->time0];
		foreach ($this->scrapeTimes as $time) {
			$stationInfo['times'][] = $time;
		}
		
		$view = View::factory('single');
		$view->stationInfo = $stationInfo;
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
			foreach ($statusNow['stationBeanList'] as $station) {
				$stationName = addslashes($station['stationName']);
				$ID = $station['id'];
				$markerScript .= "var station_$ID = stationData['station_$ID'] = {}; \n\t";
				$markerScript .= "station_$ID.id = '$ID'; \n\t";
				$markerScript .= "station_$ID.name = '$stationName'; \n\t";
				$markerScript .= "station_$ID.marker = L.circleMarker([$station[latitude], $station[longitude]]); \n\n\t";
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
				foreach ($feed['stationBeanList'] as $station) {
					$ID = $station['id'];
					$markerScript .= "station_$ID.bikes$key = $station[availableBikes]; station_$ID.docks$key = $station[availableDocks]; \n\t";
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