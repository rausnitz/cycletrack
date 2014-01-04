<?php

$stationNumbers = array();
$stationNames = array();
$stationNumbersRaw = array();
$stationNumbersObjects = array();
$stationNamesObjects = array ();

if ($feedType == "xml") {
    $xmlFeed = simplexml_load_file($feedURL); 
    
	foreach ($xmlFeed->xpath( '//terminalName') as $numbers) {
      $stationNumbersObjects[] = $numbers;
    }

	foreach ($xmlFeed->xpath( '//name') as $names) {
      $stationNamesObjects[] = $names;
    }

    foreach ($stationNumbersObjects as $a) {
	  $stationNumbers[] = "$a";
	}

	foreach ($stationNamesObjects as $a) {
	  $stationNames[] = "$a";
	}

} else {
    $jsonURL = file_get_contents($feedURL, true);
    $jsonDoc = json_decode($jsonURL, true);
    $stationInfo = $jsonDoc['stationBeanList'];

    foreach ($stationInfo as $station) {
      $stationNames[] = $station['stationName'];
      $stationNumbersRaw[] = $station['id'];
    }

    foreach ($stationNumbersRaw as $a) {
      $stationNumbers[] = str_pad($a,8,"0",STR_PAD_LEFT);
    }
}

$stationData = array_combine($stationNumbers, $stationNames);

asort($stationData);

$alphaOptions = array();
$alphaNav = array();
$stationList = array();
$linkEnds = array();
$linkList = array();

foreach ($stationData as $key => $value) {
  $linkEnds[] = "href='now.php?s=$key'>$value</a>";
}
	
foreach ($linkEnds as $link) {

  if (in_array(strtolower($link[(18 + $stationNumLength)]),$alphaOptions)) {
      $linkList[] = "<a ".$link;
  } else {
      $alphaOptions[] = strtolower($link[(18 + $stationNumLength)]);
	  $linkList[] = "<a id='".strtolower($link[(18 + $stationNumLength)])."' ".$link;
  }	

}

$alphaOptionMenu = array();
	
foreach ($alphaOptions as $a) {
  $alphaOptionMenu[] = "<a href='#" . $a . "'>" . $a . "</a>" . "&nbsp;";
}

	
foreach ($linkList as $a)	{
  $stationList[] = "<p>" . $a . "</p>";
}


?>