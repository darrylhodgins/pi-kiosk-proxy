#!/bin/sh
 
if [ -e /sys/class/net/eth0 ]; then
      MAC=$(cat /sys/class/net/eth0/address)
else
      MAC=$(cat /sys/class/net/wlan0/address)
fi

echo $MAC

