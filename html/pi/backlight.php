<?php

$power = $_GET["power"];

if ($power == 'on') {
	file_put_contents("/sys/class/backlight/rpi_backlight/bl_power", "0");
} else if ($power == 'off') {
	file_put_contents("/sys/class/backlight/rpi_backlight/bl_power", "1");
}

?>

