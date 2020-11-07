<?php
header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Fri, 30 Oct 2020 00:00:00 GMT"); // Date in the past

$power = $_GET["power"];
$powerVal = "null";

if ($power == 'on') {
	file_put_contents("/sys/class/backlight/rpi_backlight/bl_power", "0");
	$powerVal = "true";
} else if ($power == 'off') {
	file_put_contents("/sys/class/backlight/rpi_backlight/bl_power", "1");
	$powerVal = "false";
}

?>{
	"success": true,
	"power": <?php echo $powerVal ?>
}
