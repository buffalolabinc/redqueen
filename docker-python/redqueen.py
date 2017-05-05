#!/usr/bin/env python2

import sqlite3
import MySQLdb
from xbee import ZigBee
import serial
from struct import pack
import argparse
import sys
import os
import arrow

parser = argparse.ArgumentParser(description='RedQueen door system daemon.')
parser.add_argument('--baud-rate', type=int, default=115200)
parser.add_argument('--serial-port', default='ttyUSB0')
# parser.add_argument('--database', required=True)

args = parser.parse_args()

ser = serial.Serial( '/dev/%s' % args.serial_port, args.baud_rate)
xbee = ZigBee(ser)

xbee.send('at', command='AT')
xbee.send('at', command='ID')
xbee.send('at', command='CN')

print "BOOTED"
sys.stdout.flush()

# Continuously read and print packets
while True:
    try:
        response = xbee.wait_read_frame()
        print response
	sys.stdout.flush()

        if 'rf_data' in response:
            conn = MySQLdb.connect(
		host=os.environ['MYSQL_PORT_3306_TCP_ADDR'],
		port=os.environ['MYSQL_PORT_3306_TCP_PORT'],
		user="redqueen",
		passwd="redqueen",
		db="redqueen")
            cmd, data = response['rf_data'].split(':', 1)
            if cmd != 'A':
                continue

	    door_card, pin = data.split(':')

            print "Card ", door_card, " PIN ", pin
            sys.stdout.flush()

            dowToColumn = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']

            # Schedules are relative to where the door is, our only door is in EST
            dateToday = arrow.utcnow().to('America/New_York')

            dayColumn = dowToColumn[ dateToday.weekday() ] 

            query = """
            SELECT DISTINCT 
                c.id,c.pin 
            FROM 
                cards c 
            LEFT JOIN 
                card_schedule cs ON (c.id = cs.card_id) 
            LEFT JOIN 
                schedules s ON (cs.schedule_id = s.id) 
            WHERE 
                c.code = %%s 
                AND c.isActive = 1 
                AND s.%s = 1 
                AND %%s BETWEEN s.startTime AND s.endTime
            """ % (conn.escape_string(dayColumn),)

            c = conn.cursor()
            c.execute(query, (door_card, dateToday.format('HH:mm:ss'),))
            card = c.fetchone()

            valid_pin = False

            if card is None:
                print "No card found"
                sys.stdout.flush()
            elif card[1] == pin:
                print "Found card, valid pin... opening door!"
                valid_pin = True
                print { 'data': pack('>bL', 0, 5) }
                sys.stdout.flush()
                xbee.send('tx', dest_addr=response['source_addr'], dest_addr_long=response['source_addr_long'], data=pack('>bL', 0, 5))
            else:
                print "Found card, invalid pin"
                sys.stdout.flush()

            c.execute('INSERT INTO logs (code, validPin, created_at) VALUES (%s, %s, NOW())', ( door_card, valid_pin ))

            conn.commit()

            c.close()
            conn.close()
    except KeyboardInterrupt:
        break

ser.close()
