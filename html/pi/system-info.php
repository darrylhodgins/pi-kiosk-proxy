<?php 
header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Fri, 30 Oct 2020 00:00:00 GMT"); // Date in the past

$result = [
	"system" => trim(file_get_contents("/sys/firmware/devicetree/base/model")),
	"mac" => exec('/home/pi/pi-kiosk-proxy/html/pi/mac-address.sh'),
	"uptime" => exec('uptime -s')
];

echo json_encode($result);
?>
