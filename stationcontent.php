<?php

$stationData = array_combine($stationNumbers, $stationNames);
asort($stationData);
  
$linkArray = array();
$alphaNav = array(); 
$linkArray2 = array();

foreach ($stationData as $key => $value) {
  $linkArray[] = "href='now.php?s=$key'>$value</a>";
}
	
foreach ($linkArray as $link) {
  
  if (in_array(strtolower($link[(18 + $stationNumberLength)]),$alphaNav)) {
      $linkArray2[] = "<a ".$link;
  } else {
      $alphaNav[] = strtolower($link[(18 + $stationNumberLength)]);
	  $linkArray2[] = "<a id='".strtolower($link[(18 + $stationNumberLength)])."' ".$link;
  }	

}
	
echo "<span id='alphanav'><em>";

foreach ($alphaNav as $a) {

  echo "<a href='#".$a."'>".$a."</a>"."&nbsp;";

}

echo "</em></span><div id='stations'>";
	
foreach ($linkArray2 as $a)	{

  echo "<p>$a</p>";
 
}

echo "</div></div>";

?>