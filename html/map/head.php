<!DOCTYPE html>
<html>
<head>
<title>BIKESHARE IQ: <?php echo $bikeshareSystem; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">


	<link rel="stylesheet" href="http://leafletjs.com/dist/leaflet.css" />

	<script src="http://leafletjs.com/dist/leaflet.js"></script>
	<script>
function updateCurrent(station){	
var xmlhttp;
if(window.XMLHttpRequest){
	xmlhttp = new XMLHttpRequest();
} else {
	xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
}
var s = station;
var loadingdiv = document.getElementById('loading' + station);
xmlhttp.onreadystatechange = function(){
	if(xmlhttp.readyState == 4){
	document.getElementById('status' + s).innerHTML = xmlhttp.responseText;
	loadingdiv.innerHTML = "<a href='#' onclick='updateCurrent(&quot;"+s+"&quot;)'>update</a>";
	} 
}
loadingdiv.innerHTML = "&nbsp;<img height='12' src='../img/loading.gif' />";
url = "update.php?s="+s;
xmlhttp.open("GET",url,true);
xmlhttp.send();
}

	</script>
	
	<style type="text/css">
body {
    padding: 0;
    margin: 0;
}
html, body, #map {
    height: 100%;
}
.iq-popup a {
color: #4671d5;
}
.iq-popup a:hover {
text-decoration:none;
}
</style>
</head>