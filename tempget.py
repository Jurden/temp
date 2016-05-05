#!/usr/bin/python
import smbus
from datetime import datetime
import sqlite3

# Stuff for reading temperature with i2c
bus = smbus.SMBus(1)
address = 0x48
# Get temperature in Fahrenheit 
temp = 32 + 1.8 * bus.read_byte_data(address, 0)

# Sqlite
# Connect to or create database "temp" 
conn = sqlite3.connect('/var/log/temp.db')
# Creat a table "temp" if it doesn't exist
conn.execute("CREATE TABLE IF NOT EXISTS temp (time text, temp int)")
# Get local time in a nice format
time = datetime.now();
# Add the time and temperature to the table
conn.execute("INSERT INTO temp (time, temp) VALUES (?, ?)", (time, temp))
conn.commit()
conn.close()
