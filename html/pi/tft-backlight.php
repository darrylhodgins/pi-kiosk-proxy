<?php
header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Fri, 30 Oct 2020 00:00:00 GMT"); // Date in the past

$power = $_GET["power"];
$success = "false";
$powerVal = "null";

shell_exec("/usr/bin/gpio -g mode 18 out");

if ($power == 'on') {
	shell_exec("/usr/bin/gpio -g write 18 1");
	$success = "true";
	$powerVal = "true";
} else if ($power == 'off') {
	shell_exec("/usr/bin/gpio -g write 18 0");
	$success = "true";
	$powerVal = "false";
}

?>{
	"success": <?php echo $success ?>,
	"power": <?php echo $powerVal ?>
}
