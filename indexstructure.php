<?php
error_reporting(0);

echo "<!DOCTYPE html><html>
<head><title>Bikeshare IQ</title>
<link rel='stylesheet' type='text/css' href='../style.css'>
</head>
<body>
<div id='header'>
<span id='app-title'>Bikeshare IQ</span>
<br />
<span id='city'>for " . $bikeshareSystem . "</span>
</div>

<div id='intro'>
<p>(This is a work in progress. Map version coming soon. Multiple choice coming soon.) See how quickly a station is filling up or emptying out. Because this is text only, it loads fast on phones. Bookmark the stations you use regularly for even quicker reference next time.</p>
</div>

<hr />
";

if ($feedType == "xml") {
    include '../xmllist.php';
} else {
    include '../jsonlist.php';
}

echo "</body></html>";

?>