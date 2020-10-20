#!/bin/sh
/usr/bin/chromium-browser --app=http://localhost/index.html \
	--kiosk \
	--noerrdialogs \
	--disable-session-crashed-bubble \
	--disable-infobars \
	--check-for-update-interval=1 \
	--simulate-critical-update

