<!DOCTYPE html>

<html>
<head>
<title><?php echo $stationInfo['name']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link href='http://fonts.googleapis.com/css?family=Inconsolata:400,700' rel='stylesheet' type='text/css'>
<?php
echo HTML::style('assets/css/main.css');
?>

<style>

</style>
</head>

<body id="single">

<div id="main_info"><a href="<?php echo URL::base(); ?>">Cycletrack</a> <em>for <?php echo $systemName; ?></em></div>
<div id="station_name"><?php echo $stationInfo['name']; ?></div>
<div id="time_now"><?php echo $stationInfo['times'][0]; ?></div>
<div id="date_now"><?php echo date('l, M j', time()); ?></div>

<table>
	<tr id="labels">
		<td></td>
		<td>Bikes</td>
		<td>Docks</td>
	</tr>

	<?php for ($i=0; $i<4; $i++): ?>
	<tr class='<?php echo $i == 0 ? 'now' : 'recorded' ?>'>
		<td><?php echo $stationInfo['times'][$i]; ?></td>
		<td><?php echo $stationInfo['bikes'][$i]; ?></td>
		<td><?php echo $stationInfo['docks'][$i]; ?></td>
	</tr>
	<?php endfor; ?>
	
</table>

<div id="links"><a href="javascript:window.location.href=window.location.href">refresh</a> &bull; <a href="./">map</a> &bull; <a href="./list">list</a></div>

<div id="attribution">&copy; 2015 <a href="http://github.com/rausnitz" target="_blank">Zach Rausnitz</a></div>
	
</body>
</html>