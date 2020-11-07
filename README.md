# Kiosk Server

A proxying webserver and kiosk frontend for Raspberry Pi.

_Why_ would someone want something like this?

* You can build and manage your kiosk on a remote web server.  This can be shared amongst multiple Raspberry Pi kiosks.
* The kiosk website can make API calls that can control the Raspberry Pi hardware.
* You can cache large content files on the MicroSD card, which could be helpful if you have a relatively resource-heavy website that only needs to make lightweight API calls to a remote server.
* You can make the MicroSD card read-only, which can help protect the kiosk, while still allowing changes to the content on the remote server.

In my example (below), I'm using a basic PHP script to control the backlight on the official 7" touchscreen.

## Installation

### Raspbian

As of October 19, 2020, this is known to work on Raspberry Pi 3B with Raspberry Pi OS `2020-08-20-raspios-buster-armhf.img`.

Flash a MicroSD card and do your usual network and localization setup.  Important settings in `sudo raspi-config` are:

* Boot Options
    * **Wait for Network at Boot** - Enabled
    * **Desktop/CLI** - Desktop Autologin 

I also recommend changing the default user password to something more secure, and enabling SSH so you can remotely log in to the kiosk to perform maintenance.  I like to also turn off the Splash Screen.

### Install Dependencies

Install the packages required for this project:

* `git` is required to get a copy of this repository from Github
* `unclutter` hides the mouse cursor after 5 seconds of inactivity
* `nginx` is a webserver; the brains behind the whole operation
* `php-fpm` allows you to run PHP scripts on the Raspberry Pi

```bash
sudo apt update
sudo apt install -y git unclutter nginx php-fpm
```

### Clone this repository

```bash
cd /home/pi
git clone https://github.com/darrylhodgins/pi-kiosk-proxy.git
```

## PiTFT

If you're installing on a PiTFT, follow the steps from Adafruit:

### Install PiTFT

```bash
sudo pip3 install --upgrade adafruit-python-shell click==7.0
sudo apt-get install -y git
git clone https://github.com/adafruit/Raspberry-Pi-Installer-Scripts.git
cd Raspberry-Pi-Installer-Scripts
sudo python3 adafruit-pitft.py --display=28c --rotation=90 --install-type=fbcp
```

### Modify Screen Resolution

```bash
sudo vi /boot/config.txt
```

```
# hdmi_cvt=640 480 60 1 0 0 0
hdmi_cvt=320 240 60 1 0 0 0
```

### Enable GPIO for PHP

```bash
sudo usermod -a -G gpio www-data
```

## Configuration

### UI Autostart

Put this in `/etc/xdg/lxsession/LXDE-pi/autostart`:

```
@lxpanel --profile LXDE-pi
@pcmanfm --desktop --profile LXDE-pi
@xset s off
@xset -dpms
@xset s noblank

@/home/pi/pi-kiosk-proxy/scripts/run.sh
```

### Symlink NGINX configuration

You could just symlink directly from the `nginx-config/kiosk` to NGINX's `sites-enabled`, but this extra symlink provides some breadcrumbs if you find yourself hunting around in `/etc/nginx` one day.

```
sudo rm /etc/nginx/sites-enabled/default
sudo ln -s /home/pi/pi-kiosk-proxy/nginx-config/kiosk /etc/nginx/sites-available/kiosk
sudo ln -s /etc/nginx/sites-available/kiosk /etc/nginx/sites-enabled/kiosk
sudo systemctl restart nginx
```

### Edit NGINX configuration

The default configuration points to a demo website.  You'll want to modify [the kiosk configuration](./nginx-config/kiosk) to point to your remote webserver.  Note that this must be the _root_ of a website; you can't point to a subdirectory.  If your web content resides in a subdirectory, you'll also want to modify [the run.sh script](./scripts/run.sh).

### Disable Chromium caching

Edit `/etc/chromium-browser/default`

```
CHROMIUM_FLAGS="--disk-cache-dir=/dev/null --disk-cache-size=1"
```

## Control Local Hardware

This proxy provides a straightforward way to access local resources (on the Raspberry Pi), while still allowing the majority of your content to live on a shared remote server.

Your web app can make `GET` requests to control the Pi:

### HDMI output

Turn on/off connected HDMI screen

#### Examples

```
curl http://localhost/pi/hdmi.php?power=off
curl http://localhost/pi/hdmi.php?power=on
```

#### Configuration

Add `www-data` user to `video` group and restart the web server.

```bash
sudo usermod -a -G video www-data
sudo systemctl restart nginx
```

### Pi Official Touchscreen Backlight `/pi/backlight.php`

Turn on/off the backlight of the official Raspberry Pi touchscreen

#### Examples

```
curl http://localhost/pi/backlight.php?power=off
curl http://localhost/pi/backlight.php?power=on
```

#### Configuration

Add the following line to `/etc/udev/rules.d/99-com.rules`:

(TODO: This change seems to always get conflict with `apt` upgrades, so I may want to find a more elegant solution)

```
SUBSYSTEM=="backlight",RUN+="/bin/chmod 666 /sys/class/backlight/%k/brightness /sys/class/backlight/%k/bl_power"
```

### Adafruit PiTFT Backlight `/pi/tft-backlight.php`

Turn on/off the backlight of an Adafruit PiTFT

#### Examples

```
curl http://localhost/pi/tft-backlight.php?power=off
curl http://localhost/pi/tft-backlight.php?power=on
```

### System Info `/pi/system-info.php`

Get the model number, MAC address and uptime of your Pi.  This could be useful if you want to have several kiosks configured, and your remote webserver can serve different content to each.

#### Example

```
curl http://localhost/pi/system-info.php

{
	"system": "Raspberry Pi 3 Model B Rev 1.2",
	"mac": "de:ad:be:ee:ee:ef",
	"upSince": "2020-11-07 04:53:32"
}
```

## Cache Large Files

You may copy large graphics or other resources into the [`html`](./html) folder, if you know they won't be changing.

## Optimization and other configuration

### Disable the Pi configuration wizard

If you've configured your Pi from the command line, remove the Wizard that pops up when you boot into Desktop

```bash
sudo rm /etc/xdg/autostart/piwiz.desktop 
```

## Configure Pi to use Overlay File System

After you've got it all working, set your Pi to use Overlay File System.  This will prevent all changes to the Micro SD card, and help to avoid corrupted files.

1. Open `raspi-config` (`sudo raspi-config`).
2. Choose **Performance Options**, then **Overlay File System**.
3. When asked, **Would you like the overlay file system to be enabled?**, select **Yes**.
4. When asked, **Would you like the boot partition to be write-protected?**, select **Yes**.
5. Reboot