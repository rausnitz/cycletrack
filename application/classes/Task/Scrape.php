<?php defined('SYSPATH') or die('No direct script access.');

// cron job runs this task to record bikeshare system status
 
class Task_Scrape extends Minion_Task
{	
	protected function _execute(array $params)
    {
		$go = microtime(true);
		
		foreach (Kohana::$config->load('bikeshares') as $city => $data) {
			$thisGo = microtime(true);
			$feedType = "." . $data['type'];
			$destinationDirectory = DOCROOT . "assets/scrape/$city/";
			$destinationFile = $destinationDirectory . 'at-' . time() . $feedType;
			
 			try {
				if ( copy($data['feed'], $destinationFile) ) {
					$feeds = glob($destinationDirectory . 'at-*' . $feedType);
					$count = 0;
					foreach (array_reverse($feeds) as $file) {
						$count++;
						if ($count > 3) { unlink($file); }
					}
					$stop = microtime(true);
					echo $city . ' took ' . ($stop - $thisGo) . ' seconds' . PHP_EOL;
				}
			} catch (Exception $e) {
				echo $city . ' failed because: ' .  $e->getMessage();
			}
		}
		
		$stop = microtime(true);
		echo 'Scrape took ' . ($stop - $go) . ' seconds' . PHP_EOL;
    }
}