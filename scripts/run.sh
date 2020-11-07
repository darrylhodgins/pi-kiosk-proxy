#!/bin/sh

# "--app" is your remote web application, but since Nginx is proxying, it appears to us as
# if it's coming from "localhost".  If your app is in a subdirectory, e.g., if your
# app is at https://foo.bar/baz/index.php, type "http://localhost/baz/index.php" below.

/usr/bin/chromium-browser --app=http://localhost/ \
	--kiosk \
	--noerrdialogs \
	--disable-session-crashed-bubble \
	--disable-infobars \
	--check-for-update-interval=1 \
	--simulate-critical-update
