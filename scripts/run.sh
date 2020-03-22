#!/bin/sh
/usr/bin/chromium-browser --app=http://localhost \
	--kiosk \
	--noerrdialogs \
	--disable-session-crashed-bubble \
	--disable-infobars

