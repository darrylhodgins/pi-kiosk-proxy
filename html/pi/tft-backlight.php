<?php

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

?>
{
	"success": <?php echo $success ?>,
	"power": <?php echo $powerVal ?>
}
