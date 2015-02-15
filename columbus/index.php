<?

include '../config.php';
include '../settings.php';

$cityName = basename(dirname(__FILE__));
$settings = $getSettings[$cityName];

foreach ($settings as $type => $attribute) {
  ${$type} = $attribute;
}

include '../html/map/head.php';
include '../html/map/body.php';

?>