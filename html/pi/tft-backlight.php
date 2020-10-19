<?php
echo "foo";
$power = $_GET["power"];

shell_exec("/usr/bin/gpio -g mode 18 out");

if ($power == 'on') {
	shell_exec("/usr/bin/gpio -g write 18 1");
} else if ($power == 'off') {
	shell_exec("/usr/bin/gpio -g write 18 0");
}

?>
