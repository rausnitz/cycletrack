<?php

if ($feedType == "json") {
   include('functions/jsonfeed.php');
} else {
   include('functions/xmlfeed.php');
}

$stationNumsNames = array_combine($stationNumbers, $stationNames);
$stationNumsBikes = array_combine($stationNumbers, $stationBikes);
$stationNumsDocks = array_combine($stationNumbers, $stationDocks);

$timeStamp = time();

mysql_connect($mysqlHost, $mysqlUser, $mysqlPass);
mysql_select_db($dbName);
mysql_query("DROP TABLE saved4");
mysql_query("RENAME TABLE saved3 to saved4");
mysql_query("RENAME TABLE saved2 to saved3");
mysql_query("RENAME TABLE saved1 to saved2");


foreach ($stationNumsNames as $key => $value) {
  $safeKey = mysql_real_escape_string($key);
  $safeValue = mysql_real_escape_string($value);
  mysql_query("CREATE TABLE saved1(stationnumber VARCHAR(1000), stationname VARCHAR(1000), bikes VARCHAR(1000), docks VARCHAR(1000), time VARCHAR(1000))");
  mysql_query("INSERT INTO saved1(stationnumber,stationname) VALUES('$safeKey','$safeValue')");
}
	
foreach ($stationNumsBikes as $key => $value) {
  $safeKey = mysql_real_escape_string($key);
  $safeValue = mysql_real_escape_string($value);
  mysql_query("UPDATE saved1 SET bikes='$safeValue' WHERE stationnumber='$safeKey'");
}
	
foreach ($stationNumsDocks as $key => $value) {
  $safeKey = mysql_real_escape_string($key);
  $safeValue = mysql_real_escape_string($value);
  mysql_query("UPDATE saved1 SET docks='$safeValue' WHERE stationnumber='$safeKey'");
}
	
$safeTimeStamp = mysql_real_escape_string($timeStamp);
mysql_query("UPDATE saved1 SET time='$safeTimeStamp'");


?>