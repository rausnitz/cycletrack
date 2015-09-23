// get new bike/dock data and feed times
var stationDockData = <?php echo json_encode($allStationsDockData); ?>;
var unixtimes = <?php echo json_encode($unixtimes); ?>;
var times = <?php echo json_encode($times); ?>;

// redraw markers with new bike/dock data
drawMarkers();
// find the user's location, but do not re-center the map on the user
map.locate({setView: false});
