<!DOCTYPE html>

<html>
<head>
<title>Cycletrack for <?php echo $system['name']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link href='http://fonts.googleapis.com/css?family=Inconsolata:400,700' rel='stylesheet' type='text/css'>
<?php
echo HTML::style('assets/css/main.css');
?>

</head>

<body id="list">

<div id="main_info">Cycletrack <em>for <?php echo $system['name']; ?></em> <a href="<?php echo URL::base() . $city; ?>">[map]</a></div>

<div id="alphanav">
	<?php foreach ($alphaNav as $character): ?>
	<a href=<?php echo "'#$character'"; ?>><?php echo $character; ?></a>
	<?php endforeach; ?>
</div>

<?php foreach ($stationList as $name=>$data): ?>

<div class="station_link" <?php echo $data['alphaAnchor'] == true ? "id='{$data['character']}'" : '' ; ?>>
	<a href="<?php echo URL::base() . "$city/{$data['ID']}" ; ?>"><?php echo $name; ?></a>
</div>

<?php endforeach; ?>

<div id="attribution">&copy; 2015 <a href="http://github.com/rausnitz" target="_blank">Zach Rausnitz</a></div>

</body>
</html>