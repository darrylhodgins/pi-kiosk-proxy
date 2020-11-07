<?php 
header('Content-Type: application/json');
$sys = trim(file_get_contents("/sys/firmware/devicetree/base/model"));
$mac = exec('/home/pi/pi-kiosk-proxy/html/pi/mac-address.sh'); 
$uptime = exec('uptime -s');
?>{
	"system": "<?php echo $sys ?>",
	"mac": "<?php echo $mac ?>",
	"upSince": "<?php echo $uptime ?>"
}
