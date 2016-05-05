# Temperature Logger for ECE331
### By Jordan Millett
### Graph can be seen [Here](dystort.net:6969/temp.php)
-----------------------------
# Components
Raspberry Pi 2B

TC74A0 Temp sensor

# Commands for set up
## Enable I2C on the Pi

$sudo vim /etc/modules

Add the line: i2c-bcm2708



$sudo raspi-config

Go to Advanced Options, Then I2C, Enable, Load by default, Then Finish


## Install the following

$sudo apt-get update

$sudo apt-get install -y python-smbus i2c-tools sqlite3

## Get the I2C address

$sudo i2cdetect -y 1

# Python
The python script, tempget.py, uses the I2C address to get the temperature from the sensor and saves the temperature and time to a sqlite3 table stored in /var/log. Make sure the first time the script is run, it is run as root
## Cron
Cron is used to run the python script every minute

$sudo crontab -e

Add the following line at the end

\* * * * * /"YOUR DIRECTORY"/templogger/tempget.py

Restart the pi after to start getting samples

# PHP and GD set up
## Install
$sudo apt-get install lighttpd php5 php5-cgi php5-sqlite php5-gd

## Enable/Disable services
$sudo systemctl stop apache

$sudo systemctl disable apache

$sudo lighty-enable-mod fastcgi

$sudo lighty-enable-mod fastcgi-php

$sudo systemctl restart lightppd

## PHP
Add a link to graph.php to /var/www/html/

$sudo ln -s /var/www/html/temp.php /"YOUR DIRECTORY"/templogger/graph.php

Go to the IP address of the web browser and append /temp.php

---------------------
# Useful Sources
I2C with python -> http://www.instructables.com/id/Raspberry-Pi-I2C-Python/#intro

Sqlite3 in general, most importantly query clauses -> http://www.tutorialspoint.com/sqlite/

Sqlite3 with python -> http://www.tutorialspoint.com/sqlite/sqlite_python.htm


