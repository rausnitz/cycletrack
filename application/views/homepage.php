<!DOCTYPE html>

<html>
<head>
<title>Cycletrack</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link href='http://fonts.googleapis.com/css?family=Inconsolata:400,700' rel='stylesheet' type='text/css'>
<?php
echo HTML::style('assets/css/main.css');
?>
</head>


<body id="homepage">

<div id="app_name">Cycletrack</div>

<div id="intro">
	<p>Cycletrack is a mobile-friendly web app to check the status of bikeshare stations.</p>

	<p>It shows the number of bikes and docks currently at each station. It also shows how those numbers have changed in the past 30 minutes. <em>Is a station filling up or emptying out? How quickly?</em></p>

	<p>Plus there's a text-only option, for times when you don't want to load a map on your phone. Bookmark the stations you use regularly.</p>
	
	<p>Click one to start:</p>
</div>

<div id="systems_list">

<?php foreach ($systems as $city=>$data): ?>
	<div class="system_link">
		<a href="<?php echo $city; ?>"><?php echo $data['place']; ?> ... <?php echo $data['name']; ?></a>
	</div>
<?php endforeach; ?>

</div>

<div id="attribution">&copy; 2015 <a href="http://github.com/rausnitz" target="_blank">Zach Rausnitz</a></div>

</body>
</html>