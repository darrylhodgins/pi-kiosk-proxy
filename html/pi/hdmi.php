<?php
header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Fri, 30 Oct 2020 00:00:00 GMT"); // Date in the past

$power = $_GET["power"];
$success = "false";
$powerVal = "null";

// In order for this to work, the "www-data" user needs to be in the "video" group.
// sudo usermod -a -G video www-data
// and restart the web server.

if ($power == 'on') {
	exec("/usr/bin/vcgencmd display_power 1");
	$success = "true";
	$powerVal = "true";
} else if ($power == 'off') {
	exec("/usr/bin/vcgencmd display_power 0");
	$success = "true";
	$powerVal = "false";
}

?>{
	"success": <?php echo $success ?>,
	"power": <?php echo $powerVal ?>

}
