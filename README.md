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

As of March 22, 2020, this is known to work on Raspberry Pi 3B with Raspbian Desktop `2020-02-13-raspbian-buster.img`.

Flash a MicroSD card and do your usual network and localization setup.  Important settings in `sudo raspi-config` are:

* Boot Options
    * **Wait for Network at Boot** - Enabled
    * **Desktop/CLI** - Desktop Autologin 

I also recommend changing the default user password to something more secure, and enabling SSH so you can remotely log in to the kiosk to perform maintenance.

### Clone this repo

```bash
cd /home/pi
git clone https://github.com/darrylhodgins/pi-kiosk-proxy.git
```

### Dependencies

Install the packages required for this project:

* `unclutter` hides the mouse cursor after 5 seconds of inactivity
* `nginx` is a webserver; the brains behind the whole operation
* `php-fpm` allows you to run PHP scripts on the Raspberry Pi

```bash
sudo apt update
sudo apt install -y unclutter nginx php-fpm
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

The default configuration points to a demo website.  You'll want to modify [the kiosk configuration](./nginx-config/kiosk) to point to your remote webserver.

## Control Local Hardware

This proxy provides a straightforward way to access local resources (on the Raspberry Pi), while still allowing the majority of your content to live on a shared remote server.

### Backlight Control

If you want backlight.php to be able to control the backlight of the official Raspberry Pi toucscreen, add the following line to `/etc/udev/rules.d/99-com.rules`:

```
SUBSYSTEM=="backlight",RUN+="/bin/chmod 666 /sys/class/backlight/%k/brightness /sys/class/backlight/%k/bl_power"
```

## Cache Large Files

Put large graphics or other resources into the [`html`](./html) folder.
