# Dashboard Server

A proxying webserver and kiosk frontend for Raspberry Pi.

## Installation

Flash a MicroSD card and do your usual setup.  Important settings in `sudo raspi-config` are:

* Boot Options
    * **Wait for Network at Boot** - Enabled
    * **Desktop/CLI** - Desktop Autologin 

## Clone this repo

```bash
git clone https://github.com/darrylhodgins/pi-dashboard-proxy.git
```

## Dependencies

Install `apt` things:

```bash
sudo apt install unclutter nginx php-fpm
```

## Configuration

### UI Autostart

Put this in `sudo vi /etc/xdg/lxsession/LXDE-pi/autostart`:

```
@lxpanel --profile LXDE-pi
@pcmanfm --desktop --profile LXDE-pi
@xset s off
@xset -dpms
@xset s noblank

@/home/pi/pi-dashboard-proxy/scripts/run.sh
```

### Backlight Control

Append this line to end of `sudo vi /etc/udev/rules.d/99-com.rules`:

```
SUBSYSTEM=="backlight",RUN+="/bin/chmod 666 /sys/class/backlight/%k/brightness /sys/class/backlight/%k/bl_power"
```

### Symlink NGINX configuration

You could just symlink directly from the `nginx-dashboard-conf` to `sites-enabled`, but this extra symlink provides some breadcrumbs if you find yourself hunting around in `/etc/nginx` one day.

```
sudo rm /etc/nginx/sites-enabled/default
sudo ln -s /home/pi/pi-dashboard-proxy/nginx-dashboard-conf /etc/nginx/sites-available/dashboard
sudo ln -s /etc/nginx/sites-available/dashboard /etc/nginx/sites-enabled/dashboard
sudo systemctl restart nginx
```

