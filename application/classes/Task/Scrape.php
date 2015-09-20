<?php defined('SYSPATH') or die('No direct script access.');

// cron job runs this task to record bikeshare system status

class Task_Scrape extends Minion_Task
{
	protected function _execute(array $params)
    {
		$go = microtime(true); // record overall start time

		foreach (Kohana::$config->load('bikeshares') as $city => $data) {
			$thisGo = microtime(true); // record each scrape's start time
			$destinationDirectory = DOCROOT . "assets/scrape/$city/";
			$destinationFile = $destinationDirectory . 'at-' . time() . $data['type']; // file name includes scrape time

 			try {
				if ( copy($data['feed'], $destinationFile) ) { // copy feed to directory
					$feeds = glob($destinationDirectory . 'at-*' . $data['type']);
					$count = 0;
					foreach (array_reverse($feeds) as $file) {
						$count++;
						if ($count > 3) { unlink($file); } // keep only the three most recent scrapes
					}
					$stop = microtime(true); // record each scrape's end time
					echo $city . ' took ' . ($stop - $thisGo) . ' seconds' . PHP_EOL;
				}
			} catch (Exception $e) {
				echo $city . ' failed because: ' .  $e->getMessage();
			}
		}

		$stop = microtime(true); // record overall end time
		echo 'Scrape took ' . ($stop - $go) . ' seconds' . PHP_EOL;
    }
}
