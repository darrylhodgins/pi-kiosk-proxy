<?php

$power = $_GET["power"];
$powerVal = "null";

if ($power == 'on') {
	file_put_contents("/sys/class/backlight/rpi_backlight/bl_power", "0");
	$powerVal = "true";
} else if ($power == 'off') {
	file_put_contents("/sys/class/backlight/rpi_backlight/bl_power", "1");
	$powerVal = "false";
}

?>
{
	"success": true,
	"power": <?php echo $powerVal ?>
}

