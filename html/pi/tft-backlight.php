<?php
header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Fri, 30 Oct 2020 00:00:00 GMT"); // Date in the past

// Query parameters
$power = $_GET["power"];
$brightness = $_GET["brightness"];

$result = [
	"success" => false,
	"power" => null,
	"brightness" => null
];

if (is_numeric($brightness) && $brightness >= 0 && $brightness < 1024) {
	shell_exec("/usr/bin/gpio -g mode 18 pwm");
	
	shell_exec("/usr/bin/gpio -g pwm 18 $brightness");

	$result = [
		"success" => true,
		"power" => (brightness > 0),
		"brightness" => $brightness
	];
} else if ($power) {
	shell_exec("/usr/bin/gpio -g mode 18 out");
	
	if ($power == 'on') {
		shell_exec("/usr/bin/gpio -g write 18 1");
		$result = [
			"success" => true,
			"power" => true,
			"brightness" => 1023
		];
	} else if ($power == 'off') {
		shell_exec("/usr/bin/gpio -g write 18 0");
		$result = [
			"success" => true,
			"power" => false,
			"brightness" => 0
		];
	}
}

echo json_encode($result);
?>