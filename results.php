<?php

error_reporting(0);

include('../config.php');

if ($feedType == "xml") {
    include('../xmlnow.php');
} else {
    include('../jsonnow.php');
}

$station = $_GET['s'];

date_default_timezone_set($timeZone);

mysql_connect($mysqlHost, $mysqlUser, $mysqlPass);
mysql_select_db($dbName);
$safeStation = mysql_real_escape_string($station);
$result1 = mysql_query("SELECT * FROM saved1 WHERE stationnumber='$safeStation'");
$row1 = mysql_fetch_array($result1);
$result2 = mysql_query("SELECT * FROM saved2 WHERE stationnumber='$safeStation'");
$row2 = mysql_fetch_array($result2);
$result3 = mysql_query("SELECT * FROM saved3 WHERE stationnumber='$safeStation'");
$row3 = mysql_fetch_array($result3);
$result4 = mysql_query("SELECT * FROM saved4 WHERE stationnumber='$safeStation'");
$row4 = mysql_fetch_array($result4);
echo "<!DOCTYPE html><html>
<head><title>Bikeshare IQ: "
.
$stationName
.
"</title>
<link rel='stylesheet' type='text/css' href='../style.css'>
</head>";

echo "<body>
<div id='results'>
<h2>
<span id='stationheader'>"
.
$stationName
.
"</span>
</h2>
<h4>"
.

date('g:i a', time())
."<br />".

date('l, M j', time())
.
"</h4>
<div align='center' style='margin-top:20px;'>
<table cellpadding='3'>
<tr>
<td></td>
<td>&nbsp;Bikes&nbsp;</td>
<td>&nbsp;Docks&nbsp;</td>
</tr>
<tr id='now'>
<td>Now</td>
<td>".
$stationBikes
."</td><td>".
$stationDocks
."</td></tr><tr><td>"
.
date('g:i a', $row1[4])
.
"</td>
<td>".$row1[2]."</td>
<td>".$row1[3]."</td>
</tr>

<tr>
<td>". date('g:i a', $row2[4])."</td>
<td>".$row2[2]."</td>
<td>".$row2[3]."</td>
</tr>


<tr>
<td>". date('g:i a', $row3[4])."</td>
<td>".$row3[2]."</td>
<td>".$row3[3]."</td>
</tr></table>
<p style='margin-top:5%;'>
<a href='javascript:window.location.href=window.location.href'>refresh</a>&nbsp;&nbsp;&#8226;&nbsp;&nbsp;<a href='$iqURL'>go back</a></p>
</div></div>";

echo "</body></html>";
?>